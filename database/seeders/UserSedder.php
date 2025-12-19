<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data asli (username dengan prefix PW + nama)
        $list = [
            [
                "MARYADI",
                "24670130810000948",
                "6203012303890005",
                "198903232025211176",
                "Kapuas",
                "",
                "SMK BISNIS DAN MANAJEMEN",
                "Operator Layanan Operasional",
                "Dinas Lingkungan Hidup dan Kehutanan"
            ],
            [
                "MUHAMMAD ARSYAD",
                "24670130810000901",
                "6203010101800030",
                "198001012025211255",
                "Kapuas",
                "",
                "PAKET C ILMU PENGETAHUAN SOSIAL",
                "Operator Layanan Operasional",
                "Dinas Lingkungan Hidup dan Kehutanan"
            ]
        ];

        foreach ($list as $row) {
            // $username = str_replace('PW', '', $row[0]); // hilangkan "PW"
            if ($row[3] != 0) {
                $tanggal_lahir = Carbon::createFromFormat(
                    'Ymd',
                    substr($row[3], 0, 8)
                )->format('Y-m-d');
            } else {
                $tanggal_lahir = null;
            }
            $data[] = [
                'nama'     => $row[0],
                'username' => $row[1],
                'nik' => $row[2],
                'nip'     => $row[3],
                'tempat_lahir'     => $row[4],
                'tanggal_lahir'     => $tanggal_lahir,
                'pendidikan'     => $row[6],
                'jabatan'     => $row[7],
                'unit_kerja'     => $row[8],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        User::insert($data);
    }
}
