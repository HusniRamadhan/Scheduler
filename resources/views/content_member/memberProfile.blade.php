@extends('ui_dashboard.dashboard')
@section('title', 'Pengaturan Akun')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Informasi Mahasiswa -->
                <div class="col-lg-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Mahasiswa</h3>
                        </div>
                        <div class="card-body">
                            <div class="card-body">
                                <h3 class="profile-username text-center">{{ $mahasiswa->name ?? '-' }}</h3>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>NIM</b> <a class="float-right">{{ $mahasiswa->NIM ?? '-' }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Angkatan</b> <a class="float-right">{{ $mahasiswa->angkatan ?? '-' }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Email</b> <a class="float-right">{{ $user->email }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Edit Email -->
                <div class="col-lg-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Ganti Email</h3>
                        </div>
                        <div class="card-body login-card-body">
                            <form action="{{ url('/update-email') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="current_email">Email Saat Ini</label>
                                    <input type="email" class="form-control" name="current_email"
                                        placeholder="Email Saat Ini">
                                </div>
                                <div class="form-group">
                                    <label for="new_email">Email Baru</label>
                                    <input type="email" class="form-control" name="new_email" placeholder="Email Baru"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="new_email_confirmation">Konfirmasi Email Baru</label>
                                    <input type="email" class="form-control" name="new_email_confirmation"
                                        placeholder="Konfirmasi Email Baru" required>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-block">Update Email</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Form Ganti Password -->
                <div class="col-lg-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Ganti Password</h3>
                        </div>
                        <div class="card-body login-card-body">
                            <form action="{{ url('/change-password') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="old_password">Password Lama</label>
                                    <input type="password" class="form-control" name="old_password"
                                        placeholder="Password Lama" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password Baru</label>
                                    <input type="password" class="form-control" name="password" placeholder="Password Baru"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Konfirmasi Password Baru" required>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-block">Ganti password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
@endsection
