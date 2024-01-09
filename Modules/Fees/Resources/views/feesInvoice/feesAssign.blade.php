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
          <form id="feeForm" method="GET" action="{{ url('fees/fees-amount-search') }}" >
            @csrf
            <div class="mb-3">
              <label for="year" class="form-label">Year:</label>
              <input type="text" class="form-control" id="year" name="year" required>
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
                name="sm_class_id" id="selectClass">
                <option data-display="@lang('common.select_class') *" value="">
                  @lang('common.select_class') *</option>
                @foreach ($classes as $class)
                  <option value="{{ $class->id }}"
                      {{ isset($invoiceInfo) ? ($invoiceInfo->class_id == $class->id ? 'selected' : '') : '' }}>
                      {{ $class->class_name }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary" id="searchButton">Search</button>
              
            </div>
            

    


          </form>
        </div>
      </div>

    <div class="card">
      <div class="card-body">
        
        <form id="feeForm" method="POST" action="">

            <h5 class="card-title">Fee Assigned Card</h5>

            <div class="mb-3">
              <label for="select-fees" class="form-label">Select fees:</label>
              
              <select
                  class="form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}"
                  id="selectFeesType" name="fm_fees_type_id">
                  <option data-display="@lang('fees.fees_type') *" value="">@lang('fees.fees_type')
                      *</option>
                  
                  
                  
                  @foreach ($feesTypes as $feesType)
                      <option value="{{ $feesType->id }}">{{ $feesType->name }}</option>
                  @endforeach
              </select>
              
            </div>
            
            <div class="mb-3">
              <label for="amount" class="form-label">Amount:</label>
              <input type="text" class="form-control" id="amount" name="amount" required>
            </div>
            <button type="submit" class="btn btn-primary" onclick="submitFee()">Add Fees</button>
        
        </form>
      </div>
    </div>
  
    <div class="mt-4">
      <h5>Fee Data Table</h5>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">year</th>
            <th scope="col">Month</td>
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



{{-- 
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




</script> --}}




{{-- <script>
  $(document).ready(function () {
      $('#feeForm').submit(function (e) {
          e.preventDefault();

          var formData = $(this).serialize();

          $.ajax({
              url: '{{ url("fees/fees-amount-search") }}',
              type: 'POST',
              data: formData,
              dataType: 'json',
              success: function (data) {
                  // Clear existing table data
                  $('#feeTableBody').empty();

                  // Append new data to the table
                  $.each(data, function (index, fee) {
                      var row = '<tr>' +
                          '<td>' + fee.id + '</td>' +
                          '<td>' + fee.name + '</td>' +
                          '<td>' + fee.amount + '</td>' +
                          '<td>' +
                          '<button class="btn btn-sm btn-warning">Edit</button>' +
                          '<button class="btn btn-sm btn-danger">Delete</button>' +
                          '</td>' +
                          '</tr>';
                      $('#feeTableBody').append(row);
                  });
              },
              error: function (xhr, status, error) {
                  console.error(error);
              }
          });
      });
  });
</script> --}}


<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


<script>
    $(document).ready(function () {
        // Intercept the form submission
        $('#feeForm').submit(function (event) {
            event.preventDefault(); // Prevent the form from submitting traditionally

            // Perform an Ajax request
            $.ajax({
                type: 'GET',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function (data) {
                    // Handle the successful response
                    updateFeeTable(data);
                },
                error: function () {
                    // Handle errors, if any
                    console.error('Error in Ajax request');
                }
            });
        });

        // Function to update the fee table with the received data
        function updateFeeTable(data) {
            // Clear existing rows
            $('#feeTableBody').empty();

            // Append new rows based on the received data
            data.forEach(function (fee) {
                const row = $('<tr>');
                row.html(`
                    <td>${fee.id}</td>
                    <td>${fee.year}</td>
                    <td>${fee.month}</td>
                    <td>${fee.amount}</td>
                    <td>
                        <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
                        <button class="btn btn-success" onclick="editRow(this)">Edit</button>
                    </td>
                `);
                $('#feeTableBody').append(row);
            });
        }
        
    });




    function deleteRow(button) {
        const row = button.closest('tr');
        const id = row.cells[0].textContent;

        // Make an AJAX request to delete the record
        fetch(`{{ url("fees/delete-fees-type-amount") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Remove the row from the table on success
                row.remove();
                // Optionally show a success message
                alert('Record deleted successfully.');
            } else {
                // Optionally show an error message
                alert('Failed to delete record.');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function editRow(button) {
        const row = button.closest('tr');
        const id = row.cells[0].textContent;
        // Fetch the data for the selected record if needed
        // You can then populate a form for editing
    }



</script>





{{-- 
<script>
  document.getElementById('searchButton').addEventListener('click', function () {
      // Call the function to fetch and display data from the server
      fetchAndDisplayData();
  });

  function fetchAndDisplayData() {
      // Make an AJAX request to fetch data from the server
      // Update the following URL with your actual endpoint
      const url = '{{ url("fees/feesTypeAmountList") }}';

      const formData = new FormData(document.getElementById('feeForm'));

      fetch(url, {
          method: 'POST',
          body: formData,
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
        <td>${fee.year}</td>
        <td>${fee.month}</td>
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
