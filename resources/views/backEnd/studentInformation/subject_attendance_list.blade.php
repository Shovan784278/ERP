@extends('backEnd.master')
@section('title') 
@lang('student.subject_wise_attendance')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 up_breadcrumb white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('student.subject_wise_attendance')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('student.student_information')</a>
                <a href="#">@lang('student.subject_wise_attendance')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="main-title">
                    <h3 class="mb-30">@lang('common.select_criteria') </h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">  
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'subject-attendance-search', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_studentA']) }}
                        <div class="row">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}"> 
                        <div class="col-lg-3 mt-30-md">
                            <select class="w-100 bb niceSelect form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" id="select_class" name="class">
                                <option data-display="@lang('common.select_class')*" value="">@lang('common.select_class') *</option>
                                @foreach($classes as $class)
                                <option value="{{$class->id}}"  {{isset($class_id)? ($class_id == $class->id? 'selected':''):''}}>{{$class->class_name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('class'))
                            <span class="invalid-feedback invalid-select" role="alert">
                                <strong>{{ $errors->first('class') }}</strong>
                            </span>
                            @endif
                        </div> 
                        <div class="col-lg-3 mt-30-md" id="select_section_div">
                            <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }} select_section" id="select_section" name="section">
                                <option data-display="@lang('common.select_section') *" value="">@lang('common.select_section') *</option>
                                @isset($section_id)
                                @foreach($sections as $section)
                                    <option value="{{$section->section_id}}"  {{isset($section_id)? ($section_id == $section->section_id? 'selected':''):''}}>{{$section->sectionName->section_name}}</option>
                                @endforeach
                                @endisset
                            </select>
                            <div class="pull-right loader loader_style" id="select_section_loader">
                                <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                            </div>
                            @if ($errors->has('section'))
                            <span class="invalid-feedback invalid-select" role="alert">
                                <strong>{{ $errors->first('section') }}</strong>
                            </span>
                            @endif
                        </div> 
                        <div class="col-lg-3 mt-30-md" id="select_subject_div">
                            <select class="w-100 bb niceSelect form-control{{ $errors->has('subject') ? ' is-invalid' : '' }} select_subject" id="select_subject" name="subject">
                                <option data-display="{{__('student.select_subject')}} *" value="">{{__('student.select_subject')}} *</option>
                                @isset($subject_id)
                                    @foreach($subjects as $subject)
                                        @php
                                        $type = $subject->subject->subject_type == 'T' ? 'Theory' : 'Practical';
                                        @endphp
                                        <option value="{{$subject->subject_id}}"  {{isset($subject_id)? ($subject_id == $subject->subject_id? 'selected':''):''}}>{{$subject->subject->subject_name}} ({{$type}})</option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="pull-right loader loader_style" id="select_subject_loader">
                                <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                            </div>
                            @if ($errors->has('subject'))
                            <span class="invalid-feedback invalid-select" role="alert">
                                <strong>{{ $errors->first('subject') }}</strong>
                            </span>
                            @endif
                        </div> 
                        <div class="col-lg-3 mt-30-md">
                            <div class="row no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <input class="primary-input date form-control{{ $errors->has('attendance_date') ? ' is-invalid' : '' }} {{isset($date)? 'read-only-input': ''}}" id="startDate" type="text"
                                            name="attendance_date" autocomplete="off" value="{{isset($date)? $date: date('m/d/Y')}}">
                                        <label for="startDate">@lang('student.attendance_date')*</label>
                                        <span class="focus-border"></span>
                                        
                                        @if ($errors->has('attendance_date'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('attendance_date') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button class="" type="button">
                                        <i class="ti-calendar" id="start-date-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span>
                                @lang('common.search')
                            </button>
                        </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
@if(isset($students))
        <div class="row mt-40">
            <div class="col-lg-12 ">
                <div class=" white-box mb-40">
                    <div class="row"> 
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-30 text-center">@lang('student.subject_wise_attendance') </h3>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <strong> @lang('common.class'): </strong> {{$search_info['class_name']}}
                        </div>
                        <div class="col-lg-3">
                            <strong> @lang('common.section'): </strong> {{$search_info['section_name']}}
                        </div>
                        <div class="col-lg-3">
                            <strong> @lang('common.subject'): </strong> {{$search_info['subject_name']}}
                        </div>
                        <div class="col-lg-3">
                            <strong> @lang('common.date'): </strong> {{dateConvert($input['attendance_date'])}}
                        </div>
                    </div> 
                </div> 
                    <div class="row">
                        <div class="col-lg-12 col-md-12 no-gutters">
                            @if($attendance_type != "" && $attendance_type == "H")
                            <div class="alert alert-warning">@lang('student.attendance_already_submitted_as_holiday')</div>
                            @elseif($attendance_type != "" && $attendance_type != "H")
                            <div class="alert alert-success">@lang('student.attendance_already_submitted')</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-20">
                        <div class="col-lg-6  col-md-6 no-gutters text-md-left mark-holiday ">
                            @if($attendance_type != "H")
                            <form action="{{route('student-subject-holiday-store')}}" method="POST">
                                @csrf
                                <input type="hidden" name="purpose" value="mark">
                                <input type="hidden" name="class_id" value="{{$input['class']}}">
                                <input type="hidden" name="section_id" value="{{$input['section']}}">
                                <input type="hidden" name="subject_id" value="{{$input['subject']}}">
                                <input type="hidden" name="attendance_date" value="{{$input['attendance_date']}}">
                                <button type="submit" class="primary-btn fix-gr-bg mb-20">
                                    @lang('student.mark_holiday')
                                </button>
                            </form>
                            @else
                            <form action="{{route('student-subject-holiday-store')}}" method="POST">
                                    @csrf
                                <input type="hidden" name="purpose" value="unmark">
                                <input type="hidden" name="class_id" value="{{$input['class']}}">
                                <input type="hidden" name="section_id" value="{{$input['section']}}">
                                <input type="hidden" name="subject_id" value="{{$input['subject']}}">
                                <input type="hidden" name="attendance_date" value="{{$input['attendance_date']}}">
                                <button type="submit" class="primary-btn fix-gr-bg mb-20">
                                    @lang('student.unmark_holiday')
                                </button>
                            </form>
                            @endif
                        </div>
                    </div> 

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'subject-attendance-store', 'method' => 'POST', 'enctype' => 'multipart/form-data'])}}
                    <input class="subject_class" type="hidden" name="class" value="{{$input['class']}}">
                    <input class="subject_section" type="hidden" name="section" value="{{$input['section']}}">
                    <input class="subject" type="hidden" name="subject" value="{{$input['subject']}}">
                    <input class="subject_attendance_date" type="hidden" name="attendance_date" value="{{$input['attendance_date']}}">
                    <input type="hidden" name="date" value="{{isset($input['attendance_date'])? $input['attendance_date']: ''}}">
                    <div class="row ">
                        <div class="col-lg-12">
                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                   
                                    <tr>
                                        <th>@lang('common.sl')</th>
                                        <th>@lang('student.admission_no')</th>
                                        <th>@lang('student.student_name')</th>
                                        <th>@lang('student.id_number')</th>
                                        <th>@lang('student.attendance')</th>
                                        <th>@lang('common.note')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php $count=1; @endphp
                                   
                                    @foreach($students as $student)
                                    {{-- student means student record data--}}
                                        <tr>
                                            <td>{{$count++}} </td>
                                            <td>{{$student->studentDetail->admission_no}}
                                                <input type="hidden" name="attendance[{{$student->id}}]" value="{{$student->id}}">
                                                <input type="hidden" name="attendance[{{$student->id}}][student]" value="{{$student->student_id}}">
                                                <input type="hidden" name="attendance[{{$student->id}}][class]" value="{{$student->class_id}}">
                                                <input type="hidden" name="attendance[{{$student->id}}][section]" value="{{$student->section_id}}">
                                            </td>
                                            <td>{{$student->studentDetail->first_name.' '.$student->studentDetail->last_name}}</td>
                                            <td>{{$student->roll_no}}</td>
                                            <td>
                                                <div class="d-flex radio-btn-flex">
                                                    <div class="mr-20">
                                                        <input type="radio" name="attendance[{{$student->id}}][attendance_type]" id="attendanceP{{$student->id}}" value="P" class="common-radio attendanceP subject_attendance_type" {{ $student->studentDetail->DateSubjectWiseAttendances !=null ? ($student->studentDetail->DateSubjectWiseAttendances->attendance_type == "P" ? 'checked' :'') : 'checked' }}>
                                                        <label for="attendanceP{{$student->id}}">@lang('student.present')</label>
                                                    </div>
                                                    <div class="mr-20">
                                                        <input type="radio" name="attendance[{{$student->id}}][attendance_type]" id="attendanceL{{$student->id}}" value="L" class="common-radio subject_attendance_type" {{ $student->studentDetail->DateSubjectWiseAttendances !=null ? ($student->studentDetail->DateSubjectWiseAttendances->attendance_type == "L" ? 'checked' :''):''}}>
                                                        <label for="attendanceL{{$student->id}}">@lang('student.late')</label>
                                                    </div>
                                                    <div class="mr-20">
                                                        <input type="radio" name="attendance[{{$student->id}}][attendance_type]" id="attendanceA{{$student->id}}" value="A" class="common-radio subject_attendance_type" {{$student->studentDetail->DateSubjectWiseAttendances !=null ? ($student->studentDetail->DateSubjectWiseAttendances->attendance_type == "A" ? 'checked' :''):''}}>
                                                        <label for="attendanceA{{$student->id}}">@lang('student.absent')</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" name="attendance[{{$student->id}}][attendance_type]" id="attendanceH{{$student->id}}" value="F" class="common-radio subject_attendance_type" {{$student->studentDetail->DateSubjectWiseAttendances !=null ? ($student->studentDetail->DateSubjectWiseAttendances->attendance_type == "F" ? 'checked' :'') : ''}}>
                                                        <label for="attendanceH{{$student->id}}">@lang('student.half_day')</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-effect">
                                                    <textarea class="primary-input form-control note_{{$student->id}}" cols="0" rows="2" name="attendance[{{$student->id}}][note]">{{$student->studentDetail->DateSubjectWiseAttendances !=null ? $student->studentDetail->DateSubjectWiseAttendances->notes :''}}</textarea>
                                                    <label>@lang('student.add_note_here')</label>
                                                    <span class="focus-border textarea"></span>
                                                    <span class="invalid-feedback">
                                                        <strong>@lang('student.error')</strong>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">
                                        <button type="submit" class="primary-btn mr-40 fix-gr-bg nowrap submit">
                                              @lang('student.attendance')
                                        </button>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
@endif
    </div>
</section>
@endsection