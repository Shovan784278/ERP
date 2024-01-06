<?php

namespace App\Http\Controllers\Admin\Leave;

use App\User;
use App\SmClass;
use App\SmStaff;
use App\SmStudent;
use App\SmLeaveType;
use App\SmLeaveDefine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\RolePermission\Entities\InfixRole;
use App\Http\Requests\Admin\Leave\SmLeaveDefineRequest;

class SmLeaveDefineController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // User::checkAuth();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        try {
            $leave_types = SmLeaveType::where('active_status', 1)->where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            $roles = InfixRole::where('active_status', '=', '1')->where('id', '!=', 1)->where('id', '!=', 3)->where('id', '!=', 10)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
            $classes = SmClass::where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->where('active_status', 1)
                ->get(['id', 'class_name']);

            // $leave_defines = SmLeaveDefine::with('user','role')->where('active_status', 1)->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->get();

            return view('backEnd.humanResource.leave_define', compact('leave_types', 'roles', 'classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(SmLeaveDefineRequest $request)
    {
        try {
            $previous = null;
            if (!is_null($request->addition)) {
                $previous =  SmLeaveDefine::where('role_id', $request->member_type)
                    ->orWhere('user_id', $request->staff)
                    ->orWhere('user_id', $request->student)
                    ->where('type_id', $request->leave_type)
                    ->first();
            } else {
                $previous =  SmLeaveDefine::where(function ($q) use ($request) {
                    if ($request->student) {
                        $q->where('user_id', $request->student);
                    } else if ($request->staff) {
                        $q->where('user_id', $request->staff);
                    } 
                    $q->where('role_id', $request->member_type);
                    
                })
                    ->where('type_id', $request->leave_type)
                    ->where('school_id', auth()->user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->first();

                if ($previous) {
                    Toastr::error('leave.Leave already defined for this user');
                    return redirect()->back();
                }
            }

            if (is_numeric($request->student)  || is_numeric($request->staff)) {
                if (is_null($previous)) {

                    $leave_define = new SmLeaveDefine();
                    $leave_define->role_id = $request->member_type;
                    $leave_define->type_id = $request->leave_type;
                    $leave_define->days = $request->days;
                    $leave_define->school_id = Auth::user()->school_id;
                    $leave_define->academic_id = getAcademicId();

                    if (is_numeric($request->student)) {
                        $leave_define->user_id = $request->student;
                    } elseif (is_numeric($request->staff)) {
                        $leave_define->user_id = $request->staff;
                    }
                    $results = $leave_define->save();
                } else {

                    $previous->days = ($previous->days + $request->days);
                    $results = $previous->save();
                }
            } else {
                $allUsers = User::where('school_id', Auth::user()->school_id)
                    ->where('role_id', $request->member_type)
                    ->where('selected_session', getAcademicId())
                    ->where('active_status', 1)
                    ->get(['id', 'role_id']);

                if (count($allUsers) > 0) {
                    foreach ($allUsers as $user) {
                        $previous =  SmLeaveDefine::where('user_id', $user->id)
                            ->where('type_id', $request->leave_type)
                            ->where('school_id', auth()->user()->school_id)
                            ->where('academic_id', getAcademicId())
                            ->first();
                        if ($previous) {
                            continue;
                        }
                        $leave_define = new SmLeaveDefine();
                        $leave_define->role_id = $user->role_id;
                        $leave_define->type_id = $request->leave_type;
                        $leave_define->days = $request->days;
                        $leave_define->school_id = Auth::user()->school_id;
                        $leave_define->academic_id = getAcademicId();
                        $leave_define->user_id = $user->id;
                        $leave_define->save();
                    }
                }
            }



            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function show(Request $request, $id)
    {

        try {

            $leave_types = SmLeaveType::where('active_status', 1)->where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            $roles = InfixRole::where('active_status', 1)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
            $leave_defines = SmLeaveDefine::where('active_status', 1)->where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            $leave_define = SmLeaveDefine::find($id);

            $user = User::find($leave_define->user_id);
            $student = null;
            $staff = null;
            $student = SmStudent::with(['defaultClass'])->where('user_id', $user->id)->first();

            $staff = SmStaff::where('user_id', $user->id)->first();


            $classes = SmClass::get(['id', 'class_name']);



            return view('backEnd.humanResource.leave_define', compact('leave_types', 'roles', 'leave_defines', 'leave_define', 'classes', 'student', 'staff'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(SmLeaveDefineRequest $request, $id)
    {

        try {

            if (checkAdmin()) {
                $leave_define = SmLeaveDefine::find($request->id);
            } else {
                $leave_define = SmLeaveDefine::where('id', $request->id)->where('school_id', Auth::user()->school_id)->first();
            }
            $leave_define->type_id = $request->leave_type;
            $leave_define->days = $request->days;
            $leave_define->save();

            Toastr::success('Operation successful', 'Success');
            return redirect('leave-define');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function destroy(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => "required"
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $id = $request->id;
            $tables = \App\tableList::getTableList('leave_define_id', $id);

            try {
                if ($tables == null) {
                    if (checkAdmin()) {
                        SmLeaveDefine::destroy($id);
                    } else {
                        SmLeaveDefine::where('id', $id)->where('school_id', Auth::user()->school_id)->delete();
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
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function updateLeave(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => "required",
            'days' => "required|numeric"
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            if (checkAdmin()) {
                $leave_define = SmLeaveDefine::find($request->id);
            } else {
                $leave_define = SmLeaveDefine::where('id', $request->id)->where('school_id', Auth::user()->school_id)->first();
            }
            $leave_define->days = $request->days;
            $leave_define->save();

            Toastr::success('Operation successful', 'Success');
            return redirect('leave-define');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
