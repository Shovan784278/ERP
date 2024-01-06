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
          <form id="feeForm" method="POST" action="" >
            @csrf
            <div class="mb-3">
              <label for="year" class="form-label">Year:</label>
              <input type="text" class="form-control" id="year" required>
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
              

            <select class="form-control" name="months">
                @foreach($months as $month)
                    <option value="{{ $month }}">{{ $month }}</option>
                @endforeach
            </select>
              
            </div>
            <div class="mb-3">
              <label for="amount" class="form-label">Class:</label>

            <select
                class="form-control{{ $errors->has('class') ? ' is-invalid' : '' }}"
                name="class" id="selectClass">
                <option data-display="@lang('common.select_class') *" value="">
                  @lang('common.select_class') *</option>
                @foreach ($classes as $class)
                  <option value="{{ $class->id }}"
                      {{ isset($invoiceInfo) ? ($invoiceInfo->class_id == $class->id ? 'selected' : '') : '' }}>
                      {{ $class->class_name }}</option>
                @endforeach
            </select>
              
            </div>
            <button type="button" class="btn btn-primary">Search</button>
          </form>
        </div>
      </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Fee Assigned Card</h5>
        <form id="feeForm">
        <div class="mb-3">
            <label for="select-fees" class="form-label">Select fees:</label>
            
            <select
                class="form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}"
                id="selectFeesType" name="fees_type">
                <option data-display="@lang('fees.fees_type') *" value="">@lang('fees.fees_type')
                    *</option>
                
                
                
                @foreach ($feesTypes as $feesType)
                    <option value="{{ $feesType->id }}">{{ $feesType->name }}</option>
                @endforeach
            </select>
            
          </div>
          
          <div class="mb-3">
            <label for="amount" class="form-label">Amount:</label>
            <input type="text" class="form-control" id="amount" required>
          </div>
          <button type="button" class="btn btn-primary" onclick="submitFee()">Add Fees</button>
        </form>
      </div>
    </div>
  
    <div class="mt-4">
      <h5>Fee Data Table</h5>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            
            <th scope="col">Amount</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="feeTableBody">
          <!-- Fee data will be appended here -->
        </tbody>
      </table>
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




<script>



        function submitFee() {
        let select = document.getElementById('selectFeesType');
        let amount = parseInt(document.getElementById('amount').value);

        let id = select.value;
        let text = select.options[select.selectedIndex].text;

        // Check if a row with the same fee type already exists
        if (isFeeTypeAlreadyExists(id)) {
            //alert('This fee type already exists in the table.');
            toaster("This fees type already exist");
            return;
        }

        if (id === '') {
            alert('Please select a fee type.');
            return;
        }

        if (isNaN(amount) || amount <= 0) {
            alert('Please enter a valid and positive amount.');
            return;
        }

        //This is fee table

        const feeTableBody = document.getElementById('feeTableBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${id}</td>
            <td>${text}</td>
            <td>${amount}</td>
            <td>
            <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
            <button class="btn btn-success" onclick="editRow(this)">Edit</button>
            </td>
        `;

        feeTableBody.appendChild(row);

        // Reset the form after submission
        select.selectedIndex = 0; // Reset the selected index to the default option
        document.getElementById('amount').value = '';
        }

        // Function to check if a row with the given fee type already exists
        function isFeeTypeAlreadyExists(id) {
        const feeTableBody = document.getElementById('feeTableBody');
        const rows = feeTableBody.getElementsByTagName('tr');

        for (const row of rows) {
            const rowId = row.cells[0].textContent;
            if (rowId === id) {
            return true; // Found a row with the same fee type
            }
        }

        return false; // No row with the same fee type found
        }


        // Function to delete a row from the fee table
        function deleteRow(button) {
        const row = button.closest('tr');
        row.remove();
        }

        // Event listener to execute the insertStaticData function when the page is loaded
        document.addEventListener('DOMContentLoaded', insertStaticData);


        // Script for edit

        function editRow(button) {
        const row = button.closest('tr');
        const cells = row.children;

        // Assuming the first cell contains the ID, the second cell contains the text, and the third cell contains the amount
        const id = cells[0].textContent;
        const text = cells[1].textContent;
        const amount = cells[2].textContent;

        // Now you can use these values to populate your form or perform any other edit-related actions
        
        document.getElementById('selectFeesType').value = id;
        document.getElementById('amount').value = amount;

        // Optionally, you can remove the existing row from the table
        row.remove();

        document.getElementById('feeForm').reset();
        
        }

        // Function to find an existing row with the given ID
        function findExistingRow(id) {
        const feeTableBody = document.getElementById('feeTableBody');
        const rows = feeTableBody.getElementsByTagName('tr');

        for (const row of rows) {
            const rowId = row.cells[0].textContent;
            if (rowId === id) {
            return row;
            }
        }

        return null;
        }




</script>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
