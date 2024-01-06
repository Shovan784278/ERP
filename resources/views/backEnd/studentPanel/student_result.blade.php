@extends('backEnd.master')
@section('title')
    @lang('reports.result')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('exam.examinations')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('exam.examinations')</a>
                    <a href="{{ route('student_result') }}">@lang('reports.result')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="student-details">
        <ul class="nav nav-tabs tabs_scroll_nav ml-0" role="tablist">

            @foreach ($records as $key => $record)
                <li class="nav-item">
                    <a class="nav-link @if ($key == 0) active @endif " href="#tab{{ $key }}" role="tab"
                        data-toggle="tab">{{ $record->class->class_name }} ({{ $record->section->section_name }}) </a>
                </li>
            @endforeach

        </ul>
        <div class="tab-content mt-40">
            @foreach ($records as $key => $record)
                <div role="tabpanel" class="tab-pane fade  @if ($key == 0) active show @endif" id="tab{{ $key }}">
                    <div class="container-fluid p-0 ">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="no-search no-paginate no-table-info mb-2">
                                    @foreach ($exam_terms as $exam)
                                        @php
                                            $today = date('Y-m-d H:i:s');
                                            $get_results = App\SmStudent::getExamResult(@$exam->id, @$record);
                                        @endphp
                                        @if ($get_results)
                                            <div class="main-title">
                                                <h3 class="mb-0">{{ @$exam->title }}</h3>
                                            </div>
                                            @php
                                                $grand_total = 0;
                                                $grand_total_marks = 0;
                                                $result = 0;
                                                $temp_grade = [];
                                                $total_gpa_point = 0;
                                                $total_subject = count($get_results);
                                                $optional_subject = 0;
                                                $optional_gpa = 0;
                                                $onlyOptional = 0;
                                            @endphp
                                            @isset($exam->examSettings->publish_date)
                                                @if ($exam->examSettings->publish_date <= $today)
                                                    <table id="table_id" class="display school-table mb-5" cellspacing="0"
                                                        width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    @lang('common.date')
                                                                </th>
                                                                <th>
                                                                    @lang('exam.subject_full_marks')
                                                                </th>
                                                                <th>
                                                                    @lang('exam.obtained_marks')
                                                                </th>
                                                                <th>
                                                                    @lang('exam.grade')
                                                                </th>
                                                                <th>
                                                                    @lang('exam.gpa')
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($get_results as $mark)
                                                                @php
                                                                    if (!is_null($record->optionalSubjectSetup) && !is_null($record->optionalSubject)) {
                                                                        if ($mark->subject_id != @$record->optionalSubject->subject_id) {
                                                                            $temp_grade[] = $mark->total_gpa_grade;
                                                                        }
                                                                    } else {
                                                                        $temp_grade[] = $mark->total_gpa_grade;
                                                                    }
                                                                    $total_gpa_point += $mark->total_gpa_point;
                                                                    if (!is_null(@$record->optionalSubject)) {
                                                                        if (@$record->optionalSubject->subject_id == $mark->subject->id) {
                                                                            $total_gpa_point = $total_gpa_point - $mark->total_gpa_point;
                                                                            $onlyOptional = $mark->total_gpa_point;
                                                                        }
                                                                    }
                                                                    $temp_gpa[] = $mark->total_gpa_point;
                                                                    $get_subject_marks = subjectFullMark($mark->exam_type_id, $mark->subject_id);
                                                                    
                                                                    $subject_marks = App\SmStudent::fullMarksBySubject($exam->id, $mark->subject_id);
                                                                    $schedule_by_subject = App\SmStudent::scheduleBySubject($exam->id, $mark->subject_id, @$record);
                                                                    $result_subject = 0;
                                                                    $grand_total_marks += $get_subject_marks;
                                                                    if (@$mark->is_absent == 0) {
                                                                        $grand_total += @$mark->total_marks;
                                                                        if ($mark->marks < $subject_marks->pass_mark) {
                                                                            $result_subject++;
                                                                            $result++;
                                                                        }
                                                                    } else {
                                                                        $result_subject++;
                                                                        $result++;
                                                                    }
                                                                @endphp
                                                                <tr>
                                                                    <td>
                                                                        {{ !empty($schedule_by_subject->date) ? dateConvert($schedule_by_subject->date) : '' }}
                                                                    </td>
                                                                    <td>
                                                                        {{ @$mark->subject->subject_name }}
                                                                        ({{ @subjectFullMark($mark->exam_type_id, $mark->subject_id) }})
                                                                    </td>
                                                                    <td>
                                                                        {{ @$mark->total_marks }}
                                                                    </td>
                                                                    <td>
                                                                        {{ @$mark->total_gpa_grade }}
                                                                    </td>
                                                                    <td>
                                                                        {{ number_format(@$mark->total_gpa_point, 2, '.', '') }}
                                                                        @php
                                                                            if (@$record->optionalSubject!='') {
                                                                                if (@$record->optionalSubject->subject_id == $mark->subject->id) {
                                                                                    $optional_subject = 1;
                                                                                if ($mark->total_gpa_point > @$record->optionalSubjectSetup->gpa_above) {
                                                                                        $optional_gpa = @$record->optionalSubjectSetup->gpa_above;
                                                                                    echo "GPA Above ".@$record->optionalSubjectSetup->gpa_above;
                                                                                    echo "<hr>";
                                                                                    echo $mark->total_gpa_point  - @$record->optionalSubjectSetup->gpa_above;
                                                                                    } else {
                                                                                        echo "GPA Above ".@$record->optionalSubjectSetup->gpa_above;
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
                                                                    @lang('exam.grand_total'):
                                                                    {{ $grand_total }}/{{ $grand_total_marks }}
                                                                </th>
                                                                <th>
                                                                    @lang('exam.grade'):
                                                                    @php
                                                                        if (in_array($failgpaname->grade_name, $temp_grade)) {
                                                                            echo $failgpaname->grade_name;
                                                                        } else {
                                                                            $final_gpa_point = ($total_gpa_point + @$onlyOptional - @$record->optionalSubjectSetup->gpa_above)/  ($total_subject - $optional_subject);
                                                                            $average_grade = 0;
                                                                            $average_grade_max = 0;
                                                                            if ($result == 0 && $grand_total_marks != 0) {
                                                                                $gpa_point = number_format($final_gpa_point, 2, '.', '');
                                                                                if ($gpa_point >= $maxgpa) {
                                                                                    $average_grade_max = App\SmMarksGrade::where('school_id', Auth::user()->school_id)
                                                                                        ->where('academic_id', getAcademicId())
                                                                                        ->where('from', '<=', $maxgpa)
                                                                                        ->where('up', '>=', $maxgpa)
                                                                                        ->first('grade_name');
                                                                        
                                                                                    echo @$average_grade_max->grade_name;
                                                                                } else {
                                                                                    $average_grade = App\SmMarksGrade::where('school_id', Auth::user()->school_id)
                                                                                        ->where('academic_id', getAcademicId())
                                                                                        ->where('from', '<=', $final_gpa_point)
                                                                                        ->where('up', '>=', $final_gpa_point)
                                                                                        ->first('grade_name');
                                                                                    echo @$average_grade->grade_name;
                                                                                }
                                                                            } else {
                                                                                echo $failgpaname->grade_name;
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
                                                                            if($float_final_gpa_point_add >= $maxgpa){
                                                                                echo $maxgpa;
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
                                                                        if($float_final_gpa_point >= $maxgpa){
                                                                            echo number_format($maxgpa,2);
                                                                        }else {
                                                                            echo number_format($float_final_gpa_point,2);
                                                                        }
                                                                    @endphp
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                @endif
                                            @endisset
                                        @endif
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>       

    </section>
@endsection
