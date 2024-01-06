@extends('backEnd.master')
@section('title')
    @lang('reports.merit_list_report')
@endsection
@section('mainContent')
    @push('css')
        <style>
            #grade_table th{
                border: 1px solid black;
                text-align: center;
                background: #351681;
                color: white;
            }

            #grade_table td{
                color: black;
                text-align: center;
                border: 1px solid black;
            }

            #grade_table th {
                border: 1px solid #dee2e6;
                border-top-style: solid;
                border-top-width: 1px;
                text-align: left;
                background: #351681;
                color: white;
                background: #f2f2f2;
                color: #415094;
                font-size: 12px;
                font-weight: 500;
                text-transform: uppercase;
                border-top: 0px;
                padding: 5px 4px;
                background: transparent;
                border-bottom: 1px solid rgba(130, 139, 178, 0.15) !important;
            }

            #grade_table td {
                color: #828bb2;
                padding: 0 7px;
                font-weight: 400;
                background-color: transparent;
                border-right: 0;
                border-left: 0;
                text-align: left !important;
                border-bottom: 1px solid rgba(130, 139, 178, 0.15) !important;
            }

            .single-report-admit table tr th {
                border: 0;
                border-bottom: 1px solid rgba(67, 89, 187, 0.15) !important;
                text-align: left
            }

            .single-report-admit table thead tr th {
                border: 0 !important;
            }

            .single-report-admit table tbody tr:first-of-type td {
                border-top: 1px solid rgba(67, 89, 187, 0.15) !important;
            }

            .single-report-admit table tr td {
                vertical-align: middle;
                font-size: 12px;
                color: #828BB2;
                font-weight: 400;
                border: 0;
                border-bottom: 1px solid rgba(130, 139, 178, 0.15) !important;
                text-align: left
            }

            .single-report-admit table.summeryTable tbody tr:first-of-type td,
            .single-report-admit table.comment_table tbody tr:first-of-type td {
                border-top:0 !important;
            }

            .subjectList{
                display: grid;
                grid-template-columns: repeat(2,1fr);
                grid-column-gap: 50px;
                margin: 0;
                padding: 0;
            }

            .subjectList li{
                list-style: none
            }

            .subjectList li span{
                color: #828bb2;
                font-size: 14px;
            }

            .font-14{
                font-size: 14px;
            }

            .line_grid {
                display: grid;
                grid-template-columns: 120px auto;
                grid-gap: 10px;
            }
        </style>
    @endpush
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('reports.merit_list_report') </h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="#">@lang('reports.reports')</a>
                    <a href="#">@lang('reports.merit_list_report')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-8 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('common.select_criteria') </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'merit_list_report', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                    <div class="row">
                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                        <div class="col-lg-4 mt-30-md">
                            <select class="w-100 bb niceSelect form-control{{ $errors->has('exam') ? ' is-invalid' : '' }}" name="exam">
                                <option data-display="@lang('common.select_exam')*" value="">@lang('common.select_exam') *</option>
                                @foreach($exams as $exam)
                                    <option value="{{$exam->id}}" {{isset($exam_id)? ($exam_id == $exam->id? 'selected':''):''}}>{{$exam->title}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('exam'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('exam') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <select class="w-100 bb niceSelect form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" id="select_class" name="class">
                                <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class') *</option>
                                @foreach($classes as $class)
                                    <option value="{{$class->id}}" {{isset($class_id)? ($class_id == $class->id? 'selected':''):''}}>{{$class->class_name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('class'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('class') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="col-lg-4 mt-30-md" id="select_section_div">
                            <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }} select_section" id="select_section" name="section">
                                <option data-display="@lang('common.select_section')*" value="">@lang('common.select_section') *</option>
                            </select>
                            @if ($errors->has('section'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('section') }}</strong>
                                    </span>
                            @endif
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
    </section>

    @if(isset($allresult_data))
        <section class="student-details">
            <div class="container-fluid p-0">
                <div class="row mt-40">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-30 mt-0">@lang('reports.merit_list_report')</h3>
                        </div>
                    </div>
                    <div class="col-lg-8 pull-right">
                        <a href="{{route('merit-list/print', [$InputExamId, $InputClassId, $InputSectionId])}}" class="primary-btn small fix-gr-bg pull-right" target="_blank"><i class="ti-printer"> </i> @lang('common.print')</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box">
                            <div class="row justify-content-center">
                                <div class="col-lg-12">
                                    <div class="single-report-admit">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="d-flex">
                                                    <div class="col-lg-2">
                                                        <img class="logo-img" src="{{ generalSetting()->logo }}" alt="{{generalSetting()->school_name}}">
                                                    </div>
                                                    <div class="col-lg-6 ml-30">
                                                        <h3 class="text-white"> {{isset(generalSetting()->school_name)?generalSetting()->school_name:'Infix School Management ERP'}} </h3>
                                                        <p class="text-white mb-0"> {{isset(generalSetting()->address)?generalSetting()->address:'Infix School Address'}} </p>
                                                        <p class="text-white mb-0">@lang('common.email'):  {{isset(generalSetting()->email)? generalSetting()->email:'admin@demo.com'}} ,   @lang('common.phone'):  {{isset(generalSetting()->phone)?generalSetting()->phone:'admin@demo.com'}} </p>
                                                    </div>
                                                    <div class="offset-2"></div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-lg-8">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h3>@lang('reports.order_of_merit_list')</h3>
                                                                    <p class="mb-0 font-14 line_grid">
                                                                        @lang('common.academic_year')  <span class="primary-color fw-500 "> : {{optional(generalSetting()->academic_Year)->year}}</span>
                                                                    </p>
                                                                    <p class="mb-0 font-14 line_grid">
                                                                        @lang('exam.exam') <span class="primary-color fw-500">: {{$exam_name}}</span>
                                                                    </p>
                                                                    <p class="mb-0 font-14 line_grid">
                                                                        @lang('common.class') <span class="primary-color fw-500">: {{$class_name}}</span>
                                                                    </p>
                                                                    <p class="mb-0 font-14 line_grid">
                                                                        @lang('common.section') <span class="primary-color fw-500">: {{$section->section_name}}</span>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h3>@lang('common.subjects')</h3>
                                                                    <ul class="subjectList">
                                                                        @foreach($assign_subjects as $subject)
                                                                            <li>
                                                                                <p class="mb-0">
                                                                                    <span class="primary-color fw-500">{{$subject->subject->subject_name}}</span>
                                                                                </p>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- sm_marks_grades --}}
                                                        {{-- <div class="col-lg-4 text-black">
                                                            @php $marks_grade=DB::table('sm_marks_grades')->where('academic_id', getAcademicId())->get(); @endphp
                                                            @if(@$marks_grade)
                                                                <table class="table  table-bordered table-striped " id="grade_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>@lang('common.staring')</th>
                                                                            <th>@lang('common.ending')</th>
                                                                            <th>@lang('common.gpa')</th>
                                                                            <th>@lang('common.grade')</th>
                                                                            <th>@lang('common.evalution')</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($marks_grade as $grade_d)
                                                                            <tr>
                                                                                <td>{{$grade_d->percent_from}}</td>
                                                                                <td>{{$grade_d->percent_upto}}</td>
                                                                                <td>{{$grade_d->gpa}}</td>
                                                                                <td>{{$grade_d->grade_name}}</td>
                                                                                <td class="nowrap">{{$grade_d->description}}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            @endif
                                                        </div> --}}
                                                        {{--end sm_marks_grades --}}
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="w-100 mt-30 mb-20 table table-bordered meritList">
                                                        <thead>
                                                        <tr>
                                                            <th>@lang('common.name')</th>
                                                            <th>@lang('student.admission_no')</th>
                                                            <th>@lang('student.roll_no')</th>
                                                            <th>@lang('reports.position')</th>
                                                            {{-- <th>@lang('common.total_mark')</th> --}}
                                                            {{-- <th>@lang('common.obtained_marks')</th> --}}
                                                            <th>@lang('exam.total_mark')</th>
                                                            <th>@lang('reports.gpa')</th>
                                                            @foreach($subjectlist as $subject)
                                                                <th>{{$subject}}</th>
                                                            @endforeach
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($allresult_data as $key => $row)
                                                            @php
                                                                $total_student_mark = 0;
                                                                $total = 0;
                                                                $markslist = explode(',',$row->marks_string);
                                                            @endphp
                                                            <tr>
                                                                <td>{{$row->student_name}}</td>
                                                                <td>{{$row->admission_no}}</td>
                                                                <td>{{$row->studentinfo->roll_no}}</td>
                                                                <td>{{$key+1}}</td>
                                                                <td>{{$row->total_marks}}</td>
                                                                <td>{{$row->gpa_point}}</td>
                                                                @if(!empty($markslist))
                                                                    @foreach($markslist as $mark)
                                                                        @php
                                                                            $subject_mark[]= $mark;
                                                                            $total_student_mark = $total_student_mark + $mark;
                                                                            $total = $total + $subject_total_mark;
                                                                        @endphp
                                                                        <td> {{!empty($mark)? $mark:0}}</td>
                                                                    @endforeach
                                                                @endif
                                                                {{-- <td>{{$total}}</td> --}}
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
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
    @endif
@endsection