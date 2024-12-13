<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Makul extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_kuliah',
        'kode',
        'sks',
        'semester',
        // 'prasyarat',
        // 'IsAtas',
        'IsPilihan',
    ];

    public $timestamps = false;
}
