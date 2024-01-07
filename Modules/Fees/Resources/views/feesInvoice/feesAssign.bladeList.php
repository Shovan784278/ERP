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




{{-- <script>



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




</script> --}}

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
