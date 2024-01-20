@extends('backEnd.master')
@section('title')
    @if (isset($invoiceInfo))
        @lang('common.edit')
    @else
        @lang('common.add')
    @endif
    @lang('fees.fees_assign')
@endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{ url('Modules\Fees\Resources\assets\css\feesStyle.css') }}" />
    @endpush
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>
                    @if (isset($invoiceInfo))
                        @lang('common.edit')
                    @else
                        @lang('common.add')
                    @endif
                    @lang('fees.fees_assign')
                </h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="{{ route('fees.fees-invoice-list') }}">@lang('fees.fees_assign')</a>
                    <a href="#">
                        @if (isset($invoiceInfo))
                            @lang('common.edit')
                        @else
                            @lang('common.add')
                        @endif
                        @lang('fees.fees_assign')
                    </a>
                </div>
            </div>
        </div>
    </section>



<div class="container mt-4">

    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Fees Search</h5>
          <form id="feeForm" method="GET" action="{{ url('fees/fees-type-amount-entry-page') }}" >
            {{-- @csrf --}}
            <div class="mb-3">
              <label for="year" class="form-label">Year:</label>
              <input type="text" class="form-control" id="year" name="year" required>
            </div>
            <div class="mb-3">
              <label for="month" class="form-label">Month:</label>

              @php
              
              $months = [];
              for ($month = 1; $month <= 12; $month++) {
                  $dateObj   = DateTime::createFromFormat('!m', $month);
                  $months[] = $dateObj->format('F');
              }
               
              @endphp
              

            <select class="form-control" name="month">
                @foreach($months as $month)
                    <option value="{{ $month }}">{{ $month }}</option>
                @endforeach
            </select>
              
            </div>
            <div class="mb-3">
              <label for="amount" class="form-label">Class:</label>

            <select
                class="form-control{{ $errors->has('class') ? ' is-invalid' : '' }}"
                name="sm_class_id" id="selectClass">
                <option data-display="@lang('common.select_class') *" value="">
                  @lang('common.select_class') *</option>
                @foreach ($classes as $class)
                  <option value="{{ $class->id }}"
                      {{ isset($invoiceInfo) ? ($invoiceInfo->class_id == $class->id ? 'selected' : '') : '' }}>
                      {{ $class->class_name }}</option>
                @endforeach
            </select>

            <div class="d-flex justify-content-center align-items-center" style="height: 10vh;">
                {{-- <input type="text" id="searchInput" name="search_query" placeholder="Enter search query"> --}}
                <button type="submit" class="btn btn-primary" id="searchButton">Search</button>

            </div>
            

           
              
            </div>
            

    


          </form>
        </div>
      </div>
{{-- 
    <div class="card">
      <div class="card-body">
        
        <form id="feeForm" method="POST" action="">

            <h5 class="card-title">Fee Assigned Card</h5>

            <div class="mb-3">
              <label for="select-fees" class="form-label">Select fees:</label>
              
              <select
                  class="form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}"
                  id="selectFeesType" name="fm_fees_type_id">
                  <option data-display="@lang('fees.fees_type') *" value="">@lang('fees.fees_type')
                      *</option>
                  
                  
                  
                  @foreach ($feesTypes as $feesType)
                      <option value="{{ $feesType->id }}">{{ $feesType->name }}</option>
                  @endforeach
              </select>
              
            </div>
            
            <div class="mb-3">
              <label for="amount" class="form-label">Amount:</label>
              <input type="text" class="form-control" id="amount" name="amount" required>
            </div>
            <button type="submit" class="btn btn-primary" onclick="submitFee()">Add Fees</button>
        
        </form>
      </div>
    </div> --}}
  
    {{-- <div class="mt-4">
      <h5>Fee Data Table</h5>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">year</th>
            <th scope="col">Month</td>
            <th scope="col">Amount</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="feeTableBody">
          <!-- Fee data will be appended here -->
        </tbody>
      </table>
    </div> --}}
  </div>
    

</div>


{{-- modal  --}}
{{-- <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Your edit form goes here -->
                <form id="editForm">
                    <!-- Your form fields go here -->
                    <!-- Example: -->
                    <label for="amount">Amount:</label>
                    <input type="text" name="amount" id="editAmount">
                    <input type="text" name="year" id="editYear">
                    <input type="hidden" name="id" id="editRecordId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveEditedRecord()">Save changes</button>
            </div>
        </div>
    </div>
</div> --}}

 


@endsection

@push('script')
    <script type="text/javascript" src="{{ url('Modules\Fees\Resources\assets\js\app.js') }}"></script>
    <script>
        selectPosition({!! feesInvoiceSettings()->invoice_positions !!});
    </script>
@endpush






<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>


