<?php

use App\Http\Controllers\FmFeesAmountCreateController;
use Illuminate\Support\Facades\Route;
use Modules\Fees\Http\Controllers\AjaxController;
use Modules\Fees\Http\Controllers\FeesController;
use App\Http\Controllers\FmFeesTypeAmountController;
use App\Http\Controllers\FmFeesAmountGenerateController;
use App\Http\Controllers\FmFeesCollectionController;
use Modules\Fees\Entities\FmFeesType;
use Modules\Fees\Http\Controllers\FeesReportController;
use Modules\Fees\Http\Controllers\StudentFeesController;
use App\Http\Controllers\FmFeesReceiptBookController;


Route::prefix('fees')->middleware(['auth', 'subdomain'])->group(function() {
    //Fees Group
    Route::get('fees-group', [FeesController::class, 'feesGroup'])->name('fees.fees-group')->middleware('userRolePermission:1131');
    Route::post('fees-group-store', [FeesController::class, 'feesGroupStore'])->name('fees.fees-group-store')->middleware('userRolePermission:1132');
    Route::get('fees-group-edit/{id}', [FeesController::class, 'feesGroupEdit'])->name('fees.fees-group-edit');
    Route::post('fees-group-update', [FeesController::class, 'feesGroupUpdate'])->name('fees.fees-group-update')->middleware('userRolePermission:1133');
    Route::post('fees-group-delete', [FeesController::class, 'feesGroupDelete'])->name('fees.fees-group-delete')->middleware('userRolePermission:1134');

    //Fees Type
    Route::get('fees-type', [FeesController::class, 'feesType'])->name('fees.fees-type')->middleware('userRolePermission:1135');
    Route::post('fees-type-store', [FeesController::class, 'feesTypeStore'])->name('fees.fees-type-store')->middleware('userRolePermission:1136');
    Route::get('fees-type-edit/{id}', [FeesController::class, 'feesTypeEdit'])->name('fees.fees-type-edit');
    Route::post('fees-type-update', [FeesController::class, 'feesTypeUpdate'])->name('fees.fees-type-update')->middleware('userRolePermission:1137');
    Route::post('fees-type-delete', [FeesController::class, 'feesTypeDelete'])->name('fees.fees-type-delete')->middleware('userRolePermission:1138');
    
    //Fees invoice
    Route::get('fees-invoice-list', [FeesController::class, 'feesInvoiceList'])->name('fees.fees-invoice-list')->middleware('userRolePermission:1139');
    Route::get('fees-invoice', [FeesController::class, 'feesInvoice'])->name('fees.fees-invoice');

    //Shovan fees type amount API routes
    //Route::get('fees-assign', [FeesController::class, 'feesAssign'])->name('fees.fees-assign'); 
    Route::post('fees-type-amount', [FmFeesTypeAmountController::class, 'feesTypeAmount']);
    Route::get('feesTypeAmountList', [FmFeesTypeAmountController::class, 'feesTypeAmountList']);
    Route::get('fetch-updated-data', [FmFeesTypeAmountController::class, 'fetchUpdatedData']);


    //Shovan fees type search API
    Route::get('fees-amount-search', [FmFeesTypeAmountController::class, 'searchFees']);
    Route::delete('delete-fees-type-amount/{id}', [FmFeesTypeAmountController::class, 'deleteFeesTypeAmount']);
    Route::post('update-fees-type-amount/{id}', [FmFeesTypeAmountController::class, 'updateFeesTypeAmount']);
    


    //Shovan fees Students assign report page view
    Route::get('store-fees-students-report', [FmFeesCollectionController::class, 'reportPage'])->name('store-fees-students-report');


    //Shovan fees type amount INSERT DELETE UPDATE - API
    Route::post('fees-amount-insert', [FmFeesAmountCreateController::class, 'storeFeeData']);
    Route::get('fetch-fee-data', [FmFeesAmountCreateController::class, 'fetchPaginatedFeeData']);
    Route::post('update-fees-type-amount/{id}', [FmFeesAmountCreateController::class, 'updateFeesTypeAmount']);
    Route::delete('delete-fees-type-amount/{id}', [FmFeesAmountCreateController::class, 'deleteFeesTypeAmount']);
    Route::get('fetch-all-fee-data', [FmFeesAmountCreateController::class, 'fetchAllFeeData']);
    Route::post('update-fee-data/{id}', [FmFeesAmountCreateController::class, 'updateFeeData']);
    Route::get('fetch-fee-data-for-edit/{id}', [FmFeesAmountCreateController::class, 'fetchFeeDataForEdit']);
    //Route::post('update-fee-data', [FmFeesAmountCreateController::class, 'updateFeeData']);

    Route::get('/search-and-fetch-data', [FmFeesAmountCreateController::class, 'searchAndFetchData']);
    // Replace 'backend-search-endpoint' with your desired endpoint
    Route::get('/backend-search-endpoint', [FmFeesAmountCreateController::class, 'searchAndFetchData']);


    //Shovan fees type amount Page routes
    Route::get('fees-assign', [FeesController::class, 'feesAssign'])->name('fees.fees-assign');
    //Route::get('fees-type-amount-list', [FmFeesTypeAmountController::class, '']);
    Route::get('addFeesSearch', [FmFeesCollectionController::class, 'addFeesSearch'])->name('fees.addFeesSearch');
    Route::get('/fees/result', [FmFeesCollectionController::class, 'showResult'])->name('fees.result');
    Route::get('/fees/add/{studentId}', [FmFeesCollectionController::class, 'showAddFeesForm'])->name('fees.add.form');
    Route::post('/fees/soft-delete/{id}', [FmFeesCollectionController::class, 'softDelete'])->name('fees.softDelete');
    //Route::post('/fees/delete/{id}', [FmFeesCollectionController::class, 'delete'])->name('fees.delete');
    Route::post('student-fees-delete/{id}', [FmFeesCollectionController::class, 'delete'])->name('fees.delete');

    Route::get('student-fees-edit/{id}', [FmFeesCollectionController::class, 'edit'])->name('fees.edit');
    Route::post('/fees/{id}/update', [FmFeesCollectionController::class, 'update'])->name('fees.update');
 
    Route::post('/fees/payment', [FmFeesCollectionController::class, 'makePayment'])->name('fees.payment');
    Route::get('fees-type-amount-entry-page', [FmFeesTypeAmountController::class, 'feesTypeAmountEntry']);

    //Student search for fine payments
    Route::get('fees/search-students', [FmFeesCollectionController::class, 'searchStudents'])->name('fees.search-students');
    Route::get('search', [FmFeesCollectionController::class, 'searchFees'])->name('fees.search');
    Route::get('/fees/getStudentsByClass', [FmFeesCollectionController::class, 'getStudentsByClass'])->name('fees.getStudentsByClass'); // New 9/06/2024
    Route::get('/fees/fetchStudentsByClass', [FmFeesCollectionController::class, 'fetchStudentsByClass'])->name('fees.fetchStudentsByClass'); // New 9/06/2024


// web.php

// Route to fetch sections based on class ID
Route::get('/fees/getSectionsByClass/{class_id}', [FmFeesCollectionController::class, 'getSectionsByClass']);

// Route to fetch students based on class ID and section ID
Route::get('/fees/getStudentsByClassAndSection/{class_id}/{section_id}', [FmFeesCollectionController::class, 'getStudentsByClassAndSection']);

Route::get('/fees/getStudentsByClass/{class_id}', [FmFeesCollectionController::class, 'getStudentsByClass']);

// Fetch sections by class using query parameter instead of URL parameter
//Route::get('/fees/getSectionsByClass', [FmFeesCollectionController::class, 'getSectionsByClass'])->name('fees.getSectionsByClass');

// Get sections based on class_id
Route::get('/fees/getSectionsByClass', [FeesController::class, 'getSectionsByClass'])->name('fees.getSectionsByClass');

// Get students based on class_id and section_id
Route::get('/fees/getStudentsByClassAndSection', [FeesController::class, 'getStudentsByClassAndSection'])->name('fees.getStudentsByClassAndSection');

// Fetch students by class and section using query parameters
//Route::get('/fees/getStudentsByClass', [FmFeesCollectionController::class, 'getStudentsByClass'])->name('fees.getStudentsByClass');


// Fetch students based on class and section
//Route::get('/fees/getStudentsByClassAndSection', [FmFeesCollectionController::class, 'getStudentsByClassAndSection'])->name('fees.getStudentsByClassAndSection');

    
Route::get('/fees/getSectionsByClass/{class_id}', [FmFeesCollectionController::class, 'getSectionsByClass'])->name('fees.getSectionsByClass');



    //Student Fine Add
    Route::post('add', [FmFeesCollectionController::class, 'addFees'])->name('fees.add');
    Route::get('/fees/summary', [FmFeesCollectionController::class, 'getFeesSummary'])->name('fees.summary');
    Route::post('student-fees-update-amount', [FmFeesCollectionController::class, 'updateAmount'])->name('fees.updateAmount');
    Route::get('search-Fees-Due-Date', [FmFeesCollectionController::class, 'searchFeesDueDate'])->name('fees.search-Fees-Due-Date');



    //Student Fees make payment Due date Search Page
    Route::get('/fees/student-fees-date-search', [FmFeesCollectionController::class, 'dateSearchPage'])->name('fees.student-fees-date-search');


    Route::get('fees-type-amount-generate', [FmFeesAmountGenerateController::class, 'feesAssignGenerate'])->name('fees.fees-type-amount-generate');
    Route::get('get-months/{year}', [FmFeesAmountGenerateController::class, 'getMonths']);
    Route::get('get-classes/{year}/{month}', [FmFeesAmountGenerateController::class, 'getClasses']);
    Route::get('get-years', [FmFeesAmountGenerateController::class, 'getYears']);
      
    Route::get('get-months', [FmFeesAmountGenerateController::class, 'getMonths']);
    Route::get('get-classes', [FmFeesAmountGenerateController::class, 'getClasses']);
    Route::get('fees-generate-list-page', [FmFeesAmountGenerateController::class, 'feesAssignGenerateList']);
    

    //Shovan feesRecieptBook routes 
    Route::get('fees-reciept-book' ,[FmFeesReceiptBookController::class, 'AllStudents']);
    Route::post('save-fees', [FmFeesReceiptBookController::class, 'saveAllFeesForClass']);


    //fees generate route
    Route::post('fees-generate', [FmFeesAmountGenerateController::class, 'feesGenerate']);
    
    


    

    Route::post('fees-invoice-store', [FeesController::class, 'feesInvoiceStore'])->name('fees.fees-invoice-store')->middleware('userRolePermission:1140');
    Route::get('fees-invoice-edit/{id}', [FeesController::class, 'feesInvoiceEdit'])->name('fees.fees-invoice-edit');
    Route::post('fees-invoice-update', [FeesController::class, 'feesInvoiceUpdate'])->name('fees.fees-invoice-update')->middleware('userRolePermission:1145');
    Route::get('fees-invoice-view/{id}/{state}', [FeesController::class, 'feesInvoiceView'])->name('fees.fees-invoice-view');
    Route::post('fees-invoice-delete', [FeesController::class, 'feesInvoiceDelete'])->name('fees.fees-invoice-delete')->middleware('userRolePermission:1146');
    Route::post('fees-payment-store', [FeesController::class, 'feesPaymentStore'])->name('fees.fees-payment-store')->middleware('userRolePermission:1147');
    Route::get('single-payment-view/{id}', [FeesController::class, 'singlePaymentView'])->name('fees.single-payment-view');
    
    Route::get('add-fees-payment/{id}', [FeesController::class, 'addFeesPayment'])->name('fees.add-fees-payment')->middleware('userRolePermission:1144');
    Route::get('delete-single-fees-transcation/{id}', [FeesController::class, 'deleteSingleFeesTranscation'])->name('fees.delete-single-fees-transcation');

    //Bank Payment
    Route::get('bank-payment', [FeesController::class, 'bankPayment'])->name('fees.bank-payment')->middleware('userRolePermission:1148');
    Route::post('search-bank-payment', [FeesController::class, 'searchBankPayment'])->name('fees.search-bank-payment')->middleware('userRolePermission:1149');
    Route::post('approve-bank-payment', [FeesController::class, 'approveBankPayment'])->name('fees.approve-bank-payment')->middleware('userRolePermission:1150');
    Route::post('reject-bank-payment', [FeesController::class, 'rejectBankPayment'])->name('fees.reject-bank-payment')->middleware('userRolePermission:1151');

    //Fees invoice Settings
    Route::get('fees-invoice-settings', [FeesController::class, 'feesInvoiceSettings'])->name('fees.fees-invoice-settings')->middleware('userRolePermission:1152');
    Route::post('fees-invoice-settings-update', [FeesController::class, 'ajaxFeesInvoiceSettingsUpdate'])->name('fees.fees-invoice-settings-update')->middleware('userRolePermission:1153');

    //Fees Report
    Route::get('due-fees', [FeesReportController::class, 'dueFeesView'])->name('fees.due-fees')->middleware('userRolePermission:1155');
    Route::post('search-due-fees', [FeesReportController::class, 'dueFeesSearch'])->name('fees.search-due-fees');
    Route::get('fine-report', [FeesReportController::class, 'fineReportView'])->name('fees.fine-report')->middleware('userRolePermission:1158');
    Route::post('fine-search', [FeesReportController::class, 'fineReportSearch'])->name('fees.fine-search');
    Route::get('payment-report', [FeesReportController::class, 'paymentReportView'])->name('fees.payment-report')->middleware('userRolePermission:1159');
    Route::post('payment-search', [FeesReportController::class, 'paymentReportSearch'])->name('fees.payment-search');
    Route::get('balance-report', [FeesReportController::class, 'balanceReportView'])->name('fees.balance-report')->middleware('userRolePermission:1160');
    Route::post('balance-search', [FeesReportController::class, 'balanceReportSearch'])->name('fees.balance-search');
    Route::get('waiver-report', [FeesReportController::class, 'waiverReportView'])->name('fees.waiver-report')->middleware('userRolePermission:1161');
    Route::post('waiver-search', [FeesReportController::class, 'waiverReportSearch'])->name('fees.waiver-search');

    // Student
    Route::get('student-fees-list/{id}', [StudentFeesController::class, 'studentFeesList'])->name('fees.student-fees-list');
    Route::get('student-fees-payment/{id}', [StudentFeesController::class, 'studentAddFeesPayment'])->name('fees.student-fees-payment');
    Route::post('student-fees-payment-store', [StudentFeesController::class, 'studentFeesPaymentStore'])->name('fees.student-fees-payment-store');
    
    //Ajax Request
    Route::post('fees-view-payment', [AjaxController::class, 'feesViewPayment'])->name('fees.fees-view-payment')->middleware('userRolePermission:1141');
    Route::get('select-student', [AjaxController::class, 'ajaxSelectStudent'])->name('fees.select-student');
    Route::post('select-fees-type', [AjaxController::class, 'ajaxSelectFeesType'])->name('fees.select-fees-type');
    Route::get('ajax-get-all-section', [AjaxController::class, 'ajaxGetAllSection'])->name('fees.ajax-get-all-section');
    Route::get('ajax-section-all-student', [AjaxController::class, 'ajaxSectionAllStudent'])->name('fees.ajax-section-all-student');
    Route::get('ajax-get-all-student', [AjaxController::class, 'ajaxGetAllStudent'])->name('fees.ajax-get-all-student');
    Route::post('change-method', [AjaxController::class, 'changeMethod'])->name('fees.change-method');
});
