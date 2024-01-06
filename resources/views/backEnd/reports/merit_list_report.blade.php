@extends('backEnd.master')
@section('title')
@lang('reports.merit_list_report')
@endsection
@section('mainContent')
<style>
    tr{
        border: 1px solid #351681;
    }
    table.meritList{
        width: 100%;
        border: 1px solid #351681;
    }
    table.meritList th{
        padding: 2px;
        text-transform: capitalize !important;
        font-size: 11px !important;  
        text-align: center !important;
        border: 1px solid #351681;
        text-align: center; 

    }
    table.meritList td{
        padding: 2px;
        font-size: 11px !important;
        border: 1px solid #351681;
        text-align: center !important;
    } 
 .single-report-admit table tr td { 
    padding: 5px 5px !important;

        border: 1px solid #351681;
} 
.single-report-admit table tr th { 
    padding: 5px 5px !important;
    vertical-align: middle;
        border: 1px solid #351681;
}
.main-wrapper {
     display: block !important ;  
}
#main-content {
    width: auto !important;
}
hr{
    margin: 0px;
}
.gradeChart tbody td{
        padding: 0;
        border: 1px solid #351681;
    }
    table.gradeChart{
        padding: 0px;
        margin: 0px;
        width: 60%;
        text-align: right; 
    }
    table.gradeChart thead th{
        border: 1px solid #000000;
        border-collapse: collapse;
        text-align: center !important;
        padding: 0px;
        margin: 0px;
        font-size: 9px;
    }
    table.gradeChart tbody td{
        border: 1px solid #000000;
        border-collapse: collapse;
        text-align: center !important;
        padding: 0px;
        margin: 0px;
        font-size: 9px;
    }

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
</style>
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
                    <h3 class="mb-30">@lang('common.select_criteria')</h3>
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
                        <div class="col-lg-4 mt-30-md md_mb_20">
                            <select class="w-100 bb niceSelect form-control{{ $errors->has('exam') ? ' is-invalid' : '' }}" name="exam">
                                <option data-display="@lang('reports.select_exam')*" value="">@lang('reports.select_exam') *</option>
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
                        <div class="col-lg-4 mt-30-md md_mb_20">
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
                        <div class="col-lg-4 mt-30-md md_mb_20" id="select_section_div">
                            <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }} select_section" id="select_section" name="section">
                                <option data-display="@lang('common.select_section')*" value="">@lang('common.select_section') *</option>
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
                <a href="{{route('merit-list/print', [$InputExamId, $InputClassId, $InputSectionId])}}" class="primary-btn small fix-gr-bg pull-right" target="_blank"><i class="ti-printer"> </i> @lang('reports.print')</a>
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
                                            <div class="offset-2">
                                            </div>
                                            <div class="col-lg-2">
                                            <img class="logo-img" src="{{ generalSetting()->logo }}" alt="{{generalSetting()->school_name}}">
                                            </div>
                                            <div class="col-lg-6 ml-30">
                                                <h3 class="text-white"> {{isset(generalSetting()->school_name)?generalSetting()->school_name:'Infix School Management ERP'}} </h3> 
                                                <p class="text-white mb-0"> {{isset(generalSetting()->address)?generalSetting()->address:'Infix School Address'}} </p>
                                                <p class="text-white mb-0">@lang('common.email'):  {{isset(generalSetting()->email)?generalSetting()->email:'admin@demo.com'}},   @lang('common.phone'):  {{isset(generalSetting()->phone)?generalSetting()->phone:'+8801841412141'}} </p> 
                                            </div>
                                            <div class="offset-2"></div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-12">
                                            <div class="row">
                                                {{-- start col-lg-8 --}}
                                                <div class="col-lg-8"> 
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h3>@lang('reports.order_of_merit_list')</h3> 
                                                            <p class="mb-0">
                                                                @lang('reports.academic_year') : <span class="primary-color fw-500">{{@generalSetting()->academic_Year->year}}</span>
                                                            </p>
                                                            <p class="mb-0">
                                                                @lang('reports.exam') : <span class="primary-color fw-500">{{$exam_name}}</span>
                                                            </p>
                                                            <p class="mb-0">
                                                                @lang('common.class') : <span class="primary-color fw-500">{{$class_name}}</span>
                                                            </p>
                                                            <p class="mb-0">
                                                                @lang('common.section') : <span class="primary-color fw-500">{{$section->section_name}}</span>
                                                            </p>  
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h3>@lang('common.subjects')</h3> 
                                                                @foreach($assign_subjects as $subject)
                                                                    <p class="mb-0">
                                                                        <span class="primary-color fw-500">{{$subject->subject->subject_name}}</span>
                                                                    </p>
                                                                @endforeach  
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- end col-lg-8 --}}
                                                {{-- sm_marks_grades --}}
                                                <div class="col-lg-4 text-black"> 
                                                    {{-- @php $marks_grade=DB::table('sm_marks_grades')->where('academic_id', getAcademicId())->orderBy('gpa','desc')->get(); @endphp
                                                    @if(@$marks_grade)
                                                        <table class="table  table-bordered table-striped " id="grade_table">
                                                            <thead>
                                                                <tr>
                                                                    <th>@lang('reports.staring')</th>
                                                                    <th>@lang('reports.ending')</th>
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
                                                    @endif  --}}
                                                </div>
                                                {{--end sm_marks_grades --}}
                                            </div> 
                                        </div>
                                        <h3 class="primary-color fw-500 text-center">@lang('reports.order_of_merit_list')</h3>
                                        {{-- Mark Distributation Table Start --}}
                                            <div class="table-responsive">
                                                <table class="w-100 mt-30 mb-20 table table-bordered meritList">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('common.name')</th>
                                                            <th>@lang('student.admission_no')</th>
                                                            <th>@lang('student.roll_no')</th>
                                                            <th>@lang('reports.position')</th>
                                                            <th>@lang('exam.total_mark')</th>
                                                            <th>@lang('reports.gpa_without_additional')</th>
                                                            <th>@lang('exam.gpa')</th>
                                                            @foreach($subjectlist as $subject)
                                                                <th>{{$subject}}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $i=1; $subject_mark = []; $total_student_mark = 0; $total_student_mark_optional = 0; @endphp
                                                            @foreach($allresult_data as $key=>$row) 
                                                                @php
                                                                    $student_detail = App\SmStudent::where('id','=',$row->student_id)->first();
                                                                        $optional_subject='';
                                                                        $get_optional_subject=App\SmOptionalSubjectAssign::where('student_id','=',$student_detail->id)
                                                                                            ->where('session_id','=',$student_detail->session_id)
                                                                                            ->where('academic_id', getAcademicId())
                                                                                            ->first();
                                                                        if ($get_optional_subject!='') {
                                                                            $optional_subject=$get_optional_subject->subject_id;
                                                                        }
                                                                        $markslist = explode(',',$row->marks_string);
                                                                        $get_subject_id = explode(',',$row->subjects_id_string);
                                                                        $count=0;
                                                                        $additioncheck=[];
                                                                        $subject_mark=[];
                                                                @endphp
                                                                @foreach($markslist as $mark)   
                                                                    @if(App\SmOptionalSubjectAssign::is_optional_subject($row->student_id,$get_subject_id[$count]))
                                                                        @php
                                                                            $additioncheck[] = $mark;
                                                                        @endphp
                                                                    @endif
                                                                    @php
                                                                        if(App\SmOptionalSubjectAssign::is_optional_subject($row->student_id,$get_subject_id[$count])){
                                                                            $special_mark[$row->student_id]=$mark;
                                                                        }
                                                                        $count++;
                                                                    @endphp 

                                                                    @php
                                                                        $subject_mark[]= $mark;
                                                                        $total_student_mark = $total_student_mark + $mark; 
                                                                    @endphp 
                                                                @endforeach
                                                        <tr>
                                                            <td>{{$row->student_name}}</td>
                                                            <td>{{$row->admission_no}}</td>
                                                            <td>{{$row->studentinfo->roll_no}}</td>
                                                            <td>{{$key+1}}</td>
                                                            <td>{{$row->total_marks}}</td>
                                                            <td>{{number_format($row->gpa_point, 2, '.', '')}}</td>
                                                            <td>
                                                                <?php
                                                                    if($row->result == $failgpaname->gpa){
                                                                        echo number_format($failgpa, 2, '.', '');
                                                                    }else{
                                                                        if($row->result > $maxGpa){
                                                                            echo number_format($maxGpa, 2, '.', '');
                                                                        }else{
                                                                            echo $row->result;
                                                                        }
                                                                    }
                                                                ?>
                                                            </td>
                                                            @foreach($markslist as $mark)
                                                                <td> {{!empty($mark)?$mark:0}}</td>
                                                            @endforeach
                                                        </tr>
                                                            @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        {{-- Mark Distributation Table End --}}
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
