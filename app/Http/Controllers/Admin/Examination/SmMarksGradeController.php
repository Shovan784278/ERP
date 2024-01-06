<?php

namespace App\Http\Controllers\Admin\Examination;
use App\tableList;
use App\YearCheck;
use App\SmMarksGrade;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\Examination\SmMarkGradeRequest;

class SmMarksGradeController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $marks_grades = SmMarksGrade::orderBy('gpa', 'desc')->where('academic_id', getAcademicId())->get();
            // return $marks_grades;
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($marks_grades, null);
            }
            return view('backEnd.examination.marks_grade', compact('marks_grades'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function store(SmMarkGradeRequest $request)
    {
        
        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }

        // }
      
 
        try{
            $marks_grade = new SmMarksGrade();
            $marks_grade->grade_name = $request->grade_name;
            $marks_grade->gpa = $request->gpa;
            $marks_grade->percent_from = $request->percent_from;
            $marks_grade->percent_upto = $request->percent_upto;
            $marks_grade->from = $request->grade_from;
            $marks_grade->up = $request->grade_upto;
            $marks_grade->description = $request->description;
            $marks_grade->created_by=auth()->user()->id;
            $marks_grade->created_at= YearCheck::getYear() .'-'.date('m-d h:i:s');
            $marks_grade->school_id = Auth::user()->school_id;
            $marks_grade->academic_id = getAcademicId();

            $result = $marks_grade->save();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Grade has been created successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } 
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try{
            $marks_grade = SmMarksGrade::find($id);
            $marks_grades = SmMarksGrade::where('academic_id', getAcademicId())->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['marks_grade'] = $marks_grade->toArray();
                $data['marks_grades'] = $marks_grades->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.examination.marks_grade', compact('marks_grade', 'marks_grades'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        
        try{
            $marks_grade = SmMarksGrade::find($request->id);
           
            $marks_grade->grade_name = $request->grade_name;
            $marks_grade->gpa = $request->gpa;
            $marks_grade->percent_from = $request->percent_from;
            $marks_grade->percent_upto = $request->percent_upto;
            $marks_grade->description = $request->description;
            $marks_grade->from = $request->grade_from;
            $marks_grade->updated_by=auth()->user()->id;
            $marks_grade->up = $request->grade_upto;
            $result = $marks_grade->save();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Grade has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } 
            Toastr::success('Operation successful', 'Success');
            return redirect('marks-grade');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }


    public function destroy(Request $request, $id)
    {
        try{
            $tables = tableList::getTableList('id', $id);

            if($tables == null ){
                $marks_grade = SmMarksGrade::destroy($id);
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    if ($marks_grade) {
                        return ApiBaseMethod::sendResponse(null, 'Grade has been deleted successfully');
                    } else {
                        return ApiBaseMethod::sendError('Something went wrong, please try again.');
                    }
                } 
                Toastr::success('Operation successful', 'Success');
                return redirect('marks-grade');
            } else{
                $msg = 'This data already used in  : ' . $tables .' Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
}