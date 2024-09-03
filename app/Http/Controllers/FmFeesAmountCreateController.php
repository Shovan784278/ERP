<?php

namespace App\Http\Controllers;


use App\SmClass;
use Illuminate\Support\Facades\Log;
use Modules\Fees\Entities\FmFeesTypeAmount;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesType;
use Modules\Fees\Entities\FmFeesGroup;
use Modules\Fees\Entities\FmFeesWeaver;
use Modules\Fees\Entities\FmFeesInvoice;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Http\Request;


class FmFeesAmountCreateController extends Controller
{
    
    
    public function storeFeeData(Request $request)
    {
        // Validate the form data
        $request->validate([
            'academic_id' => 'required',
            'month' => 'required',
            'sm_class_id' => 'required',
            'fm_fees_type_id' => 'required',
            'amount' => 'required|numeric',
        ]);
    
        // Check for duplicate values
        $isDuplicate = FmFeesTypeAmount::where([
            'academic_id' => $request->academic_id,
            'month' => $request->month,
            'sm_class_id' => $request->sm_class_id,
            'fm_fees_type_id' => $request->fm_fees_type_id,
        ])->exists();
    
        if ($isDuplicate) {
            // If duplicate, return a response with an error message
            return response()->json([
                'status' => 'error',
                'message' => 'Duplicate values are not allowed.',
            ]);
        }
    
        // If not a duplicate, insert data into the table
        $newFeeData = FmFeesTypeAmount::create($request->all());
    
        // Fetch all fee data (optional: you can also customize this to fetch only the new record)
        $allFeeData = FmFeesTypeAmount::all();
    
        // Return a response with the updated fee data
        return response()->json([
            'status' => 'success',
            'message' => 'Fees amount added successfully!',
            'data' => $allFeeData,
        ]);
    }

    public function fetchPaginatedFeeData()
{
    // Fetch paginated fee data
    $feeData = FmFeesTypeAmount::paginate(5); // Adjust the pagination size as needed

    // Return the fee data as JSON
    return response()->json($feeData);
}


public function fetchAllFeeData(Request $request)
{
    // Fetch all fee data with pagination
    $feeData = FmFeesTypeAmount::paginate(5); // You can customize the number of records per page

    return response()->json($feeData);
}



public function editFeeData($id)
{
    // Fetch the fee data by ID
    $feeData = FmFeesTypeAmount::find($id);

    return response()->json($feeData);
}


public function deleteFeeData($id)
{
    // Find and delete the fee data by ID
    $feeData = FmFeesTypeAmount::find($id);

    if ($feeData) {
        $feeData->delete();
        return response()->json(['status' => 'success', 'message' => 'Fee data deleted successfully']);
    } else {
        return response()->json(['status' => 'error', 'message' => 'Fee data not found'], 404);
    }
}



// public function updateFeesTypeAmount(Request $request, $id) 
// {
//     try {
//         $feesTypeAmount = FmFeesTypeAmount::findOrFail($id);
//         $feesTypeAmount->update($request->all());
//         return response()->json(['status' => 'success', 'message' => 'Record updated successfully.']);
//     } catch (\Exception $e) {
//         return response()->json(['status' => 'error', 'message' => 'Failed to update record.']);
//     }
// }


public function updateFeesTypeAmount(Request $request, $id)
    {
            // dd($request->all());
            try {
                // Fetch the record by ID
                $feesTypeAmount = FmFeesTypeAmount::findOrFail($id);

                // Update the record with the new data
                $feesTypeAmount->update($request->all());

                return response()
                    ->json([

                        'status'=>"success", 
                        'message'=>"Updated Successfully!"
                ]);
            } catch (\Exception $e) {
                // Handle any exceptions
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update record.'
                ], 422);
            }
    }






public function deleteFeesTypeAmount($id)
{
    try {
        $feesTypeAmount = FmFeesTypeAmount::findOrFail($id);
        $feesTypeAmount->delete();
        return response()->json(['status' => 'success', 'message' => 'Record deleted successfully.']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Failed to delete record.']);
    }
}




public function fetchFeeDataForEdit($id)
{
    $feeData = FmFeesTypeAmount::find($id);

    return response()->json(['data' => $feeData]);
}



// In YourController.php
public function updateFeeData(Request $request, $id)
{
    // Validate the form data
    // $request->validate([
    //     'year' => 'required',
    //     // 'month' => 'required',
    //     // 'sm_class_id' => 'required',
    //     // 'fm_fees_type_id' => 'required',
    //     'amount' => 'required|numeric',
    // ]);

    // Find the record by ID
    $feeData = FmFeesTypeAmount::find($id);

    if (!$feeData) {
        return response()->json([
            'status' => 'error',
            'message' => 'Record not found.',
        ], 404);
    }

    // Update the record
    $feeData->update($request->all());

    // Return a response with the updated fee data
    return response()->json([
        'status' => 'success',
        'message' => 'Fee data updated successfully!',
        'data' => $feeData, // Return only the updated record
    ]);
}


public function searchAndFetchData(Request $request)
{
    // Validate the search input if needed
    $request->validate([
        'search_query' => 'required|string',
        // Add more validation rules as needed
    ]);

    // Perform the search query
    $searchQuery = $request->input('search_query');
    
    // Example: Perform a search on your model
    $searchResults = FmFeesTypeAmount::where('column_name', 'like', '%' . $searchQuery . '%')->get();

    // Return the search results as JSON
    return response()->json(['data' => $searchResults]);
}




}




