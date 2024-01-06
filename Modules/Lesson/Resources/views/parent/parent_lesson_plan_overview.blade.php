@extends('backEnd.master')
@section('title')
    @lang('lesson::lesson.lesson_plan_overview')
@endsection
@section('mainContent')


    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{$student_detail->full_name}}-@lang('lesson::lesson.lesson_plan_overview')</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="#">@lang('lesson::lesson.lesson')</a>
                    <a href="#">@lang('lesson::lesson.lesson_plan_overview')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">

        </div>
        <div class="row mt-40">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">

                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>@lang('lesson::lesson.lesson')</th>
                            <th>@lang('lesson::lesson.topic')</th>
                            <th>
                                @if(generalSetting()->sub_topic_enable)
                                    @lang('lesson::lesson.sup_topic')
                                @else
                                    @lang('common.note')
                                @endif
                            </th>
                            <th>@lang('lesson::lesson.completed_date') </th>
                            <th>@lang('lesson::lesson.upcoming_date') </th>
                            <th>@lang('common.status')</th>

                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($lessonPlanner as $data)


                            <tr>
                                <td>{{@$data->lessonName !=""?@$data->lessonName->lesson_title:""}}</td>

                                <td>
                                    @if(count($data->topics) > 0)
                                    @foreach ($data->topics as $topic)
                                    {{$topic->topicName->topic_title}} </br>
                                    @endforeach
                                    @else
                                        {{$data->topicName->topic_title}}
                                    @endif

                                </td>

                                <td>
                                    @if(generalSetting()->sub_topic_enable)
                                    @if (count($data->topics) > 0)
                                    @foreach ($data->topics as $topic)
                                    {{$topic->sub_topic_title}} </br>
                                    @endforeach
                                    @else
                                        {{$data->sub_topic}}
                                    @endif
                                    @else
                                        {{$data->note}}
                                    @endif
                                </td>
                                <td>


                                    {{@$data->competed_date !=""?@$data->competed_date:""}}<br>


                                </td>
                                <td>


                                    @if(date('Y-m-d')< $data->lesson_date && $data->competed_date=="")
                                        @lang('lesson::lesson.upcoming')     ({{$data->lesson_date}})<br>
                                    @elseif($data->competed_date=="")
                                        @lang('lesson::lesson.assigned_date')({{$data->lesson_date}})
                                        <br>
                                    @else

                                    @endif

                                </td>
                                <td>

                                    @if(date('Y-m-d')< $data->lesson_date && $data->competed_date=="")
                                        @lang('lesson::lesson.upcoming') <br>
                                    @elseif($data->competed_date=="")
                                        @lang('lesson::lesson.incomplete')
                                        <br>
                                    @else
                                        <strong>@lang('lesson::lesson.completed')</strong> <br>
                                    @endif

                                </td>


                            </tr>
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
