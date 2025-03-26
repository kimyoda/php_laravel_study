<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;

    protected $table = 'calculations';

    protected $fillable = ['expressions', 'result', 'is_valid'];

    protected $casts = [
        'result' => 'double',
        'is_valid' => 'boolean'
    ];
}
