<?php
namespace App\Http\Controllers\Admin\AdminSection;

use App\ApiBaseMethod;
use App\SmPostalReceive;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\AdminSection\SmPostalReceiveRequest;

class SmPostalReceiveController extends Controller
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
            $postal_receives = SmPostalReceive::get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($postal_receives->toArray(), 'Postal retrieved successfully.');
            }
            return view('backEnd.admin.postal_receive', compact('postal_receives'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
    public function store(SmPostalReceiveRequest $request)
    {
      

 

        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }
        try{
        
            $destination =  'public/uploads/postal/';
            $fileName=fileUpload($request->file,$destination);
            $postal_receive = new SmPostalReceive();
            $postal_receive->from_title = $request->from_title;
            $postal_receive->reference_no = $request->reference_no;
            $postal_receive->address = $request->address;
            $postal_receive->date = date('Y-m-d', strtotime($request->date));
            $postal_receive->note = $request->note;
            $postal_receive->to_title = $request->to_title;
            $postal_receive->file = $fileName;
            $postal_receive->created_by=Auth::user()->id;
            $postal_receive->school_id = Auth::user()->school_id;
            $postal_receive->academic_id = getAcademicId();
            $result = $postal_receive->save();


            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Postal has been created successfully.');
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

    public function show(Request $request, $id)
    {

        try{
            $postal_receives = SmPostalReceive::get();

            $postal_receive = SmPostalReceive::find($id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['postal_receives'] = $postal_receives->toArray();
                $data['postal_receive'] = $postal_receive->toArray();

                return ApiBaseMethod::sendResponse($data, 'Postal retrieved successfully.');
            }
            return view('backEnd.admin.postal_receive', compact('postal_receives', 'postal_receive'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function update(SmPostalReceiveRequest $request)
    {



        try{
        
            $destination='public/uploads/postal/';
            $postal_receive = SmPostalReceive::find($request->id);
            $postal_receive->from_title = $request->from_title;
            $postal_receive->reference_no = $request->reference_no;
            $postal_receive->address = $request->address;
            $postal_receive->date = date('Y-m-d', strtotime($request->date));
            $postal_receive->note = $request->note;
            $postal_receive->to_title = $request->to_title;
           
            $postal_receive->file = fileUpdate($postal_receive->file,$request->file,$destination);
          
            $result = $postal_receive->save();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Postal has been updated successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }

            Toastr::success('Operation successful', 'Success');
            return redirect('postal-receive');

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }


    public function destroy(Request $request, $id)
    {

        try{
            $postal_receive = SmPostalReceive::find($id);
            if ($postal_receive->file != "") {
                if (file_exists($postal_receive->file)) {
                    unlink($postal_receive->file);
                }
            }
            $result = $postal_receive->delete();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Postal has been deleted successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } 

            Toastr::success('Operation successful', 'Success');
            return redirect('postal-receive');

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
}