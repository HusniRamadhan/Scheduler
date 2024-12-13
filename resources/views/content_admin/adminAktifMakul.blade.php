@extends('ui_dashboard.dashboard')
@section('title', 'Aktivasi Mata Kuliah')
@section('pageSize', 'min-height: 1300px;')
@section('headScript')
@endsection
@section('css')
    <!-- Add CSS for switch if needed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
    <style>
        /* Ensure consistent width for all toggle switches */
        .toggle {
            min-width: 100px;
            height: 35px !important;
        }

        .toggle-handle {
            height: 35px !important;
        }

        /* Center text, make corners rounded, and bold the text in .toast-body */
        .toast-body {
            text-align: center;
            /* Center the text */
            border-radius: 25px;
            /* Make the corners rounded */
            font-weight: bold;
            /* Bold the text */
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
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
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
        document.addEventListener('DOMContentLoaded', function() {
            const refreshDataButton = document.getElementById('refreshData');
            const semesterSelect = document.getElementById('pilihMasaInput');
            const saveAktifButton = document.getElementById('saveAktif');
            const toastBody = document.querySelector('.toast-body');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Trigger data fetch on select change
            semesterSelect.addEventListener('change', function() {
                loadData();
            });

            function loadData() {
                const selectedOption = semesterSelect.options[semesterSelect.selectedIndex];
                const kodeMasaInput = selectedOption.value;
                const tahunAjaranText = selectedOption.text.trim();

                // Determine the selected semester (Gasal or Genap)
                const selectedSemester = tahunAjaranText.includes('Gasal') ? 'Gasal' : 'Genap';

                // Activate or deactivate the appropriate switches
                toggleActivation(selectedSemester);

                // Update semester values in the Pilihan tab
                updatePilihanSemester(selectedSemester);

                // Enable the save button
                saveAktifButton.disabled = false;

                // Update the data-masa attribute in the toast body
                toastBody.setAttribute('data-masa', kodeMasaInput);

                // Update the toast body text with selected academic year and semester
                toastBody.innerHTML = tahunAjaranText;

                // Change the background class from bg-warning to bg-info
                toastBody.classList.remove('bg-warning');
                toastBody.classList.add('bg-info');

                // Check if 'kode_masa_input' exists in the database
                fetch(`/admin/aktifmakul/check-kode-masa/${kodeMasaInput}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error checking kode_masa_input: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.exists) {
                            saveAktifButton.textContent = 'Ubah Data Aktivasi';
                        } else {
                            saveAktifButton.textContent = 'Simpan Data Aktivasi';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal memeriksa status kode_masa_input. Silakan coba lagi nanti.');
                    });
            }

            // Initial data load for default selection
            loadData();

            // Handle save or update on button click
            saveAktifButton.addEventListener('click', function() {
                const kodeMasaInput = toastBody.getAttribute('data-masa');
                const buttonText = saveAktifButton.textContent;

                // Collect data from all tab panes
                const data = [];
                ['Gasal', 'Genap', 'Pilihan', 'Khusus'].forEach(function(tab) {
                    const rows = document.querySelectorAll(`#${tab} tbody tr`);

                    rows.forEach(function(row) {
                        const kodeMakul = row.querySelector('td:nth-child(2)').textContent
                            .trim();
                        const switchElement = row.querySelector('.status-switch');
                        const statusAktif = $(switchElement).prop(
                            'checked'); // Get boolean value from the switch

                        data.push({
                            kode_masa_input: kodeMasaInput,
                            kode_makul: kodeMakul,
                            status_aktif: statusAktif
                        });
                    });
                });

                // If updating existing data
                if (buttonText === 'Ubah Data Aktivasi') {
                    if (confirm('Data sudah ada, ubah data yang sudah ada?')) {
                        // Delete existing entries and insert new ones
                        fetch(`/admin/aktifmakul/update-aktivasi`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({
                                    kode_masa_input: kodeMasaInput,
                                    data: data
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Error updating aktivasi: ${response.statusText}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.status === 'success') {
                                    alert('Data berhasil diperbarui.');
                                } else {
                                    alert('Terjadi masalah saat memperbarui data. Silakan coba lagi.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Gagal memperbarui data. Silakan coba lagi nanti.');
                            });
                    }
                } else {
                    // Insert new data
                    fetch('/admin/aktifmakul/save-aktivasi', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                data: data
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Error saving aktivasi: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                alert('Data berhasil disimpan.');
                            } else {
                                alert('Terjadi masalah saat menyimpan data. Silakan coba lagi.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal menyimpan data. Silakan coba lagi nanti.');
                        });
                }
            });

            // Function to toggle activation of switches
            function toggleActivation(semester) {
                const gasalSwitches = document.querySelectorAll('#Gasal .status-switch');
                const genapSwitches = document.querySelectorAll('#Genap .status-switch');
                const pilihanSwitches = document.querySelectorAll('#Pilihan .status-switch');
                const khususSwitches = document.querySelectorAll('#Khusus .status-switch');

                if (semester === 'Gasal') {
                    toggleSwitches(gasalSwitches, true);
                    toggleSwitches(genapSwitches, false);
                } else if (semester === 'Genap') {
                    toggleSwitches(genapSwitches, true);
                    toggleSwitches(gasalSwitches, false);
                }

                toggleSwitches(pilihanSwitches, false);
                toggleSwitches(khususSwitches, false);
            }

            function toggleSwitches(switchElements, activate) {
                switchElements.forEach(function(element) {
                    $(element).bootstrapToggle(activate ? 'on' : 'off');
                });
            }

            function updatePilihanSemester(semester) {
                const pilihanSemesterCells = document.querySelectorAll('#Pilihan td:nth-child(4)');
                pilihanSemesterCells.forEach(function(cell) {
                    cell.textContent = (semester === 'Gasal') ? '7' : '6';
                });
            }
        });
    </script>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- Input Configuration (Textarea for Kode Ajar) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title center-text">Aktivasi Mata Kuliah</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pilihMasaInput">Pilih Tahun Ajaran</label>
                                <div class="input-group input-group-lg">
                                    <select class="custom-select rounded-0" id="pilihMasaInput">
                                        @foreach ($masaInputs as $input)
                                            <option value="{{ $input->kode_masa_input }}">
                                                Tahun Ajaran {{ $input->tahun_ajaran }}, Semester
                                                {{ $input->semester == 0 ? 'Gasal' : 'Genap' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- <span class="input-group-append">
                                        <button id="refreshData" class="btn btn-info btn-flat">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row for Save Button and Toast Notification -->
                    <div class="row">
                        <div class="col-3">
                            <button class="btn btn-block btn-info btn-lg" id="saveAktif" disabled>Simpan Data
                                Aktivasi</button>
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
                </div>
            </div>
            <!-- Display Tab Content with Tables -->
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link" href="#Gasal" data-toggle="tab">Gasal</a></li>
                        <li class="nav-item"><a class="nav-link" href="#Genap" data-toggle="tab">Genap</a></li>
                        <li class="nav-item"><a class="nav-link active" href="#Pilihan" data-toggle="tab">Pilihan</a></li>
                        <li class="nav-item"><a class="nav-link" href="#Khusus" data-toggle="tab">Khusus</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab Pane for Gasal -->
                        <div class="tab-pane" id="Gasal">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode MK</th>
                                        <th>Mata Kuliah</th>
                                        <th>SEM</th>
                                        <th>SKS</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gasalMakul as $index => $makul)
                                        <tr>
                                            <td width="5%">{{ $index + 1 }}</td>
                                            <td width="20%">{{ $makul->kode }}</td>
                                            <td width="45%">{{ $makul->mata_kuliah }}</td>
                                            <td width="10%">{{ $makul->semester }}</td>
                                            <td width="10%">{{ $makul->sks }}</td>
                                            <td width="10%">
                                                <input type="checkbox" class="status-switch" data-id="{{ $makul->id }}"
                                                    data-toggle="toggle" data-on="Aktif" data-off="Nonaktif"
                                                    data-onstyle="success" data-offstyle="danger" data-width="100"
                                                    {{ $makul->status ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Tab Pane for Genap -->
                        <div class="tab-pane" id="Genap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode MK</th>
                                        <th>Mata Kuliah</th>
                                        <th>SEM</th>
                                        <th>SKS</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($genapMakul as $index => $makul)
                                        <tr>
                                            <td width="5%">{{ $index + 1 }}</td>
                                            <td width="20%">{{ $makul->kode }}</td>
                                            <td width="45%">{{ $makul->mata_kuliah }}</td>
                                            <td width="10%">{{ $makul->semester }}</td>
                                            <td width="10%">{{ $makul->sks }}</td>
                                            <td width="10%">
                                                <input type="checkbox" class="status-switch" data-id="{{ $makul->id }}"
                                                    data-toggle="toggle" data-on="Aktif" data-off="Nonaktif"
                                                    data-onstyle="success" data-offstyle="danger" data-width="100"
                                                    {{ $makul->status ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Tab Pane for Pilihan -->
                        <div class="active tab-pane" id="Pilihan">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode MK</th>
                                        <th>Mata Kuliah</th>
                                        <th>SEM</th>
                                        <th>SKS</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pilihanMakul as $index => $makul)
                                        <tr>
                                            <td width="5%">{{ $index + 1 }}</td>
                                            <td width="20%">{{ $makul->kode }}</td>
                                            <td width="45%">{{ $makul->mata_kuliah }}</td>
                                            <td width="10%" class="semester">{{ $makul->semester }}</td>
                                            <td width="10%">{{ $makul->sks }}</td>
                                            <td width="10%">
                                                <input type="checkbox" class="status-switch" data-id="{{ $makul->id }}"
                                                    data-toggle="toggle" data-on="Aktif" data-off="Nonaktif"
                                                    data-onstyle="success" data-offstyle="danger" data-width="100"
                                                    {{ $makul->status ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Tab Pane for Khusus -->
                        <div class="tab-pane" id="Khusus">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode MK</th>
                                        <th>Mata Kuliah</th>
                                        <th>SEM</th>
                                        <th>SKS</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($khususMakul as $index => $makul)
                                        <tr>
                                            <td width="5%">{{ $index + 1 }}</td>
                                            <td width="20%">{{ $makul->kode }}</td>
                                            <td width="45%">{{ $makul->mata_kuliah }}</td>
                                            <td width="10%">{{ $makul->semester }}</td>
                                            <td width="10%">{{ $makul->sks }}</td>
                                            <td width="10%">
                                                <input type="checkbox" class="status-switch"
                                                    data-id="{{ $makul->id }}" data-toggle="toggle" data-on="Aktif"
                                                    data-off="Nonaktif" data-onstyle="success" data-offstyle="danger"
                                                    data-width="100" {{ $makul->status ? 'checked' : '' }}>
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
    <!-- Modal FAB -->
    <button class="btn btn-primary btn-fab" id="scrollToTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>
@endsection
