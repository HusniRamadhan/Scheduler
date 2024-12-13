@extends('ui_dashboard.dashboard')
@section('title', 'Pengaturan Akun')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Form Ganti Email -->
                <div class="col-lg-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Ganti Email</h3>
                        </div>
                        <div class="card-body login-card-body">
                            <!-- Alert for Email Success or Errors -->
                            @if (session('email_success'))
                                <div class="alert alert-success">
                                    {{ session('email_success') }}
                                </div>
                            @endif

                            @if (session('email_error'))
                                <div class="alert alert-danger">
                                    {{ session('email_error') }}
                                </div>
                            @endif

                            <form action="{{ url('/update-email') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="current_email">Email Saat Ini</label>
                                    <input type="email" class="form-control" name="current_email"
                                        placeholder="Email Saat Ini" required>
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
                <div class="col-lg-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Ganti Password</h3>
                        </div>
                        <div class="card-body login-card-body">
                            <!-- Alert for Password Success or Errors -->
                            @if (session('password_success'))
                                <div class="alert alert-success">
                                    {{ session('password_success') }}
                                </div>
                            @endif

                            @if (session('password_error'))
                                <div class="alert alert-danger">
                                    {{ session('password_error') }}
                                </div>
                            @endif

                            <form action="{{ url('/change-password') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Password Lama</label>
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control" name="old_password"
                                            placeholder="Password Lama" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Password Baru</label>
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Password Baru" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Konfirmasi Password Baru</label>
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control" name="password_confirmation"
                                            placeholder="Confirm Password" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
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
