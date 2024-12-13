<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenMakul extends Model
{
    use HasFactory;

    protected $table = 'dosen_makul'; // Nama tabel
    protected $fillable = ['dosen_id', 'makul_id']; // Kolom yang dapat diisi

    // Relasi ke model Dosen
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    // Relasi ke model Makul
    public function makul()
    {
        return $this->belongsTo(Makul::class, 'makul_id');
    }
}
