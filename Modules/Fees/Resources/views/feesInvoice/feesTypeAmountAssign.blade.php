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
            <h3 class="card-title">
                Year : {{ $_GET['year'] }}, Month : {{ $_GET['month'] }},
                Class :  {{ App\SmClass::find($_GET['sm_class_id'])->class_name }}
            </h3>
            
          {{-- <h3>

            Year: {{ request('year') ?? 'N/A' }},
            Month: {{ request('month') ?? 'N/A' }},
            Class: {{ request('sm_class_id') ?? 'N/A' }}

          </h3> --}}
          <form id="feeForm" method="POST" action="{{ url('fees/fees-amount-insert') }}" onsubmit="submitFee(event)" >
            @csrf
            
           
              <input type="hidden" class="form-control" id="year" name="year" value="{{ request('year') }}" readonly required> 
            
               
                <input type="hidden" class="form-control" id="month" name="month" value="{{ request('month') }}" readonly required>
             
                
                <input type="hidden" class="form-control" id="sm_class_id" name="sm_class_id" value="{{ $_GET['sm_class_id'] }}" readonly required>
              
      
            
          
                    <div class="mb-3">
                        <label for="select-fees" class="form-label">Select fees:</label>
                        {{-- //@dd($feesTypes); --}}
                        <select class="form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}" id="selectFeesType" name="fm_fees_type_id">
                            <option data-display="@lang('fees.fees_type') *" value="">@lang('fees.fees_type') *</option>
                        
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
    </div>

   
    <div class="container mt-4">
        <!-- Your HTML content -->
    <div id="alertContainer">
    
</div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Fee Data Table</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Year</th>
                            <th scope="col">Month</th>
                            <th scope="col">Fees Type</th>
                             <th scope="col">Amount</th> 
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="feeTableBody">
                        @foreach($feeData as $fee)
                            <tr>
                                <td>{{ $fee->id }}</td>
                                <td>{{ $fee->year }}</td>
                                <td>{{ $fee->month }}</td>
                                <td>{{ $fee->fm_fees_type->name }}</td>
                                <td>{{ $fee->amount }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary" onclick="editRow({{ $fee->id }}, '{{ $fee->year }}', '{{ $fee->amount }}')">Edit</button>
                                    <button type="button" class="btn btn-danger" onclick="deleteRow({{ $fee->id }})">Delete</button>
                                    <!-- Add more buttons or actions as needed -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    


            <!-- Pagination Form -->
            {{-- <form id="paginationForm">
                <input type="hidden" name="currentPage" id="currentPage" value="1">
                <input type="hidden" name="perPage" value="5"> <!-- Set the number of items per page here -->
                <button type="button" onclick="fetchFeeData()">Load More</button>
            </form>

            <div id="pagination-container">
                <!-- Pagination links will be inserted here -->
            </div> --}}
    

</div>



<!-- Edit Modal -->
{{-- <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Fee Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form fields for editing (e.g., input fields) -->
                <div class="form-group">
                    <label for="editAmount">Amount:</label>
                    <input type="text" class="form-control" id="editAmount" placeholder="Enter Amount">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveEditedRecord()">Save Changes</button>
            </div>
        </div>
    </div>
</div> --}}





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



<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
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
                    <div class="mb-3">
                        <label for="editYear" class="form-label">Year:</label>
                        <input type="text" class="form-control" id="editYear" name="year">
                    </div>
                    <div class="mb-3">
                        <label for="editAmount" class="form-label">Amount:</label>
                        <input type="text" class="form-control" id="editAmount" name="amount">
                    </div>
                    <input type="hidden" id="editRecordId" name="id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveEditedRecord()">Save changes</button>
            </div>
        </div>
    </div>
</div>




 


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
<!-- Include Bootstrap CSS and JS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>




<script>




// function submitFee(event) {
//     // Prevent the default form submission
//     event.preventDefault();

//     // Serialize the form data
//     var formData = $('#feeForm').serialize();

//     // Make an AJAX request to submit the form data
//     $.ajax({
//         type: 'POST',
//         url: $('#feeForm').attr('action'),
//         data: formData,
//         dataType: 'json', // Specify the expected data type
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // Include CSRF token
//         },
//         success: function (data) {
//             // Handle the response data
//             console.log(data);
//             if (data.status === 'success') {
//                 // If the submission is successful, update the table
//                 updateFeeTable(data.data); // Assuming 'data' contains the inserted record
//                 // Optionally, show a success message
//                 alert('Fees amount added successfully!');
//             }
//         },
//         error: function (error) {
//             console.error('Error:', error);
//             // Handle error if needed
//         }
//     });
// }


function submitFee(event) {
    // Prevent the default form submission
    event.preventDefault();

    // Serialize the form data
    var formData = $('#feeForm').serialize();

    
    $.ajax({
        type: 'POST',
        url: $('#feeForm').attr('action'),
        data: formData,
        dataType: 'json', // Specify the expected data type
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // Include CSRF token
        },
        success: function (data) {
            // Handle the response data
            console.log(data);
            if (data.status === 'success') {
            
                // updateFeeTable(data.data); // Assuming 'data' contains the updated fee data
                // Optionally, show a success message
                alert('Fees amount added successfully!');
                location.reload();
            } else if (data.status === 'error' && data.message === 'Duplicate values are not allowed.') {
                // If duplicate values, show an error message
                alert('Duplicate values are not allowed.');
            }
        },
        error: function (error) {
            console.error('Error:', error);
            // Handle error if needed
        }
    });
}



// Attach the submitFee function to the form submission event
$('#feeForm').on('submit', submitFee);







//     function fetchPaginatedFeeData() {
//     // Make an AJAX request to fetch paginated fee data
//     $.ajax({
//         type: 'GET',
//         url: '{{ url("fees/fetch-fee-data") }}', // Adjust the URL based on your route
//         dataType: 'json',
//         success: function (data) {
//             // Handle the response data
//             console.log(data);

//             // Update the fee data table
//             updateFeeTable(data);
//         },
//         error: function (error) {
//             console.error('Error:', error);
//             // Handle error if needed
//         }
//     });
// }

function updateFeeTable(data) {
    // Update the fee data table based on the received data
    // Logic to append data to the feeTableBody
    // ...

    // You may also handle pagination if applicable
}



    




//     function fetchAllFeeData() {
//     $.ajax({
//         type: 'GET',
//         url: '{{ url("fees/fetch-all-fee-data") }}',
//         dataType: 'json',
//         success: function (data) {
//             updateFeeTable(data);
//         },
//         error: function (error) {
//             console.error('Error:', error);
//         }
//     });
// }


// function fetchFeeData() {
//     var currentPage = parseInt($('#currentPage').val());
//     var perPage = parseInt($('#perPage').val());

//     // Make an Ajax request to fetch data based on the current page and items per page
//     $.ajax({
//         type: 'GET',
//         url: '{{ url("fees/fetch-all-fee-data") }}',
//         data: { page: currentPage, per_page: perPage },
//         dataType: 'json',
//         success: function (data) {
//             // Update the fee table with the fetched data
//             updateFeeTable(data);

//             // Update the current page for the next request
//             $('#currentPage').val(currentPage + 1);

//             // Optionally, update the pagination links
//             updatePaginationLinks(data.links);
//         },
//         error: function (error) {
//             console.error('Error:', error);
//         }
//     });
// }


    

    // function updateFeeTable(data) {
    //     // Your code to update the table goes here
    //     console.log(data);
    // }


    // Fetch all fee data including pagination
//     function fetchAllFeeData(page = 1) {
//     $.ajax({
//         type: 'GET',
//         url: '{{ url("fees/fetch-all-fee-data") }}' + '?page=' + page,
//         dataType: 'json',
//         success: function (data) {
//             updateFeeTable(data.data); // Assuming data.data contains the actual fee data
//             // Optionally, update the pagination links
//             updatePaginationLinks(data.links);
//         },
//         error: function (error) {
//             console.error('Error:', error);
//         }
//     });
// }







// function saveEditedRecord() {
//     var formData = $('#editForm').serialize();

//     $.ajax({
//         type: 'POST',
//         url: '{{ url("fees/update-fee-data") }}/' + $('#editRecordId').val(),
//         data: formData,
//         dataType: 'json',
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//         },
//         success: function (data) {
//             console.log('Update successful:', data);
//             // Handle success, e.g., close the modal or show a success message
//         },
//         error: function (error) {
//             console.error('Error updating fee data:', error);
//             // Handle error, e.g., show an error message
//         }
//     });
// }



// function saveEditedRecord() {
//     // Fetch the values from the modal and save the record
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
//         body: JSON.stringify({ year, amount }),
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.status === 'success') {
//             // Update the table with the new values
//             const row = document.querySelector(`#feeTableBody tr[data-id="${id}"]`);
//             row.cells[1].textContent = year;
//             row.cells[3].textContent = amount;

//             showAlert('Record updated successfully.', 'success', 2000);
//             // Hide the modal
//             $('#editModal').modal('hide');
//         } else if (data.status === 'error') {
//             showAlert('Record updated fail. ' + data.message, 'danger');
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         showAlert('Failed to update record.', 'danger');
//     });
// }





// Function to update pagination links
// function updatePaginationLinks(links) {
//     var paginationHtml = '<ul class="pagination">';

//     // Previous page link
//     if (links.prev_page_url) {
//         paginationHtml += '<li class="page-item"><a class="page-link" href="#" onclick="fetchAllFeeData(' + (links.current_page - 1) + ')" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
//     } else {
//         paginationHtml += '<li class="page-item disabled"><span class="page-link" aria-hidden="true">&laquo;</span></li>';
//     }

//     // Page links
//     for (var i = 1; i <= links.last_page; i++) {
//         if (i === links.current_page) {
//             paginationHtml += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
//         } else {
//             paginationHtml += '<li class="page-item"><a class="page-link" href="#" onclick="console.log(' + i + '); fetchAllFeeData(' + i + ')">' + i + '</a></li>';
//         }
//     }

//     // Next page link
//     if (links.next_page_url) {
//         paginationHtml += '<li class="page-item"><a class="page-link" href="#" onclick="fetchAllFeeData(' + (links.current_page + 1) + ')" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
//     } else {
//         paginationHtml += '<li class="page-item disabled"><span class="page-link" aria-hidden="true">&raquo;</span></li>';
//     }

//     paginationHtml += '</ul>';

//     // Update the pagination container
//     $('#pagination-container').html(paginationHtml);
// }



// function fetchAndLogPageData(page) {
//     console.log('Clicked page:', page);
//     fetchAllFeeData(page);
// }




// $(document).ready(function () {
//     fetchAllFeeData();
// });




    function updateFeeTable() {
        // Make an AJAX request to fetch updated data
        $.ajax({
            type: 'GET',
            url: '{{ url("fees/fetch-all-fee-data") }}', // Adjust the URL to your controller method
            success: function (data) {
                // Update the table with the fetched data
                updateTableWithData(data);
            },
            error: function (error) {
                console.error('Error:', error);
                // Handle error if needed
            }
        });
    }


    // function updateTableWithData(data) {
    //     // Clear existing table rows
    //     $('#feeTableBody').empty();

    //     // Append new rows based on the fetched data
    //     data.data.forEach(fee => {
    //         const row = $('<tr>');
    //         row.html(`
    //             <td>${fee.id}</td>
    //             <td>${fee.year}</td>
    //             <td>${fee.month}</td>
    //             <td>${fee.amount}</td>
    //             <td>
    //                 <button class="btn btn-danger" onclick="deleteRow(${fee.id})">Delete</button>
    //                 <button class="btn btn-success" onclick="editRow(${fee.id})">Edit</button>
    //             </td>
    //         `);
    //         $('#feeTableBody').append(row);
    //     });
    // }


//     function updateTableWithData(data) {
//     try {
//         // Clear existing table rows
//         $('#feeTableBody').empty();

//         // Check if data is present and has the expected structure
//         if (data && data.data && Array.isArray(data.data)) {
//             // Append new rows based on the fetched data
//             data.data.forEach(fee => {
//                 const row = $('<tr>');
//                 row.html(`
//                     <td>${fee.id}</td>
//                     <td>${fee.year}</td>
//                     <td>${fee.month}</td>
//                     <td>${fee.amount}</td>
//                     <td>
//                         <button class="btn btn-danger" onclick="deleteRow(${fee.id})">Delete</button>
//                         <button class="btn btn-success" onclick="editRow(${fee.id})">Edit</button>
//                     </td>
//                 `);
//                 $('#feeTableBody').append(row);
//             });
//         } else {
//             console.error('Invalid or missing data structure:', data);
//         }
//     } catch (error) {
//         console.error('Error in updateTableWithData:', error);
//     }
// }




    function updateFeeTable(data) {
            // Clear existing rows
            $('#feeTableBody').empty();

            // Append new rows based on the received data
            data.forEach(function (fee) {
                const row = $('<tr>');
                row.html(`
                    <td>${fee.id}</td>
                    <td>${fee.year}</td>
                    <td>${fee.month}</td>
                    <td>${fee.amount}</td>
                    <td>
                        <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
                        
                        <button class="btn btn-success" data-toggle="modal" data-target="#editModal" onclick="prepareEditModal(this)">Edit</button>

                    </td>
                `);
                $('#feeTableBody').append(row);
            });
    }
        







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


    function editRow(id, year, amount) {
    // Set values in the modal
    document.getElementById('editYear').value = year;
    document.getElementById('editAmount').value = amount;
    document.getElementById('editRecordId').value = id;

    // Show the modal
    $('#editModal').modal('show');
}





function deleteRow(id) {
    const confirmation = confirm('Are you sure you want to delete this record?');

    if (confirmation) {
        fetch(`{{ url("fees/delete-fees-type-amount") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(response => response.json())
        .then(data => {
            const row = document.querySelector(`#feeTableBody tr[data-id="${id}"]`);

            if (row) {
                // Remove the row from the table
                row.remove();
            }

            if (data.status === 'success') {
                // Show delete success alert
                showAlert('Record deleted successfully.', 'success');
                $('#editModal').modal('hide');
        location.reload();
            } else {
                // Show delete fail alert
                showAlert('Failed to delete record.', 'danger');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}










// Function to show Bootstrap alerts
function showAlert(message, type, delay = 0) {
    // Clear previous alerts
    $('#alertContainer').empty(); 

    // Create a new alert element
    const alertElement = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">');
    alertElement.text(message);

    // Add a close button to the alert
    const closeButton = $('<button type="button" class="close" data-dismiss="alert" aria-label="Close">');
    closeButton.html('<span aria-hidden="true">&times;</span>');
    alertElement.append(closeButton);

    // Append the alert to the alertContainer
    $('#alertContainer').append(alertElement);

    // If delay is specified, hide the alert after the specified duration
    if (delay > 0) {
        setTimeout(() => {
            alertElement.alert('close'); // Close the alert after the delay
        }, delay);
    }
}


// function showAlert(message, type, duration) {
//     $('#alertContainer').empty();

//     const alertElement = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">');
//     alertElement.text(message);

//     const closeButton = $('<button type="button" class="close" data-dismiss="alert" aria-label="Close">');
//     closeButton.html('<span aria-hidden="true">&times;</span>');
//     alertElement.append(closeButton);

//     $('#alertContainer').append(alertElement);

//     // Hide the alert after the specified duration
//     if (duration) {
//         setTimeout(() => {
//             alertElement.alert('close');
//         }, duration);
//     }
// }


// Use showAlert with a 2000ms (2 seconds) delay




// function saveEditedRecord() {
//     // Fetch the values from the modal and save the record
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
//         body: JSON.stringify({ year, amount }),
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.status === 'success') {
//             // Update the table with the new values
//             const row = document.querySelector(`#feeTableBody tr[data-id="${id}"]`);
//             row.cells[1].textContent = year;
//             row.cells[3].textContent = amount;

//             showAlert('Record updated successfully.', 'success', 2000);
//             // Hide the modal
//             $('#editModal').modal('hide');
//         } else {
//             showAlert('Record updated fail.', 'danger');
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }


function saveEditedRecord() {
    // Fetch the values from the modal and save the record
    const id = document.getElementById('editRecordId').value;
    const year = document.getElementById('editYear').value;
    const amount = document.getElementById('editAmount').value;

    // Make an AJAX request to update the record
    fetch(`{{ url("fees/update-fees-type-amount") }}/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ year, amount }),
    })
    .then(response => response.json())
    .then(data => {
    console.log(data); // Log the data object to the console
    if (data.status === 'success') {
        // Update the table with the new values
        // const row = document.querySelector(`#feeTableBody tr[data-id="${id}"]`);
        // row.cells[1].textContent = year;
        // row.cells[3].textContent = amount;

        showAlert('Record updated successfully.', 'success', 2000);
        // Hide the modal
        $('#editModal').modal('hide');
        location.reload();
    } else {
        showAlert('Record updated fail. ' + data.message, 'danger');
    }
})

}





// function saveEditedRecord() {
//     // ... (other code)

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
//         body: JSON.stringify({ year, amount }),
//     })
//     .then(response => response.json())

//     .then(data => {
//         if (data.status === 'success') {
//             showAlert('Record updated successfully.', 'success', 2000);  // Show for 2 seconds
//             // Hide the modal after showing the alert
//             setTimeout(() => {
//                 $('#editModal').modal('hide');
//             }, 2000);
//         } else {
//             showAlert('Record updated fail.', 'danger');
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         showAlert('Failed to update record. Please try again.', 'danger');
//     });
// }




// Modified saveEditedRecord function
// function saveEditedRecord() {
//     const id = document.getElementById('editRecordId').value;
//     const year = document.getElementById('editYear').value;
//     const amount = document.getElementById('editAmount').value;

//     // Make an AJAX request to update the record
//     fetch(`{{ url("fees/update-fee-data") }}/${id}`, {
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
//             // const row = findExistingRow(id);
//             // if (row) {
//             //     row.cells[2].textContent = amount; 
//             // }
//             // if (row) {
//             //     row.cells[1].textContent = year; 
//             // }

//             showAlert('Record updated successfully.', 'success');

//             // Hide the modal
//             $('#editModal').modal('hide');
//             $('.modal-backdrop').remove();
//         } else {
//             showAlert('Failed to update record.', 'danger');
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }


// function saveEditedRecord() {
//     const id = document.getElementById('editRecordId').value;
//     const year = document.getElementById('editYear').value;
//     const amount = document.getElementById('editAmount').value;

//     // Make an AJAX request to update the record
//     fetch(`{{ url("fees/update-fee-data") }}/${id}`, {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': '{{ csrf_token() }}',
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({ year, amount }),
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.status === 'success') {
//             // Update the table cells with the new values
//             const row = findExistingRow(id);
//             if (row) {
//                 row.cells[2].textContent = amount;
//                 row.cells[1].textContent = year;
//             }

//             // Optionally, call the function to update the entire table
//             updateFeeTable(data.data);

//             // Show a success message
//             alert('Record updated successfully.');

//             // Hide the modal
//             $('#editModal').modal('hide');
//         } else {
//             // Optionally, show an error message
//             alert('Failed to update record.');
//         }
//     })
//     .catch(error => console.error('Error:', error));
// }




// function openEditModal(data) {
//     // Populate the modal fields with data
//     $('#editAmount').val(data.amount);
//     $('#editYear').val(data.year);
//     $('#editRecordId').val(data.id);

//     // Show the edit modal
//     $('#editModal').modal('show');
// }



function prepareEditModal(button) {
    const row = button.closest('tr');
    const id = row.cells[0].textContent;
    const year = row.cells[2].textContent;
    const amount = row.cells[3].textContent;

    // Populate modal fields
    document.getElementById('editRecordId').value = id;

    document.getElementById('editYear').value = year;

    // Set the modal amount field to the current amount
    document.getElementById('editAmount').value = amount;

    // Show the modal
    $('#editModal').modal('show');
}









</script>


<script>







