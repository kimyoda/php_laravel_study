<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';
    protected $fillable = ['subject', 'question', 'options', 'answer'];

    protected $casts = [
        'options' => 'array',
    ];
}
