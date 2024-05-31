<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FmFeesCollection extends Model
{
    protected $table = 'fm_fees_reciept_book';

    protected $fillable = [
        'some_field', // Add your table's fields here
    ];

    // public function feesReceiptBook()
    // {
    //     return $this->hasOne(FeesReceiptBook::class, 'class_id', 'id');
    // }
    use HasFactory;
}
