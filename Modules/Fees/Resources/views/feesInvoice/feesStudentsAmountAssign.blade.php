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



    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Payment
                    </div>
                    <div class="card-body">
                        <form id="feeForm" method="POST" onsubmit="submitFee(event)">
                            @csrf
                            <div class="mb-3">
                                <label for="selectFeesType" class="form-label">Fees Type:</label>
                                <select class="form-control" id="selectFeesType" name="fm_fees_type_id" required>
                                    <option value="">Fees Type *</option>
                                    @foreach ($feesTypes as $feesType)
                                        <option value="{{ $feesType->id }}">{{ $feesType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount:</label>
                                <input type="text" class="form-control" id="amount" name="amount" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">For Additional Applicable Months:</label>
                                <div>
                                    @foreach (range(1, 12) as $month)
                                        <label class="form-check-label">
                                            <input type="checkbox" name="months[]" value="{{ $month }}"> {{ date('F', mktime(0, 0, 0, $month, 10)) }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>
                        <hr>
                        <table class="table table-bordered" id="feesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Particular</th>
                                    <th>Month</th>
                                    <th>Payable Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td id="totalPayable">0.00</td>
                                    <td id="totalPaid">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Due</td>
                                    <td id="totalDue" class="text-danger">0.00</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
        const feesType = document.getElementById('selectFeesType');
        const amount = document.getElementById('amount').value;
        const selectedMonths = Array.from(document.querySelectorAll('input[name="months[]"]:checked')).map(cb => cb.value);
        const feesTypeName = feesType.options[feesType.selectedIndex].text;

        if (!feesType.value || !amount) {
            alert('Please fill all required fields');
            return;
        }

        let monthText = selectedMonths.map(month => new Date(0, month - 1).toLocaleString('en', { month: 'long' })).join(', ');

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
    
@endpush














