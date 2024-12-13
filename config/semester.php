<?php

return [
    'semesters' => [
        '1' => [
            'mata_kuliah' => [
                'Pendidikan Pancasila',
                'Pendidikan Agama',
                'Bahasa Inggris',
                'Matematika Dasar I',
                'Logika Informatika',
                'Dasar Pemrograman',
                'Pengantar Teknik Informatika'
            ],
            'kode' => [
                'UMG-55201-101',
                'UMG-55201-105',
                'UMG-55201-103',
                'INF-55201-101',
                'INF-55201-102',
                'INF-55201-103',
                'INF-55201-104'
            ],
            'sks' => [2, 3, 2, 3, 3, 4, 2]
        ],
        '2' => [
            'mata_kuliah' => [
                'Kewarganegaraan',
                'Bahasa Indonesia',
                'Probabilitas dan Statistik',
                'Matematika Dasar II',
                'Organisasi dan Arsitektur Komputer',
                'Dasar rekayasa perangkat lunak',
                'Struktur Data dan Algoritma'
            ],
            'kode' => [
                'UMG-55201-104',
                'UMG-55201-102',
                'INF-55201-105',
                'INF-55201-106',
                'INF-55201-107',
                'INF-55201-108',
                'INF-55201-109'
            ],
            'sks' => [2, 2, 3, 3, 3, 3, 4]
        ],
        '3' => [
            'mata_kuliah' => [
                'Perancangan Basis Data',
                'Teori Graf',
                'Matematika Diskrit',
                'Metode Numerik',
                'Jaringan Komputer',
                'Strategi Algoritma',
                'Sistem Operasi'
            ],
            'kode' => [
                'INF-55201-201',
                'INF-55201-202',
                'INF-55201-203',
                'INF-55201-204',
                'INF-55201-205',
                'INF-55201-206',
                'INF-55201-207'
            ],
            'sks' => [2, 2, 3, 3, 4, 4, 3]
        ],
        '4' => [
            'mata_kuliah' => [
                'Manajemen Basis Data',
                'Otomata',
                'Sistem Paralel dan Terdistribusi',
                'Pemrograman Berorientasi Objek',
                'Analisis dan Perancangan Sistem',
                'Sistem Informasi',
                'Sistem Pendukung Keputusan'
            ],
            'kode' => [
                'INF-55201-208',
                'INF-55201-209',
                'INF-55201-210',
                'INF-55201-211',
                'INF-55201-212',
                'INF-55201-213',
                'INF-55201-214'
            ],
            'sks' => [4, 2, 3, 4, 3, 2, 3]
        ],
        '5' => [
            'mata_kuliah' => [
                'Interaksi Manusia dan Komputer',
                'Pemrograman Web',
                'Pemrograman Jaringan',
                'Sistem Tertanam',
                'Kecerdasan Buatan',
                'Manajemen Proyek Perangkat Lunak',
                'Penulisan Proposal Tugas Akhir',
                'Riset Operasi'
            ],
            'kode' => [
                'INF-55201-301',
                'INF-55201-302',
                'INF-55201-303',
                'INF-55201-304',
                'INF-55201-305',
                'INF-55201-306',
                'INF-55201-310',
                'INF-55201-311'
            ],
            'sks' => [2, 4, 3, 3, 3, 2, 2, 2]
        ],
        '6' => [
            'mata_kuliah' => [
                'Teori Komputer Grafis',
                'Keamanan Informasi dan Jaringan',
                'Sosio dan Etika Profesi',
                'MK Pilihan Wajib Komputasi (PBA)',
                'MK Pilihan Wajib Jaringan (Jaringan Nirkabel)',
                'MK Pilihan Wajib Multimedia & SIG (SIG)',
                'Proyek Perangkat Lunak',
                'Kerja Praktik',
                'PMKM'
            ],
            'kode' => [
                'INF-55201-307',
                'INF-55201-309',
                'INF-55201-312',
                'INF-55201-313',
                'INF-55201-314',
                'INF-55201-315',
                'INF-55201-316',
                'INF-55201-308',
                'INF-55201-402'
            ],
            'sks' => [2, 3, 2, 3, 3, 3, 4, 2, 2]
        ],
        '7' => [
            'mata_kuliah' => ['Teknopreneur', 'TA 1 (Proposal)'],
            'kode' => ['INF-55201-401', 'INF-55201-406'],
            'sks' => [3, 2]
        ],
        '8' => [
            'mata_kuliah' => ['TA 2'],
            'kode' => ['INF-55201-407'],
            'sks' => [4]
        ],
        // Additional courses for semester 6
        '6_additional' => [
            'mata_kuliah' => [
                'Pemrograman Berbasis Kerangka Kerja',
                'Rekayasa Kebutuhan',
                'Verifikasi dan Validasi Perangkat Lunak',
                'Teknik Pengembangan Game',
                'Sistem Enterprise',
                'Pengolahan Citra Digital',
                'Pemrograman Linier',
                'Teknologi Antar Jaringan',
                'Jaringan Nirkabel',
                'Perancangan Keamanan Sistem dan Jaringan',
                'Sistem Terdistribusi',
                'Evolusi Perangkat Lunak',
                'Rekayasa Pengetahuan',
                'Sistem Game',
                'Pemrograman Perangkat Bergerak',
                'Data Mining',
                'Audit Sistem',
                'Visi Komputer',
                'Jaringan Komputer Lanjut',
                'Sistem Temu Kembali Informasi',
                'Pemodelan dan Simulasi',
                'Animasi Komputer dan Pemodelan 3D',
                'Komputasi Awan',
                'Komputasi Bergerak',
                'Komputasi Grid dan Paralel',
                'Komputasi Pervasive dan Jaringan Sensor',
                'Teknik Kompresi',
                'Robotika',
                'Virtual & Augmented Reality',
                'Design Pattern',
                'Forensik Digital',
                'Topik Khusus dalam Komputasi Jaringan',
                'Sistem Informasi Geografis II',
                'Arsitektur Perangkat Lunak',
                'Simulasi dan Game Komputer',
                'Pemrosesan Bahasa Alami',
                'CAI',
                'Logika Fuzzy',
                'Fisika Model',
                'Kriptografi',
                'Basis Data Multimedia',
                'Penalaran Berbasis Kasus',
                'Jaringan Saraf Tiruan',
                'Jaringan Multimedia'
            ],
            'kode' => [
                'INF-55201-501',
                'INF-55201-502',
                'INF-55201-503',
                'INF-55201-504',
                'INF-55201-505',
                'INF-55201-506',
                'INF-55201-507',
                'INF-55201-508',
                'INF-55201-509',
                'INF-55201-510',
                'INF-55201-511',
                'INF-55201-512',
                'INF-55201-513',
                'INF-55201-514',
                'INF-55201-515',
                'INF-55201-516',
                'INF-55201-517',
                'INF-55201-518',
                'INF-55201-519',
                'INF-55201-520',
                'INF-55201-521',
                'INF-55201-522',
                'INF-55201-523',
                'INF-55201-524',
                'INF-55201-525',
                'INF-55201-526',
                'INF-55201-527',
                'INF-55201-528',
                'INF-55201-529',
                'INF-55201-530',
                'INF-55201-531',
                'INF-55201-532',
                'INF-55201-533',
                'INF-55201-534',
                'INF-55201-535',
                'INF-55201-536',
                'INF-55201-537',
                'INF-55201-538',
                'INF-55201-539',
                'INF-55201-540',
                'INF-55201-541',
                'INF-55201-542',
                'INF-55201-543',
                'INF-55201-544'
            ],
            'sks' => [3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3]
        ],
    ]
];
