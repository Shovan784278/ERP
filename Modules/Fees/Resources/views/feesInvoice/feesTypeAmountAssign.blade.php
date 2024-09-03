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


    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h3 class="card-title">
                Year : {{ App\SmAcademicYear::find($_GET['academic_id'])->year }}, Month : {{ $_GET['month'] }},
                Class :  {{ App\SmClass::find($_GET['sm_class_id'])->class_name }}
            </h3>
            
          {{-- <h3>

            Year: {{ request('year') ?? 'N/A' }},
            Month: {{ request('month') ?? 'N/A' }},
            Class: {{ request('sm_class_id') ?? 'N/A' }}

          </h3> --}}
          <form id="feeForm" method="POST" action="{{ url('fees/fees-amount-insert') }}" onsubmit="submitFee(event)" >
            @csrf
            
           
              <input type="hidden" class="form-control" id="academic_id" name="academic_id" value="{{ request('academic_id') }}" readonly required> 
            
               
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

<form action="{{ url('fees/save-fees') }}" method="POST">
@csrf
    <input type="hidden" name="feesYear" value="{{ $_GET['academic_id'] }}">
    <input type="hidden" name="feesMonth" value="{{ $_GET['month'] }}">
    <input type="hidden" name="feesClassId" value="{{ $_GET['sm_class_id'] }}">
    
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
                            <td>
                                <input type="hidden" name="feesId[]" value="{{ $fee->id }}">
                                
                                
                                {{ $fee->id }}
                            
                            </td>
                            <td>
                                
                                <?php 
                                $academicYear = App\SmAcademicYear::find($fee->academic_id); 
                                if($academicYear) {
                                    echo $academicYear->year;
                                } else {
                                    echo "N/A";
                                }
                            ?>
                            
                            
                            </td>
                            <td>{{ $fee->month }}</td>
                            <td>{{ $fee->fm_fees_type->name }}</td>
                            <td>{{ $fee->amount }}</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="editRow({{ $fee->id }}, '{{ $fee->academic_id }}', '{{ $academicYear ? $academicYear->year : 'N/A' }}', '{{ $fee->amount }}')">Edit</button>

                                <button type="button" class="btn btn-danger" onclick="deleteRow({{ $fee->id }})">Delete</button>
                                <!-- Add more buttons or actions as needed -->
                            </td>
                          
                        </tr>
                    @endforeach
                </tbody>
                
            </table>
            <button type="submit" class="btn btn-success">Fees Generate</button>
        </div>
    </div>


</form>

        
    </div>
    
    


           

</div>










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
                    {{-- <div class="mb-3">
                        <label for="editAcademic" class="form-label">Year:</label>
                        <input type="text" class="form-control" id="editAcademic" name="editAcademic">
                    </div> --}}
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




function updateFeeTable(data) {
    // Update the fee data table based on the received data
    // Logic to append data to the feeTableBody
    // ...

    // You may also handle pagination if applicable
}



    





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




    function updateFeeTable(data) {
            // Clear existing rows
            $('#feeTableBody').empty();

            // Append new rows based on the received data
            data.forEach(function (fee) {
                const row = $('<tr>');
                row.html(`
                    <td>${fee.id}</td>
                    <td>${fee.academic_id}</td>
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
        







    

    function editRow(id, academicId, academicYear, amount) {
    // Set values in the modal
    // document.getElementById('editAcademic').value = academicYear;
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




function saveEditedRecord() {
    
        // Fetch the values from the modal and save the record
        const id = document.getElementById('editRecordId').value;
        // const academic_id = document.getElementById('editAcademic').value;
        const amount = document.getElementById('editAmount').value;

        // Make an AJAX request to update the record
        fetch(`{{ url("fees/update-fees-type-amount") }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ amount }),
        })
        .then(response => response.json())
        .then(data => {
        console.log(data); // Log the data object to the console
        if (data.status === 'success') {
            
            showAlert('Record updated successfully.', 'success', 2000);
            // Hide the modal
            $('#editModal').modal('hide');
            location.reload();
        } else {
            showAlert('Record updated fail. ' + data.message, 'danger');
        }
    })

}









function prepareEditModal(button) {
    const row = button.closest('tr');
    const id = row.cells[0].textContent;
    const year = row.cells[2].textContent;
    const amount = row.cells[3].textContent;

    // Populate modal fields
    document.getElementById('editRecordId').value = id;

    document.getElementById('editAcademic').value = academic_id;

    // Set the modal amount field to the current amount
    document.getElementById('editAmount').value = amount;

    // Show the modal
    $('#editModal').modal('show');
}









</script>


<script>
    $(document).ready(function() {
        $("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
            $("#success-alert").slideUp(500);
        });
    });
</script>









