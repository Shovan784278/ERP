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

    @if(isset($feesSummary) && $feesSummary->isNotEmpty())
    <div class="white-box mt-20">
        <div class="row">
            <div class="col-lg-12">
                <h4>Student Information</h4>
                <table class="table">
                    <tr>
                        <th>Student Name</th>
                        <td>{{ $feesSummary->first()->student->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Student ID</th>
                        <td>{{ $feesSummary->first()->student->id }}</td>
                    </tr>
                    <tr>
                        <th>Record ID</th>
                        <td>{{ $feesSummary->first()->record_id }}</td>
                    </tr>
                    <tr>
                        <th>Roll No</th>
                        <td>{{ $feesSummary->first()->student->roll_no }}</td>
                    </tr>
                    <tr>
                        <th>Class</th>
                        <td>{{ $feesSummary->first()->class->class_name }}</td>
                    </tr>
                    <tr>
                        <th>Section</th>
                        <td>{{ $feesSummary->first()->section->section_name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- <div class="white-box mt-20">
        <form id="addFeesForm" method="POST" action="{{ route('fees.add') }}">
            @csrf
            <input type="hidden" name="student_id" value="{{ $feesSummary->first()->student->id }}">
            <input type="hidden" name="record_id" value="{{ $feesSummary->first()->record_id }}">
            <input type="hidden" name="student_roll" value="{{ $feesSummary->first()->student->roll_no }}">
            <input type="hidden" name="academic_year" value="{{ $feesSummary->first()->year }}">
            <input type="hidden" name="class_id" value="{{ $feesSummary->first()->class->id }}">
            <input type="hidden" name="section_id" value="{{ $feesSummary->first()->section->id }}">

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fees_type" class="form-label">Fees Type:</label>
                        <select class="form-control" id="fees_type" name="fees_type">
                            <option value="">Select Fees Type</option>
                            @foreach ($feesTypes as $feesType)
                                <option value="{{ $feesType->id }}">{{ $feesType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount:</label>
                        <input type="text" class="form-control" id="amount" name="amount" required>
                    </div>
                </div>
                {{-- <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date:</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                </div> --}}
            {{-- </div>
            <div class="mb-3">
                <label>For Additional Applicable Months:</label>
                <div class="row">
                    @php
                        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    @endphp
                    @foreach($months as $month)
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="months[]" value="{{ $month }}" id="{{ $month }}">
                                <label class="form-check-label" for="{{ $month }}">{{ $month }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Fees</button>
        </form>
    </div> --}} 

    <div class="white-box mt-20">
        <h4>Fees Summary</h4>
        <form action="{{ route('fees.payment') }}" method="POST">
            @csrf
            <input type="hidden" name="student_id" value="{{ $feesSummary->first()->student->id }}">
            <table class="table" id="feesTable">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Particular</th>
                        <th>Month</th>
                        <th>Fees Type</th>
                        <th>Due Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feesSummary as $key => $fee)
                    <tr>
                        <td>
                            @if($fee->feesType->amount == 0 ? $fee->feesType->amount + $fee->paid_amount : $fee->feesType->amount !== 0 )
                            <input type="checkbox" name="selected_fees[]" value="{{ $fee->id }}">
                            <input type="hidden" name="selected_amount[{{ $fee->id }}]" value="{{  $fee->feesType->amount + $fee->paid_amount }}">
                            @endif
                        </td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $fee->feesType ? $fee->feesType->month : 'N/A' }}</td>
                        <td>{{ $fee->feesType ? $fee->feesType->fm_fees_type->name : 'N/A' }}</td>
                        <td class="due-amount">{{ $fee->feesType->amount == 0 ? $fee->feesType->amount + $fee->paid_amount : $fee->feesType->amount }}</td>
                        <td>
                            <button type="button" class="btn btn-danger" onclick="feeDelete({{ $fee->id }})">Delete</button>
                            @if(($fee->feesType->amount == 0 ? $fee->feesType->amount + $fee->paid_amount : $fee->feesType->amount ) == 0 )
                            <button type="button" class="btn btn-primary" onclick="feeEdit({{ $fee->id }})">Edit</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Total</th>
                        <th id="totalDue">0.00</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <button type="submit" class="btn btn-primary">Make Payment</button>
        </form>
    </div>
    







    
@else
    <div class="alert alert-warning">
        No fees records found for the given student ID.
    </div>
@endif
</div>



<!-- Edit Fee Modal -->
<div class="modal fade" id="editFeeModal" tabindex="-1" aria-labelledby="editFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFeeModalLabel">Edit Fee Amount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editFeeForm">

                    <input type="hidden" id="editFeeId" name="fee_id">
                 
                    <div class="mb-3">
                        <label for="editAmount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="editAmount" name="paid_amount" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEditFee">Save changes</button>
            </div>
        </div>
    </div>
</div>







@endsection 



@push('scripts')


<script>

    let rowIndex = 1;
    
    function submitFee(event) {
        event.preventDefault();
    
        const form = document.getElementById('addFeesForm');
        const feesType = document.getElementById('selectFeesType');
        const amount = document.getElementById('amount').value;
        const selectedMonths = Array.from(document.querySelectorAll('input[name="months[]"]:checked')).map(cb => cb.value);
        const feesTypeName = feesType.options[feesType.selectedIndex].text;
        const studentId = document.getElementById('student_id').value;
    
        if (!feesType.value || !amount) {
            alert('Please fill all required fields');
            return;
        }
    
        // Collect form data
        const formData = new FormData(form);
    
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the selected fees type from the dropdown
                feesType.querySelector(`option[value="${feesType.value}"]`).remove();
    
                // Reset the form
                form.reset();
    
                // Update the fees summary table
                let monthText = selectedMonths.join(', ');
    
                const tableBody = document.querySelector('#feesTable tbody');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${rowIndex++}</td>
                    <td>${feesTypeName}</td>
                    <td>${monthText}</td>
                    <td>${parseFloat(amount).toFixed(2)}</td>
                    <td>0.00</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                `;
                tableBody.appendChild(newRow);
            } else {
                alert('Error adding fees.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
        // function feeDelete(id) {
    
        //     alert(id);
    
        // }
    
    
        function feeDelete(id) {
        if (confirm('Are you sure you want to delete this fee?')) {
            $.ajax({
                url: "{{ url('fees/student-fees-delete/') }}"+ '/' +id,
                type: 'POST', // or 'DELETE' if your route is defined as delete
                data: {
    
                    
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Reload the page or update the UI
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error deleting fee: ' + xhr.responseText);
                }
            });
        }
    }
    
    
   
    
    
    function feeEdit(feeId) {
        // alert("helkjlaskdjlaskjdlkj");
        $.ajax({
            url: "{{ url('fees/student-fees-edit') }}/" + feeId,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Populate the modal with the fee details
                document.getElementById('editFeeId').value = feeId;
                
                document.getElementById('editAmount').value = response.fee.paid_amount;
                // Show the modal
                $('#editFeeModal').modal('show');
            },
            error: function(xhr) {
                alert('Error fetching fee details: ' + xhr.responseText);
            }
        });
    }
    
    
    
    
    
        function feeDelete(id) {
            if (confirm('Are you sure you want to delete this fee?')) {
                $.ajax({
                    url: "{{ url('fees/student-fees-delete/') }}"+ '/' +id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error deleting fee: ' + xhr.responseText);
                    }
                });
            }
        }

    $(document).ready(function() {
    $('#saveEditFee').click(function() {

        var feeId = $('#editFeeId').val();
        var amount = $('#editAmount').val();

        // Validate required fields
        if (amount == 0 && amount == '') {
            alert('Please fill all required fields');
            return;
        }

        $.ajax({
            // url: "/fees/update-amount/",
            url: "{{ url('fees/student-fees-update-amount') }}",
            type: 'POST',
            data: {
                fee_id: feeId,
                paid_amount: amount,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
               
                console.log(response);

                $('#editFeeModal').modal('hide');
                location.reload(); // Reload the page to reflect the changes
            },
            error: function(xhr) {
                alert('Error updating fee amount: ' + xhr.responseText);
            }
        });
    });
});






document.addEventListener('DOMContentLoaded', function() {
    function calculateTotalDue() {
        let totalDue = 0;
        document.querySelectorAll('#feesTable .due-amount').forEach(function(cell) {
            const dueValue = parseFloat(cell.textContent.trim()) || 0;
            totalDue += dueValue;
        });
        document.getElementById('totalDue').textContent = totalDue.toFixed(2);
    }

    calculateTotalDue(); // Initial calculation

    // Add event listeners to buttons that might affect the total due calculation
    document.querySelectorAll('#feesTable .btn').forEach(function(button) {
        button.addEventListener('click', function() {
            setTimeout(calculateTotalDue, 100); // Recalculate after any operation
        });
    });

    // Example for dynamically added rows (e.g., after an Ajax request)
    document.getElementById('makePayment').addEventListener('click', function() {
        calculateTotalDue();
    });
});

    
    
    
    
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














