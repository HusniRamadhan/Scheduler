@extends('ui_dashboard.dashboard')
@section('title', 'Pra-LIRS Mahasiswa')
@section('headScript')
    <style>
        .total-makul-sks {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener for input buttons
            document.querySelectorAll('.input-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const value = this.getAttribute('data-value');
                    const detail = this.getAttribute('data-detail');
                    const semester = this.getAttribute('data-semester');

                    // Redirect to the input page with query parameters
                    window.location.href =
                        `/user/pralirs/input?id=${id}&value=${value}&detail=${encodeURIComponent(detail)}&semester=${semester}`;
                });
            });

            // Set the semester dropdown based on URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const semester = urlParams.get('semester');
            if (semester) {
                document.getElementById('semester').value = semester;
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener for 'Cek Input' buttons
            document.querySelectorAll('.cek-input-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const kodeMasaInput = this.getAttribute('data-value');

                    // Make AJAX request to fetch makul_input data
                    fetch(`/user/pralirs/makulInputs/${kodeMasaInput}`)
                        .then(response => response.json())
                        .then(data => {
                            let tableContent = '';
                            let totalSKS = 0;
                            let totalMakul = 0;

                            data.makul_inputs.forEach(input => {
                                const makulInputData = JSON.parse(input.makul_input);
                                makulInputData.forEach((makul, i) => {
                                    // Add table row for each mata kuliah
                                    tableContent += `
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${makul.mata_kuliah}</td>
                                    <td>${makul.sks}</td>
                                </tr>`;
                                    // Increment total mata kuliah count and SKS sum
                                    totalMakul++;
                                    totalSKS += parseInt(makul.sks);
                                });
                            });

                            // Update the table content in the modal
                            document.querySelector('#modal-body-content tbody').innerHTML =
                                tableContent;

                            // Display total count of mata kuliah and total SKS
                            document.getElementById('totalMakul').textContent =
                                `Total Mata Kuliah: ${totalMakul}`;
                            document.getElementById('totalSKS').textContent =
                                `Total SKS: ${totalSKS} SKS`;
                        })
                        .catch(error => console.error('Error fetching makul_inputs:', error));
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('#masaInputTableBody tr');
            rows.forEach(row => {
                const semester = parseInt(row.getAttribute('data-semester'), 10);
                if (semester > 14) {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            {{-- Tabel Masa Input Pra Lirs --}}
            <div class="card card-default card-outline">
                <div class="card-header">
                    <h3 class="card-title">Tabel Masa Input Pra Lirs</h3>
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
                                <th width="5%">Semester</th>
                                <th width="10%">Jangka Waktu</th>
                                <th>Keterangan</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="masaInputTableBody">
                            @foreach ($masaInputs as $masaInput)
                                @php
                                    $currentDate = \Carbon\Carbon::now();
                                    $dateRange = explode(' - ', $masaInput->jangka_waktu);
                                    $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dateRange[0]);
                                    $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dateRange[1]);

                                    $isBeforeStart = $currentDate->lt($startDate); // Current date is before start date
                                    $isAfterEnd = $currentDate->gt($endDate); // Current date is after end date

                                    // Calculate the current semester for the Mahasiswa
                                    $tahunAjaranParts = explode('/', $masaInput->tahun_ajaran);
                                    $tahunAjaranStart = (int) $tahunAjaranParts[0];
                                    $angkatanYear = $mahasiswa->angkatan;

                                    $semesterValue =
                                        $tahunAjaranStart >= $angkatanYear
                                            ? ($tahunAjaranStart - $angkatanYear) * 2 +
                                                ($masaInput->semester == 0 ? 1 : 2)
                                            : 0;

                                    // Check if the authenticated user has any makul input for the current masa input
                                    $makulInputExists = $makulInputs
                                        ->where('user_id', auth()->id())
                                        ->where('kode_masa_input', $masaInput->kode_masa_input)
                                        ->first();
                                @endphp
                                @if ($semesterValue > 2 && $semesterValue <= 14)
                                    <tr data-value="{{ $masaInput->kode_masa_input }}"
                                        data-detail="Tahun Ajaran: {{ $masaInput->tahun_ajaran }} {{ $masaInput->semester == 0 ? 'Ganjil' : 'Genap' }}"
                                        data-semester="{{ $semesterValue }}">
                                        <td>{{ $masaInput->semester == 0 ? 'Ganjil' : 'Genap' }} {{ $masaInput->tahun_ajaran }}</td>
                                        <td>{{ $semesterValue }}</td>
                                        <td>{{ $masaInput->jangka_waktu }}</td>
                                        <td>{{ $masaInput->keterangan }}</td>
                                        <td class="text-center">
                                            {{-- Handle button display logic based on date range and inputs --}}
                                            @if ($isBeforeStart)
                                                <button disabled class="btn btn-sm btn-warning">
                                                    Waktu Membuat Masukan Pra-LIRS Belum Dimulai
                                                </button>
                                            @elseif ($isAfterEnd && !$makulInputExists)
                                                {{-- If after the date range and no input exists, show "Waktu Input Sudah Terlewati" --}}
                                                <button disabled class="btn btn-sm btn-danger">
                                                    Waktu Membuat Masukan Pra-LIRS Sudah Terlewati
                                                </button>
                                            @elseif ($isAfterEnd && $makulInputExists)
                                                {{-- If after the date range and input exists, show "Waktu Input Selesai" --}}
                                                <button disabled class="btn btn-sm btn-success">
                                                    Waktu Membuat Masukan Pra-LIRS Sudah Selesai
                                                </button>
                                            @elseif ($makulInputExists)
                                                {{-- During input period --}}
                                                <button type="button" class="btn btn-sm btn-primary input-btn"
                                                    data-id="{{ $masaInput->id }}"
                                                    data-value="{{ $masaInput->kode_masa_input }}"
                                                    data-detail="Tahun Ajaran: {{ $masaInput->tahun_ajaran }} {{ $masaInput->semester == 0 ? 'Ganjil' : 'Genap' }}"
                                                    data-semester="{{ $semesterValue }}">Ganti Pra-LIRS</button>
                                            @else
                                                {{-- During input period --}}
                                                <button type="button" class="btn btn-sm btn-primary input-btn"
                                                    data-id="{{ $masaInput->id }}"
                                                    data-value="{{ $masaInput->kode_masa_input }}"
                                                    data-detail="Tahun Ajaran: {{ $masaInput->tahun_ajaran }} {{ $masaInput->semester == 0 ? 'Ganjil' : 'Genap' }}"
                                                    data-semester="{{ $semesterValue }}">Pra-LIRS</button>
                                            @endif

                                            {{-- Check if there is input for the current user and masa input --}}
                                            @if ($makulInputExists)
                                                {{-- Show "Cek Input" if input exists --}}
                                                <button class="btn btn-sm btn-default cek-input-btn"
                                                    data-value="{{ $masaInput->kode_masa_input }}" data-toggle="modal"
                                                    data-target="#modal-default">
                                                    Cek Pra-LIRS
                                                </button>
                                            @else
                                                @if (!$isBeforeStart)
                                                    {{-- If input period is not before start and no input exists, show "Tidak Ada Input" --}}
                                                    <button disabled class="btn btn-sm btn-danger">
                                                        Tidak Ada Pra-LIRS
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <div class="modal fade" id="modal-default" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-default">
                <div class="modal-header">
                    <h4 class="modal-title">
                        @if ($activeInput)
                            Pra-LIRS Tahun Ajaran {{ $activeInput->semester == 0 ? 'Ganjil' : 'Genap' }} {{ $activeInput->tahun_ajaran }}
                        @else
                            Tidak Ada Pra-LIRS
                        @endif
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center">No data available</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Add total Mata Kuliah and SKS display here -->
                    <div class="total-makul-sks">
                        <p id="totalMakul">Total Mata Kuliah: 0</p>
                        <p id="totalSKS">Total SKS: 0 SKS</p>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
