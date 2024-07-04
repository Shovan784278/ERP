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

    <div class="container mt-5">
        <div class="card custom-card">
            <div class="card-header custom-card-header">
                <h4>Search Fees by Date Range</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('fees.search-Fees-Due-Date') }}">
                    {{-- @csrf --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_date" class="form-label">From:</label>
                                <input type="date" name="form_date" id="form_date" class="form-control" value="{{ request('form_date') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_date" class="form-label">To Date:</label>
                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Search Fees</button>
                </form>
            </div>
        </div>
    </div>

        @if($fees->isNotEmpty())
            <div class="white-box mt-20">
                <h4>Fees Search Results from : {{ $fromDate }} to {{ $toDate }}</h4><br><br>
                <table id="feesResults" class="display">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Class</th>
                            <th>Pay Date</th>
                            <th>Paid Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fees as $fee)
                            <tr>
                                <td>{{ $fee->student_id }}</td>
                                <td>{{ $fee->class_id }}</td>
                                <td>{{ $fee->pay_date }}</td>
                                <td>{{ $fee->paid_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="white-box mt-20">
                <h4>No fees found for the selected criteria.</h4>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>

<!-- JSZip -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<!-- PDFMake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<!-- DataTables Buttons HTML5 and Print -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#feesResults').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: 'Copy',
                className: 'btn btn-primary'
            },
            {
                extend: 'csv',
                text: 'CSV',
                className: 'btn btn-primary'
            },
            {
                extend: 'excel',
                text: 'Excel',
                className: 'btn btn-primary'
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'btn btn-primary',
                customize: function(doc) {
                    // Center align all cells in the table
                    doc.content[1].table.body.forEach(function(row) {
                        row.forEach(function(cell) {
                            cell.alignment = 'center';
                        });
                    });
                }
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary'
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search..."
        }
    });
});
</script>
@endpush



<style>
    /* Specific selector for DataTables buttons within the .results-box */
    .results-box .dt-buttons .dt-button,
    .results-box .dt-buttons .btn.btn-primary {
        background-color: #7C32FF !important; /* Use !important to override other styles */
        color: white !important; /* Use !important to ensure text color is applied */
        padding: 10px 15px !important;
        border-radius: 3px !important;
        border: none !important; /* Remove the border */
        margin: 5px !important;
    }

    .results-box .dt-buttons .dt-button:hover,
    .results-box .dt-buttons .btn.btn-primary:hover {
        background-color: #5A21CC !important; /* Darker shade for hover */
        color: white !important; /* Ensure text color is white on hover */
    }
</style>
