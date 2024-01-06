<?php

namespace App\Http\Controllers\Admin\FeesCollection;

use App\ApiBaseMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeesCollection\SmFeesMasterRequest;
use App\Models\StudentRecord;
use App\Notifications\FeesAssignNotification;
use App\SmClass;
use App\SmFeesAssign;
use App\SmFeesAssignDiscount;
use App\SmFeesGroup;
use App\SmFeesMaster;
use App\SmFeesPayment;
use App\SmFeesType;
use App\SmNotification;
use App\SmParent;
use App\SmStudent;
use App\SmStudentCategory;
use App\SmStudentGroup;
use App\tableList;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class SmFeesMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
        // User::checkAuth();
    }

    public function index(Request $request)
    {
        try {
            $fees_groups = SmFeesGroup::get();
            $fees_masters = SmFeesMaster::with('feesTypes', 'feesGroups')->get();
            $already_assigned = [];
            foreach ($fees_masters as $fees_master) {
                $already_assigned[] = $fees_master->fees_type_id;
            }
            $fees_masters = $fees_masters->groupBy('fees_group_id');
            $fees_types = SmFeesType::get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['fees_groups'] = $fees_groups->toArray();
                $data['fees_types'] = $fees_types->toArray();
                $data['fees_masters'] = $fees_masters->toArray();

                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.feesCollection.fees_master', compact('fees_groups', 'fees_types', 'fees_masters', 'already_assigned'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(SmFeesMasterRequest $request)
    {
        // return $request;

        try {
            $fees_type = SmFeesType::find($request->fees_type);
            $combination = SmFeesMaster::where('fees_group_id', $request->fees_group)->where('fees_type_id', $request->fees_type)->count();
            if ($combination == 0) {
                $fees_master = new SmFeesMaster();
                $fees_master->fees_group_id = $fees_type->fees_group_id;
                $fees_master->fees_type_id = $request->fees_type;
                $fees_master->date = date('Y-m-d', strtotime($request->date));
                $fees_master->school_id = Auth::user()->school_id;
                $fees_master->academic_id = getAcademicId();
                $fees_master->amount = $request->amount;
                $fees_master->save();

                Toastr::success('Operation successful', 'Success');
                return redirect()->back();

            } elseif ($combination == 1) {
                Toastr::error('Already fees assigned', 'Failed');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function show($id)
    {

        try {
            $fees_master = SmFeesMaster::find($id);
            $fees_groups = SmFeesGroup::get();
            $fees_types = SmFeesType::get();
            $fees_masters = SmFeesMaster::with('feesTypes', 'feesGroups')->get();

            $already_assigned = [];
            foreach ($fees_masters as $master) {
                if ($fees_master->fees_type_id != $master->fees_type_id) {
                    $already_assigned[] = $master->fees_type_id;
                }
            }

            $fees_masters = $fees_masters->groupBy('fees_group_id');
            return view('backEnd.feesCollection.fees_master', compact('fees_groups', 'fees_types', 'fees_master', 'fees_masters', 'already_assigned'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(SmFeesMasterRequest $request, $id)
    {

        try {
            $fees_type = SmFeesType::find($request->fees_type);
            $fees_master = SmFeesMaster::find($request->id);

            $fees_master->fees_type_id = $request->fees_type;
            $fees_master->date = date('Y-m-d', strtotime($request->date));
            $fees_master->amount = $request->amount;
            $fees_master->fees_group_id = $fees_type->fees_group_id;
            $fees_master->save();
            Toastr::success('Operation successful', 'Success');
            return redirect('fees-master');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {

        try {
            SmFeesMaster::destroy($id);

            Toastr::success('Operation successful', 'Success');
            return redirect('fees-master');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteSingle(Request $request)
    {
        try {
            $id_key = 'fees_master_id';
            $tables = tableList::getTableList($id_key, $request->id);
            try {
                if ($tables == null) {
                    $check_fees_assign = SmFeesAssign::where('fees_master_id', $request->id)
                        ->join('sm_students', 'sm_students.id', '=', 'sm_fees_assigns.student_id')->where('school_id',Auth::user()->school_id)->first();
                    if ($check_fees_assign != null) {
                        $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                        Toastr::error($msg, 'Failed');
                        return redirect()->back();
                    }
                    $delete_query = SmFeesMaster::destroy($request->id);
                    if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                        if ($delete_query) {
                            return ApiBaseMethod::sendResponse(null, 'Fees Master has been deleted successfully');
                        } else {
                            return ApiBaseMethod::sendError('Something went wrong, please try again.');
                        }
                    }

                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                    Toastr::error($msg, 'Failed');
                    return redirect()->back();
                }
            } catch (\Illuminate\Database\QueryException $e) {
                $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                Toastr::error('This item already used', 'Failed');
                return redirect()->back();
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteGroup(Request $request)
    {
        try {
            $id_key = 'fees_group_id';
            $tables = tableList::getTableList($id_key, $request->id);
            try {
                $assigned_master_id = [];
                $fees_group_master = SmFeesAssign::where('school_id',Auth::user()->school_id)->get();
                foreach ($fees_group_master as $key => $value) {
                    $assigned_master_id[] = $value->fees_master_id;
                }
                $feesmasters = SmFeesMaster::where('fees_group_id', $request->id)->get();
                foreach ($feesmasters as $feesmaster) {
                    if (!in_array($feesmaster->id, $assigned_master_id)) {
                        if (checkAdmin()) {
                            $delete_query = SmFeesMaster::destroy($feesmaster->id);
                        } else {
                            $delete_query = SmFeesMaster::where('id', $feesmaster->id)->where('school_id', Auth::user()->school_id)->delete();
                        }
                    } else {
                        $msg = 'This data already used in : ' . $tables . ' Please remove those data first';
                        Toastr::error($msg, 'Failed');
                        return redirect()->back();
                    }

                }
                if ($delete_query) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            } catch (\Illuminate\Database\QueryException $e) {
                $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                Toastr::error('This item already used', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesAssign(Request $request, $id)
    {
        try {
            $fees_group_id = $id;
            $classes = SmClass::get();
            $groups = SmStudentGroup::where('active_status', '=', '1')->where('school_id', Auth::user()->school_id)->get();
            $categories = SmStudentCategory::where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get();

            return view('backEnd.feesCollection.fees_assign', compact('classes', 'categories', 'groups', 'fees_group_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesAssignSearch(Request $request)
    {
        $request->validate([
            'class' => "required",
        ]);
        try {
            $section_id = 0;
            $classes = SmClass::get();
            $groups = SmStudentGroup::get();
            $categories = SmStudentCategory::get();
            $fees_group_id = $request->fees_group_id;

            $students = StudentRecord::query();
            if ($request->class != "") {
                $students->where('class_id', $request->class);
            }
            if ($request->section != "") {
                $students->where('section_id', $request->section);
                $section_id = $request->section;
            }
            // if ($request->category != "") {
            //     $students->where('student_category_id', $request->category);
            // }
            // if ($request->group != "") {
            //     $students->where('student_group_id', $request->group);
            // }

            $students = $students->with('studentDetail.gender', 'studentDetail.parents', 'studentDetail.category', 'class', 'section','studentDetail')
            
            ->get();
            $student_ids = $students->pluck('id')->toArray();

            $fees_master_ids = SmFeesMaster::where('fees_group_id', $request->fees_group_id)->pluck('id')->toArray();

            $pre_assigned = SmFeesAssign::whereIn('record_id', $student_ids)->where('school_id',Auth::user()->school_id)->whereIn('fees_master_id', $fees_master_ids)->pluck('record_id')->toArray();
            // foreach ($students as $student) {
            //     foreach ($fees_masters as $fees_master) {
            //         $assigned_student = SmFeesAssign::select('student_id')->where('student_id', $student->id)->where('fees_master_id', $fees_master->id)->first();

            //         if ($assigned_student != "") {
            //             if (!in_array($assigned_student->student_id, $pre_assigned)) {
            //                 $pre_assigned[] = $assigned_student->student_id;
            //             }
            //         }
            //     }
            // }
            // return  $pre_assigned;
            if ($pre_assigned != null) {
                $assigned_value = 1;
            } else {
                $assigned_value = 0;
            }
            $class_id = $request->class;
            $category_id = $request->category;
            $group_id = $request->group;

            $fees_assign_groups = SmFeesMaster::where('fees_group_id', $request->fees_group_id)->where('school_id', Auth::user()->school_id)->get();

            // return $request;
            return view('backEnd.feesCollection.fees_assign', compact('classes', 'categories', 'groups', 'students', 'fees_assign_groups', 'fees_group_id', 'pre_assigned', 'class_id', 'category_id', 'group_id', 'assigned_value', 'section_id'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function feesAssignStore(Request $request)
    {
        try {
            $datas= collect($request->data);
            $student_ids = $datas->unique('student_id')->pluck('student_id')->toArray();

            $fees_masters = SmFeesMaster::where('fees_group_id', $request->fees_group_id)
                            ->where('school_id', Auth::user()->school_id)
                            ->get();

            $students = SmStudent::with(['feesAssign', 'feesPayment', 
                        'feesAssignDiscount', 'forwardBalance', 
                        'user', 'parents', 'parents.parent_user'])
                        ->whereIn('id', $student_ids)
                        ->get();

            foreach ($students as $key=>$student) {
                
                $studentRecords = $datas->where('student_id',$student->id)->toArray();
                foreach($studentRecords as $studentRecord){

                    foreach ($fees_masters as $fees_master) {
                        $payment_info = $student->feesPayment->where('active_status', 1)
                                        ->where('fees_type_id', $fees_master->fees_type_id)
                                        ->where('record_id',gv($studentRecord, 'record_id')) //record
                                        ->count();
                                        
                        if (!$payment_info) {
                            // delete assign fees if no payament
                            if ($student->feesAssign->where('fees_master_id', $fees_master->id)->where('record_id',gv($studentRecord, 'record_id'))->count()) {
                                $student->feesAssign()->where('fees_master_id', $fees_master->id)->where('record_id',gv($studentRecord, 'record_id'))->delete();
                            }
                        }
    
                        if (!gbv($studentRecord, 'checked')) {
                            continue;
                        }
    
                        $assign_fees = $student->feesAssign()->where('fees_master_id', $fees_master->id)->where('record_id',gv($studentRecord, 'record_id'))->first();//record
    
                        if ($assign_fees) {
                            continue;
                        }

                        $assign_fees = new SmFeesAssign();
                        $assign_fees->student_id = $student->id;
                        $assign_fees->fees_amount = $fees_master->amount;
                        $assign_fees->fees_master_id = $fees_master->id;
                        $assign_fees->record_id = gv($studentRecord, 'record_id');
                        $assign_fees->school_id = Auth::user()->school_id;
                        $assign_fees->academic_id = getAcademicId();
    
                        $check_yearly_discount = $student->feesAssignDiscount()->where('fees_group_id', $request->fees_group_id)->first();
    
                        if ($check_yearly_discount) {
                            if ($assign_fees->fees_amount > $check_yearly_discount->applied_amount) {
                                $payable_fees = $assign_fees->fees_amount - $check_yearly_discount->applied_amount;
                                $assign_fees->applied_discount = $check_yearly_discount->applied_amount;
                                $assign_fees->fees_discount_id = $check_yearly_discount->fees_discount_id;
                                $assign_fees->fees_amount = $payable_fees;
                            }
    
                        }
    
                        $assign_fees->save();
    
                        $forward = $student->forwardBalance;
                        if ($forward) {
                            $forwardAmount = $forward->balance;
    
                            if ($forwardAmount) {
                                $fees_payment = new SmFeesPayment();
                                $fees_payment->student_id = $student->id;
                                $fees_payment->fees_type_id = $fees_master->fees_type_id;
                                $fees_payment->discount_amount = 0;
                                $fees_payment->fine = 0;
                                $fees_payment->payment_date = date('Y-m-d');
                                $fees_payment->payment_mode = @$forward->notes;
                                $fees_payment->record_id = gv($studentRecord, 'record_id');
                                $fees_payment->created_by = Auth::id();
                                $fees_payment->note = @$forward->notes;
                                $fees_payment->academic_id = getAcademicId();
                                $fees_payment->school_id = Auth::user()->school_id;
    
                                if ($forwardAmount > 0 && $fees_master->amount < $forwardAmount) {
                                    $fees_payment->amount = $fees_master->amount;
                                    $extra_forword = $forwardAmount - $fees_master->amount;
                                } else {
                                    $fees_payment->amount = $forwardAmount;
                                    $extra_forword = 0;
                                }
    
                                $fees_payment->save();
                                $forward->balance = $extra_forword;
                                $forward->save();
                            }
                        }
    
                    }

                }



                $notification = new SmNotification;
                $notification->user_id = $student->user_id;
                $notification->role_id = 2;
                $notification->date = date('Y-m-d');
                $notification->message = app('translator')->get('fees.fees_assigned');
                $notification->school_id = Auth::user()->school_id;
                $notification->academic_id = getAcademicId();
                $notification->save();

                try {
                    $user = $student->user;
                    Notification::send($user, new FeesAssignNotification($notification));
                }catch (\Exception $e) {
                    Log::info($e->getMessage());
                }

                $parent = $student->parents;

                if($parent){
                    $notification2 = new SmNotification();
                    $notification2->user_id = $parent->user_id;
                    $notification2->role_id = 3;
                    $notification2->date = date('Y-m-d');
                    $notification2->message = app('translator')->get('fees.fees_assigned_for') . ' ' . $student->full_name;
                    $notification2->school_id = Auth::user()->school_id;
                    $notification2->academic_id = getAcademicId();
                    $notification2->save();

                    try {
                        $user = $parent->parent_user;
                        if($user){
                            Notification::send($user, new FeesAssignNotification($notification2));
                        }

                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                    }
                }

            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('fees_assign',$request->fees_group_id);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->route('fees_assign',$request->fees_group_id);
        }
    }
    public function feesAssignStoreOld(Request $request)
    {
        try {
            $fees_masters = SmFeesMaster::where('fees_group_id', $request->fees_group_id)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            if ($request->checked_ids != "") {
                foreach ($request->checked_ids as $student) {
                    foreach ($fees_masters as $fees_master) {
                        $assign_fees = SmFeesAssign::where('fees_master_id', $fees_master->id)->where('school_id',Auth::user()->school_id)->delete();
                    }
                }
            }

            if ($request->checked_ids != "") {
                foreach ($request->checked_ids as $student) {
                    foreach ($fees_masters as $fees_master) {
                        $assign_fees = SmFeesAssign::where('fees_master_id', $fees_master->id)->where('student_id', $student)->where('school_id',Auth::user()->school_id)->first();

                        if ($assign_fees) {
                            continue;
                        }
                        $assign_fees = new SmFeesAssign();
                        $assign_fees->student_id = $student;
                        $assign_fees->fees_amount = $fees_master->amount;
                        $assign_fees->fees_master_id = $fees_master->id;
                        $assign_fees->school_id = Auth::user()->school_id;
                        $assign_fees->academic_id = getAcademicId();
                        $assign_fees->save();

                        //Yearly Discount assign

                        $check_yearly_discount = SmFeesAssignDiscount::where('fees_group_id', $request->fees_group_id)->where('student_id', $student)->where('school_id',Auth::user()->school_id)->first();

                        if ($check_yearly_discount) {
                            if ($assign_fees->fees_amount > $check_yearly_discount->applied_amount) {

                                $payable_fees = $assign_fees->fees_amount - $check_yearly_discount->applied_amount;

                                $assign_fees->applied_discount = $check_yearly_discount->applied_amount;
                                $assign_fees->fees_discount_id = $check_yearly_discount->fees_discount_id;
                                $assign_fees->fees_amount = $payable_fees;
                                $assign_fees->save();
                            }

                        }
                    }
                }
            }

            foreach ($request->students as $student) {
                $students_info = SmStudent::find($student);
                $notification = new SmNotification;
                $notification->user_id = $students_info->user_id;
                $notification->role_id = 2;
                $notification->date = date('Y-m-d');
                $notification->message = 'New fees Assigned';
                $notification->school_id = Auth::user()->school_id;
                $notification->academic_id = getAcademicId();
                $notification->save();

                $parent = SmParent::find($students_info->parent_id);
                $notification2 = new SmNotification;
                $notification2->user_id = $parent->user_id;
                $notification2->role_id = 3;
                $notification2->date = date('Y-m-d');
                $notification2->message = 'New fees Assigned For ' . $students_info->full_name;
                $notification2->school_id = Auth::user()->school_id;
                $notification2->academic_id = getAcademicId();
                $notification2->save();
            }
            $html = "";
            return response()->json([$html]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }
}
