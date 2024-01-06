@extends('backEnd.master')
@section('title')
    @lang('academics.class_routine')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('academics.class_routine')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a
                        href="{{ route('parent_class_routine', [$student_detail->id]) }}">@lang('academics.class_routine')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="mt-20">
        <div class="row">
            <!-- Start Student Details -->
            <div class="col-lg-12 student-details up_admin_visitor ">
                <ul class="nav nav-tabs tabs_scroll_nav ml-0" role="tablist">

                    @foreach ($records as $key => $record)
                        <li class="nav-item">
                            <a class="nav-link @if ($key == 0) active @endif " href="#tab{{ $key }}" role="tab"
                                data-toggle="tab">{{ $record->class->class_name }} ({{ $record->section->section_name }})
                            </a>
                        </li>
                    @endforeach

                </ul>


                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Start Fees Tab -->
                    @foreach ($records as $key => $record)
                        <div role="tabpanel" class="tab-pane fade  @if($key== 0) active show @endif" id="tab{{$key}}">
                            <div class="white-box">
                                <div class="container-fluid p-0">
                                    <div class="row ">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="main-title">
                                             
                                            </div>
                                        </div>
                                        <div class="col-lg-6 pull-right mb-20">
                                            <a href="{{ route('classRoutinePrint', [$record->class_id, $record->section_id]) }}"
                                                class="primary-btn small fix-gr-bg pull-right" target="_blank"><i
                                                    class="ti-printer"> </i> @lang('common.print')</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table id="" class="display school-table" cellspacing="0"
                                                width="100%">
                                                <thead>
                                                    <tr>
                                                        @php
                                                            $height = 0;
                                                            $tr = [];
                                                        @endphp
                                                        @foreach ($sm_weekends as $sm_weekend)
                                                            @php
                                                                $parentRoutine = App\SmWeekend::studentClassRoutineFromRecord($record->class_id, $record->section_id, $sm_weekend->id);
                                                            @endphp
                                                            @if ($parentRoutine->count() > $height)
                                                                @php
                                                                    $height = $parentRoutine->count();
                                                                @endphp
                                                            @endif

                                                            <th>{{ @$sm_weekend->name }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @php
                                                        $used = [];
                                                        $tr = [];
                                                        
                                                    @endphp

                                                    @foreach ($sm_weekends as $sm_weekend)
                                                        @php
                                                            $parentRoutine = App\SmWeekend::studentClassRoutineFromRecord($record->class_id, $record->section_id, $sm_weekend->id);
                                                            $i = 0;
                                                        @endphp
                                                        @foreach ($parentRoutine as $routine)
                                                            @php
                                                                if (!in_array($routine->id, $used)) {
                                                                    $tr[$i][$sm_weekend->name][$loop->index]['subject'] = $routine->subject ? $routine->subject->subject_name : '';
                                                                    $tr[$i][$sm_weekend->name][$loop->index]['subject_code'] = $routine->subject ? $routine->subject->subject_code : '';
                                                                    $tr[$i][$sm_weekend->name][$loop->index]['class_room'] = $routine->classRoom ? $routine->classRoom->room_no : '';
                                                                    $tr[$i][$sm_weekend->name][$loop->index]['teacher'] = $routine->teacherDetail ? $routine->teacherDetail->full_name : '';
                                                                    $tr[$i][$sm_weekend->name][$loop->index]['start_time'] = $routine->start_time;
                                                                    $tr[$i][$sm_weekend->name][$loop->index]['end_time'] = $routine->end_time;
                                                                    $tr[$i][$sm_weekend->name][$loop->index]['is_break'] = $routine->is_break;
                                                                    $used[] = $routine->id;
                                                                }
                                                                
                                                            @endphp
                                                        @endforeach

                                                        @php
                                                            
                                                            $i++;
                                                        @endphp

                                                    @endforeach

                                                    @for ($i = 0; $i < $height; $i++)
                                                        <tr>
                                                            @foreach ($tr as $days)
                                                                @foreach ($sm_weekends as $sm_weekend)
                                                                    <td>
                                                                        @php
                                                                            $classes = gv($days, $sm_weekend->name);
                                                                        @endphp
                                                                        @if ($classes && gv($classes, $i))
                                                                            @if ($classes[$i]['is_break'])
                                                                                <strong> @lang('academics.break') </strong>

                                                                                <span class="">
                                                                                    ({{ date('h:i A', strtotime(@$classes[$i]['start_time'])) }}
                                                                                    -
                                                                                    {{ date('h:i A', strtotime(@$classes[$i]['end_time'])) }})
                                                                                    <br> </span>
                                                                            @else
                                                                                <span class="">
                                                                                    {{ date('h:i A', strtotime(@$classes[$i]['start_time'])) }}
                                                                                    -
                                                                                    {{ date('h:i A', strtotime(@$classes[$i]['end_time'])) }}
                                                                                    <br> </span>
                                                                                <span class=""> <strong>
                                                                                        {{ $classes[$i]['subject'] }}
                                                                                    </strong>
                                                                                    ({{ $classes[$i]['subject_code'] }}) <br>
                                                                                </span>
                                                                                @if ($classes[$i]['class_room'])
                                                                                    <span class="">
                                                                                        <strong>@lang('academics.room')
                                                                                            :</strong>
                                                                                        {{ $classes[$i]['class_room'] }} <br>
                                                                                    </span>
                                                                                @endif
                                                                                @if ($classes[$i]['teacher'])
                                                                                    <span class="">
                                                                                        {{ $classes[$i]['teacher'] }} <br>
                                                                                    </span>
                                                                                @endif


                                                                            @endif

                                                                        @endif

                                                                    </td>
                                                                @endforeach



                                                            @endforeach
                                                        </tr>

                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- End Fees Tab -->
                </div>
            </div>
            <!-- End Student Details -->
        </div>

    </section>
@endsection
