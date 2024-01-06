<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmLeaveType extends Model
{
    use HasFactory;
    public function leaveDefines()
    {
        return $this->hasMany(SmLeaveDefine::class, 'type_id');
    }
}
