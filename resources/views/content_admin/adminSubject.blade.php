@extends('ui_dashboard.dashboard')
@section('title', 'Daftar Mata Kuliah dan Ruang Kelas')
@section('headScript')
    <!-- DataTables -->
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
@section('content')
    {{-- EDIT LATER --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" bis_skin_checked="1">
                        <div class="card-header p-2" bis_skin_checked="1">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#matakuliah" data-toggle="tab">Mata
                                        Kuliah</a></li>
                                <li class="nav-item"><a class="nav-link" href="#ruang" data-toggle="tab">Ruang Kelas</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body" bis_skin_checked="1">
                            <div class="tab-content" bis_skin_checked="1">
                                <div class="active tab-pane" id="matakuliah" bis_skin_checked="1">
                                    <div class="card-body">
                                        <div class="dataTables_wrapper dt-bootstrap4">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    {{-- Tabel Mata Kuliah --}}
                                                    <table class="table table-bordered table-hover dataTable dtr-inline"
                                                        id="tabel_makul">
                                                        {{-- Nama Row Tabel --}}
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="1" colspan="1" aria-sort="ascending"
                                                                    aria-label="#: activate to sort column descending"
                                                                    width="5%">
                                                                    #</th>
                                                                <th rowspan="1" colspan="1"
                                                                    aria-label="Kode Mata Kuliah: activate to sort column ascending"width="25%">
                                                                    Kode Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1"
                                                                    aria-label="Mata Kuliah: activate to sort column ascending"
                                                                    width="40%">
                                                                    Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1"
                                                                    aria-label="Semester: activate to sort column ascending"
                                                                    width="5%">
                                                                    Semester</th>
                                                                <th rowspan="1" colspan="1"
                                                                    aria-label="SKS: activate to sort column ascending"
                                                                    width="5%">
                                                                    SKS
                                                                </th>
                                                                <th rowspan="1" colspan="1"
                                                                    aria-label="Jenis Mata Kuliah: activate to sort column ascending"
                                                                    width="10%">
                                                                    Jenis Mata Kuliah
                                                                </th>
                                                                <th rowspan="1" colspan="1" width="15%">
                                                                    Aksi
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        {{-- Isi Tabel --}}
                                                        <tbody>
                                                            {{-- Looping data makuls --}}
                                                            @foreach ($makuls as $index => $makul)
                                                                <tr data-id="{{ $makul->id }}"
                                                                    data-ispilihan="{{ $makul->IsPilihan }}">
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $makul->kode }}</td>
                                                                    <td>{{ $makul->mata_kuliah }}</td>
                                                                    <td>{{ $makul->semester }}</td>
                                                                    <td>{{ $makul->sks }}</td>
                                                                    {{-- Display Wajib or Pilihan based on IsPilihan --}}
                                                                    <td>
                                                                        @if ($makul->IsPilihan)
                                                                            Pilihan
                                                                        @else
                                                                            Wajib
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <button
                                                                            class="btn btn-sm btn-primary edit-btn">Ubah</button>
                                                                        <button class="btn btn-sm btn-danger delete-btn"
                                                                            data-id="{{ $makul->id }}">Hapus</button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            {{-- Akhir dari loop --}}
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th rowspan="1" colspan="1">#</th>
                                                                <th rowspan="1" colspan="1">Kode Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1">Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1">Semester</th>
                                                                <th rowspan="1" colspan="1">SKS</th>
                                                                <th rowspan="1" colspan="1">Jenis Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1">Aksi</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="ruang" bis_skin_checked="1">
                                    <div class="card-body">
                                        <table class="table table-bordered table-hover" id="tabel_classrooms">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama Ruang Kelas</th>
                                                    <th>Jenis Ruang Kelas</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($classrooms as $index => $classroom)
                                                    <tr>
                                                        <td width="5%">{{ $index + 1 }}</td>
                                                        <td>{{ $classroom->ruang_kelas }}</td>
                                                        {{-- <td></td> --}}
                                                        <td>{{ $classroom->jenis_kelas ? 'Praktikum' : 'Teori' }}</td>
                                                        <td width="15%">
                                                            <button class="btn btn-sm btn-primary edit-classroom-btn"
                                                                data-id="{{ $classroom->id }}"
                                                                data-name="{{ $classroom->ruang_kelas }}">Ubah</button>
                                                            <button class="btn btn-sm btn-danger delete-classroom-btn"
                                                                data-id="{{ $classroom->id }}">Hapus</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama Kelas</th>
                                                    <th>Jenis Ruang Kelas</th>
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
            {{-- FAB --}}
            <div class="dropdown">
                <button class="btn btn-primary btn-fab dropdown-toggle" type="button" id="fabDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-plus"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="fabDropdown">
                    <a class="dropdown-item" data-toggle="modal" data-target="#addMakulModal">Tambah Mata Kuliah</a>
                    <a class="dropdown-item" href="#" id="addClassroomBtn">Tambah Ruang Kelas</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Mata Kuliah Modal -->
    <div class="modal fade" id="addMakulModal" tabindex="-1" role="dialog" aria-labelledby="addMakulModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMakulModalLabel">Tambah Mata Kuliah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addMakulForm" method="POST" action="{{ route('makul.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="mata_kuliah">Mata Kuliah</label>
                            <input type="text" class="form-control" id="addMataKuliah" name="mata_kuliah" required>
                        </div>
                        <div class="form-group">
                            {{-- Nanti Ganti ke Jenis Mata Kuliah, Umum(MKWUXX)/Prodi(INF-55201-SXX), XX=Nomor Urut, S=Semester --}}
                            <label for="kode">Kode</label>
                            <input type="text" class="form-control" id="addKode" name="kode" required>
                        </div>
                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <select class="form-control" id="addSemester" name="semester" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sks">SKS</label>
                            <select class="form-control" id="addSks" name="sks" required>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="IsPilihan" value="0"> <!-- Hidden default value -->
                            <input type="checkbox" id="addIsPilihan" name="IsPilihan" value="1">
                            <label for="IsPilihan" style="margin-left: 5px;">Merupakan Mata Kuliah Pilihan</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Mata Kuliah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Mata Kuliah Modal -->
    <div class="modal fade" id="editMakulModal" tabindex="-1" role="dialog" aria-labelledby="editMakulModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMakulModalLabel">Edit Mata Kuliah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editMakulForm" method="POST" action="{{ route('makul.update', ['id' => $makul->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="mata_kuliah">Mata Kuliah</label>
                            <input type="text" class="form-control" id="editMataKuliah" name="mata_kuliah"
                                value="{{ $makul->mata_kuliah }}" required>
                        </div>
                        <div class="form-group">
                            <label for="kode">Kode</label>
                            <input type="text" class="form-control" id="editKode" name="kode"
                                value="{{ $makul->kode }}" required>
                        </div>
                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <select class="form-control" id="editSemester" name="semester" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sks">SKS</label>
                            <select class="form-control" id="editSks" name="sks" required>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div>
                                <input type="checkbox" id="editIsPilihan" name="IsPilihan" value="1">
                                <label for="IsPilihan" style="margin-left: 5px;">Mata Kuliah Pilihan</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                    {{-- <div class=form-group>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select class="custom-select rounded-0" id="jenis_makul">
                                    <option value="1">UMG-</option>
                                    <option value="0">INF-</option>
                                </select>
                            </div>
                            <input type="text" class="form-control float-right" id="reservation">
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    <!-- Add/Edit Ruang Kelas Modal -->
    <div class="modal fade" id="addClassroomModal" tabindex="-1" role="dialog"
        aria-labelledby="addClassroomModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClassroomModalLabel">Tambah Ruang Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="classroomForm" method="POST">
                        @csrf
                        <!-- Use this hidden input to simulate a PUT request -->
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" name="classroom_id" id="classroomId">
                        <!-- For updating the correct classroom -->

                        <div class="form-group">
                            <label for="classroomName">Nama Ruang Kelas</label>
                            <input type="text" class="form-control" id="classroomName" name="ruang_kelas" required>
                        </div>
                        <div class="form-group">
                            <label for="jenisKelas">Semester</label>
                            <select class="form-control" id="classroomType" name="jenisKelas" required>
                                <option value="0">Teori</option>
                                <option value="1">Praktikum</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
            // Function to handle edit button click
            function handleEditClick() {
                $('.edit-btn').on('click', function() {
                    var row = $(this).closest('tr');
                    var id = row.data('id');
                    var mataKuliah = row.find('td').eq(2).text();
                    var kode = row.find('td').eq(1).text();
                    var semester = row.find('td').eq(3).text();
                    var sks = row.find('td').eq(4).text();
                    var isPilihan = row.data('ispilihan');

                    // Debugging output
                    console.log('Editing Mata Kuliah ID:', id);
                    console.log('IsPilihan:', isPilihan);

                    var formAction = $('#editMakulForm').attr('action').replace(':id', id);
                    $('#editMakulForm').attr('action', formAction);
                    $('#editMataKuliah').val(mataKuliah);
                    $('#editKode').val(kode);
                    $('#editSks').val(sks);
                    $('#editSemester').val(semester);
                    $('#editIsPilihan').prop('checked', isPilihan);

                    $('#editMakulModal').modal('show');
                });
            }

            // Function to handle delete button click
            function handleDeleteClick() {
                $('.delete-btn').on('click', function() {
                    var id = $(this).data('id'); // Get the makul ID from the button data attribute
                    var deleteUrl = '/admin/makul/' + id; // Construct the delete URL

                    if (confirm('Are you sure you want to delete this Mata Kuliah?')) {
                        $.ajax({
                            url: deleteUrl, // The delete URL with the correct ID
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}' // Include CSRF token
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Refresh the entire page after successful delete
                                    location.reload(); // Refresh the page
                                } else {
                                    alert('Failed to delete the Mata Kuliah.');
                                }
                            },
                            error: function() {
                                alert('An error occurred. Please try again.');
                            }
                        });
                    }
                });
            }

            // Initial event binding
            handleEditClick();
            handleDeleteClick();

            // Rebind events on table redraw
            table.on('draw.dt', function() {
                handleEditClick();
                handleDeleteClick();
            });
        });
        $(document).ready(function() {
            // Trigger Add Ruang Kelas Modal
            $('#addClassroomBtn').on('click', function() {
                $('#classroomForm').attr('action',
                    '{{ route('classroom.store') }}'); // Set action for adding
                $('#formMethod').val('POST'); // Set method to POST for adding
                $('#classroomForm').trigger('reset'); // Clear form fields
                $('#classroomId').val(''); // Clear the hidden ID field
                $('#addClassroomModalLabel').text('Tambah Ruang Kelas'); // Update modal title
                $('#addClassroomModal').modal('show'); // Show modal
            });

            // Trigger Edit Ruang Kelas Modal
            $('.edit-classroom-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#classroomId').val(id); // Set the hidden ID input with the classroom ID
                $('#classroomName').val(name); // Set the classroom name input
                $('#classroomForm').attr('action', '{{ route('classroom.update', ':id') }}'.replace(':id',
                    id)); // Update action URL
                $('#formMethod').val('PUT'); // Simulate PUT method for editing
                $('#addClassroomModalLabel').text('Edit Ruang Kelas'); // Update modal title
                $('#addClassroomModal').modal('show'); // Show modal
            });

            $(document).ready(function() {
                // Handle Delete Classroom with AJAX
                $('.delete-classroom-btn').on('click', function() {
                    const id = $(this).data('id'); // Get classroom ID

                    if (confirm('Apakah Anda yakin ingin menghapus ruang kelas ini?')) {
                        $.ajax({
                            url: '/admin/classroom/delete/' + id, // Correct delete URL
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}' // CSRF token for security
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Show the success alert
                                    alert('Ruang kelas berhasil dihapus.');

                                    // Reload the page after the alert
                                    location.reload(); // This will refresh the page
                                } else {
                                    alert('Gagal menghapus ruang kelas.');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText); // Log the error to debug
                                alert('An error occurred. Please try again.');
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize both DataTables
            $('#tabel_makul').DataTable();
            $('#tabel_classrooms').DataTable();
        });
    </script>
@endsection
