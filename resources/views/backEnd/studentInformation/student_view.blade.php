@extends('backEnd.master')
@section('title') 
@lang('student.student_details')
@endsection
@section('mainContent')

    @php
        function showTimelineDocName($data){
            $name = explode('/', $data);
            $number = count($name);
            return $name[$number-1];
        }
        function showDocumentName($data){
            $name = explode('/', $data);
            $number = count($name);
            return $name[$number-1];
        }
    @endphp
@php  $setting = app('school_info');  if(!empty($setting->currency_symbol)){ $currency = $setting->currency_symbol; }else{ $currency = '$'; }   @endphp

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.student_details')</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="{{route('student_list')}}">@lang('student.student_list')</a>
                    <a href="#">@lang('student.student_details')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="student-details">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-3">
                    <!-- Start Student Meta Information -->
                    <div class="main-title">
                        <h3 class="mb-20">@lang('student.student_details')</h3>
                    </div>
                    <div class="student-meta-box">
                        <div class="student-meta-top"></div>
                            <img class="student-meta-img img-100" src="{{ file_exists(@$student_detail->student_photo) ? asset($student_detail->student_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="">

                        <div class="white-box radius-t-y-0">
                            <div class="single-meta mt-10">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('student.student_name')
                                    </div>
                                    <div class="value">
                                        {{@$student_detail->first_name.' '.@$student_detail->last_name}}
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('student.admission_number')
                                    </div>
                                    <div class="value">
                                        {{@$student_detail->admission_no}}
                                    </div>
                                </div>
                            </div>
                            @if($setting->multiple_roll ==0)
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('student.roll_number')
                                    </div>
                                    <div class="value">
                                        {{@$student_detail->roll_no}}
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('student.class')
                                    </div>
                                    <div class="value">
                                        @if($student_detail->defaultClass!="")
                                            {{@$student_detail->defaultClass->class->class_name}}
                                            {{-- ({{@$academic_year->year}}) --}}
                                        @elseif ($student_detail->studentRecord !="")  
                                        {{@$student_detail->studentRecord->class->class_name}}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('student.section')
                                    </div>
                                    <div class="value">
                                        
                                        @if($student_detail->defaultClass!="")
                                        {{@$student_detail->defaultClass->section->section_name}}
                                       
                                        @elseif ($student_detail->studentRecord !="")  
                                        {{@$student_detail->studentRecord->section->section_name}}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('common.gender')
                                    </div>
                                    <div class="value">

                                        {{@$student_detail->gender !=""?$student_detail->gender->base_setup_name:""}}
                                    </div>
                                </div>
                            </div>

                            

                        </div>
                    </div>
                    <!-- End Student Meta Information -->
                    @if(count($siblings) >0 )
                        <!-- Start Siblings Meta Information -->
                        <div class="main-title mt-40">
                            <h3 class="mb-20">@lang('student.sibling_information') </h3>
                        </div>
                        @foreach($siblings as $sibling)

                                <div class="student-meta-box mb-20">
                                    <div class="student-meta-top siblings-meta-top"></div>
                                    <img class="student-meta-img img-100" src="{{asset(@$sibling->student_photo)}}" alt="">
                                    <div class="white-box radius-t-y-0">
                                        <div class="single-meta mt-10">
                                            <div class="d-flex justify-content-between">
                                                <div class="name">
                                                    @lang('student.sibling_name')
                                                </div>
                                                 <div class="value">
                                                    {{isset($sibling->full_name)?$sibling->full_name:''}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="single-meta">
                                            <div class="d-flex justify-content-between">
                                                <div class="name">
                                                    @lang('student.admission_number')
                                                </div>
                                                <div class="value">
                                                    {{@$sibling->admission_no}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="single-meta">
                                            <div class="d-flex justify-content-between">
                                                <div class="name">
                                                    @lang('student.roll_number')
                                                </div>
                                                <div class="value">
                                                    {{@$sibling->roll_no}}
                                                </div>
                                            </div>
                                        </div>


                                        <div class="single-meta">
                                            <div class="d-flex justify-content-between">
                                                <div class="name">
                                                    @lang('student.roll_number')
                                                </div>
                                                <div class="value">
                                                    {{@$sibling->roll_no}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-meta">
                                            <div class="d-flex justify-content-between">
                                                <div class="name">
                                                    @lang('student.class')
                                                </div>
                                                <div class="value">
                                                    {{@$sibling->class->class_name}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="single-meta">
                                            <div class="d-flex justify-content-between">
                                                <div class="name">
                                                    @lang('student.section')
                                                </div>
                                                <div class="value">
                                                    {{$sibling->section !=""?$sibling->section->section_name:""}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="single-meta">
                                            <div class="d-flex justify-content-between">
                                                <div class="name">
                                                    @lang('student.gender')
                                                </div>
                                                <div class="value">
                                                    {{$sibling->gender!=""? $sibling->gender->base_setup_name:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                @endforeach
                <!-- End Siblings Meta Information -->

                @endif
                </div>

                <!-- Start Student Details -->
                <div class="col-lg-9 student-details up_admin_visitor">
                    <ul class="nav nav-tabs tabs_scroll_nav" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link  @if (Session::get('studentDocuments') != 'active' && Session::get('studentRecord') != 'active' && Session::get('studentTimeline') != 'active') active @endif" href="#studentProfile" role="tab" data-toggle="tab">@lang('student.profile')</a>
                        </li>
                        @if(generalSetting()->fees_status == 0)
                            <li class="nav-item">
                                <a class="nav-link" href="#studentFees" role="tab" data-toggle="tab">@lang('fees.fees')</a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link" href="#leaves" role="tab" data-toggle="tab">@lang('leave.leave')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#studentExam" role="tab" data-toggle="tab">@lang('exam.exam')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{Session::get('studentDocuments') == 'active'? 'active':''}}" href="#studentDocuments" role="tab" data-toggle="tab">@lang('student.document')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{Session::get('studentRecord') == 'active'? 'active':''}} " href="#studentRecord" role="tab" data-toggle="tab">@lang('student.student_record')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{Session::get('studentTimeline') == 'active'? 'active':''}} " href="#studentTimeline" role="tab" data-toggle="tab">@lang('student.timeline')</a>
                        </li>
                        
                        <li class="nav-item edit-button">
                            @if(userPermission(66))
                                <a href="{{route('student_edit', [@$student_detail->id])}}"
                                class="primary-btn small fix-gr-bg">@lang('common.edit')
                                </a>
                            @endif
                        </li>
                    </ul>


                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!-- Start Profile Tab -->
                        <div role="tabpanel" class="tab-pane fade  @if (Session::get('studentDocuments') != 'active' && Session::get('studentRecord') != 'active' && Session::get('studentTimeline') != 'active') show active @endif" id="studentProfile">
                            <div class="white-box">
                                <h4 class="stu-sub-head">@lang('student.personal_info')</h4>
                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.admission_date')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{ !empty($student_detail->admission_date)? dateConvert($student_detail->admission_date):''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.date_of_birth')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{ !empty($student_detail->date_of_birth)? dateConvert($student_detail->date_of_birth):''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.age')
                                            </div>
                                        </div>

                                      

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{ \Carbon\Carbon::parse($student_detail->date_of_birth)->diff(\Carbon\Carbon::now())->format('%y years')}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.category')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{$student_detail->category != ""? $student_detail->category->category_name:""}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.group')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{$student_detail->group ? $student_detail->group->group:""}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.religion')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{$student_detail->religion != ""? $student_detail->religion->base_setup_name:""}}
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student_uid')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->student_uid)? @$student_detail->student_uid: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('psc_roll')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->psc_roll)? @$student_detail->psc_roll: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('psc_passing_year')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->psc_passing_year)? @$student_detail->psc_passing_year: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('psc_result')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->psc_result)? @$student_detail->psc_result: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('psc_institute')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->psc_institute)? @$student_detail->psc_institute: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('jsc_roll')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->jsc_roll)? @$student_detail->jsc_roll: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('jsc_passing_year')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->jsc_passing_year)? @$student_detail->jsc_passing_year: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('jsc_result')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->jsc_result)? @$student_detail->jsc_result: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('jsc_institute')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->jsc_institute)? @$student_detail->jsc_institute: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- {{-- Custom field start --}}
                                    @include('backEnd.customField._coutom_field_show')
                                    <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.name')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{$student_detail->name != ""? $student_detail->name->base_setup_name:""}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Custom field end --}} -->

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.phone_number')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                @if($student_detail->mobile)
                                                    <a href="tel:{{@$student_detail->mobile}}"> {{@$student_detail->mobile}}</a>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                


                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('common.email_address')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                @if($student_detail->email)
                                                <a href="mailto:{{@$student_detail->email}}"> {{@$student_detail->email}}</a>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- changes for lead module --abunayem--}}
                                @if(moduleStatusCheck('Lead')==true)
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('lead::lead.city')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->leadCity->city_name}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                               
                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('lead::lead.source')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{@$student_detail->source->source_name}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- end --}}
                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.present_address')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{@$student_detail->current_address}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('student.permanent_address')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{@$student_detail->permanent_address}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Start Parent Part -->
                                <h4 class="stu-sub-head mt-40">@lang('student.Parent_Guardian_Details')</h4>
                                <div class="d-flex">
                                    <div class="mr-20 mt-20">
                                            <img class="student-meta-img img-100" src="{{ file_exists(@$student_detail->parents->fathers_photo) ? asset($student_detail->parents->fathers_photo) : asset('public/uploads/staff/demo/father.png') }}" alt="">

                                    </div>
                                    <div class="w-100">
                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.father_name')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{@$student_detail->parents->fathers_name}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.occupation')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{@$student_detail->parents!=""?@$student_detail->parents->fathers_occupation:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('nid')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{@$student_detail->parents!=""?@$student_detail->parents->fathers_nid:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="">
                                                @lang('date of birth')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-7">
                                            <div class="">
                                                {{ !empty($student_detail->parents->fathers_date_of_birth)? dateConvert($student_detail->parents->fathers_date_of_birth):''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.phone_number')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{@$student_detail->parents !=""?@$student_detail->parents->fathers_mobile:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex">
                                    <div class="mr-20 mt-20">
                                             <img class="student-meta-img img-100" src="{{ file_exists(@$student_detail->parents->mothers_photo) ? asset($student_detail->parents->mothers_photo) : asset('public/uploads/staff/demo/mother.jpg')}}" alt="">
                                    </div>
                                    <div class="w-100">
                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.mother_name')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->mothers_name:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.mothers_nid')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->mothers_nid:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.phone_number')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->mothers_mobile:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex">
                                    <div class="mr-20 mt-20">
                                        <img class="student-meta-img img-100" src="{{ file_exists(@$student_detail->parents->guardians_photo) ? asset($student_detail->parents->guardians_photo) : asset('public/uploads/staff/demo/guardian.jpg')}}" alt="">

                                    </div>
                                    <div class="w-100">
                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.guardian_name')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->guardians_name:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.email_address')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->guardians_email:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.phone_number')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->guardians_mobile:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.relation_with_guardian')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->guardians_relation:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.occupation')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->guardians_occupation:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.guardian_address')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->guardians_address:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="">
                                                        @lang('student.guardians_nid')
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-7">
                                                    <div class="">
                                                        {{$student_detail->parents !=""?@$student_detail->parents->guardians_nid:""}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <!-- End Parent Part -->

                                <!-- Start Transport Part -->
                                <h4 class="stu-sub-head mt-40">@lang('student.transport_and_dormitory_info')</h4>


                                @if (!empty($student_detail->route_list_id))

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.route')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->route_list_id)? @$student_detail->route->title: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endif

                                @if (isset($student_detail->vehicle))
                                    @if (!empty($vehicle_no))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-5">
                                                <div class="">
                                                    @lang('student.vehicle_number')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-6">
                                                <div class="">
                                                    {{$student_detail->vehicle != ""? @$student_detail->vehicle->vehicle_no: ''}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    @endif


                                @endif


                                    @if (isset($driver_info))
                                        @if (!empty($driver_info->full_name))
                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-5 col-md-5">
                                                    <div class="">
                                                        @lang('student.driver_name')
                                                    </div>
                                                </div>

                                                <div class="col-lg-7 col-md-6">
                                                    <div class="">
                                                        {{$student_detail->vechile_id != ""? @$driver_info->full_name:''}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                    @endif

                                    @if (isset($driver_info))
                                        @if (!empty($driver_info->mobile))
                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-5 col-md-5">
                                                    <div class="">
                                                        @lang('student.driver_phone_number')
                                                    </div>
                                                </div>

                                                <div class="col-lg-7 col-md-6">
                                                    <div class="">
                                                        {{$student_detail->vechile_id != ""? @$driver_info->mobile:''}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif


                                    @if (isset($student_detail->dormitory))
                                        @if (!empty($student_detail->dormitory->dormitory_name))
                                        <div class="single-info">
                                            <div class="row">
                                                <div class="col-lg-5 col-md-5">
                                                    <div class="">
                                                        @lang('student.dormitory_name')
                                                    </div>
                                                </div>

                                                <div class="col-lg-7 col-md-6">
                                                    <div class="">
                                                        {{isset($student_detail->dormitory_id)?@$student_detail->dormitory->dormitory_name: ''}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                <!-- End Transport Part -->

                                <!-- Start Other Information Part -->
                                <h4 class="stu-sub-head mt-40">@lang('student.other_information')</h4>
                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('common.blood_group')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->bloodgroup_id)? @$student_detail->bloodGroup->base_setup_name: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.student_group')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->student_group_id)? @$student_detail->group->group: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.height')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->height)? @$student_detail->height: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.weight')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->weight)? @$student_detail->weight: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.previous_school_details')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->previous_school_details)? @$student_detail->previous_school_details: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.national_id_number')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->national_id_no)? @$student_detail->national_id_no: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.local_id_number')
                                            </div>
                                        </div>


                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->local_id_no)? @$student_detail->local_id_no: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('accounts.bank_account_number')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->bank_account_no)? @$student_detail->bank_account_no: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.bank_name')
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->bank_name)? @$student_detail->bank_name: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="single-info">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5">
                                            <div class="">
                                                @lang('student.ifsc_code')
                                            </div>
                                        </div>

                                        <div class="col-lg-7 col-md-6">
                                            <div class="">
                                                {{isset($student_detail->ifsc_code)? @$student_detail->ifsc_code: ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Other Information Part -->
                                
                                {{-- Custom field start --}}
                                    @include('backEnd.customField._coutom_field_show')
                                {{-- Custom field end --}}

                            </div>
                        </div>
                        <!-- End Profile Tab -->

                        <!-- Start Fees Tab -->
                        <div role="tabpanel" class="tab-pane fade" id="studentFees">
                            <div class="table-responsive">
                                @foreach($records as $record)
                                    <div class="white-box no-search no-paginate no-table-info mb-2">
                                        <div class="main-title">
                                            <h3 class="mb-10">{{$record->class->class_name}} ({{$record->section->section_name}})</h3>
                                        </div>
                                        <table class="display school-table school-table-style res_scrol" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>@lang('fees.fees_group')</th>
                                                    <th>@lang('fees.fees_code')</th>
                                                    <th>@lang('fees.due_date')</th>
                                                    <th>@lang('fees.Status')</th>
                                                    <th>@lang('fees.amount') ({{@$currency}})</th>
                                                    <th>@lang('fees.payment_ID')</th>
                                                    <th>@lang('fees.mode')</th>
                                                    <th>@lang('common.date')</th>
                                                    <th>@lang('fees.discount') ({{@$currency}})</th>
                                                    <th>@lang('fees.fine') ({{@$currency}})</th>
                                                    <th>@lang('fees.paid') ({{@$currency}})</th>
                                                    <th>@lang('fees.balance') ({{@$currency}})</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    @$grand_total = 0;
                                                    @$total_fine = 0;
                                                    @$total_discount = 0;
                                                    @$total_paid = 0;
                                                    @$total_grand_paid = 0;
                                                    @$total_balance = 0;
                                                @endphp
                                                @foreach($student_detail->feesAssign as $fees_assigned)
                                                    @if($fees_assigned->record_id == $record->id)
                                                        @php
                                                            @$grand_total += @$fees_assigned->feesGroupMaster->amount;
                                                        @endphp
                                                        @php
                                                            @$discount_amount = $fees_assigned->applied_discount;
                                                            @$total_discount += @$discount_amount;
                                                            @$student_id = @$fees_assigned->student_id;
                                                        @endphp
                                                        @php
                                                            @$paid = App\SmFeesAssign::discountSum(@$fees_assigned->student_id, @$fees_assigned->feesGroupMaster->feesTypes->id, 'amount', $fees_assigned->record_id);
                                                            @$total_grand_paid += @$paid;
                                                        @endphp
                                                        @php
                                                            @$fine = App\SmFeesAssign::discountSum(@$fees_assigned->student_id, @$fees_assigned->feesGroupMaster->feesTypes->id, 'fine', $fees_assigned->record_id);
                                                            @$total_fine += @$fine;
                                                        @endphp

                                                        @php
                                                            @$total_paid = @$discount_amount + @$paid;
                                                        @endphp
                                                        <tr>
                                                            <td>{{@$fees_assigned->feesGroupMaster->feesGroups !=""?@$fees_assigned->feesGroupMaster->feesGroups->name:""}}</td>
                                                            <td>{{@$fees_assigned->feesGroupMaster->feesTypes!=""?@$fees_assigned->feesGroupMaster->feesTypes->name:""}}</td>
                                                            <td>
                                                                @if(!empty(@$fees_assigned->feesGroupMaster))
                                                                    {{@$fees_assigned->feesGroupMaster->date != ""? dateConvert(@$fees_assigned->feesGroupMaster->date):''}}
                                                                @endif
                                                            </td>
                                                            @php
                                                                $total_payable_amount=$fees_assigned->fees_amount;
                                                                $rest_amount = $fees_assigned->feesGroupMaster->amount - $total_paid;
                                                                $balance_amount=number_format($rest_amount+$fine, 2, '.', '');
                                                                $total_balance +=  $balance_amount;
                                                            @endphp
                                                            <td>
                                                                @if($balance_amount ==0)
                                                                    <button class="primary-btn small bg-success text-white border-0">@lang('fees.paid')</button>
                                                                @elseif($paid != 0)
                                                                    <button class="primary-btn small bg-warning text-white border-0">@lang('fees.partial')</button>
                                                                @elseif($paid == 0)
                                                                    <button class="primary-btn small bg-danger text-white border-0">@lang('fees.unpaid')</button>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @php
                                                                    echo number_format($fees_assigned->feesGroupMaster->amount, 2, '.', '');
                                                                @endphp
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td> {{@$discount_amount}} </td>
                                                            <td>{{@$fine}}</td>
                                                            <td>{{@$paid}}</td>
                                                            <td>
                                                                @php echo @$balance_amount; @endphp
                                                            </td>
                                                        </tr>
                                                        @php
                                                            @$payments = App\SmFeesAssign::feesPayment(@$fees_assigned->feesGroupMaster->feesTypes->id, @$fees_assigned->student_id, $fees_assigned->record_id);
                                                            $i = 0;
                                                        @endphp
                                                        @foreach($payments as $payment)
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-right"><img src="{{asset('public/backEnd/img/table-arrow.png')}}"></td>
                                                                <td>
                                                                    @php
                                                                        @$created_by = App\User::find(@$payment->created_by);
                                                                    @endphp
                                                                    @if(@$created_by != "")
                                                                    <a href="#" data-toggle="tooltip" data-placement="right" title="{{'Collected By: '.@$created_by->full_name}}">{{@$payment->fees_type_id.'/'.@$payment->id}}</a></td>
                                                                    @endif
                                                                <td>{{$payment->payment_mode}}</td>
                                                                <td class="nowrap">{{@$payment->payment_date != ""? dateConvert(@$payment->payment_date):''}}</td>
                                                                <td>{{@$payment->discount_amount}}</td>
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
                                                                <td>{{@$payment->amount}}</td>
                                                                <td></td>
                                                            </tr>
                                                        @endforeach
                                                        @endif
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>@lang('fees.grand_total') ({{@$currency}})</th>
                                                    <th></th>
                                                    <th>{{@$grand_total}}</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>{{@$total_discount}}</th>
                                                    <th>{{@$total_fine}}</th>
                                                    <th>{{@$total_grand_paid}}</th>
                                                    <th>{{number_format($total_balance, 2, '.', '')}}</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- End Profile Tab -->
                        <!-- Start leave Tab -->
                        <div role="tabpanel" class="tab-pane fade" id="leaves">
                            <div class="white-box">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                        <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="nowrap">@lang('leave.leave_type')</th>
                                                    <th class="nowrap">@lang('leave.leave_from') </th>
                                                    <th class="nowrap">@lang('leave.leave_to')</th>
                                                    <th class="nowrap">@lang('leave.apply_date')</th>
                                                    <th class="nowrap">@lang('common.status')</th>
                                                    <th class="nowrap">@lang('common.action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @php $diff = ''; @endphp
                                            @isset($student_detail)
                                            @if(count($student_detail->studentLeave)>0)
                                            @foreach($student_detail->studentLeave as $value)
                                                <tr>
                                                    <td class="nowrap">{{@$value->leaveType->type}}</td>
                                                    <td class="nowrap">{{$value->leave_from != ""? dateConvert($value->leave_from):''}}</td>
                                                    <td class="nowrap">{{$value->leave_to != ""? dateConvert($value->leave_to):''}}</td>
                                                    <td class="nowrap">{{$value->apply_date != ""? dateConvert($value->apply_date):''}}</td>
                                                    <td class="nowrap">
                                                        @if($value->approve_status == 'P')
                                                        <button class="primary-btn small bg-warning text-white border-0"> @lang('student.pending')</button>
                                                        @endif

                                                        @if($value->approve_status == 'A')
                                                        <button class="primary-btn small bg-success text-white border-0"> @lang('student.approved')</button>
                                                        @endif

                                                        @if($value->approve_status == 'C')
                                                        <button class="primary-btn small bg-danger text-white border-0"> @lang('common.cancelled')</button>
                                                        @endif
                                                    </td>
                                                    <td class="nowrap">
                                                        <a class="modalLink" data-modal-size="modal-md" title="@lang('student.view') @lang('student.leave') @lang('student.details')" href="{{url('view-leave-details-apply', $value->id)}}"><button class="primary-btn small tr-bg"> @lang('student.view') </button></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>@lang('student.not_leaves_data')</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                        @endisset
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- End leave Tab -->

                        <!-- Start Exam Tab -->
                        <div role="tabpanel" class="tab-pane fade" id="studentExam">
                            @php
                                $exam_count= count($exam_terms);
                            @endphp
                            @if($exam_count > 1)
                                <div class="white-box no-search no-paginate no-table-info mb-2">
                                <table class="display school-table school-table-style shadow-none pb-0" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>
                                                    @lang('student.subject')
                                                </th>
                                                <th>
                                                    @lang('student.full_marks')
                                                </th>
                                                <th>
                                                    @lang('student.passing_marks')
                                                </th>
                                                <th>
                                                    @lang('student.obtained_marks')
                                                </th>
                                                <th>
                                                    @lang('student.results')
                                                </th>
                                            </tr>
                                        </thead>
                                </table>
                                </div>
                            @endif
                            <div class="white-box no-search no-paginate no-table-info mb-2">
                                @foreach($student_detail->studentRecords as $record)
                                    @foreach($exam_terms as $exam)
                                        @php
                                            $get_results = App\SmStudent::getExamResult(@$exam->id, @$record);
                                        @endphp
                                        @if($get_results)
                                            <div class="main-title">
                                                <h3 class="mb-0">{{@$exam->title}}</h3>
                                            </div>
                                            @php
                                                $grand_total = 0;
                                                $grand_total_marks = 0;
                                                $result = 0;
                                                $temp_grade=[];
                                                $total_gpa_point = 0;
                                                $total_subject = count($get_results);
                                                $optional_subject = 0;
                                                $optional_gpa = 0;
                                                $onlyOptional = 0;
                                            @endphp
                                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('student.date')</th>
                                                        <th>@lang('exam.subject_full_marks')</th>
                                                        <th>@lang('exam.obtained_marks')</th>
                                                        <th>@lang('exam.grade')</th>
                                                        <th>@lang('exam.gpa')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($get_results as $mark)
                                                        @php
                                                            if((!is_null($record->optionalSubjectSetup)) && (!is_null($record->optionalSubject))){
                                                                if($mark->subject_id != @$record->optionalSubject->subject_id){
                                                                    $temp_grade[]=$mark->total_gpa_grade;
                                                                }
                                                            }else{
                                                                $temp_grade[]=$mark->total_gpa_grade;
                                                            }
                                                            $total_gpa_point += $mark->total_gpa_point;
                                                            if(! is_null(@$record->optionalSubject)){
                                                                if(@$record->optionalSubject->subject_id == $mark->subject->id){
                                                                    $total_gpa_point = $total_gpa_point - $mark->total_gpa_point;
                                                                    $onlyOptional = $mark->total_gpa_point;
                                                                }
                                                            }
                                                            $temp_gpa[]=$mark->total_gpa_point;
                                                            $get_subject_marks =  subjectFullMark ($mark->exam_type_id, $mark->subject_id );

                                                            $subject_marks = App\SmStudent::fullMarksBySubject($exam->id, $mark->subject_id);
                                                            $schedule_by_subject = App\SmStudent::scheduleBySubject($exam->id, $mark->subject_id, @$record);
                                                            $result_subject = 0;
                                                            $grand_total_marks += $get_subject_marks;
                                                            if(@$mark->is_absent == 0){
                                                                $grand_total += @$mark->total_marks;
                                                                if($mark->marks < $subject_marks->pass_mark){
                                                                    $result_subject++;
                                                                    $result++;
                                                                }
                                                            }else{
                                                                $result_subject++;
                                                                $result++;
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                {{ !empty($schedule_by_subject->date)? dateConvert($schedule_by_subject->date):''}}
                                                            </td>
                                                            <td>
                                                                {{@$mark->subject->subject_name}} ({{ @subjectFullMark($mark->exam_type_id, $mark->subject_id )}})
                                                            </td>
                                                            <td>
                                                                {{@$mark->total_marks}}
                                                            </td>
                                                            <td>
                                                                {{@$mark->total_gpa_grade}}
                                                            </td>
                                                            <td>
                                                                {{number_format(@$mark->total_gpa_point, 2, '.', '')}}
                                                                @php
                                                                    if (@$record->optionalSubject->subject_id!='') {
                                                                        if (@$record->optionalSubject->subject_id == $mark->subject->id) {
                                                                            $optional_subject = 1;
                                                                            if ($mark->total_gpa_point > @$record->optionalSubjectSetup->gpa_above) {
                                                                                $optional_gpa = @$record->optionalSubjectSetup->gpa_above;
                                                                                echo "GPA Above ".@$record->optionalSubjectSetup->gpa_above;
                                                                                echo "<hr>";
                                                                                echo number_format($mark->total_gpa_point  - @$record->optionalSubjectSetup->gpa_above, 2, '.', '');
                                                                            } else {
                                                                                echo "GPA Above".@$record->optionalSubjectSetup->gpa_above;
                                                                                echo "<hr>";
                                                                                echo "0";
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>
                                                            @lang('exam.grand_total'): {{$grand_total}}/{{$grand_total_marks}}
                                                        </th>
                                                        <th>@lang('exam.grade'):
                                                            @php
                                                                if(in_array($fail_gpa_name->grade_name,$temp_grade)){
                                                                    echo $fail_gpa_name->grade_name;
                                                                }else {
                                                                    $final_gpa_point = ($total_gpa_point + @$onlyOptional - @$record->optionalSubjectSetup->gpa_above)/  ($total_subject - $optional_subject);
                                                                    $average_grade=0;
                                                                    $average_grade_max=0;
                                                                    if($result == 0 && $grand_total_marks != 0){
                                                                        $gpa_point=number_format($final_gpa_point, 2, '.', '');
                                                                        if($gpa_point >= $max_gpa){
                                                                            $average_grade_max = App\SmMarksGrade::where('school_id',Auth::user()->school_id)
                                                                            ->where('academic_id', getAcademicId() )
                                                                            ->where('from', '<=', $max_gpa)
                                                                            ->where('up', '>=', $max_gpa)
                                                                            ->first('grade_name');

                                                                            echo  @$average_grade_max->grade_name;
                                                                        } else {
                                                                            $average_grade = App\SmMarksGrade::where('school_id',Auth::user()->school_id)
                                                                            ->where('academic_id', getAcademicId())
                                                                            ->where('from', '<=', $final_gpa_point)
                                                                            ->where('up', '>=', $final_gpa_point )
                                                                            ->first('grade_name');
                                                                            echo  @$average_grade->grade_name;
                                                                        }
                                                                    }else{
                                                                        echo $fail_gpa_name->grade_name;
                                                                    }
                                                                }
                                                            @endphp
                                                        </th>
                                                        <th>
                                                            @if(@$record->optionalSubject->subject_id!='')
                                                                @lang('reports.without_optional')
                                                                @php
                                                                    $withoutOptionalSubject = $total_subject - $optional_subject;
                                                                    $final_gpa_point = ($total_gpa_point - $optional_gpa);
                                                                    $totalAdd = $total_gpa_point / $withoutOptionalSubject;
                                                                    $float_final_gpa_point_add=number_format($totalAdd,2);
                                                                    if($float_final_gpa_point_add >= $max_gpa){
                                                                        echo $max_gpa;
                                                                    }else {
                                                                        echo $float_final_gpa_point_add;
                                                                    }
                                                                @endphp
                                                                <br>
                                                            @endif
                                                            
                                                            @lang('exam.gpa')
                                                            @php
                                                                $final_gpa_point = 0;
                                                                $final_gpa_point = ($total_gpa_point + @$onlyOptional - @$record->optionalSubjectSetup->gpa_above)/  ($total_subject - $optional_subject);
                                                                $float_final_gpa_point=number_format($final_gpa_point,2);
                                                                if($float_final_gpa_point >= $max_gpa){
                                                                    echo number_format($max_gpa,2);
                                                                }else {
                                                                    echo number_format($float_final_gpa_point,2);
                                                                }
                                                            @endphp
                                                        </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        @endif
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <!-- End Exam Tab -->

                        <!-- Start Documents Tab -->
                        <div role="tabpanel" class="tab-pane fade {{Session::get('studentDocuments') == 'active'? 'show active':''}}" id="studentDocuments">
                            <div class="white-box">
                                <div class="text-right mb-20">
                                    <button type="button" data-toggle="modal" data-target="#add_document_madal"
                                            class="primary-btn tr-bg text-uppercase bord-rad">
                                        @lang('student.upload_document')
                                        <span class="pl ti-upload"></span>
                                    </button>
                                </div>
                                <table id="" class="table simple-table table-responsive school-table"
                                       cellspacing="0">
                                    <thead class="d-block">
                                    <tr class="d-flex">
                                        <th class="col-2">@lang('student.title')</th>
                                        <th class="col-6">@lang('student.name')</th>
                                        <th class="col-4">@lang('student.action')</th>
                                    </tr>
                                    </thead>

                                    <tbody class="d-block">
                                    @if($student_detail->document_file_1 != "")
                                        <tr class="d-flex">
                                            <td class="col-2">{{$student_detail->document_title_1}}</td>
                                            <td class="col-6">{{showDocument(@$student_detail->document_file_1)}}</td>
                                            <td class="col-4">
                                                @if (file_exists($student_detail->document_file_1))
                                                    <a class="primary-btn tr-bg text-uppercase bord-rad"
                                                        href="{{url($student_detail->document_file_1)}}" download>
                                                        @lang('common.download')<span class="pl ti-download"></span>
                                                    </a>
                                                     <a class="primary-btn icon-only fix-gr-bg" onclick="deleteDoc({{$student_detail->id}},1)"  data-id="1"  href="#">
                                                        <span class="ti-trash"></span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if($student_detail->document_file_2 != "")
                                        <tr class="d-flex">
                                            <td class="col-2">{{$student_detail->document_title_2}}</td>
                                            <td class="col-6">{{showDocument(@$student_detail->document_file_2)}}</td>
                                            <td class="col-4">
                                                @if (file_exists($student_detail->document_file_2))
                                                    <a class="primary-btn tr-bg text-uppercase bord-rad"
                                                       href="{{url($student_detail->document_file_2)}}" download>
                                                        @lang('common.download')<span class="pl ti-download"></span>
                                                    </a>
                                                    <a class="primary-btn icon-only fix-gr-bg" onclick="deleteDoc({{$student_detail->id}},2)"  data-id="2"  href="#">
                                                        <span class="ti-trash"></span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if($student_detail->document_file_3 != "")
                                        <tr class="d-flex">
                                            <td class="col-2">{{$student_detail->document_title_3}}</td>
                                            <td class="col-6">{{showDocument(@$student_detail->document_file_3)}}</td>
                                            <td class="col-4">
                                                @if (file_exists($student_detail->document_file_3))
                                                    <a class="primary-btn tr-bg text-uppercase bord-rad"
                                                    href="{{url($student_detail->document_file_3)}}" download>
                                                        @lang('common.download')<span class="pl ti-download"></span>
                                                    </a>
                                                    <a class="primary-btn icon-only fix-gr-bg" onclick="deleteDoc({{$student_detail->id}},3)"  data-id="3"  href="#">
                                                        <span class="ti-trash"></span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if($student_detail->document_file_4 != "")
                                        <tr class="d-flex">
                                            <td class="col-2">{{$student_detail->document_title_4}}</td>
                                            <td class="col-6">{{showDocument(@$student_detail->document_file_4)}}</td>
                                            <td class="col-4">
                                                @if (file_exists($student_detail->document_file_4))
                                                    <a class="primary-btn tr-bg text-uppercase bord-rad"
                                                    href="{{url($student_detail->document_file_4)}}" download>
                                                        @lang('common.download')<span class="pl ti-download"></span>
                                                    </a>

                                                    <a class="primary-btn icon-only fix-gr-bg" onclick="deleteDoc({{$student_detail->id}},4)"  data-id="4"  href="#">
                                                        <span class="ti-trash"></span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                     {{-- fgfdg --}}

                                    <div class="modal fade admin-query" id="delete-doc" >
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">@lang('common.delete')</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                    </div>

                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <form action="{{route('student_document_delete')}}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="student_id" >
                                                            <input type="hidden" name="doc_id">
                                                            <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                                                            <button type="submit" class="primary-btn fix-gr-bg">@lang('common.delete')</button>

                                                        </form>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    {{-- fgfdg --}}

                                    @foreach($student_detail->studentDocument as $document)

                                        <tr class="d-flex">
                                            <td class="col-2">{{$document->title}}</td>
                                            <td class="col-6">{{showDocument($document->file)}}</td>
                                            <td class="col-4">
                                                @if (file_exists($document->file))
                                                    <a class="primary-btn tr-bg text-uppercase bord-rad"
                                                    href="{{url($document->file)}}" download>
                                                        @lang('common.download')<span class="pl ti-download"></span>
                                                    </a>
                                                @endif
                                                <a class="primary-btn icon-only fix-gr-bg" data-toggle="modal"
                                                   data-target="#deleteDocumentModal{{$document->id}}" href="#">
                                                    <span class="ti-trash"></span>
                                                </a>

                                            </td>
                                        </tr>
                                        <div class="modal fade admin-query" id="deleteDocumentModal{{$document->id}}">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('common.delete')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            &times;
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="text-center">
                                                            <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                        </div>

                                                        <div class="mt-40 d-flex justify-content-between">
                                                            <button type="button" class="primary-btn tr-bg"
                                                                    data-dismiss="modal">@lang('common.cancel')
                                                            </button>
                                                            <a class="primary-btn fix-gr-bg"
                                                               href="{{route('delete-student-document', [$document->id])}}">
                                                                @lang('common.delete')</a>
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
                        <!-- End Documents Tab -->
                        <!-- Add Document modal form start-->
                        <div class="modal fade admin-query" id="add_document_madal">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title"> @lang('student.upload_document')</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="container-fluid">
                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'upload_document',
                                                                'method' => 'POST', 'enctype' => 'multipart/form-data', 'name' => 'document_upload']) }}
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <input type="hidden" name="student_id"
                                                           value="{{$student_detail->id}}">
                                                    <div class="row mt-25">
                                                        <div class="col-lg-12">
                                                            <div class="input-effect">
                                                                <input class="primary-input form-control{" type="text"
                                                                       name="title" value="" id="title">
                                                                <label> @lang('common.title')<span>*</span> </label>
                                                                <span class="focus-border"></span>

                                                                <span class=" text-danger" role="alert"
                                                                      id="amount_error">
                                                                    
                                                                </span>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 mt-30">
                                                    <div class="row no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <input class="primary-input" type="text"
                                                                       id="placeholderPhoto" placeholder="Document"
                                                                       disabled>
                                                                <span class="focus-border"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button class="primary-btn-small-input" type="button">
                                                                <label class="primary-btn small fix-gr-bg" for="photo"> @lang('common.browse')</label>
                                                                <input type="file" class="d-none" name="photo"
                                                                       id="photo">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- <div class="col-lg-12 text-center mt-40">
                                                    <button class="primary-btn fix-gr-bg" id="save_button_sibling" type="button">
                                                        <span class="ti-check"></span>
                                                        save information
                                                    </button>
                                                </div> -->
                                                <div class="col-lg-12 text-center mt-40">
                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg"
                                                                data-dismiss="modal">@lang('common.cancel')
                                                        </button>

                                                        <button class="primary-btn fix-gr-bg submit" type="submit">@lang('student.save')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            {{ Form::close() }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- Add Document modal form end-->
                        <!-- delete document modal -->

                        <!-- delete document modal -->
                        <!-- Start Timeline Tab -->
                        <div role="tabpanel" class="tab-pane fade {{Session::get('studentRecord') == 'active'? 'show active':''}}" id="studentRecord">
                            <div class="white-box">
                                <div class="text-right mb-20">
                                    <button  class="primary-btn-small-input primary-btn small fix-gr-bg" type="button"
                                    data-toggle="modal" data-target="#assignClass"> <span class="ti-plus pr-2"></span> @lang('common.add')</button>
                                </div>
                                <table id="" class="table simple-table table-responsive school-table"
                                       cellspacing="0">
                                    <thead class="d-block">
                                        <tr class="d-flex">
                                            <th class="col-3">@lang('common.class')</th>
                                            <th class="col-3">@lang('common.section')</th>
                                            @if($setting->multiple_roll ==1)
                                            <th class="col-3">@lang('student.id_number')</th>
                                            @endif
                                            <th class="col-3">@lang('student.action')</th>
                                        </tr>
                                    </thead>
        
                                    <tbody class="d-block">
                                        @foreach ($student_detail->studentRecords as $record)
                                            <tr class="d-flex">
                                                <td class="col-3">{{ $record->class->class_name }} @if($record->is_default) <span class="badge badge_1"> {{ __('common.default') }} </span> @endif </td>
                                                <td class="col-3">{{ $record->section->section_name }}</td>
                                                @if($setting->multiple_roll ==1)
                                                <td class="col-3">{{ $record->roll_no }}</td>
                                                @endif
                                                <td class="col-3">
                                                <a class="primary-btn icon-only fix-gr-bg modalLink" data-modal-size="small-modal"
                                                   title="@lang('student.edit_assign_class')"
                                                   href="{{route('student_assign_edit', [@$record->student_id,@$record->id])}}"><span class="ti-pencil"></span></a>
                                                <a href="#" class="primary-btn icon-only fix-gr-bg" data-toggle="modal" data-target="#deleteRecord_{{ $record->id }}">
                                                    <span class="ti-trash"></span>
                                                </a>        
                                                   
                                                </td>
                                            </tr>
                                       
                                        
                                   
                                   
                                     {{-- record delete --}}
        
                                    <div class="modal fade admin-query" id="deleteRecord_{{ $record->id }}" >
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">@lang('common.delete')</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
        
                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                    </div>
        
                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
        
                                                        <form action="{{route('student.record.delete')}}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="student_id" value="{{ $record->student_id }}">
                                                            <input type="hidden" name="record_id" value="{{ $record->id }}">
                                                          
                                                            <button type="submit" class="primary-btn fix-gr-bg">@lang('common.delete')</button>
        
                                                        </form>
        
                                                    </div>
                                                </div>
        
                                            </div>
                                        </div>
                                    </div>
                                    {{-- record delete --}}
        
                                    {{-- edit record --}}
                                  
                                    @endforeach
                                    {{-- end edit record --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- End Timeline Tab -->

                        <div role="tabpanel" class="tab-pane fade {{Session::get('studentTimeline') == 'active'? 'show active':''}}" id="studentTimeline">
                            <div class="white-box">
                                <div class="text-right mb-20">
                                    <button type="button" data-toggle="modal" data-target="#add_timeline_madal"
                                            class="primary-btn tr-bg text-uppercase bord-rad">
                                        @lang('common.add')
                                        <span class="pl ti-plus"></span>
                                    </button>

                                </div>
                                @foreach($timelines as $timeline)
                                    <div class="student-activities">
                                        <div class="single-activity">
                                            <h4 class="title text-uppercase">

                                                {{$timeline->date != ""? dateConvert($timeline->date):''}}

                                            </h4>
                                            <div class="sub-activity-box d-flex">
                                                <h6 class="time text-uppercase">10.30 pm</h6>
                                                <div class="sub-activity">
                                                    <h5 class="subtitle text-uppercase"> {{$timeline->title}}</h5>
                                                    <p>
                                                        {{$timeline->description}}
                                                    </p>
                                                </div>

                                                <div class="close-activity">

                                                    <a class="primary-btn icon-only fix-gr-bg" data-toggle="modal"
                                                       data-target="#deleteTimelineModal{{$timeline->id}}" href="#">
                                                        <span class="ti-trash text-white"></span>
                                                    </a>

                                                    @if (file_exists($timeline->file))
                                                        <a href="{{url($timeline->file)}}"
                                                           class="primary-btn tr-bg text-uppercase bord-rad" download>
                                                            @lang('common.download')<span class="pl ti-download"></span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade admin-query" id="deleteTimelineModal{{$timeline->id}}">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('common.delete')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            &times;
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="text-center">
                                                            <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                        </div>

                                                        <div class="mt-40 d-flex justify-content-between">
                                                            <button type="button" class="primary-btn tr-bg"
                                                                    data-dismiss="modal">@lang('common.cancel')
                                                            </button>
                                                            <a class="primary-btn fix-gr-bg"
                                                               href="{{route('delete_timeline', [$timeline->id])}}">
                                                                @lang('common.delete')</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                            </div>
                        </div>

                 

                    </div>
                </div>
                <!-- End Student Details -->
            </div>


        </div>
    </section>

    <!-- timeline form modal start-->
    <div class="modal fade admin-query" id="add_timeline_madal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('student.add_timeline')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student_timeline_store',
                                            'method' => 'POST', 'enctype' => 'multipart/form-data', 'name' => 'document_upload']) }}
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="student_id" value="{{$student_detail->id}}">
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{" type="text" name="title" value=""
                                                   id="title" maxlength="200">
                                            <label>@lang('student.title') <span>*</span> </label>
                                            <span class="focus-border"></span>

                                            <span class=" text-danger" role="alert" id="amount_error">
                                                
                                            </span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-30">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input date form-control" readonly id="startDate" type="text"
                                                   name="date">
                                            <label>@lang('common.date')</label>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="start-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-30">
                                <div class="input-effect">
                                    <textarea class="primary-input form-control" cols="0" rows="3" name="description"
                                              id="Description"></textarea>
                                    <label>@lang('common.description')<span></span> </label>
                                    <span class="focus-border textarea"></span>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-40">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input" type="text" id="placeholderFileFourName"
                                                   placeholder="Document"
                                                   disabled>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg"
                                                   for="document_file_4">@lang('common.browse')</label>
                                            <input type="file" class="d-none" name="document_file_4"
                                                   id="document_file_4">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-30">

                                <input type="checkbox" id="currentAddressCheck" class="common-checkbox"
                                       name="visible_to_student" value="1">
                                <label for="currentAddressCheck">@lang('student.visible_to_this_person')</label>
                            </div>


                            <!-- <div class="col-lg-12 text-center mt-40">
                                <button class="primary-btn fix-gr-bg" id="save_button_sibling" type="button">
                                    <span class="ti-check"></span>
                                    save information
                                </button>
                            </div> -->
                            <div class="col-lg-12 text-center mt-40">
                                <div class="mt-40 d-flex justify-content-between">
                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>

                                    <button class="primary-btn fix-gr-bg submit" type="submit">@lang('common.save')</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- timeline form modal end-->
    <!-- assign class form modal start-->
    <div class="modal fade admin-query" id="assignClass">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('student.assign_class')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student.record.store','method' => 'POST']) }}
                      
                           
                            <input type="hidden" name="student_id" value="{{ $student_detail->id }}">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="input-effect sm2_mb_20 md_mb_20">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('session') ? ' is-invalid' : '' }}" name="session" id="academic_year">
                                            <option data-display="@lang('common.academic_year') *" value="">@lang('common.academic_year') *</option>
                                            @foreach($sessions as $session)
                                            <option value="{{$session->id}}" {{old('session') == $session->id? 'selected': ''}}>{{$session->year}}[{{$session->title}}]</option>
                                            @endforeach
                                        </select>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('session'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('session') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-25">
                                <div class="col-lg-12">
                                    <div class="input-effect sm2_mb_20 md_mb_20" id="class-div">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('class') ? ' is-invalid' : '' }}" name="class" id="classSelectStudent">
                                            <option data-display="@lang('common.class') *" value="">@lang('common.class') *</option>
                                        </select>
                                        <div class="pull-right loader loader_style" id="select_class_loader">
                                            <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                                        </div>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('class'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('class') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-25">    
                                <div class="col-lg-12">
                                    <div class="input-effect sm2_mb_20 md_mb_20" id="sectionStudentDiv">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('section') ? ' is-invalid' : '' }}" name="section" id="sectionSelectStudent">
                                           <option data-display="@lang('common.section') *" value="">@lang('common.section') *</option>
                                        </select>
                                        <div class="pull-right loader loader_style" id="select_section_loader">
                                            <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                                        </div>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('section'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('section') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(generalSetting()->multiple_roll==1)
                            <div class="row mt-25">
                                <div class="col-lg-12">
                                    <div class="input-effect sm2_mb_20 md_mb_20">
                                        <input oninput="numberCheck(this)" class="primary-input" type="text" id="roll_number" name="roll_number"  value="{{old('roll_number')}}">
                                        <label> {{ moduleStatusCheck('Lead')==true ? __('lead::lead.id_number') : __('student.roll') }}
                                             @if(is_required('roll_number')==true) <span> *</span> @endif</label>
                                        <span class="focus-border"></span>
                                        <span class="text-danger" id="roll-error" role="alert">
                                            <strong></strong>
                                        </span>
                                        @if ($errors->has('roll_number'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('roll_number') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row mt-25">
                                <div class="col-lg-12">
                                    <input type="checkbox" id="is_default" value="1" class="common-checkbox" name="is_default">
                                    <label for="is_default">@lang('student.is_default')</label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-center mt-20">
                                <div class="mt-40 d-flex justify-content-between">
                                    <button type="button" class="primary-btn tr-bg"
                                            data-dismiss="modal">@lang('admin.cancel')</button>
                                    <button class="primary-btn fix-gr-bg submit" id="save_button_query"
                                            type="submit">@lang('admin.save')</button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- assign class form modal end-->

<script>
    function deleteDoc(id,doc){
        var modal = $('#delete-doc');
         modal.find('input[name=student_id]').val(id)
         modal.find('input[name=doc_id]').val(doc)
         modal.modal('show');
    }
</script>

@endsection
