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

    <div class="white-box mt-20">
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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date:</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                </div>
            </div>
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
    </div>

    <div class="white-box mt-20">
        <h4>Fees Summary</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Particular</th>
                    <th>Month</th>
                    <th>Fees Type</th>
                    <th>Paid Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($feesSummary as $key => $fee)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $fee->month }}</td>
                        <td>{{ $fee->feesType->fm_fees_type->name }}</td>
                        
                        {{-- <td>{{ $fee->amount }}</td> --}}
                        <td>{{ $fee->paid_amount }}</td>
                        <td>
                            <form method="POST" action="{{ url('fees/delete', $fee->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="3">Total</th>
                    <th>{{ $totalPayable }}</th>
                    <th>{{ $totalPaid }}</th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="3">Due</th>
                    <th>{{ $totalPayable - $totalPaid }}</th>
                    <th colspan="2"></th>
                </tr>
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-warning">
        No fees records found for the given student ID.
    </div>
@endif
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


   



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
@endpush














