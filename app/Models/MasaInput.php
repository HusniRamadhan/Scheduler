<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasaInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'jangka_waktu',
        // 'hari_awal',
        // 'hari_akhir',
        'keterangan',
        'kode_masa_input',
    ];
}
