@foreach ($makuls as $makul)
    @php
        // Pengecekan jika ini adalah mata kuliah pilihan dan sesuaikan nilai semesternya
        $semesterValue = $makul->semester; // Default nilai semester
        if ($isMakulPilihan) {
            // Ubah nilai semester jika isMakulPilihan true
            if ($semester == 0) {
                $semesterValue = 7;
            } elseif ($semester == 1) {
                $semesterValue = 6;
            }
        }
    @endphp

    <tr id="row-makul-{{ $makul->kode }}" data-kode="{{ $makul->kode }}" data-mata-kuliah="{{ $makul->mata_kuliah }}"
        data-sks="{{ $makul->sks }}" data-semester="{{ $semesterValue }}"
        data-info="{{ $makul->mata_kuliah }} SEM-{{ $semesterValue }} SKS-{{ $makul->sks }}">

        @if ($isMakulPilihan)
            <!-- Checkbox column for Mata Kuliah Pilihan -->
            <td>
                <div class="icheck-pomegranate d-inline">
                    <input type="checkbox" class="makul-checkbox" id="checkbox{{ $makul->kode }}" name="makulPilihan[]"
                        value="{{ $makul->kode }}">
                    <label for="checkbox{{ $makul->kode }}"></label>
                </div>
            </td>
        @endif

        <!-- No. Column (Integer) -->
        <td>{{ $loop->iteration }}</td>

        <!-- Mata Kuliah stored in data attribute -->
        <td data-kode="{{ $makul->kode }}">
            <div class="row">
                {{ $makul->mata_kuliah }}
            </div>
            <div class="row">
                ({{ $makul->kode }})
            </div>
        </td>

        <!-- Jumlah Mahasiswa -->
        <td class="jumlah-mahasiswa">{{ $jumlahMahasiswa[$makul->kode] ?? 0 }}</td>

        <!-- Kuota Kelas -->
        <td>
            <input type="number" name="kuota_{{ $makul->kode }}" id="kuota-{{ $makul->kode }}" class="form-control"
                min="1" max="99" value="{{ old('kuota_' . $makul->kode, 20) }}">
        </td>

        <!-- Aksi (Buat Kelas, Manual Kelas, Hapus Semua Kelas) -->
        <td>
            <button class="btn btn-primary btn-buat-kelas" data-kode="{{ $makul->kode }}">Otomatis</button>

            <!-- Manual Kelas Button Group Dropdown -->
            <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown"
                    aria-expanded="false">Manual
                </button>
                <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item manual-kelas-option" href="#" data-kode="{{ $makul->kode }}"
                        data-kelas="1">1</a>
                    <a class="dropdown-item manual-kelas-option" href="#" data-kode="{{ $makul->kode }}"
                        data-kelas="2">2</a>
                    <a class="dropdown-item manual-kelas-option" href="#" data-kode="{{ $makul->kode }}"
                        data-kelas="3">3</a>
                    <a class="dropdown-item manual-kelas-option" href="#" data-kode="{{ $makul->kode }}"
                        data-kelas="4">4</a>
                    <a class="dropdown-item manual-kelas-option" href="#" data-kode="{{ $makul->kode }}"
                        data-kelas="5">5</a>
                    <a class="dropdown-item manual-kelas-option" href="#" data-kode="{{ $makul->kode }}"
                        data-kelas="6">6</a>
                </div>
            </div>

            <!-- Hapus Semua Kelas Button -->
            <button class="btn btn-danger btn-hapus-semua-kelas" data-kode="{{ $makul->kode }}">Hapus Semua</button>
        </td>
    </tr>

    <!-- Placeholder for class rows -->
    <tr class="class-rows" id="class-rows-{{ $makul->kode }}">
        <td colspan="@if ($isMakulPilihan) 6 @else 5 @endif">
            <table class="table table-bordered">
                <tbody>
                    <!-- Dynamically generated class rows will be inserted here -->
                </tbody>
            </table>
        </td>
    </tr>
@endforeach
