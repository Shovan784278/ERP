<?php

namespace App\Http\Controllers\Admin\Examination;


use App\SmExamType;
use App\SmExamSetting;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\Examination\SmExamFormatSettingsRequest;

class SmExamFormatSettingsController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

    public function index(){
        try{
            $content_infos = SmExamSetting::with('examName')->get();

            $exams = SmExamType::get();

            $already_assigned = [];
                foreach ($content_infos as $content_info) {
                    $already_assigned[] = $content_info->exam_type;
                }

            return view ('backEnd.examination.exam_settings',compact('content_infos','exams','already_assigned'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(SmExamFormatSettingsRequest $request){
       
        try{
       

                $destination= 'public/uploads/exam/';
                $add_content = new SmExamSetting();
                $add_content->exam_type = $request->exam_type;
                $add_content->title = $request->title;
                $add_content->publish_date = date('Y-m-d', strtotime($request->publish_date));
                $add_content->file = fileUpload($request->file,$destination);
                $add_content->start_date = date('Y-m-d', strtotime($request->start_date));
                $add_content->end_date = date('Y-m-d', strtotime($request->end_date));
                $add_content->school_id = Auth::user()->school_id;
                $add_content->academic_id = getAcademicId();
                $add_content->save();

                Toastr::success('Operation successful', 'Success');
                return redirect('exam-settings');

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit($id){
        try{
            $content_infos = SmExamSetting::with('examName')->get();

            $editData = SmExamSetting::where('id',$id)->first();

            $exams = SmExamType::get();

            $already_assigned = [];
                foreach ($content_infos as $content_info) {
                    if ($editData->exam_type != $content_info->exam_type) {
                        $already_assigned[] = $content_info->exam_type;
                    }
                }
                // return $already_assigned;
            return view ('backEnd.examination.exam_settings',compact('editData','content_infos','exams','already_assigned'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function update(SmExamFormatSettingsRequest $request){

        
        try{

                $destination='public/uploads/exam/';
                $update_add_content = SmExamSetting::find($request->id);
                $update_add_content->exam_type = $request->exam_type;
                $update_add_content->title = $request->title;
                $update_add_content->publish_date = date('Y-m-d', strtotime($request->publish_date));              
                $update_add_content->start_date = date('Y-m-d', strtotime($request->start_date));
                $update_add_content->end_date = date('Y-m-d', strtotime($request->end_date));
                $update_add_content->school_id = Auth::user()->school_id;
                $update_add_content->academic_id = getAcademicId();
                $update_add_content->file = fileUpdate($update_add_content->file,$request->file,$destination);
                $result=$update_add_content->save();

                Toastr::success('Operation successful', 'Success');
                return redirect('exam-settings');

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id){
        try {
            $content = SmExamSetting::find($id);
            if($content->file !='' && file_exists($content->file)){
                unlink($content->file);
            }
             $content->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect('exam-settings');

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
