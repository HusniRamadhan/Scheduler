@extends('ui_dashboard.dashboard')
@section('title', 'Manajemen Pengajar Mata Kuliah')
@section('headScript')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}" />
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

        .select2-container {
            width: 100% !important;
            /* Paksa kontainer Select2 selebar modal */
        }

        /* Warna latar belakang dan teks pilihan Select2 */
        .select2-container .select2-selection__choice {
            background-color: #007bff !important;
            /* Biru sesuai tema AdminLTE */
            color: #ffffff !important;
            /* Teks putih agar terlihat jelas */
            border-color: #007bff !important;
            /* Tambahkan border biru */
            padding: 0.3em 0.6em !important;
            /* Atur padding agar terlihat rapi */
            border-radius: 0.25rem;
            /* Membuat sudut membulat seperti AdminLTE */
            margin-right: 0.3rem;
            /* Spasi antar pilihan */
        }

        /* Gaya untuk tombol hapus pilihan */
        .select2-container .select2-selection__choice__remove {
            color: #ffffff !important;
            /* Warna putih untuk ikon '×' */
            margin-right: 0.3rem;
            /* Tambahkan spasi antara ikon dan teks */
            cursor: pointer;
        }

        /* Hover efek untuk pilihan Select2 */
        .select2-container .select2-selection__choice:hover {
            background-color: #0056b3 !important;
            /* Warna biru lebih gelap saat hover */
            border-color: #0056b3 !important;
        }
    </style>
