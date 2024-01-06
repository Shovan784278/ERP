@extends('backEnd.master')
@section('title')
@lang('inventory.item_sell')
@endsection
@section('mainContent')
@push('css')
<style type="text/css">
    #productTable tbody tr{
        border-bottom: 1px solid #FFFFFF !important;
    }
    .forStudentWrapper, #selectStaffsDiv{
        display: none;
    }
</style>
@endpush
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('inventory.item_sell')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="{{route('item-sell')}}">@lang('inventory.item_sell')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
     
     {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'save-item-sell-data',
     'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'item-sell-form']) }}

     <div class="row">
        <div class="col-lg-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-title">
                        <h3 class="mb-30">
                            @if(isset($editData))
                                @lang('common.edit_sell')
                            @else
                                @lang('inventory.sells_details')    
                            @endif
                            
                        </h3>
                    </div>

                    <div class="white-box">
                        <div class="add-visitor">
                            <div class="row">
                            <div class="col-lg-12 mb-30">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('income_head_id') ? ' is-invalid' : '' }}" name="income_head_id" id="income_head_id">
                                            <option data-display="@lang('inventory.income_head')*" value="">@lang('common.select') *</option>
                                            @foreach($sell_heads as $sell_head)
                                            <option value="{{$sell_head->id}}">{{$sell_head->head}}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger" id="incomeError"></div>
                                        @if ($errors->has('income_head_id'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('income_head_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-12 mb-30">
                                        <select class="niceSelect w-100 bb form-control" name="payment_method" id="item_sell_payment_method_id">
                                            <option data-display="@lang('inventory.payment_method')*" value="">@lang('inventory.payment_method')*</option>
                                            @foreach($paymentMethhods as $key=>$value)
                                                <option data-string="{{$value->method}}" value="{{$value->id}}">{{$value->method}}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger" id="paymentError"></div>
                                    </div>
                                    <div class="col-lg-12 mb-30 d-none" id="add_item_sell_bankAccount">
                                        <div class="input-effect">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('bank_id') ? ' is-invalid' : '' }}" name="bank_id" id="account_id">
                                            @if(isset($account_id))
                                            @foreach($account_id as $key=>$value)
                                            <option value="{{$value->id}}">{{$value->account_name}} ({{$value->bank_name}})</option>
                                            @endforeach
                                            @endif
                                            </select>
                                            <div class="text-danger" id="accountError"></div>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('bank_id'))
                                            <span class="invalid-feedback invalid-select" role="alert">
                                                <strong>{{$errors->first('bank_id')}}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                 <div class="col-lg-12 mb-30">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="role_id" id="buyer_type">
                                            <option data-display="@lang('inventory.sell_to') *" value="">@lang('inventory.sell_to') *</option>
                                            @foreach($roles as $value)
                                            @if(isset($editData))
                                            <option value="{{$value->id}}" {{$value->id == $editData->role_id? 'selected':''}}>{{$value->name}}</option>
                                            @else
                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                        <div class="text-danger" id="buyerError"></div>
                                        @if ($errors->has('role_id'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('role_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="forStudentWrapper col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12 mb-30">
                                                <select class="w-100 bb niceSelect form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" id="select_class" name="class">
                                                    <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class') *</option>
                                                    @foreach($classes as $class)
                                                    <option value="{{$class->id}}"  {{( old("class") == $class->id ? "selected":"")}}>{{$class->class_name}}</option>
                                                    @endforeach
                                                </select>
                                                <div class="text-danger" id="classError"></div>
                                                @if ($errors->has('class'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('class') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-lg-12 mb-30" id="select_section_div">
                                                <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }}" id="select_section" name="section">
                                                    <option data-display="@lang('common.select_section') *" value="">@lang('common.select_section') *</option>
                                                </select>
                                                <div class="text-danger" id="sectionError"></div>
                                                @if ($errors->has('section'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('section') }}</strong>
                                                </span>
                                                @endif
                                            </div>


                                            <div class="col-lg-12 mb-30" id="select_student_div">
                                                <select class="w-100 bb niceSelect form-control{{ $errors->has('student') ? ' is-invalid' : '' }}" id="select_student" name="student">
                                                    <option data-display="@lang('common.select_student') *" value="">@lang('common.select_student') *</option>
                                                </select>
                                                <div class="text-danger" id="studentError"></div>
                                                @if ($errors->has('student'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('student') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-30" id="selectStaffsDiv">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('staff_id') ? ' is-invalid' : '' }}" name="staff_id" id="selectStaffs">
                                            <option data-display="@lang('common.name') *" value="">@lang('common.name') *</option>

                                            @if(isset($staffsByRole))
                                            @foreach($staffsByRole as $value)
                                            <option value="{{$value->id}}" {{$value->id == $editData->staff_id? 'selected':''}}>{{$value->full_name}}</option>
                                            @endforeach
                                            @else

                                            @endif
                                        </select>
                                        <div class="text-danger" id="nameError"></div>
                                        @if ($errors->has('staff_id'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('staff_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                <div class="col-lg-12 mb-30">
                                    <div class="input-effect">
                                        <input class="primary-input form-control"
                                        type="text" name="reference_no" autocomplete="off" value="{{isset($editData)? $editData->reference_no : '' }}">
                                        <label>@lang('inventory.reference_no')</label>
                                        <span class="focus-border"></span>
                                        
                                    </div>
                                </div>

                                <div class="col-lg-12 no-gutters input-right-icon mb-30">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input date form-control{{ $errors->has('sell_date') ? ' is-invalid' : '' }}"  id="sell_date" type="text"
                                            name="sell_date" value="{{isset($editData)? date('m/d/Y', strtotime($editData->sell_date)): date('m/d/Y')}}" autocomplete="off">
                                            <label>@lang('inventory.sell_date') <span></span> </label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('sell_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('sell_date') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="receive_date_icon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-20">
                                    <div class="input-effect">
                                        <textarea class="primary-input form-control" cols="0" rows="4" name="description" id="description">{{isset($editData) ? $editData->description : ''}}</textarea>
                                        <label>@lang('common.description') <span></span> </label>
                                        <span class="focus-border textarea"></span>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-9">
            
          <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-20">@lang('inventory.item_sell')</h3>
                </div>
            </div>

            <div class="offset-lg-6 col-lg-2 text-right col-md-6 mb-20">
                <button type="button" class="primary-btn small fix-gr-bg" onclick="addRowInSell();" id="addRowBtn">
                    <span class="ti-plus pr-2"></span>
                    @lang('common.add') 
                </button>
            </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
             <div class="white-box">
                <div class="alert alert-danger" id="errorMessage2">
                    <div id="itemError"></div>
                    <div id="priceError"></div>
                    <div id="quantityError"></div>                     
                </div>
                 <table class="table" id="productTable">
                    <thead>
                      <tr>
                          <th> @lang('inventory.product_name') </th>
                          <th> @lang('inventory.sell_price') </th>
                          <th> @lang('inventory.quantity') </th>
                          <th>@lang('inventory.sub_total')</th>
                          <th>@lang('common.action')</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr id="row1" class="0">
                        <td class="border-top-0">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}"> 
                            <div class="input-effect">
                                <select class="niceSelect w-100 bb form-control{{ $errors->has('category_name') ? ' is-invalid' : '' }}" name="item_id[]" id="productName1">
                                    <option data-display="@lang('common.select_item') " value="">@lang('common.select')</option>
                                    @foreach($items as $key=>$value)
                                    <option value="{{$value->id}}"
                                        @if(isset($editData))
                                        @if($editData->category_name == $value->id)
                                        selected
                                        @endif
                                        @endif
                                        >{{$value->item_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('item_id'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('item_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('unit_price') ? ' is-invalid' : '' }}"
                                    type="number" step="0.1" id="unit_price1" name="unit_price[]" onkeypress="return isNumberKey(event)" autocomplete="off" value="{{isset($editData)? $editData->unit_price : '' }}" onkeyup="getTotalByPrice(1)">

                                    <span class="focus-border"></span>
                                    @if ($errors->has('unit_price'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('unit_price') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}"
                                    type="number" id="quantity1" name="quantity[]" onkeypress="return isNumberKey(event)" autocomplete="off" onkeyup="getTotalInSell(1);" value="{{isset($editData)? $editData->quantity : '' }}">

                                    <span class="focus-border"></span>
                                    @if ($errors->has('quantity'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('quantity') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('sub_total') ? ' is-invalid' : '' }}"
                                    type="number" name="total[]" id="total1" onkeypress="return isNumberKey(event)" autocomplete="off" value="{{isset($editData)? $editData->sub_total : '0.00' }}">

                                    <span class="focus-border"></span>
                                    @if ($errors->has('sub_total'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('sub_total') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <input type="hidden" name="totalValue[]" id="totalValue1" autocomplete="off" class="form-control" />
                            </td>
                            <td>
                                <button class="primary-btn icon-only fix-gr-bg" type="button">
                                 <span class="ti-trash" id="removeSubject" onclick="deleteSubject(4)"></span>
                                 </button>
                            </td>
                        </tr>
                        <tfoot>
                            <tr>
                             <th class="border-top-0" colspan="2">@lang('inventory.total')</th>
                             <th class="border-top-0">
                                 <input type="text" class="primary-input form-control" id="subTotalQuantity" onkeypress="return isNumberKey(event)" name="subTotalQuantity" placeholder="0.00" readonly="" />
                                 <input type="hidden" class="form-control" id="subTotalQuantityValue" name="subTotalQuantityValue" />
                             </th>
                             <th class="border-top-0">
                                 <input type="text" class="primary-input form-control" id="subTotal" name="subTotal" placeholder="0.00" readonly="" />
                                 <input type="hidden" class="form-control" id="subTotalValue" name="subTotalValue" />
                             </th>
                             <th class="border-top-0"></th>
                         </tr>
                     </tfoot>

                 </tbody>
             </table>
         </div>
     </div>
 </div>

 <div class="row mt-30">
    <div class="col-lg-12">
        <div class="white-box">
            <div class="row">
              <div class="col-lg-4 mt-30-md">
                 <div class="col-lg-12">
                    <div class="input-effect">
                        <input type="checkbox" id="full_paid"  class="common-checkbox form-control{{ $errors->has('full_paid') ? ' is-invalid' : '' }}" name="full_paid" value="1">
                    <label for="full_paid">@lang('inventory.full_paid')</label>
                    </div>
                </div>
            </div>  

            <div class="col-lg-4 mt-30-md">
             <div class="col-lg-12">
                <div class="input-effect">
                    <input class="primary-input" type="number" step="0.1" value="0" name="totalPaid" id="totalPaid" onkeyup="paidAmount();">
                    <input type="hidden" id="totalPaidValue" name="totalPaidValue">
                    <label>@lang('inventory.total_paid')</label>
                    <span class="focus-border"></span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-30-md">
         <div class="col-lg-12">
            <div class="input-effect">
                <input class="primary-input" type="text" value="0.00" id="totalDue" readonly>
                <input type="hidden" id="totalDueValue" name="totalDueValue">
                <label>@lang('inventory.total_due')</label>
                <span class="focus-border"></span>
            </div>
        </div>
    </div>
<div class="col-lg-12 mt-20 text-center">
   <button class="primary-btn fix-gr-bg">
    <span class="ti-check"></span>
    @lang('inventory.sells')
</button>
</div>
</div>


</div>
</div>
</div>
</div>
</div>
{{ Form::close() }}
</div>
</section>
@endsection