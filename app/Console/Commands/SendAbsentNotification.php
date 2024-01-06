<?php

namespace App\Console\Commands;

use App\SmSchool;
use App\SmSubjectAttendance;
use Illuminate\Console\Command;
use Modules\StudentAbsentNotification\Entities\AbsentNotificationTimeSetup;

class SendAbsentNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absent_notification:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send sms for student absent';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set(timeZone());
        $current_time=date('H:i');

        $schools= SmSchool::get();
        foreach($schools as $school){
            $setups = AbsentNotificationTimeSetup::where('active_status',1)
                    ->where('time_from', '<=', $current_time)
                    ->where('school_id', $school->id)
                    ->get();

            foreach($setups as $setup){
                if ($setup) {
                    $is_school_day=SmSubjectAttendance::where('attendance_type','P')
                                    ->where('attendance_date',date('Y-m-d'))
                                    ->where('school_id', $school->id)
                                    ->first();
                    $now =  strtotime(date('H:i'));
                    $setup_time=(strtotime($setup->time_from));
                    if ($is_school_day) {
                        $absent_student = SmSubjectAttendance::where('attendance_type','A')
                        ->where('attendance_date',date('Y-m-d'))
                        ->where('notify', 0)
                        ->where('sm_subject_attendances.school_id', $school->id)
                        ->leftjoin('student_records','student_records.id','=','sm_subject_attendances.student_record_id')
                        ->select('sm_subject_attendances.*')
                        ->get();
                        $students=[];
                        foreach ($absent_student as $key => $value) {
                            $students[]=$value->student_record_id;
                        }
                        
                        foreach (array_unique($students) as $key => $student_id) {
                            $attendanceInfo = SmSubjectAttendance::where('student_record_id', $student_id)
                                                ->where('school_id', $school->id)
                                                ->where('attendance_date',date('Y-m-d'))
                                                ->where('notify', 0)
                                                ->first();

                            if($attendanceInfo && $attendanceInfo->recordDetail->studentDetail->parents){
                                $absent_subjects=SmSubjectAttendance::getAbsentSubjectList($student_id, $school->id);
                                $compact['user_email'] = @$attendanceInfo->recordDetail->studentDetail->parents->guardians_email;
                                $compact['subject_list'] = implode(',',$absent_subjects);
                                $compact['number_of_subject'] = count($absent_subjects);
                                $compact['date'] = date('Y-m-d');
                                $compact['slug'] = 'parent';
                                $compact['id'] = @$attendanceInfo->recordDetail->studentDetail->parents->id;
                                $compact['student_name'] = @$attendanceInfo->recordDetail->studentDetail->full_name;
    
                                @send_sms(@$attendanceInfo->recordDetail->studentDetail->parents->guardians_mobile, 'student_absent_notification', $compact, $school->id);
                                $attendanceInfo->notify = 1;
                                $attendanceInfo->save();
                            }
                            
                        }
                    }
                }
            }
        }
    }
}
