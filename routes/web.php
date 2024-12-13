<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Role;
use App\Http\Middleware\Member;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\MasaInputController;
use App\Http\Controllers\MakulController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\Dosen;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // If user is authenticated, redirect to home, otherwise redirect to login
    if (Auth::check()) {
        return redirect()->route('home');
    } else {
        return redirect()->route('login');
    }
});

//Aktif atau Non-aktif Registrasi
Auth::routes([
    'register' => false,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth']);
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);
Route::get('/change-password', [PasswordController::class, 'showChangePasswordForm'])->name('password.change');
Route::post('/change-password', [PasswordController::class, 'changePassword']);
Route::post('/update-email', [AdminController::class, 'updateEmail'])->name('update.email')->middleware('auth');

Route::middleware(['role', 'auth'])->group(function () {
    //ADMIN - SIDEBAR
    Route::get('/admin', [AdminController::class, 'admin'])->name('admin');
    Route::get('/admin/setting', [AdminController::class, 'adminProfile'])->name('adminProfile');
    Route::get('/admin/pengguna', [AdminController::class, 'adminPengguna'])->name('adminPengguna');
    Route::get('/admin/pengajar', [AdminController::class, 'adminPengajar'])->name('adminPengajar');
    Route::get('/admin/pralirs', [AdminController::class, 'adminPraLirs'])->name('adminPraLirs');
    Route::get('/admin/scheduling', [AdminController::class, 'adminScheduling'])->name('adminScheduling');
    Route::get('/admin/makul', [AdminController::class, 'adminSubject'])->name('adminSubject');
    Route::get('/admin/aktifmakul', [AdminController::class, 'adminAktivasi'])->name('adminAktivasi');
    Route::get('/admin/managekelas', [AdminController::class, 'adminManajemen'])->name('adminManajemen');
    //ADMIN - DASHBOARD
    Route::post('/admin/get-jadwal', [AdminController::class, 'getJadwalDataHome'])->name('get.jadwal');
    //ADMIN - PRALIRS
    Route::get('/admin/scheduling/mahasiswa-input', [AdminController::class, 'getMahasiswaInputCounts']);
    //ADMIN - PRALIRS INPUT
    Route::get('/admin/scheduling/input', [AdminController::class, 'adminSchedulingInput'])->name('adminSchedulingInput');
    Route::get('/admin/scheduling/input/ajax', [AdminController::class, 'adminSchedulingInputAjax'])->name('adminSchedulingInputAjax');
    Route::get('/admin/scheduling/input/get-available-dosens', [DosenController::class, 'getAvailableDosens']);
    Route::get('/admin/scheduling/input/get-jumlah-mahasiswa', [AdminController::class, 'getJumlahMahasiswa'])->name('get.jumlah.mahasiswa');
    Route::get('/admin/scheduling/input/makul', [AdminController::class, 'getMakulCount']);
    Route::get('/admin/scheduling/input/pilihan', [AdminController::class, 'getMakulPilihanCount']);
    Route::post('/admin/scheduling/input/scheduling-subject-code', [AdminController::class, 'schedulingSubjectCode']);
    //ADMIN - MASA INPUT
    Route::get('/admin/masa_input', [MasaInputController::class, 'index'])->name('masa_input.index');
    Route::post('/admin/masa_input', [MasaInputController::class, 'store'])->name('masa_input.store');
    Route::delete('/admin/masa_input/{id}', [MasaInputController::class, 'destroy'])->name('masa_input.destroy');
    Route::get('/admin/masa_input/{id}/edit', [MasaInputController::class, 'edit']);
    Route::put('/admin/masa_input/{id}', [MasaInputController::class, 'update']);
    //ADMIN - MATA KULIAH
    Route::get('/admin/makul', [AdminController::class, 'adminSubject'])->name('adminSubject');
    Route::post('/admin/makul', [AdminController::class, 'makulstore'])->name('makul.store');
    Route::put('/admin/makul/{id}', [AdminController::class, 'updateMakul'])->name('makul.update');
    Route::delete('/admin/makul/{id}', [AdminController::class, 'destroyMakul'])->name('makul.destroy');
    //ADMIN - AKTIVASI
    Route::post('/admin/aktifmakul/save-aktivasi', [AdminController::class, 'saveAktivasi'])->name('save.aktivasi');
    Route::get('/admin/aktifmakul/check-kode-masa/{kode_masa_input}', [AdminController::class, 'checkKodeMasa']);
    Route::post('/admin/aktifmakul/update-aktivasi', [AdminController::class, 'updateAktivasi']);
    Route::get('/admin/aktifmakul/get-status/{kode_masa_input}', [AdminController::class, 'getStatusLecturer']);
    //ADMIN - MANAJEMEN KELAS MAKUL
    Route::post('/admin/managekelas/check-masa-input', [AdminController::class, 'checkMasaInputClass']);
    Route::post('/admin/managekelas/check-makul-class', [AdminController::class, 'checkMakulClass'])->name('checkMakulClass');
    Route::post('/admin/managekelas/get-dosen-for-makul', [AdminController::class, 'getDosenForMakul']);
    Route::post('/admin/managekelas/save-makul-class', [AdminController::class, 'saveMakulClass'])->name('saveMakulClass');
    Route::post('/admin/managekelas/update-makul-class', [AdminController::class, 'updateMakulClass'])->name('updateMakulClass');
    //ADMIN - PENJADWALAN
    Route::get('/admin/scheduling/input/admin-scheduling-create', [AdminController::class, 'adminSchedulingCreate']);
    Route::get('/admin/scheduling/input/admin-scheduling-test', [AdminController::class, 'adminSchedulingTest']);
    Route::post('/admin/scheduling/input/store-jadwal', [AdminController::class, 'storeJadwalArray'])->name('admin.storeJadwalArray');
    Route::post('/admin/scheduling/input/overwrite-jadwal', [AdminController::class, 'overwriteJadwal'])->name('admin.overwriteJadwal');
    Route::post('/admin/scheduling/check-jadwal', [AdminController::class, 'checkJadwalExists'])->name('check.jadwal');
    Route::post('/admin/scheduling/get-jadwal', [AdminController::class, 'getJadwalDataScheduling'])->name('get.jadwal.scheduling');
    //ADMIN - PENGGUNA
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::put('/admin/update/{id}', [AdminController::class, 'updateAdmin'])->name('admin.update');
    Route::put('/dosen/update/{id}', [AdminController::class, 'updateDosen'])->name('dosen.update');
    Route::put('/mahasiswa/update/{id}', [AdminController::class, 'updateMahasiswa'])->name('mahasiswa.update');
    Route::delete('/admin/delete-user/{id}', [AdminController::class, 'AdminDeleteUser'])->name('delete-user');
    Route::delete('/admin/delete-dosen/{id}', [AdminController::class, 'AdminDeleteDosen'])->name('delete-dosen');
    Route::delete('/admin/delete-mahasiswa/{id}', [AdminController::class, 'AdmindeleteMahasiswa'])->name('delete-mahasiswa');
    //ADMIN - KELAS
    Route::post('/admin/classroom/store', [AdminController::class, 'adminClassroomStore'])->name('classroom.store');
    Route::put('/admin/classroom/update/{id}', [AdminController::class, 'adminClassroomUpdate'])->name('classroom.update');
    Route::delete('/admin/classroom/delete/{id}', [AdminController::class, 'adminClassroomDelete'])->name('classroom.delete');
    //ADMIN - LECTURER
    Route::post('admin/pengajar/dosen-makul/store', [AdminController::class, 'storeLecturer'])->name('store.dosenMakul');
    Route::get('admin/pengajar/dosen-makul/edit/{id}', [AdminController::class, 'editLecturer'])->name('edit.dosenMakul');
    Route::post('admin/pengajar/dosen-makul/update/{id}', [AdminController::class, 'updateLecturer'])->name('update.dosenMakul');
    Route::post('admin/pengajar/dosen-makul/destroy/{id}', [AdminController::class, 'destroyLecturer'])->name('destroy.dosenMakul');
});

Route::middleware(['member', 'auth'])->group(function () {
    //MAHASISWA - SIDEBAR
    Route::get('/user', [MemberController::class, 'user'])->name('user');
    Route::get('/user/setting', [MemberController::class, 'userProfile'])->name('userProfile');
    Route::get('/user/pralirs', [MemberController::class, 'userPraLirs'])->name('userPraLirs');
    Route::get('/user/pralirs/input', [MemberController::class, 'userInput'])->name('userInput');
    Route::get('/user/makul', [MemberController::class, 'userSubject'])->name('userSubject');
    //MAHASISWA - MASA INPUT
    // Route::get('/get-total-sks/{semester}', [MemberController::class, 'getTotalSks']);
    Route::post('/store-input', [MemberController::class, 'storeInput'])->name('store.input');
    Route::put('/input/{id}', [MemberController::class, 'updateInput'])->name('update.input');
    Route::get('/user/pralirs/makulInputs/{kodeMasaInput}', [MemberController::class, 'fetchMakulInputs']);
    Route::post('/check-existing-input', [MemberController::class, 'checkExistingInput'])->name('checkExistingInput');
});
