<?php

namespace Database\Seeders\HumanResources;

use App\SmRoute;
use App\SmStaff;
use App\User;
use Illuminate\Database\Seeder;

class StaffsTableSeeder  extends Seeder
{

    public function run($school_id , $count = 10){
       

        User::factory()->times($count)->create([
            'school_id' => $school_id,
        ])->each( function ($userStaff) use ($school_id) {
            SmStaff::factory()->times(1)->create([
                'user_id' => $userStaff->id,
                'email' => $userStaff->email,
                'first_name' => $userStaff->first_name,
                'last_name' => $userStaff->last_name,
                'full_name' => $userStaff->full_name,
                'school_id' => $school_id,
                'role_id' =>rand(4,9),
            ]);
        });

        User::factory()->times($count)->create([
            'school_id' => $school_id,
        ])->each( function ($userStaff) use ($school_id) {
            SmStaff::factory()->times(1)->create([
                'user_id' => $userStaff->id,
                'email' => $userStaff->email,
                'first_name' => $userStaff->first_name,
                'last_name' => $userStaff->last_name,
                'full_name' => $userStaff->full_name,
                'school_id' => $school_id,
                'role_id' =>4,
            ]);
        });
    }

}