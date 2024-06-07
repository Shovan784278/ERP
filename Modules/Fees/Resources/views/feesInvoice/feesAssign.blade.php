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
                
                <div class="mb-3">
                <label for="year" class="form-label">Year:</label>
                
                <select class="form-control" id="academic_id" name="academic_id">
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}" 
                            
                            
                            @if (getAcademicId() == $year->id)
                                selected
                                
                            @endif>
                            {{ $year->year }}</option>
                        @endforeach
                </select>
                


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
                    name="sm_class_id" id="selectClass" required>
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


<script>


</script>







{{-- <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script> --}}
