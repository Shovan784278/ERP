@extends('backEnd.master')
@section('title')
    @lang('leave.pending_leave_request')
@endsection
@section('mainContent')

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('leave.pending_leave_request')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('common.student')</a>
                <a href="#">@lang('leave.pending_leave_request')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">

        

        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4 no-gutters">
                    <div class="main-title">
                        <h3 class="mb-0">@lang('leave.pending_leave_request_list')</h3>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">

                    <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                        <thead>
                           
                            <tr>
                                <th>@lang('common.name')</th>
                                <th>@lang('common.type')</th>
                                <th>@lang('common.from')</th>
                                <th>@lang('common.to')</th>
                                <th>@lang('leave.apply_date')</th>
                                <th>@lang('common.status')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($apply_leaves as $apply_leave)
                            <tr>
                                <td>{{isset($apply_leave->student)? $apply_leave->student->full_name:''}}</td>
                                <td>
                                    @if($apply_leave->leaveDefine !="" && $apply_leave->leaveDefine->leaveType !="")
                                    {{$apply_leave->leaveDefine->leaveType->type}}
                                    @endif
                                </td>
                                <td  data-sort="{{strtotime($apply_leave->leave_from)}}" >
                                    {{$apply_leave->leave_from != ""? dateConvert($apply_leave->leave_from):''}}

                                </td>
                                <td  data-sort="{{strtotime($apply_leave->leave_to)}}" >
                                   {{$apply_leave->leave_to != ""? dateConvert($apply_leave->leave_to):''}}

                                </td>
                                <td  data-sort="{{strtotime($apply_leave->apply_date)}}" >
                                   {{$apply_leave->apply_date != ""? dateConvert($apply_leave->apply_date):''}}

                                </td>
                                <td>

                                    @if($apply_leave->approve_status == 'P')
                                    <button class="primary-btn small bg-warning text-white border-0 tr-bg">@lang('common.pending')</button>@endif

                                    @if($apply_leave->approve_status == 'A')
                                    <button class="primary-btn small bg-success text-white border-0 tr-bg">@lang('common.approved')</button>
                                    @endif

                                    @if($apply_leave->approve_status == 'C')
                                    <button class="primary-btn small bg-danger text-white border-0">@lang('leave.cancelled')</button>
                                    @endif

                                </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
