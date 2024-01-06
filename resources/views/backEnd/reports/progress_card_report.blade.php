@extends('backEnd.master')
@section('title')
@lang('reports.progress_card_report')
@endsection
@section('mainContent')
    <style type="text/css">
        .single-report-admit table tr th {
            border: 1px solid #a2a8c5 !important;
            vertical-align: middle;
        }

        #grade_table th {
            border: 1px solid black;
            text-align: center;
            background: #351681;
            color: white;
        }

        #grade_table td {
            color: black;
            text-align: center !important;
            border: 1px solid black;
        }

        hr {
            margin: 0;
        }

        .table-bordered {
            border: 1px solid #a2a8c5;
        }

        .single-report-admit table tr th {
            font-weight: 500;
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

        .single-report-admit table tbody tr th {
            border: 0 !important;
            border-bottom: 1px solid rgba(67, 89, 187, 0.15) !important;
        }

        .single-report-admit table.summeryTable tbody tr:first-of-type td,
        .single-report-admit table.comment_table tbody tr:first-of-type td {
            border-top: 0 !important;
        }

        /* new  style  */
        .single-report-admit {
        }

        .single-report-admit .student_marks_table {
            background: -webkit-linear-gradient(
                    90deg, #d8e6ff 0%, #ecd0f4 100%);
            background: -moz-linear-gradient(90deg, #d8e6ff 0%, #ecd0f4 100%);
            background: -o-linear-gradient(90deg, #d8e6ff 0%, #ecd0f4 100%);
            background: linear-gradient(
                    90deg, #d8e6ff 0%, #ecd0f4 100%);
        }

</style>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('reports.progress_card_report')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('reports.reports')</a>
                <a href="#">@lang('reports.progress_card_report')</a>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area mb-40">
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
                @if(session()->has('message-success') != "")
                    @if(session()->has('message-success'))
                        <div class="alert alert-success">
                            {{ session()->get('message-success') }}
                        </div>
                    @endif
                @endif
                @if(session()->has('message-danger') != "")
                    @if(session()->has('message-danger'))
                        <div class="alert alert-danger">
                            {{ session()->get('message-danger') }}
                        </div>
                    @endif
                @endif
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'progress_card_report', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                    <div class="row">
                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                        <div class="col-lg-4 mt-30-md md_mb_20">
                            <select class="w-100 bb niceSelect form-control {{ $errors->has('class') ? ' is-invalid' : '' }}"
                                    id="select_class" name="class">
                                <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class')
                                    *
                                </option>
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
                        <div class="col-lg-4 mt-30-md md_mb_20" id="select_section_div">
                            <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }} select_section"
                                    id="select_section" name="section">
                                <option data-display="@lang('common.select_section') *"
                                        value="">@lang('common.select_section') *
                                </option>
                            </select>
                            <div class="pull-right loader loader_style" id="select_section_loader">
                                <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}"
                                     alt="loader">
                            </div>
                            @if ($errors->has('section'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('section') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-4 mt-30-md md_mb_20" id="select_student_div">
                            <select class="w-100 bb niceSelect form-control{{ $errors->has('student') ? ' is-invalid' : '' }}"
                                    id="select_student" name="student">
                                <option data-display="@lang('common.select_student') *"
                                        value="">@lang('common.select_student') *
                                </option>
                            </select>
                            <div class="pull-right loader loader_style" id="select_student_loader">
                                <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}"
                                     alt="loader">
                            </div>
                        </div>
                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search"></span>
                                @lang('common.search')
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>

@if(isset($is_result_available))
    <section class="student-details">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12 no-gutters">
                    <div class="main-title d-flex ">
                        <h3 class="mb-30 flex-fill">@lang('reports.progress_card_report')</h3>
                        <div class="print_button pull-right">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'progress-card/print', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student', 'target' => '_blank']) }}

                            <input type="hidden" name="class_id" value="{{$class_id}}">
                            <input type="hidden" name="section_id" value="{{$section_id}}">
                            <input type="hidden" name="student_id" value="{{$student_id}}">
                            
                            <button type="submit" class="primary-btn small fix-gr-bg"><i class="ti-printer"> </i> @lang('common.print')
                            </button>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
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
                                                            <h3 class="text-white"> 
                                                                {{isset(generalSetting()->school_name)?generalSetting()->school_name:'Infix School Management ERP'}} 
                                                            </h3> 
                                                            <p class="text-white mb-0">
                                                                {{isset(generalSetting()->address)?generalSetting()->address:'Infix School Address'}} 
                                                            </p>
                                                            <p class="text-white mb-0">
                                                                @lang('common.email'):  {{isset(generalSetting()->email)?generalSetting()->email:'admin@demo.com'}},   @lang('common.phone'):  {{isset(generalSetting()->phone)?generalSetting()->phone:'+8801841412141'}} 
                                                            </p> 
                                                        </div>
                                                        <div class="offset-2">
                                                        </div>
                                                    </div>
                                            <div class="report-admit-img profile_100" style="background-image: url({{ file_exists(@$studentDetails->studentDetail->student_photo) ? asset($studentDetails->studentDetail->student_photo) : asset('public/uploads/staff/demo/staff.jpg') }})"></div>

                                        </div>
                                    <div class="card-body">
                                        <div class="student_marks_table">
                                            <div class="row">
                                                <div class="col-lg-7 text-black"> 
                                                    <h3 style="border-bottm:1px solid #ddd; padding: 15px; text-align:center"> 
                                                        @lang('reports.progress_card_report')
                                                    </h3>
                                                    <h3>
                                                        {{$studentDetails->studentDetail->full_name}}
                                                    </h3>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <p class="mb-0">
                                                                @lang('common.academic_year') : &nbsp;<span class="primary-color fw-500">{{generalSetting()->session_year}}</span>
                                                            </p>
                                                            <p class="mb-0">
                                                                @lang('common.section') : &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="primary-color fw-500">{{ $studentDetails->section->section_name }}</span>
                                                            </p>
                                                            <p class="mb-0">
                                                                @lang('common.class') : &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span class="primary-color fw-500">{{ $studentDetails->class->class_name }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <p class="mb-0">
                                                                @lang('student.admission_no') : <span class="primary-color fw-500">{{$studentDetails->studentDetail->admission_no}}</span>
                                                            </p>
                                                            <p class="mb-0">
                                                                @lang('student.roll') : &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="primary-color fw-500">{{$studentDetails->roll_no}}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 text-black">
                                                    @if(@$marks_grade)
                                                        <table class="table" id="grade_table">
                                                            <thead>
                                                            <tr>
                                                                <th>@lang('reports.staring')</th>
                                                                <th> @lang('reports.ending')</th>
                                                                <th>@lang('exam.gpa')</th>
                                                                <th>@lang('exam.grade')</th>
                                                                <th>@lang('homework.evalution')</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($marks_grade as $grade_d)
                                                                <tr>
                                                                    <td>{{$grade_d->percent_from}}</td>
                                                                    <td>{{$grade_d->percent_upto}}</td>
                                                                    <td>{{$grade_d->gpa}}</td>
                                                                    <td>{{$grade_d->grade_name}}</td>
                                                                    <td class="text-left">{{$grade_d->description}}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </div>
                                                <table class="table mb-0">
                                                    <thead>
                                                    <tr class="text-center">
                                                        <th rowspan="2">@lang('common.subjects')</th>
                                                        @foreach($assinged_exam_types as $assinged_exam_type)
                                                            @php
                                                                $exam_type = App\SmExamType::examType($assinged_exam_type);
                                                            @endphp
                                                            <th colspan="4">{{$exam_type->title}}</th>
                                                        @endforeach
                                                        <th rowspan="2">@lang('exam.result')</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        @foreach($assinged_exam_types as $assinged_exam_type)
                                                            <th>@lang('reports.full_mark')</th>
                                                            <th>@lang('exam.marks')</th>
                                                            <th>@lang('exam.grade')</th>
                                                            <th>@lang('exam.gpa')</th>
                                                        @endforeach
                                                    </tr>
                                                    </thead>
                                                    <tbody class="mark_sheet_body">
                                                    @php
                                                        $total_fail = 0;
                                                        $total_marks = 0;
                                                        $gpa_with_optional_count=0;
                                                        $gpa_without_optional_count=0;
                                                        $value=0;
                                                        $total_subject = 0;
                                                        $totalGpa  = 0;
                                                        $all_exam_type_full_mark=0;
                                                        $total_additional_subject_gpa=0;
                                                    @endphp
                                                    @foreach($subjects as $data)
                                                        <tr class="text-center">
                                                            @if ($optional_subject_setup!='' && $student_optional_subject!='')
                                                                @if ($student_optional_subject->subject_id==$data->subject->id)
                                                                    <td>{{$data->subject !=""?$data->subject->subject_name:""}} (@lang('common.optional'))</td>
                                                                @else
                                                                    <td>{{$data->subject !=""?$data->subject->subject_name:""}}</td>
                                                                @endif
                                                            @else
                                                                <td>{{$data->subject !=""?$data->subject->subject_name:""}}</td>
                                                            @endif
                                                            <?php
                                                            $totalSumSub = 0;
                                                            $totalSubjectFail = 0;
                                                            $TotalSum = 0;
                                                            foreach($assinged_exam_types as $assinged_exam_type){
                                                            $mark_parts = App\SmAssignSubject::getNumberOfPart($data->subject_id, $class_id, $section_id, $assinged_exam_type);
                                                            $result = App\SmResultStore::GetResultBySubjectId($class_id, $section_id, $data->subject_id, $assinged_exam_type, $student_id);

                                                            if (!empty($result)) {
                                                                $final_results = App\SmResultStore::GetFinalResultBySubjectId($class_id, $section_id, $data->subject_id, $assinged_exam_type, $student_id);

                                                                $term_base = App\SmResultStore::termBaseMark($class_id, $section_id, $data->subject_id, $assinged_exam_type, $student_id);
                                                            }
                                                            $total_subject += $assinged_exam_type;
                                                            $subject_full_mark = subjectFullMark($assinged_exam_type, $data->subject_id);
                                                            $total_additional_subject_gpa += @$optional_subject_setup->gpa_above;
                                                            if($result->count() > 0){
                                                            ?>
                                                            <td>
                                                                @php
                                                                    $all_exam_type_full_mark+=$subject_full_mark;
                                                                @endphp
                                                                {{$subject_full_mark}}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    if($final_results != ""){
                                                                        echo $final_results->total_marks;
                                                                        $totalSumSub += $final_results->total_marks;
                                                                        $totalGpa += $final_results->total_gpa_point;
                                                                        $total_marks += $final_results->total_marks;
                                                                    }else{
                                                                        echo 0;
                                                                    }
                                                                @endphp
                                                            </td>
                                                            <td>
                                                                @php
                                                                    if($final_results != ""){
                                                                        if($final_results->total_gpa_grade == @$fail_grade_name->grade_name){
                                                                            $totalSubjectFail++;
                                                                            $total_fail++;
                                                                        }
                                                                        echo $final_results->total_gpa_grade;
                                                                    }else{
                                                                        echo '-';
                                                                    }
                                                                    if ($student_optional_subject!='') {
                                                                        if ($student_optional_subject->subject_id==$data->subject->id) {
                                                                            $optional_subject_mark=$final_results->total_marks;
                                                                        }
                                                                    }
                                                                @endphp
                                                            </td>
                                                            <td>{{number_format($final_results->total_gpa_point,2,'.','')}}</td>
                                                            <?php
                                                            }else{ ?>
                                                            <td>-</td>
                                                            <td>-</td>
                                                            <td>-</td>
                                                            <td>-</td>
                                                            <?php
                                                            }
                                                            }
                                                            ?>
                                                            <td>{{$totalSumSub}}</td>
                                                            @php
                                                                if($totalSubjectFail > 0){
                                                                }else{
                                                                    $totalSumSub = $totalSumSub / count($assinged_exam_types);
                                                                }
                                                            @endphp
                                                        </tr>
                                                    @endforeach
                                                    @php
                                                        $colspan = 4 + count($assinged_exam_types) * 2;
                                                        if ($optional_subject_setup!='') {
                                                        $col_for_result=3;
                                                        } else {
                                                            $col_for_result=2;
                                                        }
                                                    @endphp
                                                    <tr class="text-center">
                                                        <th>@lang('reports.result')</th>
                                                        @php
                                                            $term_base_gpa  = 0;
                                                            $average_gpa  = 0;
                                                            $with_percent_average_gpa  = 0;
                                                            $optional_subject_total_gpa  = 0;
                                                            $optional_subject_total_above_gpa  = 0;
                                                            $without_additional_subject_total_gpa  = 0;
                                                            $with_additional_subject_addition  = 0;
                                                            $with_optional_percentage  = 0;
                                                            $total_with_optional_percentage  = 0;
                                                            $total_with_optional_subject_extra_gpa  = 0;
                                                            $with_optional_subject_extra_gpa  = 0;
                                                            $optional_subject_mark= 0;
                                                        @endphp
                                                        @foreach($assinged_exam_types as $assinged_exam_type)
                                                            @php
                                                                $exam_type = App\SmExamType::examType($assinged_exam_type);
                                                                $term_base_gpa=termWiseGpa($assinged_exam_type, $student_id);
                                                                $with_percent_average_gpa +=$term_base_gpa;

                                                                $term_base_full_mark=termWiseTotalMark($assinged_exam_type, $student_id);
                                                                $average_gpa+=$term_base_full_mark;

                                                                if($optional_subject_setup!='' && $student_optional_subject!=''){

                                                                    $optional_subject_gpa = optionalSubjectFullMark($assinged_exam_type,$student_id,@$optional_subject_setup->gpa_above,"optional_sub_gpa");
                                                                    $optional_subject_total_gpa += $optional_subject_gpa;

                                                                    $optional_subject_above_gpa = optionalSubjectFullMark($assinged_exam_type,$student_id,@$optional_subject_setup->gpa_above,"with_optional_sub_gpa");
                                                                    $optional_subject_total_above_gpa += $optional_subject_above_gpa;

                                                                    $without_subject_gpa = optionalSubjectFullMark($assinged_exam_type,$student_id,@$optional_subject_setup->gpa_above,"without_optional_sub_gpa");
                                                                    $without_additional_subject_total_gpa += $without_subject_gpa;

                                                                    $with_additional_subject_gpa = termWiseAddOptionalMark($assinged_exam_type,$student_id,@$optional_subject_setup->gpa_above);
                                                                    $with_additional_subject_addition += $with_additional_subject_gpa;

                                                                    $with_optional_subject_extra_gpa = termWiseTotalMark($assinged_exam_type,$student_id,"optional_subject");
                                                                    $total_with_optional_subject_extra_gpa += $with_optional_subject_extra_gpa;

                                                                    $with_optional_percentages=termWiseGpa($assinged_exam_type, $student_id, $with_optional_subject_extra_gpa);
                                                                    $total_with_optional_percentage += $with_optional_percentages;

                                                            }
                                                        @endphp
                                                        <th colspan="4"> @lang('reports.average_gpa') : 
                                                            {{number_format($term_base_full_mark,2,'.','')}}
                                                            </br>
                                                            {{$exam_type->title}} ({{$exam_type->percentage}}%) : {{number_format($term_base_gpa,2,'.','')}}
                                                            @if($optional_subject_setup!='' && $student_optional_subject!='')
                                                                <hr>
                                                                @lang('reports.with_optional') : 
                                                                {{number_format($with_optional_subject_extra_gpa,2,'.','')}}
                                                                </br>
                                                                @lang('reports.with_optional') ({{$exam_type->percentage}}%) : 
                                                                {{number_format($with_optional_percentages,2,'.','')}}
                                                            @endif
                                                        </th>
                                                        @endforeach
                                                        <th>
                                                            {{number_format($average_gpa,2,'.','')}}
                                                            </br>
                                                            {{number_format($with_percent_average_gpa, 2, '.', '')}}
                                                            @if($optional_subject_setup!='' && $student_optional_subject!='')
                                                                <hr>
                                                                {{number_format($total_with_optional_subject_extra_gpa, 2, '.', '')}}
                                                                </br>
                                                                {{number_format($total_with_optional_percentage, 2, '.', '')}}
                                                            @endif
                                                        </th>
                                                    </tr>
                                                    {{-- Total Marks Start --}}
                                                    <tr>
                                                        <td colspan="{{$colspan / $col_for_result - 1}}"  class="text-center">@lang('exam.total_marks')</td>
                                                        @if ($optional_subject_setup!='' && $student_optional_subject!='')
                                                            <td colspan="{{$colspan / $col_for_result + 7}}" class="text-center" style="padding:10px; font-weight:bold">{{$total_marks}} @lang('reports.out_of') {{$all_exam_type_full_mark}}</td>
                                                        @else
                                                            <td colspan="{{$colspan / $col_for_result + 9}}" class="text-center" style="padding:10px; font-weight:bold">{{$total_marks}} @lang('reports.out_of') {{$all_exam_type_full_mark}}</td>
                                                        @endif
                                                    </tr>
                                                    {{-- Total Marks End --}}
                                                    <tr>
                                                        @if($optional_subject_setup!='' && $student_optional_subject!='')
                                                            <td colspan="{{$colspan / $col_for_result - 1}}"  class="text-center">
                                                                @lang('reports.optional_total_gpa')
                                                                    <hr>
                                                                @lang('reports.gpa_above') {{@$optional_subject_setup->gpa_above}}
                                                            </td>
                                                            <td colspan="{{$colspan / $col_for_result + 7}}"
                                                                class="text-center"
                                                                style="padding:10px; font-weight:bold">
                                                                {{$optional_subject_total_gpa}}
                                                                <hr>
                                                                {{$optional_subject_total_above_gpa}}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    @php
                                                        if ($optional_subject_mark) {
                                                            $total_marks_without_optional=$total_marks-$optional_subject_mark;
                                                            $op_subject_count=count($subjects)-1;
                                                        }else{
                                                            $total_marks_without_optional=$total_marks;
                                                            $op_subject_count=count($subjects);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td colspan="{{$colspan / $col_for_result - 1}}" class="text-center">@lang('reports.total_gpa')</td>
                                                        @if ($optional_subject_setup!='' && $student_optional_subject!='')
                                                        <td colspan="4" class="text-center" style="padding:10px;">
                                                            {{number_format($total_with_optional_percentage,2,'.','')}}
                                                        </td>
                                                        <td colspan="3" class="text-center" style="padding:10px;">@lang('reports.without_additional_grade')</td>
                                                        <td colspan="2" class="text-center" style="padding:10px;">
                                                            {{number_format($with_percent_average_gpa,2,'.','')}}
                                                        </td>
                                                        @else
                                                            <td colspan="{{$colspan / $col_for_result + 9}}"
                                                                class="text-center" style="padding:10px;">
                                                                {{gradeName(number_format(termWiseFullMark($assinged_exam_types,$student_id),2,'.',''))}}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    {{-- Total Gpa Start --}}
                                                    <tr>
                                                        <td colspan="{{$colspan / $col_for_result - 1}}" class="text-center">@lang('exam.total_grade')</td>
                                                        @if ($optional_subject_setup!='' && $student_optional_subject!='')
                                                            <td colspan="4" class="text-center"
                                                                style="padding:10px; font-weight:bold">
                                                                {{gradeName(number_format($total_with_optional_percentage,2,'.',''))}}
                                                            </td>
                                                        <td colspan="3" class="text-center" style="padding:10px;">@lang('reports.without_additional_gpa')</td>
                                                        <td colspan="2" class="text-center" style="padding:10px;">
                                                            {{gradeName(number_format($with_percent_average_gpa,2,'.',''))}}
                                                        </td>
                                                        @else
                                                            <td colspan="{{$colspan / $col_for_result + 9}}"
                                                                class="text-center"
                                                                style="padding:10px; font-weight:bold">
                                                                {{number_format(termWiseFullMark($assinged_exam_types,$student_id),2,'.','')}}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    {{-- Total Gpa End --}}
                                                    {{-- Remark Start --}}
                                                    <tr>
                                                        @if($optional_subject_setup!='' && $student_optional_subject!='')
                                                            <td colspan="{{$colspan / $col_for_result - 1}}" class="text-center">@lang('reports.remarks')</td>
                                                            <td colspan="{{$colspan / $col_for_result + 7}}" class="text-center" style="padding:10px; font-weight:bold">
                                                                {{remarks(number_format($total_with_optional_percentage,2,'.',''))}}
                                                            </td>
                                                        @else
                                                            <td colspan="{{$colspan / $col_for_result - 1}}" class="text-center">@lang('reports.remarks')</td>
                                                            <td colspan="{{$colspan / $col_for_result + 9}}" class="text-center" style="padding:10px; font-weight:bold">
                                                                {{remarks(number_format(termWiseFullMark($assinged_exam_types,$student_id),2,'.',''))}}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    {{-- Remark End --}}
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
        </section>
    @endif
@endsection
