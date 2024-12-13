@extends('ui_dashboard.dashboard')
@section('title', 'Dashboard Admin')
@section('css')
    <style>
        #rowPembatasHari {
            height: 10px;
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
    <!-- Include SheetJS for Excel Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script>
        $(document).ready(function() {
            let makuls = @json($makuls); // Convert PHP $makuls to JS object

            // Trigger data fetch on select change
            $('#pilihMasaInput').on('change', function() {
                loadJadwalData();
            });

            function loadJadwalData() {
                let kodeMasaInput = $('#pilihMasaInput').val(); // Get selected value

                $.ajax({
                    url: '{{ route('get.jadwal') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        kode_masa_input: kodeMasaInput,
                    },
                    success: function(response) {
                        let tableBody = $('#jadwalTableBody');
                        tableBody.empty(); // Clear the existing table body

                        if (response.status === 'empty') {
                            tableBody.append('<tr><td colspan="13" class="text-center">' +
                                response.message + '</td></tr>');
                            $('#dlJadwal').prop('disabled', true); // Disable the download button
                        } else {
                            $.each(response.data, function(index, jadwalForDay) {
                                $.each(jadwalForDay, function(i, scheduleRow) {
                                    if (scheduleRow[2] === null || scheduleRow[2] ===
                                        "N/A") {
                                        tableBody.append(`
                                            <tr style="background-color: gray;" id="rowPembatasNull">
                                                <td colspan="10">&nbsp;</td>
                                            </tr>
                                        `);
                                    } else {
                                        if (scheduleRow.length === 13) {
                                            let makulName = 'Kode Makul Not Found';
                                            $.each(makuls, function(index, makul) {
                                                if (makul.kode === scheduleRow[
                                                        3]) {
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
                                                '<tr><td colspan="13" class="text-center">Invalid schedule data format.</td></tr>'
                                            );
                                        }
                                    }
                                });

                                tableBody.append(`
                                    <tr id="rowPembatasHari" style="background-color: black;">
                                        <td colspan="10" style="height: 10px; padding: 0; line-height: 10px;">&nbsp;</td>
                                    </tr>
                                `);
                            });

                            $('#dlJadwal').prop('disabled', false); // Enable the download button
                        }
                    },
                    error: function() {
                        alert('Error retrieving data. Please try again.');
                    }
                });
            }

            // Initial data load for default selection
            loadJadwalData();
        });
    </script>
    <script>
        // Function to export table to Excel
        function exportToExcel() {
            let tahunAjaran = $('#pilihMasaInput option:selected').text();
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

            let tableRows = $('#tabelJadwal tbody tr');
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

                // Hitung jumlah baris dari tabel langsung
                let totalRows = $('#tabelJadwal tbody tr').length;

                // Hitung count dengan membagi totalRows dengan 20, lalu dikurangi 1
                let count = Math.floor(totalRows - 5) / 20;
                console.log("Nilai count yang akan digunakan:", count);

                // Tambahkan baris pemisah setiap `count` rows
                if (actualRowCount % count === 0) {
                    worksheet.mergeCells(`A${rowIndex}:J${rowIndex}`);
                    let blackRow = worksheet.getRow(rowIndex);
                    blackRow.getCell(1).value = '';
                    blackRow.height = 10;
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
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Jadwal Mata Kuliah</h3>
                        </div>
                        <div class="card-body">
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
                                        <button id="refreshJadwal" class="btn btn-info btn-flat">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </span> --}}
                                </div>
                            </div>
                            <div class="row" style="margin-top: 25px;">
                                <div class="col-3">
                                    <button class="btn btn-block btn-info btn-lg" id="dlJadwal" disabled
                                        onclick="exportToExcel()">Unduh
                                        Jadwal</button>
                                </div>
                                <div class="col-9"></div>
                            </div>
                            <div id="jadwalData" style="margin-top: 25px;">
                                <table class="table table-bordered" id="tabelJadwal">
                                    <thead style="background-color: #17A2B8;">
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
        </div>
    </div>
    <!-- Modal FAB -->
    <button class="btn btn-primary btn-fab" id="scrollToTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>
@endsection