<script>
    // $(document).ready(function () {
    //     // Intercept the form submission
    //     // $('#feeForm').submit(function (event) {
    //     //     event.preventDefault(); // Prevent the form from submitting traditionally

    //     //     // Perform an Ajax request
    //     //     $.ajax({
    //     //         type: 'GET',
    //     //         url: $(this).attr('action'),
    //     //         data: $(this).serialize(),
    //     //         success: function (data) {
    //     //             // Handle the successful response
    //     //             updateFeeTable(data);
    //     //         },
    //     //         error: function () {
    //     //             // Handle errors, if any
    //     //             console.error('Error in Ajax request');
    //     //         }
    //     //     });
    //     // });

    //     // Function to update the fee table with the received data
    //     function updateFeeTable(data) {
    //         // Clear existing rows
    //         $('#feeTableBody').empty();

    //         // Append new rows based on the received data
    //         data.forEach(function (fee) {
    //             const row = $('<tr>');
    //             row.html(`
    //                 <td>${fee.id}</td>
    //                 <td>${fee.year}</td>
    //                 <td>${fee.month}</td>
    //                 <td>${fee.amount}</td>
    //                 <td>
    //                     <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
                        
    //                     <button class="btn btn-success" data-toggle="modal" data-target="#editModal" onclick="prepareEditModal(this)">Edit</button>

    //                 </td>
    //             `);
    //             $('#feeTableBody').append(row);
    //         });
    //     }
        
    // });




    // function deleteRow(button) {
    //     const row = button.closest('tr');
    //     const id = row.cells[0].textContent;

    //     fetch(`{{ url("fees/delete-fees-type-amount") }}/${id}`, {
    //         method: 'DELETE',
    //         headers: {
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //             'Content-Type': 'application/json',
    //         },
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.status === 'success') {
                
    //             row.remove();
               
    //             alert('Record deleted successfully.');
    //         } else {
              
    //             alert('Failed to delete record.');
    //         }
    //     })
    //     .catch(error => console.error('Error:', error));
    // }

    // function editRow(button) {
    //     const row = button.closest('tr');
    //     const id = row.cells[0].textContent;

        
    //     const editForm = document.getElementById('editForm');
    //     const formData = new FormData(editForm);

        
    //     fetch(`{{ url("fees/update-fees-type-amount") }}/${id}`, {
    //         method: 'POST',
    //         headers: {
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //         },
    //         body: formData,
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.status === 'success') {
                
    //             alert('Record updated successfully.');
    //         } else {
                
    //             alert('Failed to update record.');
    //         }
    //     })
    //     .catch(error => console.error('Error:', error));
    // }



// function saveEditedRecord() {
//     const id = document.getElementById('editRecordId').value;
//     const amount = document.getElementById('editAmount').value;

//     // Make an AJAX request to update the record
//     fetch(`{{ url("fees/update-fees-type-amount") }}/${id}`, {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': '{{ csrf_token() }}',
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({ amount }),
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.status === 'success') {
//             // Optionally, you can update the row in the table with the new data
//             // Update the row based on your response data

//             // Optionally, you can show a success message
//             alert('Record updated successfully.');

//             // Hide the modal
//             $('#editModal').modal('hide');
//         } else {
//             // Optionally, you can show an error message
//             alert('Failed to update record.');
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }

// function saveEditedRecord() {
//     const id = document.getElementById('editRecordId').value;
//     const year = document.getElementById('editYear').value;
//     const amount = document.getElementById('editAmount').value;

//     // Make an AJAX request to update the record
//     fetch(`{{ url("fees/update-fees-type-amount") }}/${id}`, {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': '{{ csrf_token() }}',
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({ amount }),
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.status === 'success') {
            
//             const row = findExistingRow(id);
//             if (row) {
//                 row.cells[2].textContent = amount; 
//             }

//             if (row) {
//                 row.cells[1].textContent = year; 
//             }


            
//             alert('Record updated successfully.');

//             // Hide the modal
//             $('#editModal').modal('hide');
//         } else {
//             // Optionally, you can show an error message
//             alert('Failed to update record.');
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }



// Function to perform search and redirect
// function searchAndRedirect() {
//     var searchQuery = $('#searchInput').val();

//     // Perform the AJAX request to the backend search endpoint
//     document.getElementById('searchButton').addEventListener('click', function() {
//             // Redirect to the search and fetch data endpoint
//             window.location.href = '{{ url("fees/fees-type-amount-entry-page") }}';
//         });


//         // Your search button click event handler
// $('#searchButton').click(function () {
//     searchAndRedirect();
// });

// }




    

// function prepareEditModal(button) {
//     const row = button.closest('tr');
//     const id = row.cells[0].textContent;
//     const year = row.cells[2].textContent;
//     const amount = row.cells[3].textContent;

//     // Populate modal fields
//     document.getElementById('editRecordId').value = id;

//     document.getElementById('editYear').value = year;

//     // Set the modal amount field to the current amount
//     document.getElementById('editAmount').value = amount;

//     // Show the modal
//     $('#editModal').modal('show');
// }









</script>







{{-- <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script> --}}
