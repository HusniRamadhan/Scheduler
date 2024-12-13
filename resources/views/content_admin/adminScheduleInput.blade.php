@extends('ui_dashboard.dashboard')
@section('title', 'Penjadwalan Mata Kuliah')
@section('pageSize', 'min-height: 1300px;')
@section('css')
    <style>
        .modal-xl {
            max-width: 100%;
            /* Change this percentage to make it larger or smaller */
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
    <style>
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
        }
    </style>
@endsection
@section('headScript')
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.css') }}">
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            let kodeAjarSelectionCount = {};
            let lecturerMap = {};
            let timeMap = {};

            $('#buatJadwalModal').on('show.bs.modal', function() {
                $.ajax({
                    url: '/admin/scheduling/input/admin-scheduling-create',
                    method: 'GET',
                    success: function(response) {
                        var jadwalBody = $('#jadwalData tbody');
                        jadwalBody.empty();

                        response.forEach(function(row) {
                            if (row.empty) {
                                jadwalBody.append(
                                    '<tr style="background-color: black;" id="rowPembatas"><td colspan="14">&nbsp;</td></tr>'
                                );
                            } else {
                                var newRow = `
                <tr class="schedule-row" data-day="${row.day}" data-session="${row.session}" data-classroom="${row.classroom}" style="background-color: gray;">
                    <td>${row.day}</td>
                    <td>${row.session_time}</td>
                    <td>
                        <select class="kode-ajar-dropdown form-control">
                        </select>
                    </td>
                    <td></td>
                    <td></td> <!-- Keterangan Column -->
                    <!-- Additional empty columns -->
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>${row.classroom}</td><td></td>
                </tr>
            `;
                                jadwalBody.append(newRow);
                            }
                        });

                        const kodeAjarData = parseTextareaData();
                        populateDropdownOptions(kodeAjarData);
                        updateTableRowOnKodeAjarSelect(kodeAjarData);
                    },
                    error: function(xhr, status, error) {
                        alert('Error while fetching schedule data: ' + error);
                    }
                });
            });

            function parseTextareaData() {
                const kodeAjarData = [];
                const textareaValue = $('#inputDescription').val().trim();

                if (textareaValue) {
                    const lines = textareaValue.split('\n');
                    lines.forEach((line) => {
                        try {
                            const parsedLine = JSON.parse(line);

                            const kodeAjarEntry = {
                                kodeAjar: parsedLine[0],
                                makulKode: parsedLine[1],
                                dosen1: parsedLine[3],
                                dosen2: parsedLine[4] || '',
                                namaDosenFormatted: parsedLine[5],
                                classLetter: parsedLine[6],
                                semester: parseInt(parsedLine[7], 10), // Ensure parsing as integer
                                sks: parseInt(parsedLine[8], 10), // Ensure parsing as integer
                                kuota: parseInt(parsedLine[9], 10),
                            };

                            kodeAjarData.push(kodeAjarEntry);
                        } catch (e) {
                            console.error('Invalid JSON line:', line, 'Error:', e.message);
                        }
                    });
                }

                return kodeAjarData;
            }

            function populateDropdownOptions(kodeAjarData) {
                const dropdowns = $('.kode-ajar-dropdown');

                dropdowns.each(function() {
                    const dropdown = $(this);
                    dropdown.empty();
                    dropdown.append('<option value="">Pilih Kode Ajar</option>');
                    dropdown.append('<option value="N/A">N/A</option>');
                    kodeAjarData.forEach((entry) => {
                        if (entry.sks === 4) {
                            dropdown.append(
                                `<option value="${entry.kodeAjar}_1">${entry.kodeAjar}_1</option>`
                            );
                            dropdown.append(
                                `<option value="${entry.kodeAjar}_2">${entry.kodeAjar}_2</option>`
                            );
                        } else {
                            dropdown.append(
                                `<option value="${entry.kodeAjar}">${entry.kodeAjar}</option>`
                            );
                        }
                    });
                });
            }

            // Function to update dropdown visibility based on selected values
            function updateDropdownVisibility() {
                const selectedValues = [];

                // Get all selected values
                $('.kode-ajar-dropdown').each(function() {
                    const selectedValue = $(this).val();
                    if (selectedValue) {
                        selectedValues.push(selectedValue);
                    }
                });

                // Hide selected values from other dropdowns
                $('.kode-ajar-dropdown').each(function() {
                    const dropdown = $(this);
                    const currentValue = dropdown.val();
                    dropdown.find('option').each(function() {
                        const option = $(this);
                        const optionValue = option.val();

                        if (selectedValues.includes(optionValue) && optionValue !== currentValue &&
                            optionValue !== 'N/A') {
                            option.hide(); // Hide already selected values
                        } else {
                            option.show(); // Show available values
                        }
                    });
                });
            }

            function resetRowAlerts() {
                $('.schedule-row').each(function() {
                    const row = $(this);
                    row.css('background-color', 'white');
                    row.find('td:eq(13)').text(''); // Clear 'Keterangan' column
                });
            }

            function checkForDuplicateLecturers() {
                const lecturerMap = {};

                $('.schedule-row').each(function() {
                    const row = $(this);
                    const keteranganCell = row.find('td:eq(13)');
                    const dosen1 = row.find('td:eq(5)').text().trim();
                    const dosen2 = row.find('td:eq(6)').text().trim();
                    const day = row.data('day');
                    const session = row.data('session');
                    const daySessionKey = `${day}-${session}`;
                    // row.css('background-color', '');
                    // keteranganCell.text('');

                    if (!lecturerMap[daySessionKey]) {
                        lecturerMap[daySessionKey] = [];
                    }

                    [dosen1, dosen2].forEach((dosen) => {
                        if (dosen && lecturerMap[daySessionKey].includes(dosen) && dosen !==
                            "MKU") {
                            row.css('background-color', 'red');
                            keteranganCell.text(
                                `Dosen "${dosen}" sudah ditempatkan pada sesi ini.`);
                        } else if (dosen) {
                            lecturerMap[daySessionKey].push(dosen);
                        }
                    });
                });
            }

            $(document).on('change', '.dosen1-select, .dosen2-select', function() {
                checkForDuplicateLecturers();
            });

            $('#buatJadwalModal').on('hidden.bs.modal', function() {
                lecturerMap = {};
                timeMap = {};
                resetRowAlerts(); // Reset all alerts when the modal is closed
                kodeAjarSelectionCount = {};
                updateDropdownVisibility();
            });

            function checkForTimeConflicts() {
                const timeMap = {};

                $('.schedule-row').each(function() {
                    const row = $(this);
                    const keteranganCell = row.find('td:eq(13)');
                    const day = row.data('day');
                    const session = row.data('session');
                    const startTime = row.find('td:eq(1)').data('start-time');
                    const endTime = row.find('td:eq(1)').data('end-time');
                    const classroom = row.find('td:eq(12)').text().trim();
                    // row.css('background-color', '');
                    // keteranganCell.text('');

                    if (!startTime || !endTime || !classroom) return;

                    const dayClassroomKey = `${day}-${classroom}`;

                    if (!timeMap[dayClassroomKey]) {
                        timeMap[dayClassroomKey] = {};
                    }

                    function checkSessionPair(previousSession, currentSession) {
                        const previous = timeMap[dayClassroomKey][previousSession];
                        if (previous) {
                            if (isTimeOverlap(previous.startTime, previous.endTime, startTime, endTime)) {
                                previous.row.css('background-color', 'red');
                                previous.row.find('td:eq(13)').text(
                                    'Waktu kuliah bertabrakan dengan sesi lain.');
                                row.css('background-color', 'red');
                                keteranganCell.text('Waktu kuliah bertabrakan dengan sesi lain.');
                            }
                        }
                    }

                    if (session === 2) {
                        checkSessionPair(1, 2);
                    } else if (session === 4) {
                        checkSessionPair(3, 4);
                    }

                    timeMap[dayClassroomKey][session] = {
                        startTime: startTime,
                        endTime: endTime,
                        row: row
                    };
                });
            }

            function isTimeOverlap(startTime1, endTime1, startTime2, endTime2) {
                return (startTime1 < endTime2 && startTime2 < endTime1);
            }

            // Handle changes in dropdown (Kode Ajar or lecturer selection)
            $(document).on('change', '.kode-ajar-dropdown, .dosen1-select, .dosen2-select', function() {
                resetRowAlerts(); // Clear all previous alerts
                checkForDuplicateLecturers(); // Check for lecturer duplicates
                checkForTimeConflicts(); // Check for time conflicts
            });

            function getSKSFromValue(kodeAjar) {
                var sksMatch = kodeAjar.match(/SKS-(\d+)/);
                return sksMatch ? parseInt(sksMatch[1]) : 0;
            }

            function calculateTimeForSKS(day, session, sks) {
                var startTime, endTime;

                if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(day)) {
                    switch (session) {
                        case 1:
                            startTime = '07:30';
                            endTime = sks === 2 || sks === 4 ? '09:10' : '10:00';
                            break;
                        case 2:
                            startTime = sks === 2 || sks === 4 ? '10:10' : '09:20';
                            endTime = '11:50';
                            break;
                        case 3:
                            startTime = '12:45';
                            endTime = sks === 2 || sks === 4 ? '14:25' : '15:15';
                            break;
                        case 4:
                            startTime = '14:45';
                            endTime = sks === 2 || sks === 4 ? '16:25' : '17:15';
                            break;
                    }
                }

                if (day === 'Jumat') {
                    switch (session) {
                        case 1:
                            startTime = '07:30';
                            endTime = '09:10';
                            break;
                        case 2:
                            startTime = '09:20';
                            endTime = '11:00';
                            break;
                        case 3:
                            startTime = '13:00';
                            endTime = sks === 2 || sks === 4 ? '14:40' : '15:30';
                            break;
                        case 4:
                            startTime = '15:35';
                            endTime = '17:15';
                            break;
                    }
                }
                return `${startTime} s/d ${endTime}`;
            }

            function updateTableRowOnKodeAjarSelect(kodeAjarData) {
                $(document).on('change', '.kode-ajar-dropdown', function() {
                    const selectedValue = $(this).val();
                    const row = $(this).closest('tr');
                    const originalDay = row.data('day');
                    const originalSessionTime = row.data('session-time');
                    const previousValue = $(this).data('previousValue');
                    const originalClassroom = row.data('classroom');
                    const day = row.data('day');
                    const session = row.data('session');

                    // Remove previous selection count
                    if (previousValue && previousValue !== 'N/A') {
                        const baseKodeAjar = previousValue.replace(/_1|_2/, ''); // Strip _1 or _2
                        if (kodeAjarSelectionCount[baseKodeAjar]) {
                            kodeAjarSelectionCount[baseKodeAjar]--;
                            if (kodeAjarSelectionCount[baseKodeAjar] === 0) {
                                delete kodeAjarSelectionCount[baseKodeAjar];
                            }
                        }
                    }

                    // Handle N/A case
                    if (selectedValue === 'N/A') {
                        $(this).data('previousValue', '');
                        row.find('td').not(':eq(2)').empty();
                        row.find('td:eq(0)').text('');
                        row.find('td:eq(1)').text('');
                        row.find('td:eq(12)').text('');
                        row.removeAttr('data-kodeAjar'); // Remove data-kodeAjar when N/A is selected
                        row.css('background-color', 'black');
                        updateDropdownVisibility();
                        return;
                    }

                    // Handle empty case (reset row)
                    if (selectedValue === '') {
                        $(this).data('previousValue', '');
                        row.find('td:first').text(originalDay);
                        row.find('td:eq(1)').text('');
                        row.find('td:eq(12)').text(originalClassroom);
                        row.find('td').not(':eq(0), :eq(1), :eq(2), :eq(12)').empty();
                        row.removeAttr('data-kodeAjar'); // Remove data-kodeAjar when no value is selected
                        row.css('background-color', 'gray');
                        updateDropdownVisibility();
                        return;
                    }

                    // Handle regular kodeAjar or kodeAjar with _1 or _2
                    const baseKodeAjar = selectedValue.replace(/_1|_2/, ''); // Strip _1 or _2
                    const entry = kodeAjarData.find((e) => e.kodeAjar === baseKodeAjar);

                    if (entry) {
                        row.find('td:first').text(originalDay);
                        row.find('td:eq(1)').text(originalSessionTime);
                        row.find('td:eq(3)').text(entry.makulKode);
                        row.find('td:eq(4)').text(entry.dosen1 + (entry.dosen2 ? `/${entry.dosen2}` : ''));
                        row.find('td:eq(5)').text(entry.dosen1);
                        row.find('td:eq(6)').text(entry.dosen2 || '');
                        row.find('td:eq(7)').text(entry.classLetter);
                        row.find('td:eq(8)').text(entry.semester);
                        row.find('td:eq(9)').text(entry.sks);
                        row.find('td:eq(10)').text(entry.kuota);
                        row.find('td:eq(11)').text(entry.namaDosenFormatted);
                        row.find('td:eq(12)').text(originalClassroom);
                        // row.css('background-color', 'white');

                        // Set data-kodeAjar to the base value
                        row.attr('data-kodeAjar', baseKodeAjar);

                        $(this).data('previousValue', selectedValue);

                        // Update selection count for the base kodeAjar
                        if (baseKodeAjar in kodeAjarSelectionCount) {
                            kodeAjarSelectionCount[baseKodeAjar]++;
                        } else {
                            kodeAjarSelectionCount[baseKodeAjar] = 1;
                        }
                    }

                    // Calculate SKS and new time if applicable
                    const sks = getSKSFromValue(selectedValue);
                    const newTime = calculateTimeForSKS(day, session, sks);
                    if (newTime) {
                        const [startTime, endTime] = newTime.split(' s/d ');
                        row.find('td:eq(1)').data('start-time', startTime);
                        row.find('td:eq(1)').data('end-time', endTime);
                        row.find('td:eq(1)').text(newTime);
                    }

                    // Check for duplicate lecturers and time conflicts
                    checkForDuplicateLecturers();
                    checkForTimeConflicts();
                    updateDropdownVisibility();
                });
            }

            //buatotomatis
            // New function for automatic schedule creation
            $('#buatOtomatis').on('click', function() {
                const spinner = $('#loading-spinner');

                // Tampilkan spinner
                spinner.show();
                // Gunakan setTimeout untuk memastikan halaman tidak membeku saat proses berjalan
                setTimeout(function() {
                    const kodeAjarData = parseTextareaData(); // Get kodeAjar data
                    const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    const sksOptionsPerRoom = {}; // Store SKS options per room

                    // Loop through each day and session
                    days.forEach((day) => {
                        for (let session = 1; session <= 4; session++) {
                            let rows = $(
                                `tr.schedule-row[data-day="${day}"][data-session="${session}"]`
                            );
                            let classroomCount = rows
                                .length; // Count classrooms available for this session

                            let semestersAvailable = getSemestersFromOptions(
                                kodeAjarData); // Get list of available semesters
                            let semesterAssignment = assignClassroomsToSemesters(
                                semestersAvailable,
                                classroomCount); // Assign classrooms to semesters

                            let semesterIndex = 0; // To track assignment across classrooms
                            rows.each(function(index) {
                                const row = $(this);
                                const dropdown = row.find('.kode-ajar-dropdown');
                                const room = row.find('td:eq(12)').text()
                                    .trim(); // Get the room number

                                // Cek apakah dropdown sudah terisi, jika ya, lewati
                                if (dropdown.val() !== '') {
                                    // Dropdown ini sudah terisi, tidak perlu diubah
                                    return;
                                }

                                // Get available options for the current semester
                                const assignedSemester = semesterAssignment[
                                    semesterIndex %
                                    semesterAssignment.length];
                                const availableOptions =
                                    getAvailableOptionsForSemester(
                                        kodeAjarData, session, day, room,
                                        sksOptionsPerRoom,
                                        lecturerMap, timeMap, assignedSemester
                                    );

                                if (availableOptions.length > 0) {
                                    // Pilih opsi pertama yang tidak menyebabkan konflik dosen atau waktu
                                    let selectedKodeAjar = availableOptions.find(
                                        option =>
                                        !isLecturerInConflict(option, day,
                                            session,
                                            kodeAjarData) &&
                                        !isTimeInConflict(day, session, room,
                                            option,
                                            kodeAjarData)
                                    );

                                    if (selectedKodeAjar) {
                                        let entry = kodeAjarData.find(e => e
                                            .kodeAjar ===
                                            selectedKodeAjar.replace(/_1|_2/,
                                                ''));

                                        if (entry) {
                                            // Isi opsi pada dropdown
                                            dropdown.val(selectedKodeAjar).trigger(
                                                'change');

                                            // Set opsi SKS untuk ruangan saat ini dan sesi berikutnya di ruangan tersebut
                                            sksOptionsPerRoom[room] =
                                                setSKSOptionsForRoom(
                                                    session, entry.sks, day, room);

                                            // Set waktu untuk SKS yang dipilih
                                            const newTime = calculateTimeForSKS(day,
                                                session,
                                                entry.sks);
                                            const [startTime, endTime] = newTime
                                                .split(' s/d ');
                                            row.find('td:eq(1)').data('start-time',
                                                startTime);
                                            row.find('td:eq(1)').data('end-time',
                                                endTime);
                                            row.find('td:eq(1)').text(newTime);

                                            // Update peta dosen dan waktu untuk sesi dan ruangan tersebut
                                            updateLecturerMap(day, session, entry
                                                .dosen1, entry
                                                .dosen2, lecturerMap);
                                            updateTimeMap(day, session, room,
                                                startTime,
                                                endTime, timeMap);

                                            // Perbarui visibilitas dropdown untuk menghindari duplikasi
                                            updateDropdownVisibility();
                                        }
                                    } else {
                                        // Tidak ada opsi tersedia tanpa konflik, pilih "N/A"
                                        dropdown.val("N/A").trigger('change');
                                    }
                                } else {
                                    // Tidak ada opsi tersedia sama sekali, pilih "N/A"
                                    dropdown.val("N/A").trigger('change');
                                }

                                semesterIndex++; // Pindah ke ruang kelas/semester berikutnya
                            });
                        }
                    });

                    // Periksa konflik setelah pengisian otomatis
                    checkForDuplicateLecturers();
                    checkForTimeConflicts();
                    // Sembunyikan spinner setelah proses selesai
                    spinner.hide();
                }, 100); // Delay kecil untuk memberikan waktu render UI
            });

            // Function to get available semesters from kodeAjarData
            function getSemestersFromOptions(kodeAjarData) {
                let semesters = new Set();
                kodeAjarData.forEach(entry => {
                    semesters.add(entry.semester); // Collect unique semesters
                });
                return Array.from(semesters);
            }

            // Function to assign classrooms to semesters
            function assignClassroomsToSemesters(semesters, classroomCount) {
                let semesterAssignment = [];

                // Jika semester lebih dari 3, rotasikan untuk memastikan semua semester terpilih
                let roomsPerSemester = Math.floor(classroomCount / semesters.length);
                let remainingRooms = classroomCount % semesters.length;

                // Distribusi ruang untuk setiap semester
                semesters.forEach((semester, i) => {
                    // Berikan sejumlah ruang ke semester ini
                    for (let j = 0; j < roomsPerSemester; j++) {
                        semesterAssignment.push(semester);
                    }
                    // Jika ada sisa ruang, berikan satu ruang tambahan ke beberapa semester pertama
                    if (remainingRooms > 0) {
                        semesterAssignment.push(semester);
                        remainingRooms--;
                    }
                });

                // Jika lebih dari 3 semester, kita akan shuffle untuk memastikan distribusi merata
                shuffleArray(semesterAssignment);

                return semesterAssignment;
            }

            // Helper function to get available options for a specific semester
            function getAvailableOptionsForSemester(kodeAjarData, session, day, room, sksOptionsPerRoom,
                lecturerMap, timeMap, assignedSemester) {
                let availableOptions = getAvailableOptionsForSession(kodeAjarData, session, day, room,
                    sksOptionsPerRoom, lecturerMap, timeMap);

                // Filter options based on the assigned semester
                return availableOptions.filter(option => {
                    let baseKodeAjar = option.replace(/_1|_2/, ''); // Strip _1 or _2 suffix
                    let entry = kodeAjarData.find(e => e.kodeAjar === baseKodeAjar);
                    return entry && entry.semester === assignedSemester;
                });
            }

            // Helper function to shuffle an array (Fisher-Yates shuffle algorithm)
            function shuffleArray(array) {
                for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]];
                }
            }

            // Helper function to get available options for a given session, day, and room
            function getAvailableOptionsForSession(kodeAjarData, session, day, room, sksOptionsPerRoom, lecturerMap,
                timeMap) {
                let availableOptions = [];

                // Get current SKS options for the room (from earlier sessions in that room)
                let currentSKS = sksOptionsPerRoom[room] || {
                    1: [2, 3, 4],
                    2: [2, 3, 4],
                    3: [2, 3, 4],
                    4: [2, 3, 4]
                };

                // For Friday, only SKS 2 and 4 are allowed for sessions 1 and 2 in that room
                if (day === 'Jumat' && (session === 1 || session === 2)) {
                    currentSKS[session] = [2, 4];
                }

                // Check available Kode Ajar based on the room's SKS limitation for that session
                kodeAjarData.forEach((entry) => {
                    if (currentSKS[session].includes(entry.sks)) {
                        if (entry.sks === 4) {
                            availableOptions.push(`${entry.kodeAjar}_1`, `${entry.kodeAjar}_2`);
                        } else {
                            availableOptions.push(`${entry.kodeAjar}`);
                        }
                    }
                });

                return availableOptions.filter(option => !isOptionSelected(option));
            }

            // Helper function to check if the selected time slot conflicts with existing schedules
            function isTimeInConflict(day, session, room, kodeAjar, kodeAjarData) {
                let baseKodeAjar = kodeAjar.replace(/_1|_2/, ''); // Remove _1 or _2 suffix for SKS-4
                let entry = kodeAjarData.find(e => e.kodeAjar === baseKodeAjar);

                if (entry) {
                    const sks = entry.sks;
                    const currentStartTime = calculateStartTimeForSKS(day, session, sks);
                    const currentEndTime = calculateEndTimeForSKS(day, session, sks);

                    let roomSchedule = timeMap[`${day}-${room}`] || [];

                    // Check if current time overlaps with any existing time slots in the room
                    return roomSchedule.some(schedule => isTimeOverlap(schedule.startTime, schedule.endTime,
                        currentStartTime, currentEndTime));
                }
                return false;
            }

            // Helper function to check if a lecturer is already assigned in the same session
            function isLecturerInConflict(kodeAjar, day, session, kodeAjarData) {
                let baseKodeAjar = kodeAjar.replace(/_1|_2/, ''); // Remove _1 or _2 suffix for SKS-4
                let entry = kodeAjarData.find(e => e.kodeAjar === baseKodeAjar);

                if (entry) {
                    let lecturersInSession = lecturerMap[`${day}-${session}`] || [];
                    return lecturersInSession.includes(entry.dosen1) || lecturersInSession.includes(entry.dosen2);
                }
                return false;
            }

            // Helper function to update lecturer map for a session
            function updateLecturerMap(day, session, dosen1, dosen2, lecturerMap) {
                let key = `${day}-${session}`;

                if (!lecturerMap[key]) {
                    lecturerMap[key] = [];
                }

                if (dosen1 && !lecturerMap[key].includes(dosen1)) {
                    lecturerMap[key].push(dosen1);
                }

                if (dosen2 && !lecturerMap[key].includes(dosen2)) {
                    lecturerMap[key].push(dosen2);
                }
            }

            // Helper function to update time map for a session and room
            function updateTimeMap(day, session, room, startTime, endTime, timeMap) {
                let key = `${day}-${room}`;

                if (!timeMap[key]) {
                    timeMap[key] = [];
                }

                timeMap[key].push({
                    session,
                    startTime,
                    endTime
                });
            }

            // Helper function to set SKS options for subsequent sessions based on current selection
            function setSKSOptionsForRoom(session, selectedSKS, day, room) {
                let sksOptions = {
                    1: [2, 3, 4],
                    2: [2, 3, 4],
                    3: [2, 3, 4],
                    4: [2, 3, 4]
                };

                if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(day)) {
                    if (session === 1 && (selectedSKS === 2 || selectedSKS === 4)) {
                        sksOptions[2] = [2, 3, 4];
                    } else if (session === 1 && selectedSKS === 3) {
                        sksOptions[2] = [2, 4];
                    } else if (session === 3 && (selectedSKS === 2 || selectedSKS === 4)) {
                        sksOptions[4] = [2, 3, 4];
                    } else if (session === 3 && selectedSKS === 3) {
                        sksOptions[4] = ['N/A'];
                    }
                } else if (day === 'Jumat') {
                    if (session === 1 || session === 2) {
                        sksOptions[session] = [2, 4];
                    } else if (session === 3) {
                        sksOptions[4] = [2];
                    }
                }

                return sksOptions;
            }

            // Helper function to check if a dropdown option has already been selected
            function isOptionSelected(option) {
                let selectedOptions = [];
                $('.kode-ajar-dropdown').each(function() {
                    let selectedValue = $(this).val();
                    if (selectedValue) {
                        selectedOptions.push(selectedValue);
                    }
                });
                return selectedOptions.includes(option);
            }

            function calculateStartTimeForSKS(day, session, sks) {
                let startTime;

                if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(day)) {
                    switch (session) {
                        case 1:
                            startTime = '07:30';
                            break;
                        case 2:
                            startTime = sks === 2 || sks === 4 ? '10:10' : '09:20';
                            break;
                        case 3:
                            startTime = '12:45';
                            break;
                        case 4:
                            startTime = '14:45';
                            break;
                    }
                } else if (day === 'Jumat') {
                    switch (session) {
                        case 1:
                            startTime = '07:30';
                            break;
                        case 2:
                            startTime = '09:20';
                            break;
                        case 3:
                            startTime = '13:00';
                            break;
                        case 4:
                            startTime = '15:35';
                            break;
                    }
                }
                return startTime;
            }

            function calculateEndTimeForSKS(day, session, sks) {
                let endTime;

                if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(day)) {
                    switch (session) {
                        case 1:
                            endTime = sks === 2 || sks === 4 ? '09:10' : '10:00';
                            break;
                        case 2:
                            endTime = '11:50';
                            break;
                        case 3:
                            endTime = sks === 2 || sks === 4 ? '14:25' : '15:15';
                            break;
                        case 4:
                            endTime = sks === 2 || sks === 4 ? '16:25' : '17:15';
                            break;
                    }
                } else if (day === 'Jumat') {
                    switch (session) {
                        case 1:
                            endTime = '09:10';
                            break;
                        case 2:
                            endTime = '11:00';
                            break;
                        case 3:
                            endTime = sks === 2 || sks === 4 ? '14:40' : '15:30';
                            break;
                        case 4:
                            endTime = '17:15';
                            break;
                    }
                }
                return endTime;
            }

            function setSKSOptionsForRoom(session, selectedSKS, day, room) {
                let sksOptions = {
                    1: [2, 3, 4],
                    2: [2, 3, 4],
                    3: [2, 3, 4],
                    4: [2, 3, 4]
                };

                if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(day)) {
                    if (session === 1 && (selectedSKS === 2 || selectedSKS === 4)) {
                        sksOptions[2] = [2, 3, 4];
                    } else if (session === 1 && selectedSKS === 3) {
                        sksOptions[2] = [2, 4];
                    } else if (session === 3 && (selectedSKS === 2 || selectedSKS === 4)) {
                        sksOptions[4] = [2, 3, 4];
                    } else if (session === 3 && selectedSKS === 3) {
                        sksOptions[4] = ['N/A'];
                    }
                } else if (day === 'Jumat') {
                    if (session === 1 || session === 2) {
                        sksOptions[session] = [2, 4];
                    } else if (session === 3) {
                        sksOptions[4] = [2];
                    }
                }

                return sksOptions;
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            var kodeMasaInput = '{{ $masaInput->kode_masa_input ?? '' }}'; // Pastikan ini string

            $('#saveJadwal').on('click', function() {
                var jadwalPerHari = {};
                var ajaxRequests = []; // Menampung semua request AJAX
                var daysWithExistingData = []; // Menampung hari dengan data yang sudah ada
                var overwriteRequests = []; // Track overwrite requests

                // Looping setiap row tabel untuk mengumpulkan data berdasarkan hari
                $('#tabelJadwal tbody tr').each(function() {
                    var row = $(this);
                    if (row.attr('id') === 'rowPembatas') return; // Abaikan row pembatas

                    var kodeAjar = row.find('td:eq(2) select').val(); // Nilai 'kode_ajar'
                    var day = row.attr('data-day'); // Ambil data hari dari atribut 'data-day'

                    var rowData = {
                        'data_hari': day,
                        'jadwal_data': [
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(0)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(1)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : kodeAjar,
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(3)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(4)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(5)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(6)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(7)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(8)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(9)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(10)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(11)').text().trim(),
                            kodeAjar === "N/A" || kodeAjar === null ? "N/A" : row.find(
                                'td:eq(12)').text().trim()
                        ]
                    };

                    // Kumpulkan data jadwal berdasarkan hari
                    if (!jadwalPerHari[day]) {
                        jadwalPerHari[day] = [];
                    }
                    jadwalPerHari[day].push(rowData.jadwal_data);
                });

                // Kirim data ke server untuk setiap hari dan cek apakah sudah ada
                $.each(jadwalPerHari, function(day, jadwalHari) {
                    // Simpan setiap request dalam array
                    let request = $.ajax({
                        url: '{{ route('admin.storeJadwalArray') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            kodeMasaInput: kodeMasaInput,
                            data_hari: day,
                            jadwalData: jadwalHari
                        },
                        success: function(response) {
                            if (response.exists) {
                                // Jika data sudah ada, tambahkan hari ke array 'daysWithExistingData'
                                daysWithExistingData.push(day);
                            }
                        },
                        error: function() {
                            console.log(
                                'Terjadi kesalahan saat mengecek data untuk hari ' +
                                day + '.');
                        }
                    });

                    // Tambahkan request ke array
                    ajaxRequests.push(request);
                });

                // Tunggu semua request selesai
                $.when.apply($, ajaxRequests).done(function() {
                    // Jika ada hari dengan data yang sudah ada, tampilkan konfirmasi sekali
                    if (daysWithExistingData.length > 0) {
                        if (confirm('Data sudah ada. Anda yakin ingin mengubahnya?')) {
                            // User memilih proceed, lakukan overwrite untuk semua hari yang sudah ada datanya
                            $.each(jadwalPerHari, function(day, jadwalHari) {
                                if (daysWithExistingData.includes(day)) {
                                    let overwriteRequest = $.ajax({
                                        url: '{{ route('admin.overwriteJadwal') }}',
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            kodeMasaInput: kodeMasaInput,
                                            data_hari: day,
                                            jadwalData: jadwalHari
                                        },
                                        success: function() {
                                            console.log('Data untuk ' + day +
                                                ' berhasil di-update.');
                                        },
                                        error: function() {
                                            console.log(
                                                'Gagal meng-update data untuk hari ' +
                                                day + '.');
                                        }
                                    });
                                    // Track overwrite requests
                                    overwriteRequests.push(overwriteRequest);
                                }
                            });

                            // Setelah semua overwrite selesai, tampilkan alert dan redirect
                            $.when.apply($, overwriteRequests).done(function() {
                                alert('Semua data berhasil di-update.');
                                window.location.href =
                                    '{{ route('adminScheduling') }}'; // Redirect setelah selesai
                            });
                        } else {
                            console.log('Pengiriman data dibatalkan oleh pengguna.');
                        }
                    } else {
                        // Jika tidak ada hari dengan data yang sudah ada, simpan data baru langsung
                        $.each(jadwalPerHari, function(day, jadwalHari) {
                            $.ajax({
                                url: '{{ route('admin.storeJadwalArray') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    kodeMasaInput: kodeMasaInput,
                                    data_hari: day,
                                    jadwalData: jadwalHari
                                },
                                success: function() {
                                    console.log('Data untuk ' + day +
                                        ' berhasil disimpan.');
                                },
                                error: function() {
                                    console.log(
                                        'Terjadi kesalahan saat menyimpan data untuk hari ' +
                                        day + '.');
                                }
                            });
                        });

                        // Tampilkan alert setelah menyimpan data baru dan redirect
                        alert('Semua data berhasil disimpan.');
                        window.location.href =
                            '{{ route('adminScheduling') }}'; // Redirect setelah selesai
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Pre-fill the textarea with data from the controller
            $('#inputDescription').val(`{!! $dataKelas !!}`);
        });
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
    <script>
        $(document).ready(function() {
            // Function to process inputDescription and fill "Jumlah Kelas" and "Kapasitas"
            function updateTableFromTextarea() {
                // Get the content of the textarea
                var inputDescription = $('#inputDescription').val().trim();

                // Split by new lines to get each array value (assuming each line represents one array)
                var lines = inputDescription.split("\n");

                // Initialize an object to store the data for each makul
                var makulData = {};

                // Process each line/array and extract data
                lines.forEach(function(line) {
                    // Parse the string into an array (assuming format `["${kodeAjar}", "${makulKode}", "${dosenFormatted}", "${dosen1}", "${dosen2}", "${namaDosenFormatted}", "${classLetter}", "${semesterValue}", "${sks}", "${kuota}"]`)
                    var values = line.replace(/[\[\]"]/g, '').split(',');

                    var makulKode = values[1].trim();
                    var kuota = values[9].trim();

                    // Check if the makulKode exists in the makulData object
                    if (!makulData[makulKode]) {
                        // Initialize if not already there
                        makulData[makulKode] = {
                            jumlahKelas: 0,
                            kapasitas: kuota // Store the capacity (same for all entries of the same makulKode)
                        };
                    }

                    // Increment the class count for the corresponding makulKode
                    makulData[makulKode].jumlahKelas++;
                });

                // Now, update the table based on the extracted data
                $('tr[data-kode]').each(function() {
                    var makulKode = $(this).data('kode');

                    // Check if the data exists for this makulKode
                    if (makulData[makulKode]) {
                        // Update Jumlah Kelas
                        $('#jumlah-kelas-' + makulKode).text(makulData[makulKode].jumlahKelas || '-');

                        // Update Kapasitas
                        $('#kapasitas-' + makulKode).text(makulData[makulKode].kapasitas || '-');
                    } else {
                        // If no data for this makulKode, show "-"
                        $('#jumlah-kelas-' + makulKode).text('-');
                        $('#kapasitas-' + makulKode).text('-');
                    }
                });
            }

            // Trigger the function to update the table on page load
            updateTableFromTextarea();

            // Reprocess the data when "refresh" is clicked
            $('#refreshClassing').click(function() {
                updateTableFromTextarea();
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
                    <!-- Input Configuration (Textarea for Kode Ajar) -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputDescription">Kode Ajar</label>
                                <textarea id="inputDescription" name="inputDescription" class="form-control" rows="4" style="height: 121px;"
                                    readonly></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Buat Jadwal Button -->
                    <div class="row">
                        <button type="submit" class="btn btn-info btn-block" data-toggle="modal"
                            data-target="#buatJadwalModal">Buat Jadwal</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#khusus" data-toggle="tab">Khusus</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#pilihan" data-toggle="tab">Pilihan</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#semester" data-toggle="tab">Semester</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="khusus">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Mata Kuliah</th>
                                            <th width="10%">Jumlah Mahasiswa</th>
                                            <th width="10%">Jumlah Kelas</th>
                                            <th width="10%">Kapasitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($specialMakuls as $makul)
                                            <tr id="{{ $makul->kode }}" data-kode="{{ $makul->kode }}"
                                                data-info="{{ $makul->mata_kuliah }} SEM-{{ $makul->semester }} SKS-{{ $makul->sks }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $makul->mata_kuliah }}</td>
                                                <td class="jumlah-mahasiswa">{{ $jumlahMahasiswa[$makul->kode] ?? 0 }}</td>
                                                <td class="jumlah-kelas" id="jumlah-kelas-{{ $makul->kode }}"></td>
                                                <td class="kapasitas" id="kapasitas-{{ $makul->kode }}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="pilihan">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Mata Kuliah</th>
                                            <th width="10%">Jumlah Mahasiswa</th>
                                            <th width="10%">Jumlah Kelas</th>
                                            <th width="10%">Kapasitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($makulPilihan as $makul)
                                            <tr id="{{ $makul->kode }}" data-kode="{{ $makul->kode }}"
                                                data-info="{{ $makul->mata_kuliah }} SEM-{{ $makul->semester }} SKS-{{ $makul->sks }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $makul->mata_kuliah }}</td>
                                                <td class="jumlah-mahasiswa">{{ $jumlahMahasiswa[$makul->kode] ?? 0 }}</td>
                                                <td class="jumlah-kelas" id="jumlah-kelas-{{ $makul->kode }}"></td>
                                                <td class="kapasitas" id="kapasitas-{{ $makul->kode }}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="semester">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Mata Kuliah</th>
                                            <th width="10%">Jumlah Mahasiswa</th>
                                            <th width="10%">Jumlah Kelas</th>
                                            <th width="10%">Kapasitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($makuls as $makul)
                                            <tr id="{{ $makul->kode }}" data-kode="{{ $makul->kode }}"
                                                data-info="{{ $makul->mata_kuliah }} SEM-{{ $makul->semester }} SKS-{{ $makul->sks }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $makul->mata_kuliah }}</td>
                                                <td class="jumlah-mahasiswa">{{ $jumlahMahasiswa[$makul->kode] ?? 0 }}</td>
                                                <td class="jumlah-kelas" id="jumlah-kelas-{{ $makul->kode }}"></td>
                                                <td class="kapasitas" id="kapasitas-{{ $makul->kode }}"></td>
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
    </div>
    <!-- Modal HTML -->
    <div class="modal fade" id="buatJadwalModal" tabindex="-1" aria-labelledby="buatJadwalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buatJadwalModalLabel">Jadwal Mata Kuliah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Display the fetched data here as a string -->
                    <!-- Otomatis Button -->
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-12" style="display:none;">
                            <div class="form-group col-6">
                                <input type="email" class="form-control" id="kode_masa_input"
                                    value="{{ $masaInput->kode_masa_input ?? '' }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-block btn-info btn-lg" id="buatOtomatis">Buat Otomatis</button>
                        </div>
                    </div>
                    <div id="jadwalData" style="margin-top: 20px;">
                        <table class="table table-bordered" id="tabelJadwal">
                            <thead>
                                <tr>
                                    <th width="5%">Hari</th>
                                    <th width="10%">Jadwal Kuliah</th>
                                    <th width="20%">Pilih Kode Ajar</th>
                                    <th width="10%">Kode MK</th>
                                    <th width="5%">Kode Dosen</th>
                                    <th width="3%">Dosen 1</th>
                                    <th width="3%">Dosen 2</th>
                                    <th width="2%">Kelas</th>
                                    <th width="2%">SMTR</th>
                                    <th width="2%">SKS</th>
                                    <th width="3%">Kapasitas</th>
                                    <th width="15%">Nama Pengajar</th>
                                    <th width="3%">Ruang</th>
                                    <th width="17%">Keterangan</th> <!-- New column for Keterangan -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Schedule rows will be inserted here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveJadwal">Simpan Jadwal</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal FAB -->
    <button class="btn btn-primary btn-fab" id="scrollToTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>
    <div id="loading-spinner"
        style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="overlay">
            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>
@endsection
