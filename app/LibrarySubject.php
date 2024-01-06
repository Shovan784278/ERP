<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibrarySubject extends Model
{
    use HasFactory;
    public function subjectBook()
    {
        return $this->belongsTo('App\Book', 'book', 'id');
    }
}
