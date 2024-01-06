@extends('backEnd.master')
@section('title')
@lang('communicate.sms_template')
@endsection
@section('mainContent')


<section class="sms-breadcrumb mb-40 white-box up_breadcrumb">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('communicate.sms_template')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('communicate.biometrics')</a>
                <a href="#">@lang('communicate.sms_template')</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        @if(isset($certificate))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{route('student-certificate')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('common.add')
                </a>
            </div>
        </div>
        @endif
        <div class="row">
           
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">@if(isset($certificate))
                                    @lang('common.edit')
                                @else
                                    @lang('common.add')
                                @endif
                                @lang('communicate.sms_template')
                            </h3>
                        </div>
                          @if(userPermission(50))
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => array('sms-template-store',$template->id), 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                         @endif
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        @if(session()->has('message-success'))
                                        <div class="alert alert-success">
                                            {{ session()->get('message-success') }}
                                        </div>
                                        @elseif(session()->has('message-danger'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger') }}
                                        </div>
                                        @endif
                                        <span class="text-primary">[name] [check_in_time] [father_name] [AttendanceDate] [checkout_time] [early_checkout_time] [dob] [present_address] [guardian] [created_at] [admission_no] [roll_no] [class] [section] [gender] [admission_date] [category] [cast] [father_name] [mother_name] [religion] [email] [phone]</span>
                                        
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('admission_pro') ? ' is-invalid' : '' }}" cols="0" rows="2" name="admission_pro" maxlength="500">{{isset($template)? $template->admission_pro: old('admission_pro')}}</textarea>
                                            <label>@lang('communicate.student_admission_progress_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('admission_pro'))
                                                <span class="error text-danger"><strong>{{ $errors->first('admission_pro') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('student_admit') ? ' is-invalid' : '' }}" cols="0" rows="2" name="student_admit" maxlength="500">{{isset($template)? $template->student_admit: old('student_admit')}}</textarea>
                                            <label>@lang('communicate.student_admitted_message_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('student_admit'))
                                                <span class="error text-danger"><strong>{{ $errors->first('student_admit') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('login_disable') ? ' is-invalid' : '' }}" cols="0" rows="2" name="login_disable" maxlength="500">{{isset($template)? $template->login_disable: old('login_disable')}}</textarea>
                                            <label>@lang('communicate.login_permission_disable_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('login_disable'))
                                                <span class="error text-danger"><strong>{{ $errors->first('login_disable') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('exam_schedule') ? ' is-invalid' : '' }}" cols="0" rows="2" name="exam_schedule" maxlength="500">{{isset($template)? $template->exam_schedule: old('exam_schedule')}}</textarea>
                                            <label>@lang('exam.exam_schedule') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('exam_schedule'))
                                                <span class="error text-danger"><strong>{{ $errors->first('exam_schedule') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('exam_publish') ? ' is-invalid' : '' }}" cols="0" rows="2" name="exam_publish" maxlength="500">{{isset($template)? $template->exam_publish: old('exam_publish')}}</textarea>
                                            <label>@lang('communicate.exam_publish') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('exam_publish'))
                                                <span class="error text-danger"><strong>{{ $errors->first('exam_publish') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('due_fees') ? ' is-invalid' : '' }}" cols="0" rows="2" name="due_fees" maxlength="500">{{isset($template)? $template->due_fees: old('due_fees')}}</textarea>
                                            <label>@lang('communicate.due_fees') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('due_fees'))
                                                <span class="error text-danger"><strong>{{ $errors->first('due_fees') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('collect_fees') ? ' is-invalid' : '' }}" cols="0" rows="2" name="collect_fees" maxlength="500">{{isset($template)? $template->collect_fees: old('collect_fees')}}</textarea>
                                            <label>@lang('fees.collect_fees') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('collect_fees'))
                                                <span class="error text-danger"><strong>{{ $errors->first('collect_fees') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('stu_promote') ? ' is-invalid' : '' }}" cols="0" rows="2" name="stu_promote" maxlength="500">{{isset($template)? $template->stu_promote: old('stu_promote')}}</textarea>
                                            <label>@lang('student.student_promote') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('stu_promote'))
                                                <span class="error text-danger"><strong>{{ $errors->first('stu_promote') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('attendance_sms') ? ' is-invalid' : '' }}" cols="0" rows="2" name="attendance_sms" maxlength="500">{{isset($template)? $template->attendance_sms: old('attendance_sms')}}</textarea>
                                            <label>@lang('communicate.attendance_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('attendance_sms'))
                                                <span class="error text-danger"><strong>{{ $errors->first('attendance_sms') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('late_sms') ? ' is-invalid' : '' }}" cols="0" rows="2" name="late_sms" maxlength="500">{{isset($template)? $template->late_sms: old('late_sms')}}</textarea>
                                            <label>@lang('communicate.late_attendance_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('late_sms'))
                                                <span class="error text-danger"><strong>{{ $errors->first('late_sms') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('absent') ? ' is-invalid' : '' }}" cols="0" rows="2" name="absent" maxlength="500">{{isset($template)? $template->absent: old('absent')}}</textarea>
                                            <label>@lang('communicate.student_absent_attendance_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('absent'))
                                                <span class="error text-danger"><strong>{{ $errors->first('absent') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('er_checkout') ? ' is-invalid' : '' }}" cols="0" rows="2" name="er_checkout" maxlength="500">{{isset($template)? $template->er_checkout: old('er_checkout')}}</textarea>
                                            <label>@lang('communicate.student_early_checkout_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('er_checkout'))
                                                <span class="error text-danger"><strong>{{ $errors->first('er_checkout') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('st_checkout') ? ' is-invalid' : '' }}" cols="0" rows="2" name="st_checkout" maxlength="500">{{isset($template)? $template->st_checkout: old('st_checkout')}}</textarea>
                                            <label>@lang('communicate.student_checkout_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('st_checkout'))
                                                <span class="error text-danger"><strong>{{ $errors->first('st_checkout') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('st_credentials') ? ' is-invalid' : '' }}" cols="0" rows="2" name="st_credentials" maxlength="500">{{isset($template)? $template->st_credentials: old('st_credentials')}}</textarea>
                                            <label>@lang('communicate.student_credentials_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('st_credentials'))
                                                <span class="error text-danger"><strong>{{ $errors->first('st_credentials') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('staff_credentials') ? ' is-invalid' : '' }}" cols="0" rows="2" name="staff_credentials" maxlength="500">{{isset($template)? $template->staff_credentials: old('staff_credentials')}}</textarea>
                                            <label>@lang('communicate.staff_credentials_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('staff_credentials'))
                                                <span class="error text-danger"><strong>{{ $errors->first('staff_credentials') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('holiday') ? ' is-invalid' : '' }}" cols="0" rows="2" name="holiday" maxlength="500">{{isset($template)? $template->holiday: old('holiday')}}</textarea>
                                            <label>@lang('communicate.holiday_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('holiday'))
                                                <span class="error text-danger"><strong>{{ $errors->first('holiday') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('leave_app') ? ' is-invalid' : '' }}" cols="0" rows="2" name="leave_app" maxlength="500">{{isset($template)? $template->leave_app: old('leave_app')}}</textarea>
                                            <label>@lang('communicate.leave_application_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('leave_app'))
                                                <span class="error text-danger"><strong>{{ $errors->first('leave_app') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('approve_sms') ? ' is-invalid' : '' }}" cols="0" rows="2" name="approve_sms" maxlength="500">{{isset($template)? $template->approve_sms: old('approve_sms')}}</textarea>
                                            <label>@lang('communicate.leave_approve_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('approve_sms'))
                                                <span class="error text-danger"><strong>{{ $errors->first('approve_sms') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('birth_st') ? ' is-invalid' : '' }}" cols="0" rows="2" name="birth_st" maxlength="500">{{isset($template)? $template->birth_st: old('birth_st')}}</textarea>
                                            <label>@lang('communicate.student_birthday_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('birth_st'))
                                                <span class="error text-danger"><strong>{{ $errors->first('birth_st') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('birth_staff') ? ' is-invalid' : '' }}" cols="0" rows="2" name="birth_staff" maxlength="500">{{isset($template)? $template->birth_staff: old('birth_staff')}}</textarea>
                                            <label>@lang('communicate.staff_birthday_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('birth_staff'))
                                                <span class="error text-danger"><strong>{{ $errors->first('birth_staff') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('cheque_bounce') ? ' is-invalid' : '' }}" cols="0" rows="2" name="cheque_bounce" maxlength="500">{{isset($template)? $template->cheque_bounce: old('cheque_bounce')}}</textarea>
                                            <label>@lang('communicate.cheque_bounce_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('cheque_bounce'))
                                                <span class="error text-danger"><strong>{{ $errors->first('cheque_bounce') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('l_issue_b') ? ' is-invalid' : '' }}" cols="0" rows="2" name="l_issue_b" maxlength="500">{{isset($template)? $template->l_issue_b: old('l_issue_b')}}</textarea>
                                            <label>@lang('communicate.library_book_issue_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('l_issue_b'))
                                                <span class="error text-danger"><strong>{{ $errors->first('l_issue_b') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('re_issue_book') ? ' is-invalid' : '' }}" cols="0" rows="2" name="re_issue_book" maxlength="500">{{isset($template)? $template->re_issue_book: old('re_issue_book')}}</textarea>
                                            <label>@lang('communicate.return_issue_books_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('re_issue_book'))
                                                <span class="error text-danger"><strong>{{ $errors->first('re_issue_book') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('sms_text') ? ' is-invalid' : '' }}" cols="0" rows="2" name="sms_text" maxlength="500">{{isset($template)? $template->sms_text: old('sms_text')}}</textarea>
                                            <label>@lang('communicate.body_sms') <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('sms_text'))
                                                <span class="error text-danger"><strong>{{ $errors->first('sms_text') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div> --}}

                               
	                           @php 
                                  $tooltip = "";
                                  if(userPermission(50) ){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                                
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip" title="{{$tooltip}}">
                                            <span class="ti-check"></span>
                                            @if(isset($certificate))
                                                @lang('common.update')
                                            @else
                                                @lang('common.save')
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</section>
@endsection