@endsection
@section('script')
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
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
            // Initialize DataTables
            $('#pengajarTable').DataTable();
            $('#dosenTable').DataTable();
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $("#dosens").select2({
                width: 'resolve' // Menyesuaikan lebar ke elemen induk
            });
            $("#editDosens").select2({
                width: 'resolve' // Menyesuaikan lebar ke elemen induk
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#addPengajar').click(function() {
                // Open the Dosen modal or perform any action
                $('#addPengajarModal').modal('show');
            });

            $('.edit-btn').on('click', function() {
                let makulId = $(this).data('id');
                $.get(`/admin/pengajar/dosen-makul/edit/${makulId}`, function(data) {
                    $('#editMakul').val(data.makul.mata_kuliah); // Nama mata kuliah
                    $('#formEditPengajar').attr('action',
                        `/admin/pengajar/dosen-makul/update/${makulId}`);
                    $('#editDosens').empty();
                    // Populate dosens with pre-selected values
                    data.dosens.forEach(dosen => {
                        let selected = data.dosenMakul.some(dm => dm.dosen_id == dosen.id) ?
                            'selected' : '';
                        $('#editDosens').append(
                            `<option value="${dosen.id}" ${selected}>${dosen.nama_dosen}</option>`
                        );
                    });
                    $('#editPengajarModal').modal('show');
                });
            });

            $('.delete-btn').on('click', function() {
                let makulId = $(this).data('id');
                $.get(`/admin/pengajar/dosen-makul/edit/${makulId}`, function(data) {
                    $('#deleteDosens').empty();
                    // Populate dosens with checkbox options
                    data.dosenMakul.forEach(dm => {
                        $('#deleteDosens').append(
                            `<label><input type="checkbox" class="dosen-checkbox" name="dosen_id[]" value="${dm.dosen.id}"> ${dm.dosen.nama_dosen}</label><br>`
                        );
                    });

                    // Set default button text
                    $('#deleteButton').text('Hapus Semua Data');

                    // Add change listener for checkboxes
                    $('.dosen-checkbox').on('change', function() {
                        const anyChecked = $('.dosen-checkbox:checked').length > 0;
                        if (anyChecked) {
                            $('#deleteButton').text('Hapus Data Yang Dipilih');
                        } else {
                            $('#deleteButton').text('Hapus Semua Data');
                        }
                    });

                    $('#formDeletePengajar').attr('action',
                        `/admin/pengajar/dosen-makul/destroy/${makulId}`);
                    $('#deletePengajarModal').modal('show');
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Handle Dosen button click
            $('#addDosen').click(function() {
                // Open the Dosen modal or perform any action
                $('#addDosenModal').modal('show');
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
            handleDelete('.delete-dosen-btn', '{{ route('delete-dosen', ':id') }}');
        });
    </script>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" bis_skin_checked="1">
                        <div class="card-header p-2" bis_skin_checked="1">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#dosen" data-toggle="tab">Dosen</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#pengajar" data-toggle="tab">Pengajar Mata
                                        Kuliah</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body" bis_skin_checked="1">
                            <div class="tab-content" bis_skin_checked="1">
                                <div class="active tab-pane" id="dosen" bis_skin_checked="1">
                                    <div class="card-body">
                                        <table class="table table-bordered table-hover dataTable dtr-inline"
                                            id="dosenTable">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Kode Mata Kuliah</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dosens as $dosen)
                                                    <tr data-id="{{ $dosen->id }}">
                                                        <td>{{ $dosen->nama_dosen }}</td>
                                                        <td>{{ $dosen->kode_dosen }}</td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-primary edit-dosen-btn">Ubah</button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger delete-dosen-btn">Hapus</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Kode Mata Kuliah</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="pengajar" bis_skin_checked="1">
                                    <div class="card-body">
                                        <div class="dataTables_wrapper dt-bootstrap4">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    {{-- Tabel Mata Kuliah --}}
                                                    <table class="table table-bordered table-hover dataTable dtr-inline"
                                                        id="pengajarTable">
                                                        {{-- Nama Row Tabel --}}
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="1" colspan="1" aria-sort="ascending"
                                                                    aria-label="#: activate to sort column descending"
                                                                    width="5%">
                                                                    #</th>
                                                                <th rowspan="1" colspan="1"
                                                                    aria-label="Kode Mata Kuliah: activate to sort column ascending"width="20%">
                                                                    Kode Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1"
                                                                    aria-label="Mata Kuliah: activate to sort column ascending"
                                                                    width="30%">
                                                                    Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1"
                                                                    aria-label="Semester: activate to sort column ascending"
                                                                    width="30%">
                                                                    Pengajar</th>
                                                                <th rowspan="1" colspan="1" width="15%">
                                                                    Aksi
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        {{-- Isi Tabel --}}
                                                        <tbody>
                                                            @foreach ($dosenmakul->groupBy('makul_id') as $makulId => $group)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $group->first()->makul->kode }}</td>
                                                                    <td>{{ $group->first()->makul->mata_kuliah }}</td>
                                                                    <td>
                                                                        <ul>
                                                                            @foreach ($group as $item)
                                                                                <li>{{ $item->dosen->nama_dosen }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </td>
                                                                    <td>
                                                                        <button class="btn btn-sm btn-primary edit-btn"
                                                                            data-id="{{ $makulId }}">Ubah</button>
                                                                        <button class="btn btn-sm btn-danger delete-btn"
                                                                            data-id="{{ $makulId }}">Hapus</button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th rowspan="1" colspan="1">#</th>
                                                                <th rowspan="1" colspan="1">Kode Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1">Mata Kuliah</th>
                                                                <th rowspan="1" colspan="1">Pengajar</th>
                                                                <th rowspan="1" colspan="1">Aksi</th>
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
            <a class="dropdown-item" href="#addDosenModal" id="addDosen">Dosen</a>
            <a class="dropdown-item" href="#addPengajarModal" id="addPengajar">Data Pengajar</a>
        </div>
    </div>
    <!-- Modal Template -->
    <div class="modal fade" id="addPengajarModal" tabindex="-1" role="dialog" aria-labelledby="addPengajarModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPengajarModalLabel">Tambah Pengajar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Isi modal sesuai kebutuhan -->
                    <form method="POST" action="{{ route('store.dosenMakul') }}">
                        @csrf
                        <div class="form-group">
                            <label for="makuls">Mata Kuliah</label>
                            <select class="form-control" id="makuls" name="makul_id">
                                @foreach ($makuls as $makul)
                                    <option value="{{ $makul->id }}">{{ $makul->mata_kuliah }} ({{ $makul->kode }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="dosens">Dosen Pengajar</label>
                        <div class="form-group">
                            <div>
                                <select multiple="multiple" class="form-control" id="dosens" name="dosen_id[]">">
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}">{{ $dosen->nama_dosen }}
                                            ({{ $dosen->kode_dosen }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit -->
    <div class="modal fade" id="editPengajarModal" tabindex="-1" role="dialog"
        aria-labelledby="editPengajarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPengajarModalLabel">Edit Pengajar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formEditPengajar" action="">
                        @csrf
                        <div class="form-group">
                            <label for="editMakul">Mata Kuliah</label>
                            <input type="text" class="form-control" id="editMakul" name="makul" readonly>
                        </div>
                        <label for="editDosens">Dosen Pengajar</label>
                        <div class="form-group">
                            <select multiple="multiple" class="form-control" id="editDosens" name="dosen_id[]"></select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Hapus -->
    <div class="modal fade" id="deletePengajarModal" tabindex="-1" role="dialog"
        aria-labelledby="deletePengajarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePengajarModalLabel">Hapus Pengajar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formDeletePengajar" action="">
                        @csrf
                        <p>Pilih dosen yang ingin dihapus:</p>
                        <div id="deleteDosens"></div>
                        <button type="submit" class="btn btn-danger" id="deleteButton">Hapus Semua Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Dosen Modal -->
    <div class="modal fade" id="addDosenModal" tabindex="-1" role="dialog" aria-labelledby="addDosenModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDosenModalLabel">Tambah Dosen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <form action="{{ route('admin.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="dosen">
                            <div class="form-group">
                                <label>Nama Dosen</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Kode Dosen</label>
                                <input type="text" name="kode_dosen" class="form-control" required>
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
    <!-- Edit Dosen Modal -->
    <div class="modal fade" id="editDosenModal" tabindex="-1" role="dialog" aria-labelledby="editDosenModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDosenModalLabel">Edit Dosen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editDosenForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" id="editDosenName" class="form-control"
                                autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label>Kode Dosen</label>
                            <input type="text" name="kode_dosen" id="editDosenKode" class="form-control" required>
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
