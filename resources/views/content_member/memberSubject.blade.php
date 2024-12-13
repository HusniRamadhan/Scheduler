@extends('ui_dashboard.dashboard')
@section('title', 'Daftar Mata Kuliah')
@section('headScript')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card"> {{-- collapsed-card --}}
                        <div class="card-header">
                            <h5 class="card-title">Data Mata Kuliah</h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6"></div>
                                    <div class="col-sm-12 col-md-6"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        {{-- Tabel Mata Kuliah --}}
                                        <table class="table table-bordered table-hover dataTable dtr-inline"
                                            id="tabel_makul">
                                            {{-- Nama Row Tabel --}}
                                            <thead>
                                                <tr>
                                                    <th rowspan="1" colspan="1" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending" width="5%">
                                                        #</th>
                                                    <th rowspan="1" colspan="1"
                                                        aria-label="Kode Mata Kuliah: activate to sort column ascending"width="25%">
                                                        Kode Mata Kuliah</th>
                                                    <th rowspan="1" colspan="1"
                                                        aria-label="Mata Kuliah: activate to sort column ascending"
                                                        width="50%">
                                                        Mata Kuliah</th>
                                                    <th rowspan="1" colspan="1"
                                                        aria-label="Semester: activate to sort column ascending"
                                                        width="10%">
                                                        Semester</th>
                                                    <th rowspan="1" colspan="1"
                                                        aria-label="SKS: activate to sort column ascending" width="10%">
                                                        SKS
                                                    </th>
                                                </tr>
                                            </thead>
                                            {{-- Isi Tabel --}}
                                            <tbody>
                                                {{-- Looping data makuls --}}
                                                @foreach ($makuls as $index => $makul)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $makul->kode }}</td>
                                                        <td>{{ $makul->mata_kuliah }}</td>
                                                        <td>{{ $makul->semester }}</td>
                                                        <td>{{ $makul->sks }}</td>
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
            $('#tabel_makul').DataTable();
        });
    </script>
@endsection
