<?php

namespace Modules\Fees\Http\Controllers;

use App\User;
use App\SmSchool;
use App\SmAddIncome;
use App\SmBankAccount;
use App\SmBankStatement;
use App\SmPaymentMethhod;
use Illuminate\Routing\Controller;
use Modules\Fees\Entities\FmFeesWeaver;
use Modules\Fees\Entities\FmFeesInvoice;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Fees\Entities\FmFeesInvoiceChield;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Entities\FmFeesTransactionChield;

class FeesExtendedController extends Controller
{
    public function invStore($request)
    {
        $storeFeesInvoice = new FmFeesInvoice();
        $storeFeesInvoice->class_id = $request->class;
        $storeFeesInvoice->create_date = date('Y-m-d', strtotime($request->create_date));
        $storeFeesInvoice->due_date = date('Y-m-d', strtotime($request->due_date));
        $storeFeesInvoice->payment_status = $request->payment_status;
        $storeFeesInvoice->payment_method = $request->payment_method;
        $storeFeesInvoice->bank_id = $request->bank;
        $storeFeesInvoice->student_id = $request->student;
        $storeFeesInvoice->record_id = $request->record_id;
        $storeFeesInvoice->school_id = auth()->user()->school_id;
        $storeFeesInvoice->academic_id = getAcademicId();
        $storeFeesInvoice->save();
        $storeFeesInvoice->invoice_id = feesInvoiceNumber($storeFeesInvoice);
        $storeFeesInvoice->save();

        if($request->payment_status != "not"){
            $storeTransaction = new FmFeesTransaction();
            $storeTransaction->fees_invoice_id = $storeFeesInvoice->id;
            $storeTransaction->payment_method = $request->payment_method;
            $storeTransaction->bank_id = $request->bank;
            $storeTransaction->student_id = $request->student;
            $storeTransaction->record_id = $request->record_id;
            $storeTransaction->user_id = auth()->user()->id;
            $storeTransaction->paid_status = 'approve';
            $storeTransaction->school_id = auth()->user()->school_id;
            $storeTransaction->academic_id = getAcademicId();
            $storeTransaction->save();
        }

        foreach ($request->feesType as $key => $type) {
            $storeFeesInvoiceChield = new FmFeesInvoiceChield();
            $storeFeesInvoiceChield->fees_invoice_id = $storeFeesInvoice->id;
            $storeFeesInvoiceChield->fees_type = $type;
            $storeFeesInvoiceChield->amount = (float)$request->amount[$key];
            $storeFeesInvoiceChield->weaver = (float)$request->weaver[$key];
            $storeFeesInvoiceChield->sub_total = (float)$request->sub_total[$key];
            $storeFeesInvoiceChield->note = $request->note[$key];
            if ($request->paid_amount[$key] > 0) {
                $storeFeesInvoiceChield->paid_amount = (float)$request->paid_amount[$key];
                $storeFeesInvoiceChield->due_amount = (float)$request->sub_total[$key] - (float)$request->paid_amount[$key];
            } else {
                $storeFeesInvoiceChield->due_amount = (float)$request->sub_total[$key];
            }
            $storeFeesInvoiceChield->school_id = auth()->user()->school_id;
            $storeFeesInvoiceChield->academic_id = getAcademicId();
            $storeFeesInvoiceChield->save();

            if ($request->paid_amount[$key] > 0) {
                $storeTransactionChield = new FmFeesTransactionChield();
                $storeTransactionChield->fees_transaction_id = $storeTransaction->id;
                $storeTransactionChield->fees_type = $type;
                $storeTransactionChield->weaver = (float)$request->weaver[$key];
                $storeTransactionChield->paid_amount = (float)$request->paid_amount[$key];
                $storeTransactionChield->note = $request->note[$key];
                $storeTransactionChield->school_id = auth()->user()->school_id;
                $storeTransactionChield->academic_id = getAcademicId();
                $storeTransactionChield->save();

                // Income
                if($request->payment_status != "not"){
                    addIncome($request->payment_method, 'Fees Collect', $request->paid_amount[$key], $storeTransaction->id, auth()->user()->id);
                }

                // Bank
                if ($request->payment_method == "Bank") {
                    $payment_method = SmPaymentMethhod::where('method', $request->payment_method)->first();
                    $bank = SmBankAccount::where('id', $request->bank)
                        ->where('school_id', auth()->user()->school_id)
                        ->first();
                    $after_balance = $bank->current_balance + $request->paid_amount[$key];

                    $bank_statement = new SmBankStatement();
                    $bank_statement->amount = $request->paid_amount[$key];
                    $bank_statement->after_balance = $after_balance;
                    $bank_statement->type = 1;
                    $bank_statement->details = "Fees Payment";
                    $bank_statement->item_sell_id = $storeTransaction->id;
                    $bank_statement->payment_date = date('Y-m-d');
                    $bank_statement->bank_id = $request->bank;
                    $bank_statement->school_id = auth()->user()->school_id;
                    $bank_statement->payment_method = $payment_method->id;
                    $bank_statement->save();

                    $current_balance = SmBankAccount::find($request->bank);
                    $current_balance->current_balance = $after_balance;
                    $current_balance->update();
                }
            }

            $storeWeaver = new FmFeesWeaver();
            $storeWeaver->fees_invoice_id = $storeFeesInvoice->id;
            $storeWeaver->fees_type = $type;
            $storeWeaver->student_id = $request->student;
            $storeWeaver->weaver = $request->weaver[$key];
            $storeWeaver->note = $request->note[$key];
            $storeWeaver->school_id = auth()->user()->school_id;
            $storeWeaver->academic_id = getAcademicId();
            $storeWeaver->save();
        }
    }

