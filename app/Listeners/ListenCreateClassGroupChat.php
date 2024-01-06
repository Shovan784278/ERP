<?php

namespace App\Listeners;

use App\Events\CreateClassGroupChat;
use App\Models\InvitationType;
use App\SmClass;
use App\SmClassSection;
use App\SmSection;
use App\SmStaff;
use App\SmSubject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Chat\Entities\Group;
use Modules\Chat\Entities\GroupUser;
use Modules\Chat\Entities\Invitation;

class ListenCreateClassGroupChat
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CreateClassGroupChat  $event
     * @return void
     */
    public function handle(CreateClassGroupChat $event)
    {
        $group = Group::where('name', 'like', '%'.$this->groupName($event->assign_subject, false).'%')->first();
        $section = SmSection::find($event->assign_subject->section_id);
        $teacher = SmStaff::find($event->assign_subject->teacher_id)->staff_user;
        if ($group){
            if($group->create_by != $teacher->id){
                GroupUser::where('user_id', $group->created_by)->where('group_id', $group->id)->delete();

                $group->created_by = $teacher->id;
                $group->name = $this->groupName($event->assign_subject);
                $group->save();

                GroupUser::create([
                    'user_id' => $teacher->id,
                    'group_id' => $group->id,
                    'role' => 1,
                    'added_by' => $group->created_by
                ]);
            }
        }else{

            $group = Group::create([
               'name' => $this->groupName($event->assign_subject),
               'created_by' => $teacher->id,
            ]);

            GroupUser::create([
                'user_id' => $teacher->id,
                'group_id' => $group->id,
                'added_by' => $group->created_by,
                'role' => 1,
            ]);

            foreach ($section->students as $student){
                GroupUser::create([
                    'user_id' => $student->id,
                    'group_id' => $group->id,
                    'added_by' => $group->created_by,
                    'role' => 2
                ]);

                $exist = Invitation::where('from', $group->created_by)->where('to', $student->id)->first();
                if (is_null($exist) && $group->created_by != $student->id){
                    $invitation = Invitation::create([
                        'from' => $group->created_by,
                        'to' => $student->id,
                        'status' => 1
                    ]);
                    InvitationType::create([
                        'invitation_id' => $invitation->id,
                        'type' => 'class-teacher',
                        'section_id' => $section->id,
                        'class_teacher_id' => $group->created_by,
                    ]);
                }
            }
        }

    }

    public function groupName($data, $withTeacherId = true){
        $class = SmClass::find($data->class_id);
        $section = SmSection::find($data->section_id);
        $subject = SmSubject::find($data->subject_id);
        $teacher = SmStaff::find($data->teacher_id)->staff_user;

        if ($withTeacherId){
            $code = $data->school_id.$data->class_id.$data->section_id.$data->subject_id.$teacher->id;
        }else{
            $code = $data->school_id.$data->class_id.$data->section_id.$data->subject_id;
        }
        return $class->class_name. '('.$section->section_name. ')-'.$subject->subject_name.'-'.$code;
    }
}
