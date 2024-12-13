<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalFile extends Model
{
    use HasFactory;

    protected $table = 'jadwal_file';

    // Define fillable fields to prevent mass assignment issues
    protected $fillable = [
        'kode_masa_input',
        'data_hari',
        'data_jadwal_per_hari'
    ];
}