    public function addFeesAmount($transcation_id, $total_paid_amount)
    {
        $transcation = FmFeesTransaction::find($transcation_id);
        $allTranscations = FmFeesTransactionChield::where('fees_transaction_id', $transcation->id)->get();
        foreach ($allTranscations as $key => $allTranscation) {
            $transcationId = FmFeesTransaction::find($allTranscation->fees_transaction_id);

            $fesInvoiceId = FmFeesInvoiceChield::where('fees_invoice_id', $transcationId->fees_invoice_id)
                ->where('fees_type', $allTranscation->fees_type)
                ->first();

            $storeFeesInvoiceChield = FmFeesInvoiceChield::find($fesInvoiceId->id);
            $storeFeesInvoiceChield->due_amount = (float)$storeFeesInvoiceChield->due_amount - $allTranscation->paid_amount;
            $storeFeesInvoiceChield->paid_amount = (float)$storeFeesInvoiceChield->paid_amount + $allTranscation->paid_amount;
            $storeFeesInvoiceChield->update();

            // Income
            $payment_method = SmPaymentMethhod::where('method', $transcation->payment_method)->first();
            $income_head = generalSetting();

            $add_income = new SmAddIncome();
            $add_income->name = 'Fees Collect';
            $add_income->date = date('Y-m-d');
            $add_income->amount = $allTranscation->paid_amount;
            $add_income->fees_collection_id = $transcation->fees_invoice_id;
            $add_income->active_status = 1;
            $add_income->income_head_id = $income_head->income_head_id;
            $add_income->payment_method_id = $payment_method->id;
            if ($payment_method->id == 3) {
                $add_income->account_id = $transcation->bank_id;
            }
            $add_income->created_by = auth()->user()->id;
            $add_income->school_id = auth()->user()->school_id;
            $add_income->academic_id = getAcademicId();
            $add_income->save();

            if ($transcation->payment_method == "Bank") {
                $bank = SmBankAccount::where('id', $transcation->bank_id)
                    ->where('school_id', auth()->user()->school_id)
                    ->first();

                $after_balance = $bank->current_balance + $total_paid_amount;

                $bank_statement = new SmBankStatement();
                $bank_statement->amount = $allTranscation->paid_amount;
                $bank_statement->after_balance = $after_balance;
                $bank_statement->type = 1;
                $bank_statement->details = "Fees Payment";
                $bank_statement->payment_date = date('Y-m-d');
                $bank_statement->item_sell_id = $transcation->id;
                $bank_statement->bank_id = $transcation->bank_id;
                $bank_statement->school_id = auth()->user()->school_id;
                $bank_statement->payment_method = $payment_method->id;
                $bank_statement->save();

                $current_balance = SmBankAccount::find($transcation->bank_id);
                $current_balance->current_balance = $after_balance;
                $current_balance->update();
            }
            $fees_transcation = FmFeesTransaction::find($transcation->id);
            $fees_transcation->paid_status = 'approve';
            $fees_transcation->update();
        }

        if ($transcation->add_wallet_money > 0) {
            $user = User::find($transcation->user_id);
            $walletBalance = $user->wallet_balance;
            $user->wallet_balance = $walletBalance + $transcation->add_wallet_money;
            $user->update();

            $addPayment = new WalletTransaction();
            $addPayment->amount = $transcation->add_wallet_money;
            $addPayment->payment_method = $transcation->payment_method;
            $addPayment->user_id = $user->id;
            $addPayment->type = 'diposit';
            $addPayment->status = 'approve';
            $addPayment->note = 'Fees Extra Payment Add';
            $addPayment->school_id = auth()->user()->school_id;
            $addPayment->academic_id = getAcademicId();
            $addPayment->save();

            $school = SmSchool::find($user->school_id);
            $compact['full_name'] = $user->full_name;
            $compact['method'] = $transcation->payment_method;
            $compact['create_date'] = date('Y-m-d');
            $compact['school_name'] = $school->school_name;
            $compact['current_balance'] = $user->wallet_balance;
            $compact['add_balance'] = $transcation->add_wallet_money;
            $compact['previous_balance'] = $user->wallet_balance - $transcation->add_wallet_money;

            @send_mail($user->email, $user->full_name, "fees_extra_amount_add", $compact);

            sendNotification($user->id, null, null, $user->role_id, "Fees Xtra Amount Add");
        }
    }
}
