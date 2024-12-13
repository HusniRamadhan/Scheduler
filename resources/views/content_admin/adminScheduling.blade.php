@extends('ui_dashboard.dashboard')
@section('title', 'Administrasi Jadwal')
{{-- Custom CSS for the full-width modal --}}
@section('css')
    <style>
        .modal-xl {
            max-width: 90%;
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
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection
@section('script')
    {{-- Ensure jQuery and Bootstrap JS are properly loaded --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script> <!-- Add Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script> <!-- ExcelJS for export -->
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
    <script>
        // Initialize DataTable
        $('#masaInputTable').DataTable();
    </script>
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
    {{-- Include the exportToExcel function here --}}
    <script>
        // Function to export table to Excel
        function exportToExcel() {
            let tahunAjaran = $('#masaInputTableBody tr .lihat-jadwal').data('tahun-ajaran');
            let makuls = @json($makuls);

            let workbook = new ExcelJS.Workbook();
            let worksheet = workbook.addWorksheet('Jadwal Kuliah');

            function mergeAndStyle(rowIndex, text) {
                let row = worksheet.getRow(rowIndex);
                worksheet.mergeCells(`A${rowIndex}:J${rowIndex}`);
                row.getCell(1).value = text;
                row.getCell(1).alignment = {
                    vertical: 'middle',
                    horizontal: 'center'
                };
                row.getCell(1).font = {
                    name: 'Times New Roman',
                    bold: true
                };
            }

            // Top 5 rows
            mergeAndStyle(1,
                `JADWAL KULIAH SEMESTER ${tahunAjaran.includes('Ganjil') ? 'Ganjil' : 'Genap'} TA ${tahunAjaran.match(/\d{4}\/\d{4}/)[0]}`
            );
            mergeAndStyle(2, 'PROGRAM STUDI INFORMATIKA');
            mergeAndStyle(3, 'FAKULTAS TEKNIK');
            mergeAndStyle(4, 'UNIVERSITAS TANJUNGPURA');
            mergeAndStyle(5, ''); // Empty row

            // Header row
            worksheet.getRow(6).values = ['HARI', 'JADWAL KULIAH', 'KODE MK', 'NAMA MATAKULIAH', 'KELAS', 'SMTR', 'SKS',
                'KAP', 'NAMA PENGAJAR', 'RUANG'
            ];
            worksheet.getRow(6).alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            worksheet.getRow(6).font = {
                name: 'Times New Roman',
                bold: true
            };

            // Set column widths
            worksheet.columns = [{
                    width: 8
                }, {
                    width: 20
                }, {
                    width: 20
                }, {
                    width: 35
                },
                {
                    width: 8
                }, {
                    width: 8
                }, {
                    width: 8
                }, {
                    width: 8
                },
                {
                    width: 35
                }, {
                    width: 8
                }
            ];

            let tableRows = $('#jadwalTableBody tr');
            let rowIndex = 7;
            let actualRowCount = 0;

            tableRows.each(function() {
                let row = $(this);

                if (row.is('#rowPembatasHari')) {
                    return;
                }

                if (row.is('#rowPembatasNull')) {
                    worksheet.mergeCells(`A${rowIndex}:J${rowIndex}`);
                    let mergedRow = worksheet.getRow(rowIndex);
                    mergedRow.getCell(1).value = '';
                    mergedRow.height = 40;
                    mergedRow.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: {
                            argb: 'FF808080'
                        }
                    };
                    mergedRow.alignment = {
                        vertical: 'middle'
                    }; // Ensure vertical middle alignment
                    rowIndex++;
                    actualRowCount++;
                } else {
                    let cells = row.find('td');
                    let rowData = [];

                    cells.each(function(index) {
                        if (index < 10) {
                            if (index === 8) { // Column for "NAMA PENGAJAR"
                                let text = $(this).text().trim();
                                if (text.includes('/')) {
                                    // Split by slash and add a new line between lecturers
                                    let names = text.split('/');
                                    rowData.push(`${names[0]}\n/${names[1]}`);
                                } else {
                                    rowData.push(text);
                                }
                            } else {
                                rowData.push($(this).text().trim());
                            }
                        }
                    });

                    let newRow = worksheet.addRow(rowData);
                    newRow.height = 40; // Set height to 40px for non-separator rows
                    // Ensure vertical middle alignment for each cell in the row
                    newRow.eachCell({
                        includeEmpty: true
                    }, function(cell, colNumber) {
                        cell.alignment = {
                            vertical: 'middle'
                        };

                        // Enable text wrapping for column I (index 9)
                        if (colNumber === 9) {
                            cell.alignment.wrapText = true; // Allow wrapping in column I
                        }
                    });
                    rowIndex++;
                    actualRowCount++;
                }

                let classroomCount = {{ $classroomCount }};
                if (actualRowCount % classroomCount === 0) {
                    worksheet.mergeCells(`A${rowIndex}:J${rowIndex}`);
                    let blackRow = worksheet.getRow(rowIndex);
                    blackRow.getCell(1).value = '';
                    blackRow.height = 10; // Set row height to 10px for black separator
                    blackRow.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: {
                            argb: 'FF000000'
                        }
                    };
                    rowIndex++;
                }
            });

            // Apply Times New Roman font and borders (except rows 1-5)
            worksheet.eachRow({
                includeEmpty: true
            }, function(row, rowNum) {
                row.eachCell({
                    includeEmpty: true
                }, function(cell) {
                    cell.font = {
                        name: 'Times New Roman'
                    };

                    if (rowNum > 5) { // Skip top 5 rows (no borders for them)
                        cell.border = {
                            top: {
                                style: 'thin'
                            },
                            left: {
                                style: 'thin'
                            },
                            bottom: {
                                style: 'thin'
                            },
                            right: {
                                style: 'thin'
                            }
                        };
                    }
                });
            });

            // Generate the file name and download
            let fileName =
                `Jadwal Tahun Ajaran ${tahunAjaran.includes('Ganjil') ? 'Ganjil' : 'Genap'}, TA ${tahunAjaran.match(/\d{4}\/\d{4}/)[0]}.xlsx`;

            workbook.xlsx.writeBuffer().then(function(data) {
                let blob = new Blob([data], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = fileName;
                link.click();
            });
        }

        $(document).ready(function() {
            // Function to check if a schedule exists for each kode_masa_input
            function checkScheduleAvailability() {
                $('#masaInputTableBody tr').each(function() {
                    var row = $(this);
                    var kodeMasaInput = row.data('value'); // Retrieve kode_masa_input from the row

                    // AJAX call to check if this kode_masa_input exists in jadwal_file
                    $.ajax({
                        url: '{{ url('/admin/scheduling/check-jadwal') }}', // API route to check schedule
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            kode_masa_input: kodeMasaInput
                        },
                        success: function(response) {
                            if (response.exists) {
                                // Show "Lihat Jadwal" and change "Buat Jadwal" to "Edit Jadwal"
                                row.find('.lihat-jadwal')
                                    .show(); // Show the "Lihat Jadwal" button
                                row.find('.buat-jadwal').text(
                                    'Edit Jadwal'); // Change button text to "Edit Jadwal"
                            } else {
                                // Hide "Lihat Jadwal" if no data exists
                                row.find('.lihat-jadwal')
                                    .hide(); // Hide the "Lihat Jadwal" button
                                row.find('.buat-jadwal').text(
                                    'Buat Jadwal'); // Keep button text as "Buat Jadwal"
                            }
                        },
                        error: function() {
                            console.error('Error checking schedule availability.');
                        }
                    });
                });
            }

            // Call the function on page load
            checkScheduleAvailability();

            // Handle click event for "Lihat Jadwal" button
            $('.lihat-jadwal').on('click', function() {
                // Ambil kode_masa_input dari elemen <tr>, bukan dari tombol
                var kodeMasaInput = $(this).closest('tr').data('value');

                console.log(kodeMasaInput); // Debug: pastikan kode_masa_input benar

                // Lakukan AJAX request untuk mendapatkan jadwal
                $.ajax({
                    url: '{{ route('get.jadwal.scheduling') }}', // Sesuaikan dengan route post yang benar
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        kode_masa_input: kodeMasaInput
                    },
                    success: function(response) {
                        console.log(response); // Debug: cek response dari server
                        let tableBody = $('#jadwalTableBody'); // Body dari tabel modal
                        tableBody.empty(); // Kosongkan isi tabel sebelumnya

                        if (response.status === 'empty') {
                            tableBody.append('<tr><td colspan="10" class="text-center">' +
                                response.message + '</td></tr>');
                            $('#dlJadwal').prop('disabled', true); // Disable tombol unduh
                        } else {
                            // Loop for populating table rows
                            $.each(response.data, function(index, jadwalForDay) {
                                $.each(jadwalForDay, function(i, scheduleRow) {
                                    if (scheduleRow[2] === null || scheduleRow[
                                            2] === "N/A") {
                                        tableBody.append(`
                                            <tr style="background-color: gray;" id="rowPembatasNull">
                                                <td colspan="10">&nbsp;</td>
                                            </tr>
                                        `);
                                    } else {
                                        if (scheduleRow.length === 13) {
                                            let makulName =
                                                'Kode Makul Not Found';
                                            $.each(@json($makuls),
                                                function(index, makul) {
                                                    if (makul.kode ===
                                                        scheduleRow[3]) {
                                                        makulName = makul
                                                            .mata_kuliah;
                                                        return false; // Break loop
                                                    }
                                                });

                                            tableBody.append(`
                                                <tr data-kodeAjar="${scheduleRow[2]}">
                                                    <td>${scheduleRow[0]}</td>
                                                    <td>${scheduleRow[1]}</td>
                                                    <td>${scheduleRow[3]}</td>
                                                    <td>${makulName}</td>
                                                    <td>${scheduleRow[7]}</td>
                                                    <td>${scheduleRow[8]}</td>
                                                    <td>${scheduleRow[9]}</td>
                                                    <td>${scheduleRow[10]}</td>
                                                    <td>${scheduleRow[11].replace(/\//g, '<br>/')}</td>
                                                    <td>${scheduleRow[12]}</td>
                                                </tr>
                                            `);
                                        } else {
                                            tableBody.append(
                                                '<tr><td colspan="10" class="text-center">Invalid schedule data format.</td></tr>'
                                            );
                                        }
                                    }
                                });

                                // Add row separator for days
                                tableBody.append(`
                                    <tr id="rowPembatasHari" style="background-color: black;">
                                        <td colspan="10" style="height: 10px; padding: 0; line-height: 10px;">&nbsp;</td>
                                    </tr>
                                `);
                            });

                            // Enable the download button
                            $('#dlJadwal').prop('disabled', false);
                        }

                        // Show the modal after data is loaded
                        $('#jadwalModal').modal('show');
                    },
                    error: function() {
                        console.error('Error fetching schedule data.');
                    }
                });
            });
        });
    </script>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Tabel Tahun Ajaran</h3>
                </div>
                <div class="card-body">
                    <table id="masaInputTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="8%">Tahun Ajaran</th>
                                <th width="5%">Semester</th>
                                <th width="12%">Jangka Waktu</th>
                                <th>Keterangan</th>
                                <th width="10%">Total Input</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="masaInputTableBody">
                            @foreach ($masaInputs as $masaInput)
                                @php
                                    // Extract 'totalMahasiswa' and 'totalKRS' from the inputCounts array
                                    $totalMahasiswa = $inputCounts[$masaInput->tahun_ajaran]['totalMahasiswa'] ?? 0;
                                    $totalKRS = $inputCounts[$masaInput->tahun_ajaran]['totalKRS'] ?? 0;
                                @endphp
                                <tr data-value="{{ $masaInput->kode_masa_input }}">
                                    <td>{{ $masaInput->tahun_ajaran }}</td>
                                    <td>{{ $masaInput->semester == 0 ? 'Ganjil' : 'Genap' }}</td>
                                    <td>{{ $masaInput->jangka_waktu }}</td>
                                    <td>{{ $masaInput->keterangan }}</td>
                                    <td>
                                        @if ($totalMahasiswa > 0)
                                            {{ $totalKRS }}/{{ $totalMahasiswa }}
                                        @else
                                            0/0
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-primary buat-jadwal"
                                            onclick="window.location.href='{{ url('/admin/scheduling/input') }}?tahun_ajaran={{ $masaInput->tahun_ajaran }}&semester={{ $masaInput->semester }}'">
                                            Buat Jadwal
                                        </button>
                                        <button class="btn btn-info lihat-jadwal" style="display: none;"
                                            data-tahun-ajaran="{{ $masaInput->tahun_ajaran }}"
                                            data-semester="{{ $masaInput->semester }}"
                                            data-value="{{ $masaInput->kode_masa_input }}">
                                            Lihat Jadwal
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal to display the jadwal --}}
    <div class="modal fade" id="jadwalModal" tabindex="-1" aria-labelledby="jadwalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jadwalModalLabel">Jadwal Mahasiswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" style="margin-top: 25px;">
                        <div class="col-3">
                            <button class="btn btn-block btn-info btn-lg" id="dlJadwal" disabled
                                onclick="exportToExcel()">Unduh
                                Jadwal</button>
                        </div>
                        <div class="col-9"></div>
                    </div>
                    <div id="jadwalData" style="margin-top: 25px;">
                        <table class="table table-bordered" id="jadwalTable" style="margin-top: 25px;">
                            <thead>
                                <tr>
                                    <th>HARI</th>
                                    <th>JADWAL KULIAH</th>
                                    <th>KODE MK</th>
                                    <th>NAMA MATAKULIAH</th>
                                    <th>KELAS</th>
                                    <th>SMTR</th>
                                    <th>SKS</th>
                                    <th>KAP</th>
                                    <th>NAMA PENGAJAR</th>
                                    <th>RUANG</th>
                                </tr>
                            </thead>
                            <tbody id="jadwalTableBody">
                                <tr>
                                    <td colspan="10" class="text-center">BELUM ADA DATA JADWAL</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="btn btn-primary btn-fab" id="scrollToTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>
@endsection
