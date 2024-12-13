@extends('ui_dashboard.dashboard')
@section('title', 'Pengisian Pra-LIRS')
@section('pageSize', 'min-height: 1300px;')
@section('css')
    <!-- Drag and Drop Sorting -->
    <link rel="stylesheet" href="{{ asset('dist/css/dragndropsorting.css') }}">
    <style>
        /* Define background colors for SKS thresholds */
        .sks-low {
            background-color: #d4edda;
        }

        /* Green for SKS <= 10 */
        .sks-medium {
            background-color: #fff3cd;
        }

        /* Yellow for 11 <= SKS <= 18 */
        .sks-high {
            background-color: #f8d7da;
        }

        /* Red for 19 <= SKS <= 24 */
    </style>
    <style>
        span.square {
            background: #0266a8;
            background: -webkit-linear-gradient(#0266a8 0%, #005389 100%);
            background: -o-linear-gradient(#0266a8 0%, #005389 100%);
            background-image: linear-gradient(#0266a8 0%, #005389 100%);
            border-radius: 5px;
            border-bottom: 1px solid #FFF;
            display: inline-block;
            height: 28px;
            width: 28px;
        }

        .arrow {
            position: relative;
        }

        .arrow.up:after {
            position: absolute;
            top: 9px;
            left: 7px;
            width: 0;
            height: 0;
            content: '';
            border-style: solid;
            border-width: 0 7px 8px 7px;
            border-color: transparent transparent #FFFFFF transparent;
            border-radius: 2px;
        }

        .arrow.down:after {
            position: absolute;
            top: 10px;
            left: 7px;
            width: 0;
            height: 0;
            content: '';
            border-style: solid;
            border-width: 8px 7px 0 7px;
            border-color: #FFFFFF transparent transparent transparent;
            border-radius: 2px;
        }
    </style>
    <style>
        .nav.nav-pills a {
            display: inline-flex;
            align-items: center;
            padding-left: 1.25rem;
            /* Padding kiri */
            padding-right: 1.25rem;
            /* Padding kanan */
        }
    </style>
@endsection
@section('headScript')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
@endsection
@section('script')
    <!-- Drag and Drop Sorting -->
    <script src="{{ asset('dist/js/dragndropsorting.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dapatkan nilai dari elemen dengan id "semester"
            var semesterValue = parseInt(document.getElementById('semester').value);

            // Sembunyikan semua tab awalnya
            function hideAllTabs() {
                // Sembunyikan semua tab-link di nav-pills
                for (var i = 1; i <= 7; i++) {
                    document.querySelector('a[href="#semester' + i + '"]').style.display = 'none';
                }
                document.querySelector('a[href="#pilihan"]').style.display = 'none';
                document.querySelector('a[href="#khusus"]').style.display = 'none';
            }

            // Tampilkan tab sesuai aturan
            function showTabs(tabsToShow, showPilihan = false, showKhusus = false) {
                hideAllTabs(); // Sembunyikan semua tab-link dulu
                tabsToShow.forEach(function(tab) {
                    document.querySelector('a[href="#semester' + tab + '"]').style.display =
                        'block'; // Tampilkan tab
                });
                if (showPilihan) {
                    document.querySelector('a[href="#pilihan"]').style.display = 'block'; // Tampilkan tab Pilihan
                }
                if (showKhusus) {
                    document.querySelector('a[href="#khusus"]').style.display = 'block'; // Tampilkan tab Khusus
                }
            }

            // Tampilkan tab-pane yang sesuai dengan semesterValue
            switch (semesterValue) {
                case 1:
                    showTabs([1]);
                    break;
                case 2:
                    showTabs([2]);
                    break;
                case 3:
                    showTabs([1, 3, 5]);
                    break;
                case 4:
                    showTabs([2, 4, 6]);
                    break;
                case 5:
                    showTabs([1, 3, 5, 7], true);
                    break;
                case 6:
                    showTabs([2, 4, 6], true, true);
                    break;
                case 7:
                    showTabs([1, 3, 5, 7], true, true);
                    break;
                case 8:
                case 10:
                case 12:
                case 14:
                    showTabs([2, 4, 6], true, true);
                    break;
                case 9:
                case 11:
                case 13:
                    showTabs([1, 3, 5, 7], true, true);
                    break;
                default:
                    hideAllTabs(); // Jika tidak ada kondisi yang cocok, sembunyikan semuanya
                    break;
            }
        });
    </script>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header" data-card-widget="collapse">
                            <h3 class="card-title">Pengisian Pra-LIRS</h3>
                        </div>
                        <div class="card-body">
                            {{-- Input masa aktif input pra lirs ke database --}}
                            <form action="{{ route('store.input') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label>Cara Penggunaan</label>
                                            <div class="info-box" style="background-color:#f0f0f0">
                                                <div class="row">
                                                    <ol>
                                                        <li style="height: 36px;">Pilih Mata Kuliah pada tabel bagian kiri
                                                            menggunakan
                                                            <span class="btn btn-success"
                                                                style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-plus"></i>
                                                            </span>
                                                        </li>
                                                        <li style="height: 36px;">Mata Kuliah yang dipilih akan muncul di
                                                            bagian kanan.</li>
                                                        <li style="height: 36px;">Mata Kuliah yang ada di bagian kanan dapat
                                                            dihapus menggunakan
                                                            <span class="btn btn-danger"
                                                                style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-trash"></i>
                                                            </span>
                                                        </li>
                                                        <li style="height: 36px;">Urutan Mata Kuliah dapat diatur ulang
                                                            menggunakan <em>drag and drop</em>.
                                                        </li>
                                                        <li style="height: 36px;">Mata Kuliah dengan jumlah SKS ≤ 10 akan
                                                            memiliki teks warna
                                                            <strong><span style="color: #28a745;">hijau</span>.</strong>
                                                        </li>
                                                        <li style="height: 36px;">Mata Kuliah dengan jumlah SKS antara 11
                                                            hingga 18 akan memiliki teks warna
                                                            <strong><span style="color: #ffc107;">kuning</span>.</strong>
                                                        </li>
                                                        <li style="height: 36px;">Mata Kuliah dengan jumlah SKS > 18 akan
                                                            memiliki teks warna
                                                            <strong><span style="color: #dc3545;">merah</span>.</strong>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group" style="Display: none;">
                                            <label>Semester</label>
                                            <select class="custom-select rounded-0" id="semester" name="semester">
                                                <option value="{{ request()->get('semester') }}"
                                                    {{ request()->get('semester') == request()->get('semester') ? 'selected' : '' }}>
                                                    {{ request()->get('semester') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Total SKS dari Mata Kuliah yang Dipilih</label>
                                            <input type="text" id="inputName" class="form-control" value="0"
                                                disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Total SKS yang Dapat Dipilih</label>
                                            <input type="text" id="inputmax" class="form-control" value="24"
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="display:none;">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="tahunAjaranSelect">Tahun Ajaran Input</label>
                                            <select id="tahunAjaranSelect" name="tahunAjaranSelect" class="form-control">
                                                @if (isset($value) && isset($detail))
                                                    <option value="{{ $value }}">{{ $detail }}</option>
                                                @else
                                                    <option value="">Please select a valid input period</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="display:none;">
                                    {{-- <div class="row"> --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="inputDescription">Input Configuration</label>
                                            <textarea id="inputDescription" name="inputDescription" class="form-control" rows="4" style="height: 121px;"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="submitButton" name="action" value="create" style="float:right;"
                                    class="btn btn-primary">
                                    Simpan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <div class="card" bis_skin_checked="1">
                                <div class="card-header p-2" bis_skin_checked="1">
                                    <ul class="nav nav-pills align-center">
                                        <a><strong>Semester</strong></a>
                                        @for ($i = 1; $i <= $maxSemester; $i++)
                                            <li class="nav-item">
                                                <a class="nav-link" href="#semester{{ $i }}" data-toggle="tab"
                                                    style="display:none;">{{ $i }}</a>
                                            </li>
                                        @endfor
                                        <li class="nav-item">
                                            <a class="nav-link" href="#pilihan" data-toggle="tab"
                                                style="display:none;">Pilihan</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#khusus" data-toggle="tab"
                                                style="display:none;">Khusus</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body" bis_skin_checked="1">
                                    <div class="tab-content" bis_skin_checked="1">
                                        @for ($i = 1; $i <= $maxSemester; $i++)
                                            <div class="tab-pane {{ $i == 1 ? 'active' : '' }}"
                                                id="semester{{ $i }}" bis_skin_checked="1">
                                                {{-- Semester {{ $i }} --}}
                                                <div class="row">
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            @foreach ($makuls as $makul)
                                                                @if ($makul->semester == $i)
                                                                    <tr data-semester="{{ $i }}"
                                                                        data-kode="{{ $makul->kode }}">
                                                                        <td class="align-middle">{{ $makul->mata_kuliah }}
                                                                        </td>
                                                                        <td class="align-middle" style="width: 20px">
                                                                            {{ $makul->sks }}</td>
                                                                        <td class="text-center" style="width: 100px">
                                                                            <span class="btn btn-success"
                                                                                data-makul="{{ $makul->mata_kuliah }}"
                                                                                data-sks="{{ $makul->sks }}"
                                                                                data-kode="{{ $makul->kode }}"
                                                                                style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                                                <i class="fas fa-plus"></i>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endfor
                                        {{-- Mata Kuliah Pilihan --}}
                                        <div class="tab-pane" id="pilihan" bis_skin_checked="1">
                                            <div class="row">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        @foreach ($makulPilihan as $makul)
                                                            <tr data-semester="{{ $makul->semester }}"
                                                                data-kode="{{ $makul->kode }}">
                                                                <td class="align-middle">{{ $makul->mata_kuliah }}</td>
                                                                <td class="align-middle" style="width: 20px">
                                                                    {{ $makul->sks }}</td>
                                                                <td class="text-center" style="width: 100px">
                                                                    <span class="btn btn-success"
                                                                        data-makul="{{ $makul->mata_kuliah }}"
                                                                        data-sks="{{ $makul->sks }}"
                                                                        data-kode="{{ $makul->kode }}"
                                                                        style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                                        <i class="fas fa-plus"></i>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        {{-- Mata Kuliah Khusus --}}
                                        <div class="tab-pane" id="khusus" bis_skin_checked="1">
                                            <div class="row">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        @foreach ($specialCourses as $makul)
                                                            <tr data-semester="{{ $makul->semester }}"
                                                                data-kode="{{ $makul->kode }}">
                                                                <td class="align-middle">{{ $makul->mata_kuliah }}</td>
                                                                <td class="align-middle" style="width: 20px">
                                                                    {{ $makul->sks }}</td>
                                                                <td class="text-center" style="width: 100px">
                                                                    <span class="btn btn-success"
                                                                        data-makul="{{ $makul->mata_kuliah }}"
                                                                        data-sks="{{ $makul->sks }}"
                                                                        data-kode="{{ $makul->kode }}"
                                                                        style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                                        <i class="fas fa-plus"></i>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            {{-- Input Box Matakuliah --}}
                            <div id="sortable-list" class="sortable-list">
                                <table class="table">
                                    <tbody id="selected-courses">
                                        {{-- Input Mata Kuliah di sini --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
