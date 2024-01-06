<?php 
namespace App\PaymentGateway;

use App\User;
use Exception;
use PayPal\Api\Item;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\RedirectUrls;
use App\SmPaymentGatewaySetting;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Brian2694\Toastr\Facades\Toastr;
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Modules\Fees\Entities\FmFeesTransaction;
use Illuminate\Validation\ValidationException;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Http\Controllers\FeesExtendedController;

class PaypalPayment{
    private $_api_context;
    private $mode;
    private $client_id;
    private $secret;

    public function __construct()
    {
        $paypalDetails = SmPaymentGatewaySetting::where('school_id',auth()->user()->school_id)
                        ->select('gateway_username', 'gateway_password', 'gateway_signature', 'gateway_client_id', 'gateway_secret_key')
                        ->where('gateway_name', '=', 'Paypal')
                        ->first();

        if(!$paypalDetails || !$paypalDetails->gateway_secret_key){
            Toastr::warning('Paypal Credentials Can Not Be Blank', 'Warning');
            return redirect()->send()->back();
        }

        $paypal_conf = Config::get('paypal');
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypalDetails->gateway_client_id,
                $paypalDetails->gateway_secret_key
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function handle($data)
    { 
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item1 = new Item();
        $item1->setName('Fees Collection')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($data['amount']);

        $itemList = new ItemList();
        $itemList->setItems(array($item1));

        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($data['amount']);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(URL::to('payment_gateway_success_callback','PayPal'))
        ->setCancelUrl(URL::to('payment_gateway_cancel_callback','PayPal'));

        $payment = new Payment();
        $payment->setIntent("order")
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions(array($transaction));

        try{
            $payment->create($this->_api_context);

            if($data['type'] == "Wallet"){
                $addPayment = new WalletTransaction();
                $addPayment->amount= $data['amount'];
                $addPayment->payment_method= $data['payment_method'];
                $addPayment->user_id= $data['user_id'];
                $addPayment->type= $data['wallet_type'];
                $addPayment->school_id= auth()->user()->school_id;
                $addPayment->academic_id= getAcademicId();
                $addPayment->save();

                Session::put('paypal_payment_id', $payment->getId());
                Session::put('payment_type', $data['type']);
                Session::put('wallet_payment_id',  $addPayment->id);
            }else{
                Session::forget('amount');
                Session::put('payment_type', $data['type']);
                Session::put('invoice_id', $data['invoice_id']);
                Session::put('amount', $data['amount']);
                Session::put('payment_method',  $data['payment_method']);
                Session::put('transcation_id',  $data['transcationId']);

                Session::put('paypal_payment_id', $payment->getId());
                Session::put('fees_payment_id',  $data['transcationId']);
            }
        }catch(Exception $e) {
            throw ValidationException::withMessages(['amount'=>$e->getMessage()]);
        }
        return $approvalUrl = $payment->getApprovalLink();
    }


    public function successCallback()
    {
    $request = App::make(Request::class);
      try {
            $payment_id = Session::get('paypal_payment_id');
            Session::forget('paypal_payment_id');
            if (empty($request->input('PayerID')) || empty($request->input('token'))) {
                Session::put('error','Payment failed');
                return Redirect::route('paywithpaypal');
            }
            
            $payment = Payment::get($payment_id, $this->_api_context);        
            $execution = new PaymentExecution();
            $execution->setPayerId($request->input('PayerID'));        
            $result = $payment->execute($execution, $this->_api_context);

            if ($result->getState() == 'approved') { 
                $paypal_fees_paymentId = Session::get('paypal_fees_paymentId');
                $paypal_wallet_paymentId = Session::get('paypal_wallet_paymentId');

                if(Session::get('type')== 'Wallet' && !is_null($paypal_wallet_paymentId)){
                    $status = WalletTransaction::find($paypal_wallet_paymentId);
                    $status->status = "approve";
                    $status->updated_at = date('Y-m-d');
                    $result = $status->update();
                    if($result){
                        $user = User::find($status->user_id);
                        $currentBalance = $user->wallet_balance;
                        $user->wallet_balance = $currentBalance + $request->amount;
                        $user->update();
                        $gs = generalSetting();
                        $compact['full_name'] =  $user->full_name;
                        $compact['method'] =  $status->payment_method;
                        $compact['create_date'] =  date('Y-m-d');
                        $compact['school_name'] =  $gs->school_name;
                        $compact['current_balance'] =  $user->wallet_balance;
                        $compact['add_balance'] =  $request->amount;

                        @send_mail($user->email, $user->full_name, "wallet_approve", $compact);
                    }
                    return redirect()->route('wallet.my-wallet');

                }elseif(Session::get('payment_type') == "Fees" && !is_null(Session::get('fees_payment_id'))){
                    $transcation= FmFeesTransaction::find(Session::get('fees_payment_id'));

                    $extendedController = new FeesExtendedController();
                    $extendedController->addFeesAmount(Session::get('fees_payment_id'), null);
                    
                    Session::put('success', 'Payment success');
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->to(url('fees/student-fees-list',$transcation->student_id));
                }else{
                    Toastr::error('Operation Failed paypal', 'Failed');
                    return redirect()->back();
                }
            }
        }catch(\Exception $e) {
            Log::info($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->send()->back();
        }
    }

    public function cancelCallback(){
        
    }
}
