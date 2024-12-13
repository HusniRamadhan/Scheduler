@extends('ui_dashboard.dashboard')
@section('title', 'Manajemen Kelas')
@section('pageSize', 'min-height: 1300px;')
@section('css')
    <style>
        /* Center text, make corners rounded, and bold the text in .toast-body */
        .toast-body {
            text-align: center;
            /* Center the text */
            border-radius: 25px;
            /* Make the corners rounded */
            font-weight: bold;
            /* Bold the text */
        }

        /* Center the h3 text inside the #noData div */
        #noData h3 {
            text-align: center;
            font-weight: bold;
        }
    </style>
    <style>
        /* Custom FAB Styles */
        .btn-fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1051;
            /* Higher than Bootstrap's modal */
        }
    </style>
@endsection
@section('headScript')
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the scroll to top button and the modal by their IDs
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');
            const buatJadwalModal = $('#buatJadwalModal');
            const modalBody = document.querySelector('.modal-body'); // Target modal body for scrolling

            // Add an event listener to the scroll to top button
            scrollToTopBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default action

                // Scroll the modal content to the top if the modal is visible
                if (buatJadwalModal.hasClass('show')) {
                    // Try scrolling the modal-content instead of modal-body
                    const modalContent = document.querySelector('.modal-content');
                    modalContent.scrollTo({
                        top: 0,
                        behavior: 'smooth' // Smooth scrolling within the modal
                    });
                }

                // Scroll the main page to the top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth' // Smooth scrolling for the page
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initially hide existData and noData divs
            $('#existData').hide();
            $('#noData').hide();

            // On refreshClassing button click
            $('#pilihMasaInput').change(function() {
                var selectedMasa = $(this).val();
                // Check if the selected kode_masa_input exists in makul_class table
                $.ajax({
                    url: '/admin/managekelas/check-makul-class',
                    method: 'POST',
                    data: {
                        kode_masa_input: selectedMasa,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        var selectedText = $('#pilihMasaInput option:selected').text();
                        $('.toast-body').text(selectedText);
                        $('.toast-body').attr('data-masa', selectedMasa);

                        if (response.exists) {
                            $('#saveClassData').text('Ubah Data Kelas'); // Change button text
                            $('#saveClassData').prop('disabled', false);
                        } else {
                            $('#saveClassData').text('Simpan Data Kelas'); // Reset button text
                            $('#saveClassData').prop('disabled', false);
                        }
                    }
                });

                $.ajax({
                    url: '/admin/managekelas/check-masa-input', // Route for checking data
                    method: 'POST',
                    data: {
                        kode_masa_input: selectedMasa,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        var selectedText = $('#pilihMasaInput option:selected').text();
                        $('.toast-body').text(selectedText);
                        $('.toast-body').attr('data-masa', selectedMasa);

                        if (response.exists) {
                            // If the data exists, show existData and change button to "Ubah Data Kelas"
                            $('#existData').show();
                            $('#noData').hide();
                            $('#saveClassData').prop('disabled', false);
                            $('.toast-body').removeClass('bg-warning bg-danger').addClass(
                                'bg-success');
                        } else {
                            // If no data exists, show noData and change button to "Simpan Data Kelas"
                            $('#existData').hide();
                            $('#noData').show();
                            $('#saveClassData').prop('disabled', true);
                            $('.toast-body').removeClass('bg-success bg-warning').addClass(
                                'bg-danger');
                        }
                    }
                });
            });

            // On saveClassData button click (for saving or updating data)
            $('#saveClassData').click(function() {
                var kodeMasaInput = $('#pilihMasaInput').val();
                var dataKelas = $('#inputDescription').val();

                // Check if the button text is "Ubah Data Kelas"
                if ($('#saveClassData').text() === 'Ubah Data Kelas') {
                    // Show a confirmation alert
                    if (confirm('Data sudah ada, apakah ingin diubah?')) {
                        // Perform AJAX request to update the existing record
                        $.ajax({
                            url: '/admin/managekelas/update-makul-class',
                            method: 'POST',
                            data: {
                                kode_masa_input: kodeMasaInput,
                                data_kelas: dataKelas,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert('Data kelas berhasil diperbarui!');
                                } else {
                                    alert('Gagal memperbarui data kelas.');
                                }
                            },
                            error: function() {
                                alert('Terjadi kesalahan saat memperbarui data.');
                            }
                        });
                    }
                } else {
                    // Save new data
                    $.ajax({
                        url: '/admin/managekelas/save-makul-class',
                        method: 'POST',
                        data: {
                            kode_masa_input: kodeMasaInput,
                            data_kelas: dataKelas,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Data kelas berhasil disimpan!');
                            } else {
                                alert('Gagal menyimpan data kelas.');
                            }
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat menyimpan data.');
                        }
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#pilihMasaInput').change(function() {
                var kodeMasaInput = $('#pilihMasaInput').val();

                // AJAX request to getJumlahMahasiswa
                $.ajax({
                    url: '{{ route('get.jumlah.mahasiswa') }}',
                    method: 'GET',
                    data: {
                        kode_masa_input: kodeMasaInput
                    },
                    success: function(response) {
                        // Update jumlah mahasiswa dynamically
                        for (var kode in response) {
                            $('#jumlah-mahasiswa-' + kode).text(response[kode]);
                        }
                    },
                    error: function() {
                        alert('Error fetching jumlah mahasiswa.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // ========================================
            // Event delegation for "Otomatis" buttons
            // ========================================
            $(document).on('click', '.btn-buat-kelas', function() {
                var row = $(this).closest('tr');
                var makulKode = row.data('kode');
                var kodeInfo = row.data('info');
                var kuota = $('#kuota-' + makulKode).val();
                var jumlahMahasiswa = row.find('.jumlah-mahasiswa').text();
                var jumlahKelas = Math.ceil(jumlahMahasiswa / kuota);
                var semesterValue = row.data('semester');

                $('#class-rows-' + makulKode + ' tbody').empty(); // Clear existing rows

                // Generate new rows using the createRow function
                for (let i = 0; i < jumlahKelas; i++) {
                    let classLetter = String.fromCharCode(65 + i);
                    createRow(makulKode, kodeInfo, classLetter, semesterValue, row.data('sks'),
                        true); // true for automatic
                }

                attachDosenDropdownBehavior(); // Reattach behavior to dropdowns
                updateTextareaKodeAjar(); // Update textarea
            });

            // ========================================
            // Event handler for "Manual" dropdown items
            // ========================================
            $(document).on('click', '.manual-kelas-option', function(e) {
                e.preventDefault();

                var makulKode = $(this).data('kode');
                var jumlahKelas = $(this).data('kelas');
                var row = $('#row-makul-' + makulKode);
                var kodeInfo = row.data('info');
                var semesterValue = row.data('semester');

                $('#class-rows-' + makulKode + ' tbody').empty(); // Clear rows

                // Generate new rows using the createRow function
                for (let i = 0; i < jumlahKelas; i++) {
                    let classLetter = String.fromCharCode(65 + i);
                    createRow(makulKode, kodeInfo, classLetter, semesterValue, row.data('sks'),
                        false); // false for manual
                }

                attachDosenDropdownBehavior(); // Reattach behavior to dropdowns
                updateTextareaKodeAjar(); // Update textarea
            });

            // ========================================
            // Create new class row function
            // ========================================
            function createRow(makulKode, kodeInfo, classLetter, semesterValue, sks, isAutomatic) {
                $.ajax({
                    url: '/admin/managekelas/get-dosen-for-makul',
                    type: 'POST',
                    data: {
                        makul_id: makulKode // Pastikan ini adalah kode, bukan id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var dosenOptions = response.dosens.map(function(dosen) {
                            return `<option value="${dosen.kode_dosen}">${dosen.nama_dosen}</option>`;
                        });

                        // Tambahkan row baru
                        var newRow = `
                            <tr data-info="${kodeInfo}" data-kode="${makulKode}" data-kelas="${classLetter}" data-semester="${semesterValue}" data-sks="${sks}">
                                <td>${classLetter}</td>
                                <td>
                                    <select class="form-control dosen1-select" name="dosen1_${makulKode}_${classLetter}" data-makul="${makulKode}" data-class="${classLetter}">
                                        <option value="">Dosen 1</option>
                                        ${dosenOptions.join('')}
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control dosen2-select" name="dosen2_${makulKode}_${classLetter}" data-makul="${makulKode}" data-class="${classLetter}" disabled>
                                        <option value="">Dosen 2</option>
                                        ${dosenOptions.join('')}
                                    </select>
                                </td>
                                <td class="kode-ajar">${kodeInfo} <span style="color: red;">(Dosen Belum Dipilih)</span></td>
                                <td>
                                    <button class="btn btn-danger btn-hapus-kelas" data-kelas="${classLetter}" data-kode="${makulKode}">Hapus</button>
                                </td>
                            </tr>`;
                        $('#class-rows-' + makulKode + ' tbody').append(newRow);

                        // Re-bind fungsi lain
                        attachDosenDropdownBehavior(); // Attach dropdown behavior
                        updateTextareaKodeAjar(); // Update textarea
                    }
                });
            }

            // ========================================
            // Attach behavior to dropdowns (enable/disable)
            // ========================================
            function attachDosenDropdownBehavior() {
                $(document).on('change', '.dosen1-select', function() {
                    const dosen1Value = $(this).val();
                    const dosen2Dropdown = $(this).closest('tr').find('.dosen2-select');

                    if (dosen1Value) {
                        dosen2Dropdown.prop('disabled', false); // Enable Dosen 2 if Dosen 1 selected
                    } else {
                        dosen2Dropdown.val(''); // Clear selection
                        dosen2Dropdown.prop('disabled', true); // Disable if no Dosen 1
                    }

                    const row = $(this).closest('tr');
                    const classLetter = row.data('kelas');
                    const dosen1 = row.find('.dosen1-select').val();
                    const dosen2 = row.find('.dosen2-select').val();
                    updateKodeAjar(row, classLetter, dosen1, dosen2); // Update kode ajar
                });

                $(document).on('change', '.dosen2-select', function() {
                    const row = $(this).closest('tr');
                    const classLetter = row.data('kelas');
                    const dosen1 = row.find('.dosen1-select').val();
                    const dosen2 = $(this).val();
                    updateKodeAjar(row, classLetter, dosen1, dosen2); // Update kode ajar
                });
            }

            // ========================================
            // Update Kode Ajar and Textarea based on selected instructors
            // ========================================
            function updateKodeAjar(row, classLetter, dosen1, dosen2) {
                var kodeInfo = row.data('info');
                var classType = dosen2 ? `S${classLetter} ${classLetter}` : classLetter;
                var kodeDosen = dosen2 ? `${dosen1}/${dosen2}` : dosen1 ||
                    '<span style="color: red;">(Dosen Belum Dipilih)</span>';
                var kodeAjar = `${kodeInfo} ${classType} ${kodeDosen}`;
                row.find('.kode-ajar').html(kodeAjar);

                updateTextareaKodeAjar(); // Update textarea
            }

            // ========================================
            // Update textarea with Kode Ajar data
            // ========================================
            function updateTextareaKodeAjar() {
                var kodeAjarTexts = [];

                $('tr[data-info]').each(function() {
                    var row = $(this);
                    var kodeAjar = row.find('.kode-ajar').text();
                    var makulKode = row.data('kode');
                    var classLetter = row.data('kelas');
                    var semesterValue = row.data('semester');
                    var sks = row.data('sks');
                    var dosen1 = row.find('.dosen1-select').val();
                    var dosen2 = row.find('.dosen2-select').val();
                    var kuota = $('#kuota-' + makulKode).val();

                    if (kodeAjar && kodeAjar.trim() !== "") {
                        var namaDosen1 = row.find('.dosen1-select option:selected').text();
                        var namaDosen2 = dosen2 ? row.find('.dosen2-select option:selected').text() : '';
                        var dosenFormatted = dosen2 ? `${dosen1}/${dosen2}` : dosen1;
                        var namaDosenFormatted = dosen2 ? `${namaDosen1}/${namaDosen2}` : namaDosen1;
                        var result =
                            `["${kodeAjar}", "${makulKode}", "${dosenFormatted}", "${dosen1}", "${dosen2}", "${namaDosenFormatted}", "${classLetter}", "${semesterValue}", "${sks}", "${kuota}"]`;

                        kodeAjarTexts.push(result.trim());
                    }
                });

                // Update the textarea with Kode Ajar data
                $('#inputDescription').val(kodeAjarTexts.join('\n'));
            }

            // ========================================
            // Event handler for deleting class rows
            // ========================================
            $(document).on('click', '.btn-hapus-kelas', function() {
                $(this).closest('tr').remove(); // Remove the class row
                updateTextareaKodeAjar(); // Update textarea
            });

            // ========================================
            // Event handler for "Hapus Semua Kelas" button
            // ========================================
            $(document).on('click', '.btn-hapus-semua-kelas', function() {
                var makulKode = $(this).data('kode'); // Get Mata Kuliah code
                $('#class-rows-' + makulKode + ' tbody').empty(); // Clear all class rows
                updateTextareaKodeAjar(); // Update textarea
            });
        });
    </script>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title center-text">Penjadwalan</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pilihMasaInput">Pilih Tahun Ajaran</label>
                                <div class="input-group input-group-lg">
                                    <select class="custom-select rounded-0" id="pilihMasaInput">
                                        <option value="" disabled selected>Pilih Tahun Ajaran</option>
                                        <!-- Placeholder -->
                                        @foreach ($masaInputs as $input)
                                            <option value="{{ $input->kode_masa_input }}">
                                                Tahun Ajaran {{ $input->tahun_ajaran }}, Semester
                                                {{ $input->semester == 0 ? 'Gasal' : 'Genap' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <button class="btn btn-block btn-info btn-lg" id="saveClassData" disabled>Simpan Data
                                Kelas</button>
                        </div>
                        <div class="col-2">
                        </div>
                        <div class="col-5">
                            <div class="toast-body bg-warning" bis_skin_checked="1" data-masa="">
                                Belum Memilih Tahun Ajaran Jadwal
                            </div>
                        </div>
                        <div class="col-2">
                        </div>
                    </div>
                    <!-- Input Configuration (Textarea for Kode Ajar) -->
                    {{-- <div class="row" style="display: none;"> --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputDescription">Kode Kelas</label>
                                <textarea id="inputDescription" name="inputDescription" class="form-control" rows="4" style="height: 121px;"
                                    readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tab Structure -->
            <div class="card" id="existData">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link" href="#Gasal" data-toggle="tab">Gasal</a></li>
                        <li class="nav-item"><a class="nav-link" href="#Genap" data-toggle="tab">Genap</a></li>
                        <li class="nav-item"><a class="nav-link" href="#Pilihan" data-toggle="tab">Pilihan</a></li>
                        <li class="nav-item"><a class="nav-link" href="#Khusus" data-toggle="tab">Khusus</a></li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Gasal Tab -->
                        <div class="tab-pane" id="Gasal">
                            @include('partials.adminManageClass_makul_table', [
                                'makuls' => $gasalMakul,
                                'jumlahMahasiswa' => $jumlahMahasiswa,
                            ])
                        </div>

                        <!-- Genap Tab -->
                        <div class="tab-pane" id="Genap">
                            @include('partials.adminManageClass_makul_table', [
                                'makuls' => $genapMakul,
                                'jumlahMahasiswa' => $jumlahMahasiswa,
                            ])
                        </div>

                        <!-- Pilihan Tab -->
                        <div class="tab-pane" id="Pilihan">
                            @include('partials.adminManageClass_makul_table', [
                                'makuls' => $pilihanMakul,
                                'jumlahMahasiswa' => $jumlahMahasiswa,
                            ])
                        </div>

                        <!-- Khusus Tab -->
                        <div class="tab-pane" id="Khusus">
                            @include('partials.adminManageClass_makul_table', [
                                'makuls' => $khususMakul,
                                'jumlahMahasiswa' => $jumlahMahasiswa,
                            ])
                        </div>
                    </div>
                </div>
            </div>
            {{-- Tidak Ada Data --}}
            <div class="card bg-danger" id="noData">
                <div class="card-body">
                    <div class="card-body">
                        <h3>TIDAK ADA MATA KULIAH YANG TELAH DIAKTIVASI PADA TAHUN AJARAN INI</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal FAB -->
    <button class="btn btn-primary btn-fab" id="scrollToTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>
@endsection
