<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    // protected $table = 'mahasiswas'; // Pastikan nama tabel sudah benar
    protected $fillable = ['name', 'NIM', 'angkatan', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function semesters()
    // {
    //     return $this->hasMany(MahasiswaSemester::class, 'NIM', 'NIM');
    // }
}
