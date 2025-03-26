<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calculation
{
    use HasFactory;

    protected $table = 'calculations';

    protected $fillable = ['expression', 'result', 'is_valid'];

    protected $casts = [
        'result' => 'double',
        'is_valid' => 'boolean'
    ];
}
