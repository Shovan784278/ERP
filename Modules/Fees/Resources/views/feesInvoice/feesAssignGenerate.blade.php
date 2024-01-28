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
          <form id="feeForm" method="POST" action="{{ url('fees/fees-generate') }}" >
            @csrf 

            <div class="mb-3">
                <label for="date" class="form-label">Date:</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required readonly>
            </div>

            {{-- <div class="mb-3">
                <label for="year" class="form-label">Year:</label>
                <select class="form-control" id="year" name="year"></select>
            </div>     --}}
            
            <div class="mb-3">
                <label for="year" class="form-label">Year:</label>
                
                <select class="form-control" id="year" name="year">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
            </select>
            </div>


            <div class="mb-3">
                <label for="class" class="form-label">Class:</label>
                <select class="form-control" name="sm_class_id" id="sm_class_id" required disabled>
                    <!-- Options will be dynamically loaded here -->
                </select>
            </div>

            
            <div class="mb-3">
                <label for="month" class="form-label">Month:</label>
                <select class="form-control" name="month" id="month" required disabled>
                    <!-- Options will be dynamically loaded here -->
                </select>
            </div>
            
     

            <div class="d-flex justify-content-center align-items-center" style="height: 10vh;">
                {{-- <input type="text" id="searchInput" name="search_query" placeholder="Enter search query"> --}}
                <button type="submit" class="btn btn-primary" id="searchButton">Generate</button>

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
    $(document).ready(function () {
        


        function fetchMonths(selectedYear, selectedClass) {
            $.ajax({
                url: 'get-months?year=' + selectedYear + '&class=' + selectedClass,
                type: 'GET',
                success: function (data) {
                    populateDropdown(data, '#month');
                },
                error: handleAjaxError
            });
        }
 


        function handleAjaxError(xhr, status, error) {
            console.log('Error fetching options');
            console.log('Status:', status);
            console.log('Error:', error);
        }

        

        $('#year').change(function () {
            const selectedYear = $(this).val();

            // Fetch and populate the class dropdown based on the selected year
            if (selectedYear) {
                $.ajax({
                url: 'get-classes?year=' + selectedYear,
                type: 'GET',
                success: function (data) {
                    

                    console.log('Received data:', data);
                        let targetSelect = '#sm_class_id';
                        // Clear existing options
                        $(targetSelect).empty();
                        
                        // Add a default option
                        const defaultOption = $('<option>').val('').text('Select');
                        //console.log(typeof data);
                        $(targetSelect).append(defaultOption);

                        data.forEach(function (item,i) {
                        
                        let values = Object.values(item);
                        //console.log(values[0]);
                        
                        let option=`<option value="${values[1]}">${values[0]}</option>`
                        $(targetSelect).append(option);
                    })
                    $('#sm_class_id').attr('disabled', false);


                    

                },
                error: handleAjaxError
            });


            } else {
                // Reset the class and month dropdowns if no year is selected
                $('#sm_class_id').empty().attr('disabled', true);
                $('#month').empty().attr('disabled', true);
            }


        });


        $('#sm_class_id').change(function () {
            const selectedYear = $('year').val();
            const selectedClass = $(this).val();

            // Fetch and populate the class dropdown based on the selected year
            if (selectedClass) {
                $.ajax({
                url: 'get-months?year='+ selectedYear +'&class=' + selectedClass,
                type: 'GET',
                success: function (data) {
                    

                    console.log('Received data:', data);
                        let targetSelect = '#month';
                        // Clear existing options
                        $(targetSelect).empty();
                        
                        // Add a default option
                        const defaultOption = $('<option>').val('').text('Select');
                        //console.log(typeof data);
                        $(targetSelect).append(defaultOption);

                        data.forEach(function (item,i) {
                        
                        
                        //console.log(values[0]);
                        
                        let option=`<option value="${item}">${item}</option>`
                        $(targetSelect).append(option);
                    })
                    $('#month').attr('disabled', false);


                    

                },
                error: handleAjaxError
            });


            } else {
                // Reset the class and month dropdowns if no year is selected
                $('#sm_class_id').empty().attr('disabled', true);
                $('#month').empty().attr('disabled', true);
            }


        });


        
    });

</script>
