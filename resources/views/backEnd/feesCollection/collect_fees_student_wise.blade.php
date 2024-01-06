@extends('backEnd.master')
@section('title') 
@lang('fees.collect_fees')
@endsection
@section('mainContent')
@php  $setting = generalSetting(); if(!empty($setting->currency_symbol)){ $currency = $setting->currency_symbol; }else{ $currency = '$'; } @endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('fees.fees_collection')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('fees.fees_collection')</a>
                <a href="{{route('collect_fees')}}">@lang('fees.collect_fees')</a>
                <a href="{{route('fees_collect_student_wise', [$student->id])}}">@lang('fees.student_wise')</a>
            </div>
        </div>
    </div>
</section>
<section class="student-details mb-40">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-30">@lang('fees.student_fees')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="student-meta-box">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-5 col-md-6">
                                <div class="single-meta mt-20">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="value text-left">
                                                @lang('common.name')
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="name">
                                                {{@$student->studentDetail->full_name}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-meta">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="value text-left">
                                                @lang('student.father_name')
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="name">
                                                {{@$student->studentDetail->parents != ""? @$student->studentDetail->parents->fathers_name:""}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-meta">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="value text-left">
                                                @lang('fees.mobile')
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="name">
                                                {{@$student->studentDetail->mobile}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-meta">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="value text-left">
                                                @lang('student.category')
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="name">
                                                {{@$student->studentDetail->category !=""?@$student->studentDetail->category->category_name:""}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offset-lg-2 col-lg-5 col-md-6">
                                <div class="single-meta mt-20">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="value text-left">
                                               @lang('common.class_sec')
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="name">
                                                @php
                                                    if(@$student->class !="" && @$student->section !="")
                                                    {
                                                        echo $student->class->class_name .'('.$student->section->section_name.')';
                                                    }
                                                @endphp
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-meta">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="value text-left">
                                                @lang('student.admission_no')
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="name">
                                                {{@$student->studentDetail->admission_no}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-meta">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="value text-left">
                                               @lang('student.roll_no')
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="name">
                                                {{@$student->studentDetail->roll_no}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="hidden" id="url" value="{{URL::to('/')}}">
<input type="hidden" id="student_id" value="{{@$student->id}}">
<section class="">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="d-flex justify-content-between">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('fees.add_fees')</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                <table class="display school-table school-table-style" cellspacing="0" width="100%">
                    <thead>
                       
                        <tr>
                            <td class="text-right" colspan="14">
                                <a href="" id="fees_groups_invoice_print_button" class="primary-btn medium fix-gr-bg" target="">
                                    <i class="ti-printer pr-2"></i>
                                    @lang('fees.invoice_print')
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>@lang('fees.fees')</th>
                            <th>@lang('fees.due_date')</th>
                            <th>@lang('common.status')</th>
                            <th>@lang('fees.amount') ({{generalSetting()->currency_symbol}})</th>
                            <th>@lang('fees.payment_id')</th>
                            <th>@lang('fees.mode')</th>
                            <th>@lang('common.date')</th>
                            <th>@lang('fees.discount') ({{generalSetting()->currency_symbol}})</th>
                            <th>@lang('fees.fine') ({{generalSetting()->currency_symbol}})</th>
                            <th>@lang('fees.paid') ({{generalSetting()->currency_symbol}})</th>
                            <th>@lang('fees.balance')</th>
                            <th>@lang('common.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grand_total = 0;
                            $total_fine = 0;
                            $total_discount = 0;
                            $total_paid = 0;
                            $total_grand_paid = 0;
                            $total_balance = 0;
                        @endphp
                        @foreach($fees_assigneds as $fees_assigned)
                        @php
                            $grand_total += $fees_assigned->feesGroupMaster->amount;
                            $discount_amount = $fees_assigned->applied_discount;
                            $total_discount += $discount_amount;
                            $student_id = $fees_assigned->student_id;
                            $paid = App\SmFeesAssign::discountSum($fees_assigned->student_id, $fees_assigned->feesGroupMaster->feesTypes->id, 'amount' ,$fees_assigned->record_id);
                            $total_grand_paid += $paid;
                            $fine = App\SmFeesAssign::discountSum($fees_assigned->student_id, $fees_assigned->feesGroupMaster->feesTypes->id, 'fine', $fees_assigned->record_id);
                            $total_fine += $fine;
                            $total_paid = $discount_amount + $paid;
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox" id="fees_group.{{$fees_assigned->id}}" class="common-checkbox fees-groups-print" name="fees_group[]" value="{{$fees_assigned->id}}">
                                <label for="fees_group.{{$fees_assigned->id}}"></label>
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            </td>
                            <td>
                                {{@$fees_assigned->feesGroupMaster->feesGroups->name}} / {{@$fees_assigned->feesGroupMaster->feesTypes->name}}
                            </td>
                            <td>
                                @if($fees_assigned->feesGroupMaster !="")
                                    {{$fees_assigned->feesGroupMaster->date != ""? dateConvert($fees_assigned->feesGroupMaster->date):''}}
                                @endif
                            </td>
                            <td>
                                @php
                                    $rest_amount = $fees_assigned->feesGroupMaster->amount - $total_paid;
                                    $total_balance +=  $rest_amount;
                                    $balance_amount = number_format($rest_amount+$fine, 2, '.', '');
                                @endphp
                                @if($balance_amount == 0)
                                    <button class="primary-btn small bg-success text-white border-0">@lang('fees.paid')</button>
                                @elseif($paid != 0)
                                    <button class="primary-btn small bg-warning text-white border-0">@lang('fees.partial')</button>
                                @elseif($paid == 0)
                                    <button class="primary-btn small bg-danger text-white border-0">@lang('fees.unpaid')</button>
                                @endif
                            </td>
                            <td>{{$fees_assigned->feesGroupMaster->amount}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{$discount_amount}}</td>
                            <td>{{$fine}}</td>
                            <td>{{$paid}}</td>
                            <td> 
                                @php
                                    $rest_amount = $fees_assigned->fees_amount;
                                    $total_balance +=  $rest_amount;
                                    echo $balance_amount;
                                @endphp
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                        @lang('common.select')
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @if(userPermission(111))
                                            @if($balance_amount != 0) 
                                                <a class="dropdown-item modalLink" data-modal-size="modal-lg" 
                                                title="{{@$fees_assigned->feesGroupMaster->feesGroups->name.': '. $fees_assigned->feesGroupMaster->feesTypes->name}}"  
                                                href="{{route('fees-generate-modal', [$balance_amount, $fees_assigned->student_id, $fees_assigned->feesGroupMaster->fees_type_id,$fees_assigned->fees_master_id,$fees_assigned->id,$fees_assigned->record_id])}}" >@lang('fees.add_fees') </a>
                                            @else
                                                <a class="dropdown-item"  target="_blank">Payment Done</a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                            @php
                                $payments = App\SmFeesAssign::feesPayment($fees_assigned->feesGroupMaster->feesTypes->id, $fees_assigned->student_id, $fees_assigned->record_id);
                                $i = 0;
                            @endphp
                            @foreach($payments as $payment)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">
                                    <img src="{{asset('public/backEnd/img/table-arrow.png')}}">
                                </td>
                                <td>
                                    @php
                                        $created_by = App\User::find($payment->created_by);
                                    @endphp
                                    @if($created_by != "")
                                        <a href="#" data-toggle="tooltip" data-placement="right" title="{{'Collected By: '.$created_by->full_name}}">{{$payment->fees_type_id.'/'.$payment->id}}</a>
                                </td>
                                    @endif
                                <td>{{$payment->payment_mode}}</td>
                                <td class="nowrap">{{$payment->payment_date != ""? dateConvert($payment->payment_date):''}}</td>
                                <td class="text-center">{{$payment->discount_amount}}</td>
                                <td>
                                    {{$payment->fine}}
                                    @if($payment->fine!=0)
                                        @if (strlen($payment->fine_title) > 14)
                                            <spna class="text-danger nowrap" title="{{$payment->fine_title}}">
                                                ({{substr($payment->fine_title, 0, 15) . '...'}})
                                            </spna>
                                        @else
                                            @if ($payment->fine_title=='')
                                                {{$payment->fine_title}}
                                            @else
                                                <spna class="text-danger nowrap">
                                                    ({{$payment->fine_title}})
                                                </spna>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{$payment->amount}}
                                </td>
                                <td></td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>@lang('fees.grand_total') ({{generalSetting()->currency_symbol}})</th>
                            <th></th>
                            <th>{{ number_format($grand_total, 2, '.', '') }}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{ number_format($total_discount, 2, '.', '') }}</th>
                            <th>{{ number_format($total_fine, 2, '.', '') }}</th>
                            <th>{{ number_format($total_grand_paid, 2, '.', '') }}</th>
                                @php
                                    $show_balance=$grand_total+$total_fine-$total_discount;
                                @endphp
                            <th>{{ number_format($show_balance-$total_grand_paid, 2, '.', '') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
            </div>
        </div>
    </div>
</section>
@endsection