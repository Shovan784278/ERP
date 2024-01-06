<?php

namespace App\Http\Controllers;

use Stripe;
use App\YearCheck;
use App\SmFeesType;
use PayPal\Api\Item;
use PayPal\Api\Payer;
use App\SmFeesPayment;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use App\SmGeneralSettings;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\RedirectUrls;
use App\SmFeesAssignDiscount;
use App\SmPaymentGatewaySetting;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\URL;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class SmCollectFeesByPaymentGateway extends Controller
{

    private $_api_context;
    private $mode;
    private $client_id;
    private $secret;


    // public function __construct()
    // {

    //     $paypal_configuration = \Config::get('paypal');
    //     $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_configuration['client_id'], $paypal_configuration['secret']));
    //     $this->_api_context->setConfig($paypal_configuration['settings']);
    // }

    public function __construct()
    {


        $paypalDetails = SmPaymentGatewaySetting::select('gateway_username', 'gateway_password', 'gateway_signature', 'gateway_client_id', 'gateway_secret_key')
            ->where('gateway_name', '=', 'Paypal')->first();

        $paypal_conf = Config::get('paypal');
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypalDetails->gateway_client_id,
                $paypalDetails->gateway_secret_key
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);

    }

    public function collectFeesByGateway($amount, $student_id, $type)
    {

        try {
            $amount = $amount;
            $fees_type_id = $type;
            $student_id = $student_id;
            $discounts = SmFeesAssignDiscount::where('student_id', $student_id)->get();

            $applied_discount = [];
            foreach ($discounts as $fees_discount) {
                $fees_payment = SmFeesPayment::select('fees_discount_id')->where('fees_discount_id', $fees_discount->id)->first();
                if (isset($fees_payment->fees_discount_id)) {
                    $applied_discount[] = $fees_payment->fees_discount_id;
                }
            }
            return view('backEnd.feesCollection.collectFeesByGateway', compact('amount', 'discounts', 'fees_type_id', 'student_id', 'applied_discount'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payByPaypal(Request $request)
    {
        try {
            $fees_type = SmFeesType::find($request->fees_type_id);
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $item_1 = new Item();

            $item_1->setName('Item 1')
                /** item name **/
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setPrice($request->get('real_amount'));
            /** unit price **/

            $item_list = new ItemList();
            $item_list->setItems(array($item_1));

            $amount = new Amount();
            $amount->setCurrency('USD')
                ->setTotal($request->get('real_amount'));

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription($fees_type->name);

            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(URL::to('paypal-return-status'))
                /** Specify return URL **/
                ->setCancelUrl(URL::to('paypal-return-status'));


            $payment = new Payment();
            $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));

            $user = Auth::user();
            $fees_payment = new SmFeesPayment();
            $fees_payment->student_id = $request->student_id;
            $fees_payment->fees_type_id = $request->fees_type_id;
            $fees_payment->amount = $request->real_amount;
            $fees_payment->assign_id = $request->assign_id;
            $fees_payment->payment_date = date('Y-m-d');
            $fees_payment->payment_mode = 'Paypal';
            $fees_payment->created_by = $user->id;
            $fees_payment->record_id = $request->record_id;
            $fees_payment->school_id = Auth::user()->school_id;
            $fees_payment->academic_id = getAcademicId();
            $fees_payment->active_status = 0;
            $fees_payment->save();


            $payment->create($this->_api_context);

            Session::put('paypal_payment_id', $payment->getId());
            Session::put('paypal_fees_paymentId', $fees_payment->id);

            return redirect($payment->getApprovalLink());
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getPaymentStatus(Request $request)
    {
        $paypal_fees_paymentId = Session::get('paypal_fees_paymentId');
        $fees_payment = null;
        $url = route('login');
        if (!is_null($paypal_fees_paymentId)) {
            $fees_payment = SmFeesPayment::find($paypal_fees_paymentId);
        }

        if (auth()->check()) {
            $role_id = auth()->user()->role_id;
            if ($role_id == 3 && $fees_payment) {
                $url = route('parent_fees', $fees_payment->student_id);
            } else if ($role_id == 2) {
                $url = route('student_fees');
            } else {
                $url = route('dashboard');
            }
        }

        try {
            $payment_id = Session::get('paypal_payment_id');
            Session::forget('paypal_payment_id');
            if (empty($request->input('PayerID')) || empty($request->input('token'))) {
                \Session::put('error', 'Payment failed');
                return redirect($url);
            }
            $payment = Payment::get($payment_id, $this->_api_context);
            $execution = new PaymentExecution();
            $execution->setPayerId($request->input('PayerID'));
            $result = $payment->execute($execution, $this->_api_context);

            if ($result->getState() == 'approved' && $fees_payment) {
                $fees_payment->active_status = 1;
                $fees_payment->save();
                Session::put('success', 'Payment success');
                Toastr::success('Operation successful', 'Success');

            } else {
                Toastr::error('Operation Failed', 'Failed');
            }

            return redirect($url);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect($url);
        }
    }


    public function collectFeesStripe($amount, $student_id, $type)
    {
        try {
            $amount = $amount;
            $fees_type_id = $type;
            $student_id = $student_id;
            $discounts = SmFeesAssignDiscount::where('student_id', $student_id)->get();
            $stripe_publisher_key = SmPaymentGatewaySetting::where('gateway_name', '=', 'Stripe')->first()->stripe_publisher_key;

            $applied_discount = SmFeesPayment::select('fees_discount_id')->whereIn('fees_discount_id', $discounts->pluck('id')->toArray())->pluck('fees_discount_id')->toArray();

            return view('backEnd.feesCollection.collectFeesStripeView', compact('amount', 'discounts', 'fees_type_id', 'student_id', 'applied_discount', 'stripe_publisher_key'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function stripeStore(Request $request)
    {
        try {
            $system_currency = '';
            $currency_details = SmGeneralSettings::select('currency')->where('id', 1)->first();
            if (isset($currency_details)) {
                $system_currency = $currency_details->currency;
            }
            $stripeDetails = SmPaymentGatewaySetting::select('stripe_api_secret_key', 'stripe_publisher_key')->where('gateway_name', '=', 'Stripe')->first();

            Stripe\Stripe::setApiKey($stripeDetails->stripe_api_secret_key);
            $charge = Stripe\Charge::create([
                "amount" => $request->real_amount * 100,
                "currency" => $system_currency,
                "source" => $request->stripeToken,
                "description" => "Student Fees payment"
            ]);
            if ($charge) {
                $user = Auth::user();
                $fees_payment = new SmFeesPayment();
                $fees_payment->student_id = $request->student_id;
                $fees_payment->fees_type_id = $request->fees_type_id;
                $fees_payment->amount = $request->real_amount;
                $fees_payment->payment_date = date('Y-m-d');
                $fees_payment->payment_mode = 'Stripe';
                $fees_payment->created_by = $user->id;
                $fees_payment->school_id = Auth::user()->school_id;
                $fees_payment->save();

                Toastr::success('Operation successful', 'Success');
                return redirect('student-fees');

            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('student-fees');

            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
