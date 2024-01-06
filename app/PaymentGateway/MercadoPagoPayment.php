<?php 
namespace App\PaymentGateway;

use App\User;
use App\SmStudent;
use App\SmPaymentGatewaySetting;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Wallet\Http\Controllers\WalletController;
use Modules\Fees\Http\Controllers\FeesExtendedController;

class MercadoPagoPayment{

    public function handle($data)
    {
        try{
            $mercadoPagoDetails = SmPaymentGatewaySetting::where('gateway_name', '=', 'MercadoPago')
                                ->where('school_id',auth()->user()->school_id)
                                ->select('mercado_pago_public_key', 'mercado_pago_acces_token')
                                ->first();

            if(!$mercadoPagoDetails || !$mercadoPagoDetails->mercado_pago_acces_token){
                Toastr::warning('MercadoPago Credentials Can Not Be Blank', 'Warning');
                if($data['type'] == 'Fees'){
                    return response()->json(['target_url'=>route('fees.student-fees-list', $data['studentId'])]);
                }
            }
            
            \MercadoPago\SDK::setAccessToken($mercadoPagoDetails->mercado_pago_acces_token);

            $payment = new \MercadoPago\Payment();
            $payment->transaction_amount = (float)$data['amount'];
            $payment->token = $data['token'];
            $payment->description = $data['description'];
            $payment->installments = $data['installments'];
            $payment->payment_method_id = $data['payment_method_id'];
            $payment->issuer_id = (int)$data['issuer_id'];

            $payer = new \MercadoPago\Payer();
            $payer->email = $data['payer']['email'];
            $payment->payer = $payer;
            $p = $payment->save();

            if($p && $payment->status == 'approved'){
                if($data['type'] == 'Fees'){
                    $paymentResponse = array(
                        'type' => $data['type'],
                        'trxID' => $data['traxId'],
                        'amount' => (float)$data['amount'],
                        'studentId' => $data['studentId'],
                    );
                }elseif($data['type'] == 'Wallet'){
                    $paymentResponse = array(
                        'type' => $data['type'],
                        'trxID' => $data['traxId'],
                        'amount' => (float)$data['amount'],
                        'userId' => $data['userId'],
                    );
                }
                
                $result = $this->successCallback($paymentResponse);
                return $result;
            }else{
                $result = $this->fail($data['type'], $data['studentId']);
                return $result;
            }
        }catch(\Exception $e){
            return response()->json(['message'=>$e]);
        }
    }


    public function successCallback($paymentResponse)
    {
      try {
            if($paymentResponse['type'] == 'Fees'){
                $transcationInfo = FmFeesTransaction::find($paymentResponse['trxID']);
                $extendedController = new FeesExtendedController();
                $extendedController->addFeesAmount($paymentResponse['trxID'], $paymentResponse['amount']);
                $student = SmStudent::with('parents')->find($transcationInfo->student_id);

                sendNotification("Mercado Payment Done", null, 1, 1);
                sendNotification("Mercado Payment Done", null, $student->user_id, 2);
                sendNotification("Mercado Payment Done", null, $student->parents->user_id, 3);

                Toastr::success('Operation successful', 'Success');
                return response()->json(['target_url'=>route('fees.student-fees-list', $transcationInfo->student_id)]);

            }elseif($paymentResponse['type'] == 'Wallet'){
                $user = User::find($paymentResponse['userId']);
                $currentamount = $user->wallet_balance;
                $addedAmount = $currentamount + $paymentResponse['amount'];
                $user->wallet_balance = $addedAmount;
                $user->update();
    
                $status = WalletTransaction::find($paymentResponse['trxID']);
                $status->status = 'approve';
                $status->updated_at = date('Y-m-d');
                $status->update();
    
                $compact['user_email'] = $user->email;
                $compact['method'] = $status->payment_method;
                $compact['create_date'] = $status->created_by;
                $compact['current_balance'] = $user->wallet_balance;
                $compact['add_balance'] = $paymentResponse['amount'];
    
                @send_mail($user->email, $user->full_name, "wallet_approve", $compact);
                $controller = new WalletController();
                $controller->sendNotification($user->id, $user->role_id, "Wallet Approve");
                
                Toastr::success('Operation successful', 'Success');
                return response()->json(['target_url'=>url('wallet/my-wallet')]);
            }else{
                return response()->json(['message'=>'Error']);
            }
        }catch(\Exception $e) {
            Log::info($e->getMessage());
            Toastr::error('Transaction is Invalid');
            return response()->json(['target_url'=>route('fees.student-fees-list', $paymentResponse['studentId'])]);
        }
    }

    public function fail($type, $studentId)
    {
        if($type == 'Fees'){
            Toastr::error('Transaction is Invalid');
            return response()->json(['target_url'=>route('fees.student-fees-list', $studentId)]);
        }
    }
}
