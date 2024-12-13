<table class="table table-bordered">
    <thead>
        <tr>
            <th width="5%">No.</th>
            <th>Mata Kuliah</th>
            <th width="10%">Jumlah Mahasiswa</th>
            <th width="10%">Kapasitas</th>
            <th width="20%">Aksi Jumlah Kelas</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($makuls as $makul)
            @php
                // Set default semester value
                $semesterValue = $makul->semester;

                // Check if Mata Kuliah is an elective (Pilihan)
                if ($makul->IsPilihan) {
                    // Get the selected semester value from the dropdown
                    $selectedSemester = request('semester', 0); // Default to 0 (Gasal) if not provided

                    // Adjust the semester value based on the selected semester
                    if ($selectedSemester == 0) {
                        $semesterValue = 7; // Assign to semester 7 for Gasal
                    } elseif ($selectedSemester == 1) {
                        $semesterValue = 6; // Assign to semester 6 for Genap
                    }
                }
            @endphp

            <tr id="row-makul-{{ $makul->kode }}" data-kode="{{ $makul->kode }}"
                data-mata-kuliah="{{ $makul->mata_kuliah }}" data-sks="{{ $makul->sks }}"
                data-semester="{{ $semesterValue }}" {{-- Updated with adjusted semester --}}
                data-info="{{ $makul->mata_kuliah }} SEM-{{ $semesterValue }} SKS-{{ $makul->sks }}">

                <!-- Other row elements remain unchanged -->
                <td>{{ $loop->iteration }}</td>
                <td data-kode="{{ $makul->kode }}">
                    <div class="row">
                        {{ $makul->mata_kuliah }}
                    </div>
                    <div class="row">
                        ({{ $makul->kode }})
                    </div>
                </td>
                <td class="jumlah-mahasiswa" id="jumlah-mahasiswa-{{ $makul->kode }}">
                    {{ $jumlahMahasiswa[$makul->kode] ?? 0 }}
                </td>
                <td>
                    <input type="number" name="kuota_{{ $makul->kode }}" id="kuota-{{ $makul->kode }}"
                        class="form-control" min="1" max="99"
                        value="{{ old('kuota_' . $makul->kode, 20) }}">
                </td>
                <td>
                    <button class="btn btn-primary btn-buat-kelas" data-kode="{{ $makul->kode }}"
                        data-is-pilihan="{{ $makul->IsPilihan }}">Otomatis</button>

                    <!-- Manual Kelas Button Group Dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown"
                            aria-expanded="false">Manual</button>
                        <div class="dropdown-menu" role="menu">
                            @for ($i = 1; $i <= 6; $i++)
                                <a class="dropdown-item manual-kelas-option" href="#"
                                    data-kode="{{ $makul->kode }}" data-kelas="{{ $i }}"
                                    data-is-pilihan="{{ $makul->IsPilihan }}">{{ $i }}</a>
                            @endfor
                        </div>
                    </div>

                    <button class="btn btn-danger btn-hapus-semua-kelas" data-kode="{{ $makul->kode }}">Hapus
                        Semua</button>
                </td>
            </tr>
            <tr class="class-rows" id="class-rows-{{ $makul->kode }}" style="background-color:#EEEEEE">
                <td colspan="5">
                    <table class="table table-bordered">
                        <tbody style="background-color:#FFFFFF">
                            <!-- Dynamically generated class rows will be inserted here -->
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
