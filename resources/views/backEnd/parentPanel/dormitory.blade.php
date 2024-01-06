@extends('backEnd.master')
@section('title') 
@lang('dormitory.dormitory')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('dormitory.dormitory') </h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="{{route('student_dormitory')}}">@lang('dormitory.dormitory')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row mt-40">
            <div class="col-lg-6 col-md-6">
                <div class="main-title">
                    <h3 class="mb-30">@lang('student.student_information')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 mb-30">
                <!-- Start Student Meta Information -->
                <div class="student-meta-box">
                    <div class="student-meta-top"></div>
                    <img class="student-meta-img img-100" src="{{file_exists($student_detail->student_photo) ? asset($student_detail->student_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="">
                    <div class="white-box radius-t-y-0">
                        <div class="single-meta mt-10">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('student.student_name')
                                </div>
                                <div class="value">
                                    {{$student_detail->first_name.' '.$student_detail->last_name}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('student.admission_no')
                                </div>
                                <div class="value">
                                    {{$student_detail->admission_no}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('student.roll_number')
                                </div>
                                <div class="value">
                                     {{$student_detail->roll_no}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('common.class')
                                </div>
                                <div class="value">
                                    @if($student_detail->class !="" && $student_detail->session !="")
                                   {{$student_detail->class->class_name}} ({{$student_detail->session->session}})
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('common.section')
                                </div>
                                <div class="value">
                                    {{$student_detail->section !=""?$student_detail->section->section_name:""}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('common.gender')
                                </div>
                                <div class="value">
                                    {{$student_detail->gender !=""?$student_detail->gender->base_setup_name:""}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Student Meta Information -->

            </div>
            <div class="col-lg-9">

                <div class="row">
                    <div class="col-lg-12">

                        <table id="default_table2" class="display school-table  " cellspacing="0" width="100%">

                            <thead>
                                <tr>
                                    <th>@lang('dormitory.dormitory')</th>
                                    <th>@lang('dormitory.room_name')</th>
                                    <th>@lang('dormitory.room_type')</th>
                                    <th>@lang('dormitory.no_of_bed')</th>
                                    <th>@lang('common.status')</th>
                                    <th>@lang('dormitory.cost_per_bed')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($room_lists as $values)
                                    @foreach($values as $room_list)
                                    <tr>
                                        <td>{{isset($room_list->dormitory->dormitory_name)? $room_list->dormitory->dormitory_name:''}}</td>
                                        <td>{{$room_list->name}}</td>
                                        <td>{{isset($room_list->roomType->type)? $room_list->roomType->type: ''}}</td>
                                        <td>{{$room_list->number_of_bed}}</td>
                                        <td>
                                            @if($student_detail->room_id == $room_list->id)
                                                <button class="primary-btn small fix-gr-bg">@lang('dormitory.assigned')</button>
                                            @endif

                                        </td>
                                        <td>{{$room_list->cost_per_bed}}</td>
                                    </tr>
                                    @endforeach
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
