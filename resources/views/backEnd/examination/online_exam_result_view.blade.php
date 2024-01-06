@extends('backEnd.master')
@section('title')
@lang('exam.result_view')
@endsection

@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('exam.examinations') </h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('exam.examinations')</a>
                <a href="{{route('online-exam')}}">@lang('exam.online_exam')</a>
                <a href="{{route('online_exam_result', [$online_exam_question->id])}}">@lang('exam.result_view')</a>
            </div>
        </div>
    </div>
</section>

<section class="mt-20">
    <div class="container-fluid p-0">
        <div class="row mt-40">
            <div class="col-lg-6 col-md-6">
                <div class="main-title">
                    <h3 class="mb-0">@lang('exam.result_view')</h3>
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
                <table id="table_id" class="display school-table school-table-style" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('student.admission_no')</th>
                            <th>@lang('student.student')</th>
                            <th>@lang('common.class_Sec')</th>
                            <th>@lang('exam.exam')</th>
                            <th>@lang('common.subject')</th>
                            <th>@lang('exam.total_marks')</th>
                            <th>@lang('exam.obtained_marks')</th>
                            <th>@lang('reports.result')</th>
                        </tr>
                    </thead>
                    <tbody>
                
                            @foreach($students as $student)
                            <tr>
                                <td>{{$student->admission_no}}</td>
                                <td>{{$student->full_name}}</td>
                                <td>{{$student->class!=""?$student->class->class_name:""}} ({{$student->section!=""?$student->section->section_name:""}})</td>
                                <td>{{$online_exam_question->title}}</td>
                                <td>{{$online_exam_question->subject!=""?$online_exam_question->subject->subject_name:""}}</td>
                                <td>{{$total_marks}}</td>
                                <td>
                                    @if(in_array($student->id, $present_students))
                                        @php
                                            $obtained_marks = App\SmOnlineExam::obtainedMarks($online_exam_question->id, $student->id);
                                            if($obtained_marks->status == 1){
                                                echo "Waiting for marks";
                                            }else{
                                                echo $obtained_marks->total_marks;
                                            }
                                        @endphp
                                    @else
                                        @lang('exam.absent')
                                    @endif
                                    
                                </td>
                                <td>
                                    @if(in_array($student->id, $present_students))
                                    @php
                                    $result = $obtained_marks->total_marks * 100 / $total_marks;
                                    @endphp
                                    @if ($obtained_marks->status == 1)
                                    @lang('exam.marks_waiting_for')
                                    @else
                                    @if($result >= $online_exam_question->percentage)
                                        @lang('exam.pass')
                                    @else
                                        @lang('exam.fail')
                                    @endif
                                    @endif
                                        {{-- @php
                                            if($obtained_marks->status == 1){
                                                echo "Waiting for marks";
                                            }else{
                                                
                                                $result = $obtained_marks->total_marks * 100 / $total_marks;
                                                if($result >= $online_exam_question->percentage){
                                                    echo "Pass";  
                                                }else{
                                                    echo "Fail";
                                                }
                                            }
                                        @endphp --}}
                                    @else

                                        @lang('exam.absent')
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


@endsection
