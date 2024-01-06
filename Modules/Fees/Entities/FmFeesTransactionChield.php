<?php

namespace Modules\Fees\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FmFeesTransactionChield extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Fees\Database\factories\FmFeesTransactionChieldFactory::new();
    }

    public function feesType()
    {
        return $this->belongsTo('Modules\Fees\Entities\FmFeesGroup', 'fees_type', 'id');
    }

    public function transcationFeesType()
    {
        return $this->belongsTo('Modules\Fees\Entities\FmFeesType', 'fees_type', 'id')->withDefault();
    }
}
