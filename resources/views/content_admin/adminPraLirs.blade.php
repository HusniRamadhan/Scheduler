@extends('ui_dashboard.dashboard')
@section('title', 'Administrasi Pra-Lirs')
@section('headScript')
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!-- Include SweetAlert2 CSS and JS files -->
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Tahun Ajaran Pra-LIRS</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Input masa aktif input pra lirs ke database --}}
                    <form action="{{ route('masa_input.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tahun Ajaran</label>
                                    <div class="row">
                                        {{-- Format Select YYYY/YYYY+1, dengan batas 7 tahun dari sekarang --}}
                                        <select name="tahun_ajaran" class="form-control">
                                            @for ($i = 0; $i <= 7; $i++)
                                                @php
                                                    $currentYear = date('Y');
                                                    $year = $currentYear + $i;
                                                    $nextYear = $year + 1;
                                                    $academicYear = $year . '/' . $nextYear;
                                                @endphp
                                                <option value="{{ $academicYear }}">{{ $academicYear }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester" class="custom-select rounded-0">
                                        <option value="1">Ganjil</option>
                                        <option value="0">Genap</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Durasi Aktif Pra-LIRS (DD/MM/YYYY)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        {{-- Input DatePicker dengan model DD/MM/YYYY - DD/MM/YYYY --}}
                                        <input type="text" name="masa_input" class="form-control float-right datepicker"
                                            id="reservation">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputDescription">Deskripsi</label>
                                    <textarea name="keterangan" id="inputDescription" class="form-control" rows="4"
                                        placeholder="Tulis deskripsi jika diperlukan (Opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" style="float:right;" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
            {{-- Tabel Masa Input Pra Lirs --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Tabel Periode Tahun Ajaran</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="masaInputTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="10%">Tahun Ajaran</th>
                                <th width="10%">Semester</th>
                                <th width="10%">Jangka Waktu</th>
                                <th>Keterangan</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="masaInputTableBody">
                            @foreach ($masaInputs as $masaInput)
                                <tr>
                                    <td>{{ $masaInput->tahun_ajaran }}</td>
                                    <td>{{ $masaInput->semester == 0 ? 'Ganjil' : 'Genap' }}</td>
                                    <td>{{ $masaInput->jangka_waktu }}</td>
                                    <td>{{ $masaInput->keterangan }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary edit-btn"
                                            data-id="{{ $masaInput->id }}">Ubah</button>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $masaInput->id }}">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data Tahun Ajaran Pra-LIRS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form untuk mengedit data Masa Input -->
                    <form id="editForm">
                        @csrf
                        <div class="form-group">
                            <label for="tahun_ajaran_edit">Tahun Ajaran</label>
                            <select name="tahun_ajaran" class="form-control" id="tahun_ajaran_edit">
                                @for ($i = 0; $i <= 7; $i++)
                                    @php
                                        $currentYear = date('Y');
                                        $year = $currentYear + $i;
                                        $nextYear = $year + 1;
                                        $academicYear = $year . '/' . $nextYear;
                                    @endphp
                                    <option value="{{ $academicYear }}">{{ $academicYear }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="semester_edit">Semester</label>
                            <select name="semester" class="form-control" id="semester_edit">
                                <option value="0">Ganjil</option>
                                <option value="1">Genap</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="reservationEdit">Durasi Aktif Pra-LIRS (DD/MM/YYYY)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" name="masa_input" class="form-control float-right datepicker"
                                    id="reservationEdit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="keterangan_edit">Deskripsi</label>
                            <textarea name="keterangan" class="form-control" id="keterangan_edit" rows="4"
                                placeholder="Tulis deskripsi jika diperlukan (Opsional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveEdit">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    {{-- Year Picker --}}
    <script src="{{ asset('dist/js/adds/yearPicker.js') }}"></script>
    {{-- Date Picker Adminlte --}}
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script src="{{ asset('plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
    <script src="{{ asset('plugins/dropzone/min/dropzone.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize date range picker for the main form
            $('#reservation').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });

            // Edit Button: Open the modal and load the data for the selected record
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id'); // Get the ID of the record
                $('#saveEdit').data('id', id); // Store the ID in the save button for later use

                // AJAX request to fetch the data
                $.ajax({
                    url: `/admin/masa_input/${id}/edit`,
                    method: 'GET',
                    success: function(data) {
                        // Populate the modal form fields with the data from the server
                        $('#tahun_ajaran_edit').val(data.tahun_ajaran);
                        $('#semester_edit').val(data.semester);
                        $('#reservationEdit').val(data.start_date + ' - ' + data.end_date);
                        $('#keterangan_edit').val(data.keterangan);

                        // Open the edit modal
                        $('#editModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to fetch record data.', 'error');
                    }
                });
            });

            // Save Edit Button: Handle the form submission
            $('#saveEdit').on('click', function() {
                let id = $(this).data('id'); // Get the stored ID from the button

                // Disable the button to prevent multiple clicks
                $(this).prop('disabled', true);

                // Submit the form data using AJAX
                $.ajax({
                    url: `/admin/masa_input/${id}`, // Send the request to the correct endpoint
                    method: 'PUT',
                    data: $('#editForm').serialize(), // Serialize the form data for submission
                    success: function(response) {
                        // Show success message and reload the page
                        Swal.fire({
                            title: 'Success',
                            text: 'Data updated successfully',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // Reload to reflect the updated data
                            }
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Error updating data.', 'error');
                    },
                    complete: function() {
                        // Re-enable the button after the AJAX call completes
                        $('#saveEdit').prop('disabled', false);
                    }
                });
            });

            // Handle Delete Button click with SweetAlert2 confirmation
            $(document).on('click', '.delete-btn', function() {
                let masaInputId = $(this).data('id'); // Get the record ID

                // Show a confirmation dialog before deleting
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, send the delete request via AJAX
                        $.ajax({
                            url: '/admin/masa_input/' + masaInputId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content') // CSRF token for security
                            },
                            success: function(response) {
                                // Show success message and reload the page after deletion
                                Swal.fire('Deleted!', 'Your record has been deleted.',
                                        'success')
                                    .then(() => {
                                        location
                                    .reload(); // Reload to reflect changes
                                    });
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'There was an error deleting the record.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Initialize DataTable
            $('#masaInputTable').DataTable();
        });
    </script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('dist/js/ajax-form-pralirs.js') }}"></script>
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
@endsection
