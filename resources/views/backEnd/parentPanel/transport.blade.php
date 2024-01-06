@extends('backEnd.master')
@section('title')
{{$student_detail->first_name.' '.$student_detail->last_name}} @lang('transport.transport')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('transport.transport')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="{{route('student_subject')}}">@lang('transport.transport')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
       
        <div class="row">

            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-30">@lang('transport.transport_route_list')</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                        <div class="col-lg-3 mb-40 pb-30">
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
                                                   {{$student_detail->class !=""?$student_detail->class->class_name:""}} ({{$student_detail->session !=""?$student_detail->session->session:""}})
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
                        <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('common.route')</th>
                                    <th>@lang('transport.vehicle')</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routes as $route)
                                <tr>
                                    <td valign="top">{{$route->route->title}}</td>
                                    <td>
                                        <table>
                                            @php
                                              $vehicles = explode(",",$route->vehicle_id);
                                            @endphp
                                            @foreach($vehicles as $vehicle)
                                            <tr>
                                                <td>
                                                    @php $vehicle = App\SmVehicle::find($vehicle);
                                                    @endphp
                                                    {{$vehicle->vehicle_no}}
                                                </td>
                                                <td >
                                                    <div class="col-sm-6">
                                                        
                                                    @if($student_detail->route_list_id == $route->route->id && $student_detail->vechile_id == $vehicle->id)
                                                        <a href="javascript:void(0)" class="primary-btn small fix-gr-bg">@lang('transport.assigned')</a> 
                                                    @endif
                                                    </div>
                                                     
                                                    <div class="col-sm-6">
                                                         
                                                         {{-- <a class="primary-btn small fix-gr-bg modalLink" title="Transport Details" data-modal-size="modal" href="{{route('student_transport_view_modal', [$route->route->id, $vehicle->id])}}">View</a> --}}
                                                         <a class="primary-btn small fix-gr-bg" data-toggle="modal" data-target="#transportView{{$route->route->id}}{{$vehicle->id}}"  href="#">@lang('common.view')</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td> 
                                </tr>
                                <div class="modal fade admin-query" id="transportView{{$route->route->id}}{{$vehicle->id}}" >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">{{$route->route->title}}</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                {{-- <div class="text-center">
                                                    <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                </div> --}}

                                                <div class="mt-40 d-flex justify-content-between">
                                                    <div class="single-meta">
                                                        <div class="row">
                                                            <div class="col-lg-12 no-gutters">
                                                                <div class="main-title">
                                                                    <h3 class="mb-0 text-center">@lang('common.route'): {{$route->route->title}}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="student-meta-box">
                                                                    <div class="single-meta mt-20">
                                                                        <div class="row">
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="value text-left">
                                                                                    @lang('transport.vehicle_no') :
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="name">
                                                                                    {{$vehicle->vehicle_no}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="single-meta">
                                                                        <div class="row">
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="value text-left">
                                                                                    @lang('transport.vehicle_model'):
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="name">
                                                                                    {{$vehicle->vehicle_model}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="single-meta">
                                                                        <div class="row">
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="value text-left">
                                                                                    @lang('transport.made')
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="name">
                                                                                    {{$vehicle->made_year}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if (!empty($vehicle->driver_id))
                                                                        
                                                                    
                                                                    @php
                                                                        $driver_info=App\SmStaff::where('id','=',$vehicle->driver_id)->first();
                                                                    @endphp
                                                                    <div class="single-meta">
                                                                        <div class="row">
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="value text-left">
                                                                                    @lang('transport.driver_name')
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="name">
                                                                                    {{$driver_info->full_name}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="single-meta">
                                                                        <div class="row">
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="value text-left">
                                                                                    @lang('transport.driver_license')    
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="name">
                                                                                    {{@$driver_info->driving_license}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="single-meta">
                                                                        <div class="row">
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="value text-left">
                                                                                    @lang('transport.driver_contact')  
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-6 col-md-6">
                                                                                <div class="name">
                                                                                    {{$driver_info->emergency_mobile}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endif


                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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