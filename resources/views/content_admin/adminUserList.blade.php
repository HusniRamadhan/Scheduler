@extends('ui_dashboard.dashboard')
@section('title', 'List Pengguna')
@section('pageSize', 'min-height: 1300px;')
@section('headScript')
    <!-- DataTables -->
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        .btn-fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
@endsection
@section('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables with 20 data per page
            $('#adminTable').DataTable();
            $('#mahasiswaTable').DataTable();
        });
    </script>
    <script>
        $(document).ready(function() {
            // Function to handle delete
            function handleDelete(buttonSelector, url) {
                $(document).on('click', buttonSelector, function() {
                    if (confirm('Are you sure you want to delete this item?')) {
                        let row = $(this).closest('tr');
                        let id = row.data('id');

                        $.ajax({
                            url: url.replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                alert(response.success);
                                row.remove();
                            },
                            error: function(response) {
                                alert('Failed to delete the item.');
                            }
                        });
                    }
                });
            }

            // Handle delete for user, dosen, and mahasiswa
            handleDelete('.delete-user-btn', '{{ route('delete-user', ':id') }}');
            handleDelete('.delete-dosen-btn', '{{ route('delete-dosen', ':id') }}');
            handleDelete('.delete-mahasiswa-btn', '{{ route('delete-mahasiswa', ':id') }}');
        });
    </script>
    <script>
        $(document).ready(function() {
            // Handle Administrasi button click
            $('#addAdmin').click(function() {
                // Open the Administrasi modal or perform any action
                $('#addAdminModal').modal('show');
            });

            // Handle Mahasiswa button click
            $('#addMahasiswa').click(function() {
                // Open the Mahasiswa modal or perform any action
                $('#addMahasiswaModal').modal('show');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Edit Admin
            $('.edit-admin-btn').click(function() {
                var row = $(this).closest('tr');
                var id = row.data('id');
                var name = row.find('td').eq(0).text();
                var email = row.find('td').eq(1).text();

                $('#editAdminName').val(name);
                $('#editAdminEmail').val(email);

                $('#editAdminForm').attr('action', '/admin/update/' + id);
                $('#editAdminModal').modal('show');
            });

            // Edit Dosen
            $('.edit-dosen-btn').click(function() {
                var row = $(this).closest('tr');
                var id = row.data('id');
                var name = row.find('td').eq(0).text();
                var kode_dosen = row.find('td').eq(2).text();

                $('#editDosenName').val(name);
                // $('#editDosenKode').val(kode_dosen);

                $('#editDosenForm').attr('action', '/dosen/update/' + id);
                $('#editDosenModal').modal('show');
            });

            // Edit Mahasiswa
            $('.edit-mahasiswa-btn').click(function() {
                var row = $(this).closest('tr');
                var id = row.data('id');
                var name = row.find('td').eq(0).text();
                var NIM = row.find('td').eq(1).text();
                var angkatan = row.find('td').eq(2).text();

                $('#editMahasiswaName').val(name);
                $('#editMahasiswaNIM').val(NIM);
                $('#editMahasiswaAngkatan').val(angkatan);

                $('#editMahasiswaForm').attr('action', '/mahasiswa/update/' + id);
                $('#editMahasiswaModal').modal('show');
            });
        });
    </script>
    <!-- Place this in your Blade view file -->
    @if (session('nimError'))
        <script>
            alert("{{ session('nimError') }}");
        </script>
    @endif
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" bis_skin_checked="1">
                        <div class="card-header p-2" bis_skin_checked="1">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#administrator"
                                        data-toggle="tab">Admin</a></li>
                                <li class="nav-item"><a class="nav-link" href="#mahasiswa" data-toggle="tab">Mahasiswa</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body" bis_skin_checked="1">
                            <div class="tab-content" bis_skin_checked="1">
                                <div class="active tab-pane" id="administrator" bis_skin_checked="1">
                                    <div class="card-body">
                                        <table class="table table-bordered table-hover dataTable dtr-inline"
                                            id="adminTable">
                                            <thead>
                                                <tr>
                                                    <th width="35%">Nama</th>
                                                    <th width="35%">Email</th>
                                                    <th width="30%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($users as $user)
                                                    <tr data-id="{{ $user->id }}">
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary edit-admin-btn">Ubah</button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger delete-user-btn">Hapus</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="mahasiswa" bis_skin_checked="1">
                                    <div class="card-body">
                                        <table class="table table-bordered table-hover dataTable dtr-inline"
                                            id="mahasiswaTable">
                                            <thead>
                                                <tr>
                                                    <th width="45%">Nama</th>
                                                    <th width="20%">NIM</th>
                                                    <th width="10%">Angkatan</th>
                                                    {{-- <th width="10%">Semester</th> --}}
                                                    <th width="25%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($mahasiswas as $mahasiswa)
                                                    <tr data-id="{{ $mahasiswa->id }}">
                                                        <td>{{ $mahasiswa->name }}</td>
                                                        <td>{{ $mahasiswa->NIM }}</td>
                                                        <td>{{ $mahasiswa->angkatan }}</td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary edit-mahasiswa-btn">Ubah</button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger delete-mahasiswa-btn">Hapus</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>NIM</th>
                                                    <th>Angkatan</th>
                                                    {{-- <th>Semester</th> --}}
                                                    <th>Aksi</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- FAB --}}
        <div class="dropdown">
            <button class="btn btn-primary btn-fab dropdown-toggle" type="button" id="fabDropdown" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-plus"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="fabDropdown">
                <a class="dropdown-item" href="#addAdminModal" id="addAdmin">Admin</a>
                <a class="dropdown-item" href="#addMahasiswaModal" id="addMahasiswa">Mahasiswa</a>
            </div>
        </div>
        <!-- Add Admin Modal -->
        <div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-labelledby="addAdminModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdminModalLabel">Tambah Admin</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <form action="{{ route('admin.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="admin">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Mahasiswa Modal -->
        <div class="modal fade" id="addMahasiswaModal" tabindex="-1" role="dialog"
            aria-labelledby="addMahasiswaModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMahasiswaModalLabel">Tambah Mahasiswa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <form action="{{ route('admin.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="mahasiswa">
                                <div class="form-group">
                                    <label>Nama Mahasiswa</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>NIM</label>
                                    <input type="text" name="NIM" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Angkatan</label>
                                    <input type="text" name="angkatan" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-labelledby="editAdminModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAdminModalLabel">Edit Admin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editAdminForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" id="editAdminName" class="form-control"
                                autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="editAdminEmail" class="form-control"
                                autocomplete="email" required>
                        </div>
                        <div class="form-group">
                            <label>Password (Leave blank if not changing)</label>
                            <input type="password" name="password" id="editAdminPassword" class="form-control"
                                autocomplete="new-password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Mahasiswa Modal -->
    <div class="modal fade" id="editMahasiswaModal" tabindex="-1" role="dialog"
        aria-labelledby="editMahasiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMahasiswaModalLabel">Edit Mahasiswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editMahasiswaForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" id="editMahasiswaName" class="form-control"
                                autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label>NIM</label>
                            <input type="text" name="NIM" id="editMahasiswaNIM" class="form-control"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Angkatan</label>
                            <input type="text" name="angkatan" id="editMahasiswaAngkatan" class="form-control"
                                autocomplete="off" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
