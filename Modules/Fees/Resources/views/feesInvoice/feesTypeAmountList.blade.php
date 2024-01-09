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

        if (isFeeTypeAlreadyExists(id)) {
            toaster("This fees type already exists");
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
        select.selectedIndex = 0;
        document.getElementById('amount').value = '';
    }

    function isFeeTypeAlreadyExists(id) {
        const feeTableBody = document.getElementById('feeTableBody');
        const rows = feeTableBody.getElementsByTagName('tr');

        for (const row of rows) {
            const rowId = row.cells[0].textContent;
            if (rowId === id) {
                return true;
            }
        }

        return false;
    }

    function deleteRow(button) {
        const row = button.closest('tr');
        row.remove();
    }

    function editRow(button) {
        const row = button.closest('tr');
        const cells = row.children;

        const id = cells[0].textContent;
        const text = cells[1].textContent;
        const amount = cells[2].textContent;

        document.getElementById('selectFeesType').value = id;
        document.getElementById('amount').value = amount;

        row.remove();
    }

    document.getElementById('searchButton').addEventListener('click', function () {
        // Fetch and display data from the server
        fetchAndDisplayData();
    });

    function fetchAndDisplayData() {
        // Make an AJAX request to fetch data from the server
        // Update the following URL with your actual endpoint
        const url = 'fees/fees-amount-search';
        
        // Assuming you're using fetch API
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Add any other headers if needed
            },
            body: JSON.stringify({
                // Add your search parameters here (year, month, class, etc.)
            }),
        })
        .then(response => response.json())
        .then(data => {
            // Clear existing table data
            const feeTableBody = document.getElementById('feeTableBody');
            feeTableBody.innerHTML = '';

            // Append new data to the table
            data.forEach(fee => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${fee.id}</td>
                    <td>${fee.name}</td>
                    <td>${fee.amount}</td>
                    <td>
                        <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
                        <button class="btn btn-success" onclick="editRow(this)">Edit</button>
                    </td>
                `;
                feeTableBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
    }
</script> --}}



<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
