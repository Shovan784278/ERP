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


    <div class="container">
        <div class="white-box mt-20">
            <form method="GET" action="{{ route('fees.result') }}">
           
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student ID:</label>
                            <input type="text" class="form-control" id="student_id" name="student_id" value="{{ old('student_id', request('student_id')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year:</label>
                            <select class="form-control" id="academic_year" name="academic_year">
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year', request('academic_year')) == $year->id ? 'selected' : '' }}>
                                        {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Search Student</button>
            </form>
        </div>
  
    </div>
    


    <div class="container">
        <div class="white-box mt-20">
            <form method="GET" action="{{ route('fees.result') }}">
           
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Form:</label>
                            <input type="text" class="form-control" id="student_id" name="student_id" value="{{ old('student_id', request('student_id')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">To Date:</label>
                           <input type="date" name="" id="">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Search Student</button>
            </form>
        </div>
  
    </div>
    




@endsection 



@push('scripts')

  <script>

        let rowIndex = 1;

        function submitFee(event) {
        event.preventDefault();
        const feesType = document.getElementById('selectFeesType');
        const amount = document.getElementById('amount').value;
        const selectedMonths = Array.from(document.querySelectorAll('input[name="months[]"]:checked')).map(cb => cb.value);
        const feesTypeName = feesType.options[feesType.selectedIndex].text;

        if (!feesType.value || !amount) {
            alert('Please fill all required fields');
            return;
        }

        let monthText = selectedMonths.map(month => new Date(0, month - 1).toLocaleString('en', { month: 'long' })).join(', ');

        const student = document.getElementById('student_id').value || 0;


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

        updateTotals(parseFloat(amount));
    }
</script> 


   

 {{-- <script>
    $(document).ready(function() {
        $('#addFeesForm').submit(function(event) {
            event.preventDefault();
            
            $.ajax({
                url: '{{ route('fees.add') }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#successMessage').show();
                    $('#errorMessage').hide();
                    loadFeesSummary();
                },
                error: function(response) {
                    $('#errorMessage').text(response.responseJSON.message).show();
                    $('#successMessage').hide();
                }
            });
        });

        function loadFeesSummary() {
            var student_id = $('#student_id').val();
            var academic_year = $('#academic_year').val();

            $.ajax({
                url: '{{ url('fees/summary') }}',
                method: 'GET',
                data: {
                    student_id: student_id,
                    academic_year: academic_year
                },
                success: function(response) {
                    var tbody = $('#feesSummaryTable tbody');
                    tbody.empty();
                    response.feesSummary.forEach(function(fee) {
                        var row = '<tr>' +
                            '<td>' + fee.student_id + '</td>' +
                            '<td>' + fee.fees_type_name + '</td>' +
                            '<td>' + fee.amount + '</td>' +
                            '<td>' + fee.months + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                }
            });
        }

        $('#student_id, #academic_year').change(function() {
            loadFeesSummary();
        });

        // Initial load
        loadFeesSummary();
    });
</script>  --}}



 {{-- <script>
    $(document).ready(function() {
    function loadFeesSummary() {
        var student_id = $('#student_id').val();
        var academic_year = $('#academic_year').val();

        $.ajax({
            url: '{{ route('fees.summary') }}',
            method: 'GET',
            data: {
                student_id: student_id,
                academic_year: academic_year
            },
            success: function(response) {
                var tbody = $('#feesSummaryTable tbody');
                tbody.empty();
                if (response.feesSummary.length > 0) {
                    $('#feesSummaryTable').show();
                    response.feesSummary.forEach(function(fee) {
                        var row = '<tr>' +
                            '<td>' + fee.student_id + '</td>' +
                            '<td>' + fee.fees_type_name + '</td>' +
                            '<td>' + fee.paid_amount + '</td>' +
                            '<td>' + fee.pay_Year_Month + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                } else {
                    $('#feesSummaryTable').hide();
                }
            },
            error: function(response) {
                console.error(response);
            }
        });
    }

    $('#searchStudentForm').submit(function(event) {
        event.preventDefault();
        var student_id = $('#searchStudentId').val();
        var academic_year = $('#searchAcademicYear').val();

        $('#student_id').val(student_id);
        $('#academic_year').val(academic_year);

        loadFeesSummary();
    });

    $('#addFeesForm').submit(function(event) {
        event.preventDefault();

        $.ajax({
            url: '{{ route('fees.add') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#successMessage').show().delay(3000).fadeOut();
                $('#errorMessage').hide();
                loadFeesSummary();
            },
            error: function(response) {
                if (response.status === 422) { // Validation error
                    var errors = response.responseJSON.errors;
                    var errorMessages = '';
                    for (var key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            errorMessages += errors[key][0] + '<br>';
                        }
                    }
                    $('#errorMessage').html(errorMessages).show();
                    $('#successMessage').hide();
                } else {
                    $('#errorMessage').text('An error occurred. Please try again.').show();
                    $('#successMessage').hide();
                }
            }
        });
    });

    // Initial load if student_id and academic_year are already set
    if ($('#student_id').val() && $('#academic_year').val()) {
        loadFeesSummary();
    }
});

</script>  --}}



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
@endpush














