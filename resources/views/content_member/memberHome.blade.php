@extends('ui_dashboard.dashboard')
@section('title', 'Dashboard Mahasiswa')
@section('script')
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-4">
                    <div class="card card-success"
                        data-kode="{{ $activeInput ? $activeInput->kode_masa_input : ($nextActiveInput ? $nextActiveInput->kode_masa_input : 'N/A') }}">
                        <div class="card-header">
                            <h3 class="card-title">Periode Tahun Ajaran</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            Tanggal Hari Ini:
                            <h2>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</h2>

                            @php
                                use Carbon\Carbon;

                                // Function to format jangka_waktu if it exists
                                function formatJangkaWaktu($jangka_waktu)
                                {
                                    if ($jangka_waktu) {
                                        [$start_date, $end_date] = explode(' - ', $jangka_waktu);
                                        $formatted_start_date = Carbon::createFromFormat(
                                            'd/m/Y',
                                            $start_date,
                                        )->translatedFormat('j F Y');
                                        $formatted_end_date = Carbon::createFromFormat(
                                            'd/m/Y',
                                            $end_date,
                                        )->translatedFormat('j F Y');
                                        return "{$formatted_start_date} - {$formatted_end_date}";
                                    }
                                    return null;
                                }
                            @endphp

                            @if ($activeInput)
                                Tahun Ajaran:
                                <h2>{{ $activeInput->semester == '0' ? 'Ganjil' : 'Genap' }}
                                    {{ $activeInput->tahun_ajaran }}</h2>
                                Periode Mengisi Pra-LIRS:
                                <h2>{{ formatJangkaWaktu($activeInput->jangka_waktu) }}</h2>
                            @else
                                Tahun Ajaran:
                                <h2>{{ $nextActiveInput ? $nextActiveInput->tahun_ajaran : 'Tidak Ada' }}</h2>
                                Periode Mengisi Pra-LIRS:
                                <h2>Tidak Ada Periode yang Aktif</h2>
                                Periode Berikutnya:
                                <h2>{{ $nextActiveInput ? formatJangkaWaktu($nextActiveInput->jangka_waktu) : 'Tidak Ada' }}
                                </h2>
                                @if ($nextActiveInput)
                                    <h3>Periode mengisi Pra-LIRS {{ $nextActiveInput->tahun_ajaran }} Belum Dimulai</h3>
                                @endif
                            @endif
                        </div>
                        <div class="card-footer">
                            @if ($activeInput)
                                <a href="{{ route('userPraLirs') }}" class="btn btn-sm btn-primary">
                                    Arahkan ke halaman Pra-Lirs
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-primary" disabled>Belum bisa dilakukan
                                    input</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="card card-blue"
                        data-kode="{{ $activeInput ? $activeInput->kode_masa_input : ($nextActiveInput ? $nextActiveInput->kode_masa_input : 'N/A') }}">
                        <div class="card-header">
                            <h3 class="card-title">
                                Periode Pra-LIRS
                                @if ($activeInput)
                                    {{ $activeInput->tahun_ajaran . ' ' . ($activeInput->semester == 0 ? 'Ganjil' : 'Genap') }}
                                @elseif ($nextActiveInput)
                                    {{ $nextActiveInput->tahun_ajaran . ' ' . ($nextActiveInput->semester == 0 ? 'Ganjil' : 'Genap') }}
                                @else
                                    N/A
                                @endif
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (!empty($makulData))
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Mata Kuliah</th>
                                            <th>SKS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($makulData as $makul)
                                            <tr>
                                                <td>{{ $makul['urutan'] }}</td>
                                                <td>{{ $makul['mata_kuliah'] }}</td>
                                                <td>{{ $makul['sks'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="total-makul-sks">
                                    <p>Total Mata Kuliah: {{ count($makulData) }}</p>
                                    <p>Total SKS: {{ array_sum(array_column($makulData, 'sks')) }} SKS</p>
                                </div>
                            @else
                                <p>
                                    @if ($activeInput)
                                        Belum Ada Pra-LIRS Tahun Ajaran
                                        {{ $activeInput->semester == '0' ? 'Ganjil' : 'Genap' }}
                                        {{ $activeInput->tahun_ajaran }}
                                    @elseif ($nextActiveInput)
                                        Input Belum Dimulai
                                        {{-- Input {{ $nextActiveInput->tahun_ajaran }} Belum Dimulai --}}
                                    @else
                                        Belum Ada Pra-LIRS Tahun Ajaran {{ $activeInput->semester == '0' ? 'Ganjil' : 'Genap' }} {{ $activeInput->tahun_ajaran }} yang Dibuat
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
