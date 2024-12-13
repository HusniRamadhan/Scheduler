<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturer_name=[
            "Anggi Perwitasari S.T. M.T.",
            "Anggi Srimurdianti S.T. M.T.",
            "Dr. Arif BPN S.T. M.T.",
            "Prof. Dr. Herry Sujaini S.T. M.T.",
            "Dr. Yus Sholva S.T. M.T.",
            "Rina Septiriana S.T. M.Cs.",
            "Enda Esyudha Pratama S.T. M.T.",
            "Eva Faja Ripanti S.Kom MMSI Ph.D",
            "H. Hengky Anra S.T. M.Kom",
            "Hafiz Muhardi S.T. M.Kom",
            "Haried Novriando S.Kom M.Eng",
            "Helen Sastypratiwi S.T. M.Eng",
            "Helfi Nasution S.Kom M.Cs",
            "Heri Priyanto S.T. M.T.",
            "M.Azhar Irwansyah S.T. M.Eng.",
            "MKU", //Mata Kuliah Umum
            "Morteza Muthahhari S.Kom. M.T.I",
            "Novi Safriadi S.T. M.T.",
            "Rudy Dwi Nyoto S.T. M.Eng",
            "Tursina ST M.Cs",
            "Yulianti S.Kom MMSI",
            "Alfian Abdul Jalid S.Kom. M.Cs.",
            "Desepta Isna Ulumi S.Kom M.Kom.",
            "Fauzan Asrin S.Kom M. Kom.",
            "Khairul Hafidh S.T. M.Kom.",
            "Niken Candraningrum S.T. M.Cs.",
            "Riadi Budiman S.T. M.T.",
            "Prof. Dr. -Ing. Seno D. Panjaitan S.T. M.T. IPM.",
            "Prof. Dr. Eng. Ir. Rudi Kurnianto S.T. M.T. IPM."
        ];
        $lecturer_code=[
            "AP",
            "AS",
            "ABP",
            "HS",
            "YS",
            "RS",
            "EE",
            "EFR",
            "HA",
            "HM",
            "HN",
            "HSP",
            "HLN",
            "HP",
            "AI",
            "MKU", //Mata Kuliah Umum
            "TZ",
            "NS",
            "RDN",
            "TR",
            "YL",
            "AAJ",
            "DIU",
            "FA",
            "KH",
            "NC",
            "RB",
            "SP",
            "RK"
        ];
        for ($i=0; $i<count($lecturer_name);$i++){
            Dosen::create([
                'nama_dosen' => $lecturer_name[$i],
                'kode_dosen' => $lecturer_code[$i],
            ]);
        }
    }
}
