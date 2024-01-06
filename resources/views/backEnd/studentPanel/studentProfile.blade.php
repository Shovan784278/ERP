@extends('backEnd.master')

@section('title')
    @lang('student.my_profile')
@endsection

@section('mainContent')
    @php  @$setting = generalSetting(); if(!empty(@$setting->currency_symbol)){ @$currency = @$setting->currency_symbol; }else{ @$currency = '$'; }   @endphp
    <section class="student-details">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Start Student Meta Information -->
                    <div class="main-title">
                        <h3 class="mb-20">@lang('student.welcome_to') <strong> {{@$student_detail->full_name}}</strong>
                        </h3>
                    </div>

                </div>
            </div>
            <div class="row">
                @if(userPermission(2))
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('student_subject') }}" class="d-block">
                            <div class="white-box single-summery">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>@lang('common.subject')</h3>
                                        <p class="mb-0">@lang('student.total_subject')</p>
                                    </div>
                                    <h1 class="gradient-color2">

                                        @if(isset($totalSubjects))
                                            {{count(@$totalSubjects)}}
                                        @endif
                                    </h1>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if(userPermission(3))
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('student_noticeboard') }}" class="d-block">
                            <div class="white-box single-summery">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>@lang('student.notice')</h3>
                                        <p class="mb-0">@lang('student.total_notice')</p>
                                    </div>
                                    <h1 class="gradient-color2">
                                        @if(isset($totalNotices))
                                            {{count(@$totalNotices)}}
                                        @endif
                                    </h1>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if(userPermission(4))
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('student_exam_schedule') }}" class="d-block">
                            <div class="white-box single-summery">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>@lang('student.exam')</h3>
                                        <p class="mb-0">@lang('student.total_exam')</p>
                                    </div>
                                    <h1 class="gradient-color2">
                                        @if(isset($exams))
                                            {{count(@$exams)}}
                                        @endif
                                    </h1>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if(userPermission(5))
                    <div class="col-lg-3 col-md-6">
                        <div class="white-box single-summery">
                            @if(moduleStatusCheck('OnlineExam'))
                                <a href="{{ route('om_student_online_exam') }}" class="d-block">
                                    @else
                                        <a href="{{ route('student_online_exam') }}" class="d-block">
                                            @endif
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h3>@lang('student.online_exam')</h3>
                                                    <p class="mb-0">@lang('student.total_online_exam')</p>
                                                </div>
                                                <h1 class="gradient-color2">
                                                    @if(isset($online_exams))
                                                        {{count(@$online_exams)}}
                                                    @endif
                                                </h1>
                                            </div>
                                        </a>
                        </div>

                    </div>
                @endif
                @if(userPermission(6))

                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('student_teacher') }}" class="d-block">
                            <div class="white-box single-summery">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>@lang('student.teachers')</h3>
                                        <p class="mb-0">@lang('student.total_teachers')</p>
                                    </div>
                                    <h1 class="gradient-color2"> @if(isset($teachers))
                                            {{count(@$teachers)}}
                                        @endif</h1>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if(userPermission(7))
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('student_book_issue') }}" class="d-block">
                            <div class="white-box single-summery">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>@lang('student.issued_book')</h3>
                                        <p class="mb-0">@lang('student.total_issued_book')</p>
                                    </div>
                                    <h1 class="gradient-color2">
                                        @if(isset($issueBooks))
                                            {{count(@$issueBooks)}}
                                        @endif
                                    </h1>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if(userPermission(8))
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('student_homework') }}" class="d-block">
                            <div class="white-box single-summery">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>@lang('student.pending_home_work')</h3>
                                        <p class="mb-0">@lang('student.total_pending_home_work')</p>
                                    </div>
                                    <h1 class="gradient-color2">
                                        @if(isset($homeworkLists))
                                            {{count(@$homeworkLists)}}
                                        @endif
                                    </h1>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if(userPermission(9))
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('student_my_attendance') }}" class="d-block">
                            <div class="white-box single-summery">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>@lang('student.attendance_in_current_month')</h3>
                                        <p class="mb-0">@lang('student.total_attendance_in_current_month')</p>
                                    </div>
                                    <h1 class="gradient-color2">
                                        @if(isset($attendances))
                                            {{count(@$attendances)}}
                                        @endif
                                    </h1>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif


            </div>
            @if(userPermission(10))
                <section class="mt-50">
                    <div class="container-fluid p-0">
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="main-title">
                                            <h3 class="mb-30">@lang('student.calendar')</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="white-box">
                                            <div class='common-calendar'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            @endif
        </div>
        </div>
    </section>

    <div id="fullCalModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span
                                class="sr-only">close</span></button>
                    <h4 id="modalTitle" class="modal-title"></h4>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="There are no image" id="image" height="150" width="auto">
                    <div id="modalBody"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <?php

    $count_event = 0;
    @$calendar_events = array();

    foreach ($holidays as $k => $holiday) {

        @$calendar_events[$k]['title'] = $holiday->holiday_title;

        $calendar_events[$k]['start'] = $holiday->from_date;

        $calendar_events[$k]['end'] = Carbon::parse($holiday->to_date)->addDays(1)->format('Y-m-d');

        $calendar_events[$k]['description'] = $holiday->details;

        $calendar_events[$k]['url'] = $holiday->upload_image_file;

        $count_event = $k;
        $count_event++;
    }



    foreach ($events as $k => $event) {
        @$calendar_events[$count_event]['title'] = $event->event_title;

        $calendar_events[$count_event]['start'] = $event->from_date;

        $calendar_events[$count_event]['end'] = Carbon::parse($event->to_date)->addDays(1)->format('Y-m-d');
        $calendar_events[$count_event]['description'] = $event->event_des;
        $calendar_events[$count_event]['url'] = $event->uplad_image_file;
        $count_event++;
    }





    ?>
@endsection
@section('script')

    <script type="text/javascript">
        /*-------------------------------------------------------------------------------
           Full Calendar Js
        -------------------------------------------------------------------------------*/
        if ($('.common-calendar').length) {
            $('.common-calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                eventClick: function (event, jsEvent, view) {
                    $('#modalTitle').html(event.title);
                    $('#modalBody').html(event.description);
                    $('#image').attr('src', event.url);
                    $('#fullCalModal').modal();
                    return false;
                },
                height: 650,
                events: <?php echo json_encode($calendar_events);?> ,
            });
        }


    </script>

@endsection
