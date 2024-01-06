<?php

namespace App\Exports;

use App\SmStudent;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;


class AllStudentExport implements FromCollection,WithHeadings
{
    public function headings():array{
        return[
            'Admission Number',
            'Full Name',
            'Class (Section)',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $all_student_data = [];
        $student_infos = SmStudent::where('school_id',Auth::user()->school_id)
                  
                    ->select('admission_no', 'full_name','id')
                    ->with('studentRecords')
                    ->get();
        
        foreach($student_infos as $student_info){
            $data= [];
            foreach($student_info->studentRecords as $record){
                $data[]= $record->class->class_name." (". $record->section->section_name . ")";
            }
            $classSection = implode(', ', $data);
            $all_student_data[] = [
                $student_info->admission_no,
                $student_info->full_name,
                $classSection,
                
            ];
        }
        return collect($all_student_data);
    }
}
