<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakulAktif extends Model
{
    use HasFactory;

    protected $table = 'makul_aktif';

    protected $fillable = [
        'kode_masa_input',
        'kode_makul',
        'status_aktif'
    ];
}
