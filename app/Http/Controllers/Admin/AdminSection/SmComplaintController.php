<?php

namespace App\Http\Controllers\Admin\AdminSection;


use App\SmComplaint;
use App\SmSetupAdmin;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\AdminSection\SmComplaintRequest;


class SmComplaintController extends Controller
{

    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

    public function index(Request $request)
    {


        try {
            $complaints = SmComplaint::with('complaintType','complaintSource')->get();
            $complaint_types = SmSetupAdmin::where('type', 2)->get();
            $complaint_sources = SmSetupAdmin::where('type', 3)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['complaints'] = $complaints->toArray();
                $data['complaint_types'] = $complaint_types->toArray();
                $data['complaint_sources'] = $complaint_sources->toArray();
                return ApiBaseMethod::sendResponse($data, 'Complaints retrieved successfully.');
            }
            return view('backEnd.admin.complaint', compact('complaints', 'complaint_types', 'complaint_sources'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function create()
    {
        //
    }

    public function store(SmComplaintRequest $request)
    {

 

        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }
        try {

            $destination =  'public/uploads/complaint/';
            $fileName=fileUpload($request->file,$destination);
            $complaint = new SmComplaint();
            $complaint->complaint_by = $request->complaint_by;
            $complaint->complaint_type = $request->complaint_type;
            $complaint->complaint_source = $request->complaint_source;
            $complaint->phone = $request->phone;
            $complaint->date = date('Y-m-d', strtotime($request->date));
            $complaint->description = $request->description;
            $complaint->action_taken = $request->action_taken;
            $complaint->assigned = $request->assigned;
            $complaint->file = $fileName;
            $complaint->school_id = Auth::user()->school_id;
            $complaint->academic_id = getAcademicId();
            $result = $complaint->save();


            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Complaint has been created successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }

            Toastr::success('Operation successful', 'Success');
            return redirect('complaint');

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }



    public function show($id)
    {

        try {
            $complaint = SmComplaint::find($id);
            return view('backEnd.admin.complaintDetails', compact('complaint'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function edit(Request $request, $id)
    {
        try {
            $complaints = SmComplaint::get();
            $complaint = SmComplaint::find($id);

            $complaint_types = SmSetupAdmin::where('type', 2)->get();
            $complaint_sources = SmSetupAdmin::where('type', 3)->get();
            
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['complaints'] = $complaints->toArray();
                $data['complaint'] = $complaint->toArray();
                $data['complaint_types'] = $complaint_types->toArray();
                $data['complaint_sources'] = $complaint_sources->toArray();

                return ApiBaseMethod::sendResponse($data, 'complaint retrieved successfully.');
            }

            return view('backEnd.admin.complaint', compact('complaint', 'complaints', 'complaint_types', 'complaint_sources'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function update(SmComplaintRequest $request)
    {
       
        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        try {


            $destination =  'public/uploads/complaint/';
           
            $complaint = SmComplaint::find($request->id);
            $complaint->complaint_by = $request->complaint_by;
            $complaint->complaint_type = $request->complaint_type;
            $complaint->complaint_source = $request->complaint_source;
            $complaint->phone = $request->phone;
            $complaint->date = date('Y-m-d', strtotime($request->date));
            $complaint->description = $request->description;
            $complaint->action_taken = $request->action_taken;
            $complaint->assigned = $request->assigned;        
            $complaint->file = fileUpdate($complaint->file,$request->file,$destination);          
            $result = $complaint->save();


            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Complaint has been updated successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } 
            Toastr::success('Operation successful', 'Success');
            return redirect('complaint');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            $complaint = SmComplaint::find($id);
            if ($complaint->file != "") {
                if (file_exists($complaint->file)) {
                    unlink($complaint->file);
                }
            }
            $result = $complaint->delete();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Complaint has been deleted successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }
            Toastr::success('Operation successful', 'Success');
            return redirect('complaint');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function complaint()
    {
        $complaints = SmComplaint::all();
        return $this->sendResponse($complaints->toArray(), 'Complaint retrieved successfully.');
    }
}