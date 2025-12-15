<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ServicePdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function search(Request $request)
    {
        $request->validate([
            'no_peserta' => 'required_without:nik|size:17',
            'nik' => 'required_without:no_peserta|digits:16',
        ], [
            'no_peserta.required_without' => 'Nomor peserta wajib diisi jika NIK tidak diisi.',
            'no_peserta.size' => 'Nomor peserta harus berjumlah 17 karakter.',
            'nik.required_without' => 'NIK wajib diisi jika Nomor Peserta tidak diisi.',
            'nik.digits' => 'NIK harus berjumlah 16 digit angka.',
        ]);


        $query = User::query();

        if ($request->no_peserta) {
            $query->where('username', $request->no_peserta);
        }

        if ($request->nik) {
            $query->where('nik', $request->nik);
        }

        $result = $query->first();

        return view('welcome', [
            'result' => $result,
            'has_search' => true,
        ]);
    }
    public function cetakSpk(Request $request, ServicePdf $pdf)
    {
        $query = User::query();

        if ($request->no_peserta) {
            $query->where('username', $request->no_peserta);
        }

        if ($request->nik) {
            $query->where('nik', $request->nik);
        }
        $result = $query->first();
        $nip = $result->nip;
        $tanggal_lahir = DateTime::createFromFormat('Ymd', substr($nip, 0, 8))
                  ->format('d-m-Y');
        $html = view('spk.rinder', ['result' => $result, 'tanggal_lahir' => $tanggal_lahir])->render();

        // $filename = $result->usernmae . '_SPKPW.pdf';

        // INI CARA BENAR UNTUK PREVIEW
        return $pdf->generate($html, 'nip-spkpw.pdf', 'I');

        // return abort(404);
    }

    public function uploadSpk(Request $request)
    {
        $request->validate([
            'spk_final' => 'required|mimes:pdf|max:2048',
            // max:2048 = 2MB
        ]);
        $nopeserta = $request->nopeserta;
        $ext = $request->file('spk_final')->extension();
        $fileName = $nopeserta . '_SPK.' . $ext;

        // Path folder public/spk
        $destination = public_path('spk');

        // Pastikan folder ada, kalau tidak buat
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        // Pindahkan file
        $request->file('spk_final')->move($destination, $fileName);

        return back()->with('success', 'Dokumen berhasil diupload!');
    }


public function updateData() {
        $list_data = $this->getPegawaiData();
        DB::transaction(function () use ($list_data) {
            foreach ($list_data as $item) {
                User::updateOrCreate(
                    [
                        'nik' => $item[2], // KEY PENCARIAN
                    ],
                    [
                        'nama'          => $item[0],
                        'username'      => $item[1],
                        'nip'           => $item[3] ?? null,
                        'tempat_lahir'  => $item[4] ?? null,
                        'tanggal_lahir' => null,
                        'pendidikan'    => $item[5] ?? null,
                        'jabatan'       => $item[6] ?? null,
                        'unit_kerja'    => $item[7] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil di-update / ditambahkan'
        ]);
    }


    private function getPegawaiData(): array {
        return 
        [
    [
        "ANDRE SUNTOSO",
        "24670130810000970",
        "6203050504990003",
        "199904052025211087",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "MARYADI",
        "24670130810000948",
        "6203012303890005",
        "198903232025211176",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ABDUL SAHID",
        "24670130810000891",
        "6203012912690001",
        "196911292025211022",
        "Kapuas",
        "SMEA KEUANGAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AHMAD BAIHAQI",
        "24670130810000344",
        "6203071203000001",
        "200003122025211050",
        "Banjarbaru",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Baru"
    ],
    [
        "RONNI",
        "24670130810000978",
        "6371041805820015",
        "198205182025211115",
        "Palangka Raya",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "BUDIYATI",
        "24670130820000503",
        "6203015604690006",
        "196904162025212008",
        "Temanggung",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RUSLAN",
        "24670130810000566",
        "6203012801880005",
        "198801282025211125",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Kelurahan Selat Hilir"
    ],
    [
        "SAIFUL FITRI",
        "24670130810000840",
        "6203010303940009",
        "199503032025211133",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "THOMAS",
        "24670130810000875",
        "6203023110900002",
        "199010312025211095",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "WAHDINI",
        "24670130810000621",
        "6203010807890005",
        "198907132025211174",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "A. SYARIF",
        "24670130810000596",
        "6203011303960012",
        "199603132025211122",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AAN SUMARNI, S.AP",
        "24670130820000056",
        "6203015212810007",
        "198112122025212081",
        "Kapuas",
        "S-1 ADMINSTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "HARTOYO",
        "24670130810000326",
        "6203010701690006",
        "196901072025211028",
        "Ngawi",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ABDI IRIANTO WIBOWO",
        "24670130810000506",
        "6203011910920002",
        "199210192025211098",
        "Kapuas",
        "SMK PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HENDRI",
        "24670130810000486",
        "6203010404660013",
        "196901242025211016",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "ICUN I. DAMIS",
        "24670130810000896",
        "6203021304690001",
        "196904132025211026",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ABDUL AZIZ",
        "24670130810000904",
        "6203010709830006",
        "198309072025211120",
        "Kapuas",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ISKANDAR",
        "24670130810001042",
        "6203052306690001",
        "196906232025211020",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "ABDUL GANI, S.Pi",
        "24670130810000994",
        "6203013004900008",
        "199004302025211121",
        "Kapuas",
        "D-IV BUDIDAYA PERAIRAN",
        "Penata Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "KORNELIUS",
        "24670130810000579",
        "6203011206690011",
        "196906122025211051",
        "Bengkulu",
        "SMA ILMU ILMU BIOLOGI",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "LAMRI",
        "24670130810000463",
        "6203011605690001",
        "196905162025211024",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ABDUL HAMID",
        "24670130810000757",
        "6203080605850004",
        "198609182025211115",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "LEMBAI",
        "24670130820000473",
        "6203104506690002",
        "196906052025212019",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ABDUL KADIR",
        "24670130810000556",
        "6271030505810015",
        "199105052025211235",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "ABDUL RAHIM",
        "24670130810000413",
        "6203020405990001",
        "199905042025211056",
        "Kapuas",
        "SMK KEUANGAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ABDUL RAHMAN",
        "24670130810000372",
        "6203020405990002",
        "199905042025211057",
        "Kapuas",
        "SMK KEUANGAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MARANTUTI",
        "24670130820000637",
        "6203096011690002",
        "196911202025212016",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ABDUL SAID, S.Kom",
        "24670130810000204",
        "3524082805850001",
        "198505282025211096",
        "Lamongan",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana"
    ],
    [
        "ABDUL SIDIK",
        "24670130810000045",
        "6203082408880002",
        "198808242025211129",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Basarang"
    ],
    [
        "ABDUL SUKUR",
        "24670130810000776",
        "6203012808880014",
        "198808272025211127",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "NENENG LITALIE, S.Hut",
        "24670110820000462",
        "6203105009690003",
        "196909102025212020",
        "Kapuas",
        "S-1 KEHUTANAN",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Timpah"
    ],
    [
        "ABDULLAH",
        "24670130810000715",
        "6203011402940005",
        "199402142025211133",
        "Hulu Sungai Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AHMAD MAULANA",
        "24670130810000126",
        "6203012207000006",
        "200007222025211046",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ABDURRAHMAN SHIDDIQ, S.Pd",
        "24670110810000093",
        "6203042201000002",
        "200001222025211045",
        "Kapuas",
        "D-IV PENDIDIKAN GURU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BANGUN HARJO"
    ],
    [
        "NGATMINASIH",
        "24670130820000301",
        "6203016908690004",
        "196908292025212010",
        "Jember",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ABU YAZID",
        "24670130810000713",
        "6203010505930009",
        "199305052025211220",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "ACHMAD AJI PRAYOGA",
        "24670130810000397",
        "6203010205980003",
        "199805022025211103",
        "Surabaya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ACHMAD FAUZAN",
        "24670130810000057",
        "6203012006900013",
        "199006202025211153",
        "Kotawaringin Barat",
        "SMA ILMU SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "ACHMAD IRAWAN MISTER",
        "24670130810000643",
        "6203011111930011",
        "199311112025211161",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ACHMAD JAM'AN JAILANI, S. Pd",
        "24670110810000102",
        "6203032906880002",
        "198806292025211120",
        "Palangka Raya",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Mambulau Barat"
    ],
    [
        "ACHMAD SOLEH",
        "24670130810000990",
        "3308102304830002",
        "198304232025211097",
        "Magelang",
        "SLTP/SMP SEDERAJAT",
        "Pengelola Umum Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ADE ANGGARA PUTRA, S.Kom",
        "24670120110000562",
        "6203012304930009",
        "199304232025211148",
        "Banjarmasin",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ADE KUSUMA, S.Pd",
        "24670110810000230",
        "6271010209870003",
        "198709272025211122",
        "Palangka Raya",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tumbang Diring"
    ],
    [
        "ADE NOTRAPIAN",
        "24670130810000826",
        "6213012606860001",
        "198606262025211200",
        "Barito Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Dadahup"
    ],
    [
        "ADE SAPUTRI",
        "24670130820000117",
        "6203016702890004",
        "198902272025212112",
        "Kapuas",
        "SMK TEKNOLOGI INFORMASI DAN KOMUNIKASI",
        "Operator Layanan Operasional",
        "Dinas Perumahan, Kawasan Permukiman, dan Pertanahan"
    ],
    [
        "ADELIA LAVENAZHARY, S.Psi.",
        "24670620120000281",
        "6203016711940004",
        "199411272025212118",
        "Kapuas",
        "S-1 PSIKOLOGI",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "ADETHYA WULANDARI, S.Sos",
        "24670130820000230",
        "6203014211880009",
        "198811022025212095",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ADETIA",
        "24670130810000264",
        "6203012911910008",
        "199111292025211111",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "ADI DARMA",
        "24670020110000808",
        "6271031804930004",
        "199304182025211128",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ADI PUTRA",
        "24670130810001034",
        "6203010602890005",
        "198902062025211142",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ADI SUCIPTO, A.md",
        "24670130810000174",
        "6203012301810006",
        "198101232025211072",
        "Kapuas",
        "D-III TEKNIK INDUSTRI",
        "Pengelola Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "ADI SUTRISNO",
        "24670130810000889",
        "6271032610820005",
        "198210262025211076",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "ADITIA PUTRA PRATAMA",
        "24670130810000328",
        "6271011410950005",
        "199510142025211132",
        "Palangka Raya",
        "STM PAKET C",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ADITYA MAULANA, Amd.kep",
        "24670140810000130",
        "6203062207980002",
        "199807222025211082",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ADITYA RAHMANI, S.Pd",
        "24670110810000286",
        "6203030309900005",
        "199009032025211124",
        "Kapuas",
        "S-1/A-IV PENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Serapat Timur"
    ],
    [
        "ADITYAS PRAYOGO",
        "24670130810000092",
        "6203010204890009",
        "198904022025211147",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "PRIANTO YANTINO",
        "24670130810000580",
        "6371052209690003",
        "196909222025211021",
        "Barito Timur",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ADJINOR OCTAVIA",
        "24670130820000570",
        "6204065510900006",
        "199010152025212170",
        "Banjarmasin",
        "SMA ILMU SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Perumahan, Kawasan Permukiman, dan Pertanahan"
    ],
    [
        "ADOLF SULTAN, S.T.",
        "24670130810000336",
        "6203010503840015",
        "198403052025211120",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "ADRI",
        "24670130810000818",
        "6203011806810001",
        "198106182025211090",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "AHMAD MUNADI, S.Kom",
        "24670130810000088",
        "6203070504000003",
        "200004052025211059",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "ADRIANA MARIA CRISTINA, S.Pd",
        "24670110820000354",
        "6203014502950009",
        "199502052025212144",
        "Palangka Raya",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Timpah Satu Atap"
    ],
    [
        "ADRIANUR",
        "24670130810000301",
        "6203012110990009",
        "199910212025211070",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Gudang Farmasi Kesehatan"
    ],
    [
        "AHMAD MURSYID",
        "24670130810000592",
        "6203083108000001",
        "200008312025211050",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "AFNANINGSIH",
        "24670130820000122",
        "6203016703850008",
        "198503272025212083",
        "Bima",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AFRIANTI THEODORA SIJABAT, A.Md.Kep",
        "24670140820000042",
        "6203106104880001",
        "198804202025212135",
        "Simalungun",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Timpah"
    ],
    [
        "SEKO WINARNO",
        "24670130810001029",
        "6203100704690002",
        "196904072025211041",
        "Kapuas",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Kecamatan Timpah"
    ],
    [
        "AGUNG ADHITAMA",
        "24670130810000791",
        "6203011601920003",
        "199201162025211112",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AGUNG SETIAWAN",
        "24670130810000375",
        "6301022508830002",
        "198308252025211086",
        "Tanah Laut",
        "D-III SEMUA JURUSAN",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana"
    ],
    [
        "SITI MUSYAROPAH",
        "24670130820000604",
        "6203044403690004",
        "196903042025212012",
        "Jember",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AGUS IRAWAN",
        "24670130810000587",
        "6203020808930001",
        "199308082025211142",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "AGUS IRAWAN",
        "24670130810000832",
        "6203082208990001",
        "199908202025211079",
        "Kapuas",
        "SMK AGRIBISNIS TANAMAN PANGAN DAN HORTIKULTURA",
        "Operator Layanan Operasional",
        "Kecamatan Basarang"
    ],
    [
        "AGUS MARTINUS, S.P",
        "24670110810000202",
        "6203110108980001",
        "199708012025211097",
        "Kapuas",
        "AKADEMI PERTANIAN",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Kapuas Tengah Satu Atap"
    ],
    [
        "SRI SUSANTI",
        "24670130820000478",
        "6203016912690003",
        "196912292025212013",
        "Barito Utara",
        "SMEA",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "AGUS PRIANTO",
        "24670130810000023",
        "6203011608990005",
        "199908162025211060",
        "Kapuas",
        "SMK PERHOTELAN",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "TRIS VENTILOWATI",
        "24670130820000462",
        "6203016810690003",
        "196910282025212010",
        "Kapuas",
        "SMA ILMU ILMU SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "YUSUA",
        "24670130810000583",
        "6203011605690002",
        "196905162025211023",
        "Kapuas",
        "SMA ILMU ILMU BIOLOGI",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AGUS SAMSIANOR",
        "24670130810000824",
        "6203010208820002",
        "198208262025211096",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ABDUL SYAHID",
        "24670130810000837",
        "6203070302700005",
        "197002032025211047",
        "Barito Kuala",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "AGUS WIDODO",
        "24670130810000618",
        "6203081108890002",
        "198908112025211152",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AHMAD ROIZ MAULANA AKBAR, A.md.Tra",
        "24670130810000586",
        "6203011004000005",
        "200004102025211065",
        "Kapuas",
        "D-III MANAJEMEN TRANSPORTASI",
        "Pengelola Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "AGUSTINA, S.Pd",
        "24670110820000370",
        "6210115808000001",
        "200008182025212062",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Pujon"
    ],
    [
        "AGUSTINA",
        "24670130820000484",
        "6203015708880007",
        "198808172025212209",
        "Barito Kuala",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ATIE",
        "24670130820000614",
        "6203027108720002",
        "197008312025212010",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AGUSTINUS, S.Pd",
        "24670110810000112",
        "6203091708920007",
        "199208172025211258",
        "Kapuas",
        "S-1 PENDIDIKAN JASMANI KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SMP NEGERI 12 MANTANGAI SATU ATAP"
    ],
    [
        "AGUSTINUS",
        "24670130810000928",
        "6203010108840001",
        "198408012025211122",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "DEWI",
        "24670130820000340",
        "6203095108710002",
        "197008112025212014",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "UPT Puskesmas Mantangai"
    ],
    [
        "AHMAD ANHAR FAUJI",
        "24670130810000431",
        "6211050210930002",
        "199210022025211157",
        "Pulang Pisau",
        "SMK AGRIBISNIS PERIKANAN",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "ANANDA BASTARI",
        "24670130820000215",
        "6203016704000001",
        "200004272025212060",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "AHMAD BAKRI",
        "24670130810001037",
        "6203061903950001",
        "199503192025211102",
        "Barito Kuala",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AHMAD FAUZAN",
        "24670130810000565",
        "6203010701860008",
        "198601082025211113",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "AHMAD FAUZANNOR, S.Pd",
        "24670110810000196",
        "6203041908950002",
        "199508192025211117",
        "Kapuas",
        "S-1 BIMBINGAN DAN KONSELING PENDIDIKAN ISLAM",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 TAMBAN CATUR"
    ],
    [
        "AHMAD GAJALI, S.Pd.I",
        "24670130810000498",
        "6203010107880247",
        "198807012025211188",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AHMAD HARIRI, S.Pd",
        "24670110810000231",
        "3402083006980001",
        "199806302025211088",
        "Sampang",
        "S-1 PENDIDIKAN MATEMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 7 Timpah Satu Atap"
    ],
    [
        "AHMAD HIDAYAT, S.Pd.I",
        "24670130810000442",
        "6203030512840003",
        "198412052025211091",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Penata Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "AHMAD HIDAYAT",
        "24670130810000478",
        "6203022201870002",
        "198701222025211089",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "ANDI SISWANTO",
        "24670130810000048",
        "6203071503000006",
        "200003152025211045",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "AHMAD ISJANI",
        "24670130810000675",
        "6203012301980002",
        "199801232025211088",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MARIYATI",
        "24670130820000475",
        "6203014503700015",
        "197003052025212010",
        "Kapuas",
        "SMEA",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "AHMAD JAMALIANSYAH",
        "24670130810000834",
        "6203070205930001",
        "199305022025211133",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MURJANI",
        "24670130810000761",
        "6271031010700012",
        "197010102025211064",
        "Barito Kuala",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "AHMAD KUSMADI",
        "24670130810000703",
        "6203012310950003",
        "199510232025211095",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "BRYAN JOGI KRISTIANSEN",
        "24670130810000447",
        "6203012410000005",
        "200010242025211036",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "CHINDIHA MARDENDY",
        "24670130810000610",
        "6210052903000002",
        "200003292025211045",
        "Gunung Mas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DAVID KURNIANTO",
        "24670120110000303",
        "6203022701000004",
        "200001272025211047",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "AHMAD MUZAKIR, S.Kom",
        "24670130810000389",
        "6203012810970003",
        "199710282025211109",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "AHMAD NORYANI",
        "24670130810000679",
        "6203012311940007",
        "199412232025211136",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AHMAD RAFI'I",
        "24670130810000907",
        "6203010304810007",
        "198104032025211085",
        "Kapuas",
        "SMK SEKRETARIS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AHMAD REFANI",
        "24670130810000439",
        "6203071405970004",
        "199705142025211094",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AHMAD REFKA, S.pd.I",
        "24670110810000278",
        "6203040506970002",
        "199706052025211126",
        "Kapuas",
        "S-1 PENDIDIKAN SEJARAH",
        "Guru Ahli Pertama",
        "SD Negeri 1 Simpang Bunga Tanjung"
    ],
    [
        "AHMAD REZA GIFARI",
        "24670130810000117",
        "6203010301930005",
        "199201032025211125",
        "Banjarmasin",
        "SMA BAHASA",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "AHMAD RIFAI, S,Pd",
        "24670110810000156",
        "6203040611950006",
        "199511062025211114",
        "Kapuas",
        "S-1 AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 2 PEMATANG"
    ],
    [
        "DONI SAPUTRA",
        "24670130810000226",
        "6203090807000003",
        "200007052025211059",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "AHMAD RIPA'I",
        "24670130810000946",
        "6203011406890004",
        "198906142025211154",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AHMAD ROBBY PUTRA",
        "24670130810000113",
        "6203011011990012",
        "199911102025211095",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Gudang Farmasi Kesehatan"
    ],
    [
        "EWALDO JOSANTIO",
        "24300420110065568",
        "6203012306000002",
        "200006232025211058",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "AHMAD ROKHIM",
        "24670130810000607",
        "6203012009890003",
        "198909202025211154",
        "Lamongan",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "FARHAN FADHILAH, S.Kom",
        "24670120110000486",
        "6203011809000004",
        "200009182025211052",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "AHMAD SALAHUDDIN AL AYUBI",
        "24670130810000387",
        "6203012002980011",
        "199802202025211091",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "AHMAD SAMSURI, S.pd.I",
        "24670110810000220",
        "6203170202950001",
        "199502022025211130",
        "Kapuas",
        "D-IV PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD 2 Bamban Raya"
    ],
    [
        "AHMAD SAPUAN NURMISSUARI",
        "24670130810000211",
        "6203012510950005",
        "199510252025211132",
        "Banjarmasin",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Laboratorium Kesehatan Daerah Kabupaten Kapuas"
    ],
    [
        "AHMAD SOLEHAN",
        "24670130810000827",
        "6203012706830004",
        "198306272025211104",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "NORAINAH",
        "24670130820000649",
        "6203094704700004",
        "197004072025212007",
        "Kapuas",
        "STM BANGUNAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AHMAD YANI",
        "24670130810000637",
        "6203012711810003",
        "198111272025211086",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kelurahan Dahirang"
    ],
    [
        "AHMAD ZAINUL LUTHFI, S.Pd.I.",
        "24670110810000186",
        "6203011701930001",
        "199301172025211102",
        "Pulang Pisau",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bakungin"
    ],
    [
        "AHMAD ZIADI",
        "24670130810000407",
        "6203012002850003",
        "198502282025211108",
        "Palangka Raya",
        "MADRASAH ALIYAH BAHASA",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "NURA WARNIE",
        "24670130820000564",
        "6203026505700003",
        "197005252025212018",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AHMADI, S.Pd",
        "24670130810000871",
        "6203062406980001",
        "199806242025211095",
        "Kapuas",
        "SMK TEKNIK KOMPUTER DAN JARINGAN",
        "Guru Ahli Pertama",
        "SD NEGERI 2 TUMBANG MUROI"
    ],
    [
        "AKHMAD",
        "24670130810000229",
        "6203010601860007",
        "198601062025211102",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "AKHMAD RAHMADANI",
        "24670130810000914",
        "6203030203870002",
        "198703022025211151",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "AKHMAD RIZAL",
        "24670130810000649",
        "6203022307870006",
        "198707232025211121",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "AKHMADI ARDAYA",
        "24670130810001045",
        "6203011406830003",
        "198306142025211116",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Inspektorat"
    ],
    [
        "ALAN BUDI KUSUMA",
        "24670130810000644",
        "6203010205910007",
        "199105022025211167",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ALBERT SASIA, S.Sos",
        "24670130810000118",
        "6203021901860003",
        "198601192025211077",
        "Banjarmasin",
        "S-1 ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "ALBERT YITRAN PUNUF",
        "24670130810000222",
        "6106220604830001",
        "198304062025211135",
        "Kapuas Hulu",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "GERY PRATAMA",
        "24670130810000732",
        "6203020809000003",
        "200009182025211053",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Kelurahan Dahirang"
    ],
    [
        "ALEXSANDER KURNIAWAN, S.AP",
        "24670130810000277",
        "6203022911960002",
        "199611292025211106",
        "Kapuas",
        "S-1 ADMINSTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ALFIAN NOOR, S.pd.I",
        "24670110810000161",
        "6203010709010006",
        "200109072025211037",
        "Kapuas",
        "S-1 DIV PENDIDIKAN GURU SD",
        "Guru Ahli Pertama",
        "SD Negeri 3 Pulau Kupang"
    ],
    [
        "ALFIANSYAH",
        "24670130810000736",
        "6203011606840003",
        "198407162025211115",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ALFRET BERTHOLOMEOS LUSI",
        "24670130810000716",
        "6203022508820001",
        "198208252025211105",
        "Rote Ndao",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ALI MUHAMMAD",
        "24670130810000490",
        "6203011804840001",
        "198404182025211140",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ALI YUAN, A.Md.Kep",
        "24670140810000040",
        "6203120710900002",
        "198910072025211177",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "ALIMIN",
        "24670130810000830",
        "6203011008850007",
        "198508102025211173",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ALPIAH",
        "24670130820000116",
        "6203016507910006",
        "199107252025212130",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ALVIYAN",
        "24670130810000125",
        "6203013103980003",
        "199803312025211076",
        "Kapuas",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "AMALIAH, S.Pd",
        "24670110820000471",
        "6203045903970002",
        "199805102025212125",
        "Kapuas",
        "S-1 PENDIDIKAN GURU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Simpang Bunga Tanjung"
    ],
    [
        "AMANINDA, A.Md.Keb",
        "24670140820000166",
        "6203014304960006",
        "199604032025212128",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "AMNAH",
        "24670130820000370",
        "6203015210840012",
        "198410122025212074",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "AMRULAH, SH",
        "24670130810000612",
        "6203010912850007",
        "198512092025211105",
        "Kapuas",
        "HUKUM",
        "Penata Layanan Operasional",
        "Kelurahan Pulau Kupang"
    ],
    [
        "ANA MARIA, S.Pd",
        "24670110820000683",
        "6203097009920002",
        "199209302025212130",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Basuta Raya"
    ],
    [
        "ANANDA ASTRIANA LEONITA, S.Psi",
        "24670220120001721",
        "6203017107960004",
        "199607312025212106",
        "Palangka Raya",
        "S-1 PSIKOLOGI",
        "Penata Layanan Operasional",
        "Subbagian Keuangan dan Aset"
    ],
    [
        "HARTOPO",
        "24670020110001826",
        "6203150508000001",
        "200008052025211056",
        "Kapuas",
        "D-I PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "REINHARD SEPTADI",
        "24670130810000155",
        "6203012109700002",
        "197009212025211033",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "ANANG MA'RUF, S.Pd",
        "24670110810000152",
        "6203070907010004",
        "200107092025211047",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Dadahup Raya"
    ],
    [
        "ANDI",
        "24670130810001027",
        "6203010402920007",
        "199202042025211157",
        "Kapuas",
        "SMA BAHASA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SRI MULATSIH",
        "24670130820000588",
        "6371036005700008",
        "197005202025212021",
        "Blora",
        "SEKOLAH MENENGAH KESEJAHTRAAN KELUARGA",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "SRI MULYAHASANAH",
        "24670130820000457",
        "6471046706700004",
        "197006272025212010",
        "Barito Utara",
        "SMA ILMU ILMU SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "ANDI FRINATA M. SUJAT, A.Md.Kep",
        "24670140810000068",
        "6203121003990004",
        "199903102025211064",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "ANDI PRIMANTO",
        "24670130810000042",
        "6203022911990001",
        "199911292025211072",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "ISMI AULIA",
        "24670130820000398",
        "6203016802000007",
        "200002282025212094",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "ANDIKA FIRMAN ARNOWO",
        "24670130810000851",
        "6203022404870002",
        "198704242025211174",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ANDIKA PRATAMA",
        "24670130810000569",
        "6203050709970001",
        "199709072025211088",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ISTIHARAH",
        "24670320120001351",
        "6204014409000004",
        "200009042025212057",
        "Barito Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ANDRE FAISAL",
        "24670130810000598",
        "6203102604950002",
        "199504262025211094",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ANDREANSYAH, S.Kom.",
        "24670110810000183",
        "6203011309940010",
        "199409132025211113",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Timur"
    ],
    [
        "ANDRI LIANI, A.Md.Kep",
        "24670140810000124",
        "6203062807980004",
        "199807282025211103",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ANDRI OKTORI SAHARI",
        "24670130810000299",
        "6203012410960003",
        "199610242025211104",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "ANDRIANTO",
        "24670130810000641",
        "6203012510970002",
        "199710252025211073",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "ANDRIANTO",
        "24670130810000363",
        "6211031008900001",
        "199008102025211200",
        "Palangka Raya",
        "S-1 TEKNOLOGI PENDIDIKAN",
        "Penata Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "ANGGI",
        "24670130810000449",
        "6203011304870001",
        "198704132025211120",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "ANGGRAINI SAPUTRI",
        "24670130820000172",
        "6203015704880004",
        "198804172025212122",
        "Pulang Pisau",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "ANI ARIANDI",
        "24670130810000595",
        "6203013008890002",
        "199108302025211116",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ANIE, S.E",
        "24670130820000434",
        "6203014507990015",
        "199907052025212111",
        "Kapuas",
        "S-1 MANAJAMEN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ANISA",
        "24670130820000447",
        "6203016512970008",
        "199712252025212111",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ANISA NORINAYAH, S.Pd",
        "24670130820000272",
        "6203015610970004",
        "199710162025212128",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Penata Layanan Operasional",
        "Bidang Pembinaan Ketenagaan"
    ],
    [
        "SUTER, S.Sos.",
        "24670130810000053",
        "6203050704700001",
        "197004072025211034",
        "Kotawaringin Timur",
        "S-1 ADMINITRASI NEGARA",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "ANITA RAHMAN, S.Pd",
        "24670130820000608",
        "6203015308950003",
        "199508132025212137",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Penata Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ANJAR RETNO ASTRINI, A.Md. RMIK",
        "24670120120001600",
        "6203016905940003",
        "199405292025212155",
        "Kapuas",
        "D-III PEREKAM MEDIS DAN INFORMASI KESEHATAN",
        "Perekam Medis Terampil",
        "UPT Puskesmas Selat"
    ],
    [
        "ANJAS LAMUNDE",
        "24670130810001022",
        "6203021803810002",
        "198103182025211074",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ANNISA, S.PD",
        "24670110820000297",
        "6203046402010003",
        "200102242025212037",
        "Tanah Laut",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 TAMBAN JAYA"
    ],
    [
        "ANNISA, S.Pd",
        "24670110820000527",
        "6203016507960004",
        "199607252025212134",
        "Kapuas",
        "S-1 PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 3 Terusan Mulya"
    ],
    [
        "ANNISA AKHIRUL PUTRI, S.Pd",
        "24670130820000019",
        "6203026706880001",
        "198806272025212118",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "ANNISA NORACHMA",
        "24670130820000244",
        "6203014105810010",
        "198105012025212049",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ANSARI, S.Kom",
        "24670130810000694",
        "6203010709930007",
        "199309072025211119",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "TETI HARIYATI",
        "24670130820000012",
        "6203076308700002",
        "197008232025212009",
        "Kapuas",
        "SMKK BUSANA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ANSHARI",
        "24670130810000078",
        "6203011612850004",
        "198512162025211087",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ANTONIUS CHRISTIADI S, S.Pt",
        "24670130810000993",
        "6203012109900003",
        "199009212025211153",
        "Kapuas",
        "S-1 PETERNAKAN",
        "Penata Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "ANTONY, S.pd.I",
        "24670110810000277",
        "6203110606850002",
        "198502122025211139",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Tengah"
    ],
    [
        "ANTRIS IKA PUJI RAHAYU",
        "24670130820000367",
        "3504134112970004",
        "199712012025212115",
        "Tulungagung",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "APIPAH, A.Md.Kep",
        "24670140820000225",
        "6203014408940004",
        "199408032025212136",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Melati"
    ],
    [
        "APRI",
        "24670130810000764",
        "6203022508860003",
        "198608252025211121",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "APRI RANDO SANJAYA, S.Pd",
        "24670110810000239",
        "6271030504900007",
        "199004052025211151",
        "Gunung Mas",
        "S-1/A-IV PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Balai Banjang"
    ],
    [
        "WARTINI",
        "24670130820000659",
        "6210056210700002",
        "197010222025212005",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "APRIANI",
        "24670130820000546",
        "6203015704910002",
        "199104072025212162",
        "Kapuas",
        "SMK PERHOTELAN DAN JASA PARIWISATA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "APRIANTO",
        "24670130810000940",
        "6203053004940002",
        "199404302025211120",
        "Kapuas",
        "SLTA KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "APRIANTO, S.Pd",
        "24670220110003520",
        "6203022404900003",
        "199004242025211187",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "APRILA HAMPASKA, S.Pd",
        "24670110810000197",
        "6203024404980002",
        "199604062025211145",
        "Gunung Mas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Danau Pantau"
    ],
    [
        "APRILIUS SAJUDI, SE",
        "24670130810000485",
        "6203020704930005",
        "199304072025211144",
        "Kapuas",
        "S-1 EKONOMI MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "APRINATA",
        "24670130810000822",
        "6203101604820002",
        "198304162025211100",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "WAWAN",
        "24670130810000338",
        "6203071505700003",
        "197005152025211061",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "APRIONO",
        "24670130810000687",
        "6203021102880002",
        "198702112025211128",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "APRIYANTI, S.E.",
        "24670130820000218",
        "6371045604810008",
        "198104162025212043",
        "Malang",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "APRIYENI, S.Kep.,Ners",
        "24670140820000241",
        "6203014804940003",
        "199404082025212159",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Pulau Telo"
    ],
    [
        "WAWAN KUSWANDA",
        "24670130810000575",
        "6203012004700004",
        "197004202025211050",
        "Kapuas",
        "SMEA PERDAGANGAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ARBAINAH",
        "24670130820000523",
        "6203016011850003",
        "198511202025212101",
        "Barito Kuala",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ARBAINSYAH",
        "24670130810000955",
        "6203012711820004",
        "198211272025211077",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "ARBANI, S.Pd",
        "24301220110100031",
        "6203010308930009",
        "199308032025211115",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ARDI NUGROHO",
        "24670130810000007",
        "6203013009840005",
        "198209212025211088",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "ARDIAN",
        "24670130810000562",
        "6203013006840001",
        "198406302025211105",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ABDUL HADI",
        "24670130810000888",
        "6203062406710003",
        "197106242025211023",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "ARDIANSYAH BUDIARGOY",
        "24670130810000085",
        "6371011606900006",
        "199006162025211176",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Dahirang"
    ],
    [
        "ARI, S.Pd",
        "24670110810000303",
        "6203060904990001",
        "199904092025211068",
        "Kapuas",
        "S-1 ILMU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Banama"
    ],
    [
        "ARI AJI SAPUTRA, A.Md.Kep",
        "24670140810000134",
        "6211060801000003",
        "200001082025211072",
        "Pulang Pisau",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "KHAIRUL AWALUDIN, SP",
        "24670020110000594",
        "6203012406000004",
        "200006242025211056",
        "Kapuas",
        "SMK PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "ANSARI SUBHAN",
        "24670130810000157",
        "6203011110710007",
        "197110112025211036",
        "Banjarmasin",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ARI SANDY, A.md",
        "24670130810000189",
        "6203010308960005",
        "199608032025211105",
        "Kapuas",
        "D-III TEKNIK SIPIL",
        "Pengelola Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ARI SANDY",
        "24670130810000534",
        "6203012809900003",
        "199009282025211106",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "ARIADI, S.pd.I",
        "24670110810000110",
        "6203032909990002",
        "199909292025211068",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Harapan Baru"
    ],
    [
        "ARIADI",
        "24670130810000613",
        "6203021012940001",
        "199312202025211125",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ARIANOR",
        "24670130810000341",
        "6203031105960004",
        "199505112025211126",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Sei Pasah"
    ],
    [
        "ARIANTO",
        "24670720110001010",
        "6211030109940001",
        "199409012025211134",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ARIANTO",
        "24670130810000594",
        "6203022112860001",
        "198612212025211122",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ARIANTONI, S.Pd",
        "24670110810000294",
        "6203092201960001",
        "199601222025211096",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tumbang Muroi"
    ],
    [
        "ARIF FEBRIANA HANDOKO, S. Kom",
        "24670120110000043",
        "6203010107930049",
        "199302112025211110",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ARIF HIDAYAT, S.Kom",
        "24670130810000668",
        "6203012310920004",
        "199210232025211131",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "ARIF RACHMAN, A.md",
        "24670130810000242",
        "6203011605870004",
        "198705162025211136",
        "Kapuas",
        "D-III MANAJEMEN INFORMATIKA",
        "Pengelola Layanan Operasional",
        "Kelurahan Barimba"
    ],
    [
        "KURNIAJI",
        "24670130810000743",
        "6203012802000009",
        "200002282025211062",
        "Kapuas",
        "SMK TEKNIK KENDARAAN RINGAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ARIFA RAHMAWATI, A.Md.A.K.",
        "24670140820000356",
        "6203086808990002",
        "199908292025212076",
        "Kapuas",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ARIFIN",
        "24670130810000340",
        "6203011103940002",
        "199408182025211124",
        "Kapuas",
        "SMK TEKNIK KENDARAAN RINGAN",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "ARIP RAMADANI, S.H",
        "24670130810000642",
        "6203010502960008",
        "199602042025211083",
        "Palangka Raya",
        "S-1 SARJANA HUKUM",
        "Penata Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "DEDAE",
        "24670130810000655",
        "6203101908710002",
        "197108192025211045",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Kecamatan Timpah"
    ],
    [
        "ARISADI",
        "24670130810000050",
        "6203072909890002",
        "198909092025211254",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ARISHA, S.Pd",
        "24670110820000539",
        "6203014304900014",
        "199504032025212157",
        "Banjar",
        "S-1 PENDIDIKAN TEKNOLOGI INFORMASI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Murung"
    ],
    [
        "ARLANA RIDHO SYAHDEWA, S.Pd",
        "24670110810000206",
        "6203091305010004",
        "200105132025211036",
        "Kulon Progo",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sumber Makmur"
    ],
    [
        "ARLIANSYAH",
        "24670130810000292",
        "6203011306820001",
        "198206132025211096",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ARLINA NURHAYANI, S.Pd",
        "24670110820000368",
        "6203016611930008",
        "199311262025212114",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 3 Teluk Palinget"
    ],
    [
        "ARMADAN",
        "24670130810000930",
        "6203050903950002",
        "199503092025211112",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ARMAN, S.Pd",
        "24670110810000113",
        "6203091308920001",
        "199210132025211108",
        "Palangka Raya",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mantangai Tengah"
    ],
    [
        "HARTONI TANJUNG",
        "24670130810000963",
        "6203012608710003",
        "197108262025211027",
        "Tapanuli Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ARMIAH, S.Pd",
        "24670110820000684",
        "6203146402900001",
        "199002242025212121",
        "Kapuas",
        "S-1 PENDIDIKAN SEJARAH",
        "Guru Ahli Pertama",
        "SD NEGERI1 SIDOMULYO"
    ],
    [
        "ARNANTI, S.E",
        "24670130820000086",
        "6203016010820008",
        "198210202025212078",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "UPT Puskesmas Panamas"
    ],
    [
        "ARPANI, S.Pd",
        "24670110810000223",
        "6304031507000001",
        "200007152025211059",
        "Barito Kuala",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Basuta Raya"
    ],
    [
        "ARTAWAN",
        "24670130810001014",
        "6203010611870006",
        "198711062025211134",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ARTINA, S.Pd",
        "24670110820000627",
        "6203065011860001",
        "198611102025212162",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 3 Palingkau Baru"
    ],
    [
        "IWAN KITING",
        "24670130810000870",
        "6203012609710004",
        "197109262025211021",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ASIAH, S.Pd.I",
        "24670110820000279",
        "6203014308890005",
        "198908032025212146",
        "Banjar",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 6 Pulau Kupang"
    ],
    [
        "ASMA WAHYUDI",
        "24670130810000729",
        "6203060101830005",
        "198301012025211274",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "ASMURI",
        "24670130810000558",
        "6203011202930003",
        "199302122025211111",
        "Kapuas",
        "SMA/MA (IPA)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ASRI MARYANI",
        "24670130820000226",
        "6203097110910001",
        "199110312025212108",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ASTRI ARYANTY, S.Pd",
        "24670110820000304",
        "6203015501870004",
        "198701152025212139",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 2 DADAHUP"
    ],
    [
        "ASTRY PEBRI ASIH",
        "24670130820000274",
        "6212015202870003",
        "198702122025212130",
        "Gunung Mas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ASYIFA DAMAYANTI, S.E",
        "24301220120046650",
        "6203034907970001",
        "199705142025212125",
        "Kapuas",
        "S-1 PERBANKAN SYARIAH",
        "Penata Layanan Operasional",
        "UPT Puskesmas Palingkau"
    ],
    [
        "KAWOT",
        "24670130810000539",
        "6203022202710001",
        "197102222025211015",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Sei Pasah"
    ],
    [
        "MASJAKAWATI",
        "24670130820000452",
        "6203017110710001",
        "197110312025212006",
        "Kapuas",
        "SMA ILMU SOSIAL",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RAHMAWATI, S.Pd.I",
        "24670110820000289",
        "6203016212710006",
        "197112222025212013",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 11 Mantangai Satu Atap"
    ],
    [
        "AULIA AGUSTININGSIH",
        "24670130820000287",
        "6203015608990014",
        "199908162025212090",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "UPT Puskesmas Basarang"
    ],
    [
        "AULIA OKTAVIA ANGGRAINI, A.Md.Keb",
        "24670120120000653",
        "6203014710950014",
        "199510072025212144",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Panamas"
    ],
    [
        "AULIA RAHMAH, S.pd.I",
        "24670110820000346",
        "6203014608950009",
        "199508062025212140",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 6 Selat Hilir"
    ],
    [
        "M BAYU PANGESTU",
        "24670130810000015",
        "6203011403000004",
        "200003142025211048",
        "Pulang Pisau",
        "SMK BIDANG BISNIS DAN PEMASARAN",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "AWALIAH",
        "24670130820000297",
        "7371094712870007",
        "198712072025212093",
        "Ujung Pandang",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "AWALIYAH, S.Pd",
        "24670110820000439",
        "6203045012960001",
        "199612102025212147",
        "Kapuas",
        "S-1/A-IV PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Cemara Labat"
    ],
    [
        "AXEL CRYSTIAN DAVID",
        "24670130810000064",
        "6203011711970004",
        "199711112025211136",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "AYU",
        "24670130820000334",
        "6203094401980002",
        "199801042025212107",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "M. ARIF",
        "24670130810001032",
        "6203012704000004",
        "200004272025211054",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AYU ANDIRA, A.Md.A.K.",
        "24670120120001733",
        "6210024611970004",
        "199711062025212106",
        "Kapuas",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "UPT Puskesmas Jangkang"
    ],
    [
        "AYU APRILIANI, S.Kom",
        "24670120120000788",
        "6203014404980002",
        "199804042025212142",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "AYU DITA SUBROTO",
        "24680520120000965",
        "6203017003960003",
        "199603302025212096",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "AYU FITRIA, S.pd.I",
        "24670110820000555",
        "6202084712000001",
        "200012072025212061",
        "Kotawaringin Timur",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD NEGERI 4 DANAU RAWAH"
    ],
    [
        "AYU LESTARI",
        "24670130820000338",
        "6271024106910001",
        "199106012025212186",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "AYU LESTARI, A.Md.Kep",
        "24670140820000214",
        "6203145202970001",
        "199702122025212127",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Jangkang"
    ],
    [
        "AYU SELVIANA",
        "24670130820000233",
        "6203014809910003",
        "199109082025212138",
        "Pontianak",
        "SMK MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "M. JEFPRI",
        "24670130810000954",
        "6203010605000001",
        "200005062025211061",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AYU TRISNAWATI",
        "24670130820000245",
        "6304024201930001",
        "199301022025212170",
        "Barito Kuala",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "UPT Puskesmas Melati"
    ],
    [
        "AYU WULANDARI, S.Pd.I",
        "24670130820000595",
        "6203016408850007",
        "198508242025212079",
        "Kediri",
        "S-1 PAI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Basarang"
    ],
    [
        "AYUNI PUTRI DEWI",
        "24670130820000234",
        "6213094906920001",
        "199206092025212139",
        "Barito Timur",
        "SLTA/SMA SEDERAJAT",
        "Penata Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "AYUPRATAE",
        "24670130810000941",
        "6203016307830002",
        "198307232025211098",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "AZATUL ADAWIYAH, S.Pd",
        "24301220120186292",
        "6203016509960003",
        "199609252025212133",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sidomulyo"
    ],
    [
        "AZHAR AYUNI, S.pd.I",
        "24670110820000590",
        "3501084110990001",
        "199910022025212089",
        "Pacitan",
        "S-1 TADRIS ILMU PENGETAHUAN ALAM",
        "Guru Ahli Pertama",
        "SMP NEGERI 6 BATAGUH SATU ATAP"
    ],
    [
        "AZHAR FANSYURI, S.Kom",
        "24670120110000144",
        "6203012510900011",
        "199010252025211127",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 BASARANG SATU ATAP"
    ],
    [
        "MAULENY ATIKA RAHMAH",
        "24670130820000309",
        "6201066006000001",
        "200006202025212089",
        "Kotawaringin Timur",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "BADARUDIN",
        "24670130810000635",
        "6203010107950316",
        "199609062025211106",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "BAGUS DWI SUTRISNO, S.Pi",
        "24670130810000131",
        "6203012008840006",
        "198408202025211121",
        "Banjarbaru",
        "D-IV BUDIDAYA PERAIRAN",
        "Penata Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "BAGUS HERMAWAN",
        "24670130810000878",
        "6203011308970004",
        "199708132025211084",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "BAHARSYAH",
        "24670130810000813",
        "6203011404930001",
        "199304142025211155",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Laboratorium Kesehatan Daerah Kabupaten Kapuas"
    ],
    [
        "BAHJAH, S.Pd.I",
        "24670110820000358",
        "6203015805840002",
        "198405182025212077",
        "Tabalong",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Sei Pitung"
    ],
    [
        "BAHRANSYAH",
        "24670130810000322",
        "6203050405810002",
        "198105042025211121",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "BAHRIANOOR",
        "24670130810000350",
        "6203010303840008",
        "198403032025211185",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "BAHRIANSYAH, S.Pd",
        "24670110810000208",
        "6203051810000003",
        "200010182025211052",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Saka Mangkahai"
    ],
    [
        "BAHRUDIN",
        "24670130810000391",
        "6203012911830004",
        "198311292025211070",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "BAINAH, S.Pd",
        "24670110820000773",
        "6203095107990003",
        "200006302025212059",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Manusup Hilir"
    ],
    [
        "BAMBANG AGUS SUMARYONO",
        "24670130810000625",
        "6203010708990003",
        "199908072025211074",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "BAMBANG HIDAYAT",
        "24670130810000962",
        "6203011310940004",
        "199410132025211104",
        "Kapuas",
        "SMK TEKNIK KOMPUTER DAN INFORMATIKA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "BAMBANG IRAWAN",
        "24670130810000689",
        "6203051501910008",
        "199101152025211147",
        "Kapuas",
        "D-I AKUNTANSI",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "BAMBANG IRAWAN, S.pd.I",
        "24670110810000281",
        "6203010808930004",
        "199308082025211147",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Baranggau"
    ],
    [
        "RAYANI",
        "24670130820000185",
        "6203016002710005",
        "197102202025212010",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RITA TRIYANIE",
        "24670130820000380",
        "6211024306710001",
        "197106032025212019",
        "Kapuas",
        "SMA BIOLOGI",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "BANRUSLIN, S.Pd",
        "24670110810000292",
        "6203022011860005",
        "198611202025211125",
        "Banjarmasin",
        "S-1 ILMU PENGETAHUAN SOSIAL",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Bataguh Satu Atap"
    ],
    [
        "BASTOMI, S.Pd.I",
        "24670110810000169",
        "6203041611850001",
        "198511162025211088",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Budi Mufakat"
    ],
    [
        "RUSLIN",
        "24670130810000876",
        "6203011703710002",
        "197103172025211026",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "BAYU NARTOKO",
        "24670130810000221",
        "6203011409910004",
        "199109142025211126",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "BELINAE, S.Pd",
        "24670110820000603",
        "6203094502910002",
        "199102052025212142",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Selat Barat"
    ],
    [
        "BELLA KRISTANTI, A.Md.Keb",
        "24670140820000114",
        "6203014402970007",
        "199702042025212129",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "BELLA SEFTIANAE, A.Md.Kep",
        "24670140820000385",
        "6203056609000003",
        "200009262025212060",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "BENNY SATRIO ARRAHMAN, S.Pd",
        "24670110810000297",
        "6203083105000002",
        "200005302025211045",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 4 Selat Hulu"
    ],
    [
        "BERDI SAPUTRA, S.Kom.",
        "24670110810000258",
        "6203100606870004",
        "198706172025211153",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Timpah"
    ],
    [
        "BERIANTO",
        "24670130810000529",
        "6203050501830001",
        "198301052025211113",
        "Kapuas",
        "SEKOLAH TEKNOLOGI MENENGAH",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MIDUN",
        "24670130810000657",
        "6203021912000004",
        "200012192025211043",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "BETI ARIANI",
        "24670130820000033",
        "6203025704850001",
        "198504172025212087",
        "Barito Timur",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "BETTY ARY DEMPAL",
        "24670130820000431",
        "6203075003980005",
        "199803102025212101",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Lama"
    ],
    [
        "BINTI MASLAHAH, S.Pd.I",
        "24670110820000299",
        "1706105609900001",
        "199009162025212140",
        "Mukomuko",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP NEGERI 13 MANTANGAI SATU ATAP"
    ],
    [
        "BOBBY OKTOVIANOTO.P, A.Md.Kep",
        "24670120110000743",
        "6203013010930002",
        "199310302025211128",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pulau Telo"
    ],
    [
        "BOBIE, S.Kom",
        "24670110810000158",
        "6203102407000002",
        "200007272025211062",
        "Kapuas",
        "S-1 ILMU KOMPUTER",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Timpah Satu Atap"
    ],
    [
        "BOBY DARMAWANTO",
        "24670130810000514",
        "6203072212970001",
        "199712222025211088",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "BOBY IRWANSYAH",
        "24670130810000329",
        "6203012701920007",
        "199201272025211119",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "YAN MELDA",
        "24670130810000745",
        "6203012001710004",
        "197101202025211028",
        "Kapuas",
        "SMA A.1/FISIKA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "BOY KASSUMAWIJAYA, S.Kom",
        "24670130810000289",
        "6203011403880003",
        "198803142025211138",
        "Kapuas",
        "S-1 KOMPUTER SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "BOY PRASMANA",
        "24670130810000841",
        "6271030512840003",
        "198412052025211092",
        "Barito Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "BRAYEN SUKMA MINTELO",
        "24670130810000670",
        "6203010406990012",
        "199906042025211067",
        "Kapuas",
        "SMK PERHOTELAN DAN JASA PARIWISATA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "BRILLIANA PUTRI, S.Pd",
        "24670110820000340",
        "6203115212980003",
        "199812112025212090",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Pasak Talawang"
    ],
    [
        "MUHAMAD RAFI'I, S.Kom.",
        "24670120110000301",
        "6203022010000002",
        "200010202025211046",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "BUDI, S.E",
        "24670130810000711",
        "6203011304960006",
        "199605132025211122",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "BUDI YANTO",
        "24670130810000484",
        "6203030312840002",
        "198412032025211091",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "BUDIANSYAH",
        "24670130810000678",
        "6203012512860006",
        "198612252025211153",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "BUDIANTO, S. Pd. I",
        "24670110810000099",
        "6304132809870001",
        "198709282025211128",
        "Barito Kuala",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Guru Ahli Pertama",
        "SD NEGERI 1 TAMBAN MAKMUR"
    ],
    [
        "BUDIMAN",
        "24670130810000794",
        "6203010103920007",
        "199203012025211131",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "BUDIMAN",
        "24670130810000406",
        "6203012108810005",
        "198108212025211082",
        "Kapuas",
        "SMK BUDIDAYA TERNAK",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "YULIANTO.",
        "24670130810000330",
        "6203020807710002",
        "197107082025211051",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "CARLES",
        "24670130810000551",
        "6210060104840004",
        "198404142025211131",
        "Gunung Mas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Kecamatan Timpah"
    ],
    [
        "CAROLINA",
        "24670130820000263",
        "6203016303870004",
        "198703232025212143",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "ZULUDIN.HD",
        "24670130810000911",
        "6203011010710011",
        "197110102025211064",
        "Hulu Sungai Utara",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "CHAIDIR FADLI, A.Md",
        "24670130810000127",
        "6203031204910002",
        "199304122025211140",
        "Kapuas",
        "D-III MANAJEMEN INFORMATIKA",
        "Pengelola Layanan Operasional",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "CHAIRULLAH",
        "24670130810000956",
        "6203012106990004",
        "199906212025211079",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "CHANDRA LIELA",
        "24670130820000128",
        "6203014107890346",
        "198907012025212156",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "CHARLOS APRIADY, S.T",
        "24670130810000274",
        "6210051104890002",
        "198904112025211162",
        "Gunung Mas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MUHAMMAD IRVAN NORRAHMAN, S.Kom",
        "24670130810000317",
        "6203012105000002",
        "200005212025211059",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "CHOLID FADLULLAH",
        "24670130810000351",
        "6203082810850003",
        "198510282025211135",
        "Kapuas",
        "D-I ADMINISTRASI KANTOR",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "CHRISMAYA ASTANTI",
        "24670130820000229",
        "6203015303870008",
        "198703132025212133",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "CHRISTIAN ADAM, S.E.",
        "24670130810000429",
        "6203011504960007",
        "199604152025211112",
        "Palangka Raya",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "CHRISTIAN HARDIENATHA",
        "24670130810000681",
        "6203072309890001",
        "198909232025211138",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "CHRISTIANA, S.Pd",
        "24670110820000388",
        "6203114912860001",
        "198609092025212141",
        "Kapuas",
        "S-1 PENDIDIKAN GURU OLAHRAGA DAN KESEHATAN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Gawing"
    ],
    [
        "CHRISTINA, S.Kom",
        "24670130820000001",
        "6203015804890002",
        "198904162025212133",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "CHYNTIA CAROLINA, S.Pd",
        "24670020120000100",
        "6203014909940002",
        "199409092025212170",
        "Kapuas",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "CICI, S.Pd",
        "24670110820000752",
        "6203014301980004",
        "199801032025212132",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palangkau Lama"
    ],
    [
        "CICI KUMALA SARI, S. H",
        "24670130820000169",
        "6203014504850007",
        "198504052025212119",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "CICILIA",
        "24670130820000427",
        "6203025505830002",
        "198305152025212088",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "CIPTO, S.Pd",
        "24670110810000142",
        "6271031404930003",
        "199304142025211156",
        "Kapuas",
        "S-1 PENDIDIKAN PANCASILA DAN KEWARGANEGARAAN",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Hulu"
    ],
    [
        "ABAU",
        "24670130810000378",
        "6203021907720004",
        "197207192025211030",
        "Kapuas",
        "SEKOLAH TEKNOLOGI MENENGAH",
        "Operator Layanan Operasional",
        "Kecamatan Mandau Talawang"
    ],
    [
        "CITRA DEWI, SH",
        "24670130820000120",
        "6203015502890010",
        "198902152025212136",
        "Kapuas",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Kecamatan Bataguh"
    ],
    [
        "CITRA DEWI JAYANTI",
        "24670130820000112",
        "6203076712870001",
        "198712272025212137",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "DADANG, S.Pd",
        "24670110820000396",
        "6210026702840001",
        "198402272025212061",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD NEGERI 1 DANDANG"
    ],
    [
        "DADANG HAWARI",
        "24670130810000685",
        "6203010407980005",
        "199807042025211082",
        "Kapuas",
        "SMK ELEKTRONIKA AUDIO VIDEO",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DAENI YURI NINGTYAS",
        "24670130820000176",
        "6271036208930006",
        "199308222025212136",
        "Demak",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Gudang Farmasi Kesehatan"
    ],
    [
        "DAHLIA, S.Pd",
        "24670110820000492",
        "6203045701920002",
        "199308272025212167",
        "Kapuas",
        "S-1 PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Simpang Bunga Tanjung"
    ],
    [
        "DAHLIA",
        "24670130820000498",
        "6203014311820010",
        "198211032025212054",
        "Kapuas",
        "SMK SEKRETARIS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "DAHLINA, S.Pd.I",
        "24670110820000286",
        "6203015910930003",
        "199310192025212102",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Pasah"
    ],
    [
        "DAMAYANTI, S.Pd.I",
        "24670110820000324",
        "6203084201870001",
        "198701022025212118",
        "Kapuas",
        "S-1 PAI",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BATU NINDAN"
    ],
    [
        "DANIEL KRISTIADY",
        "24670130810000169",
        "6203050512970004",
        "199712052025211105",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "DARMAN",
        "24670130810000749",
        "6203010201820010",
        "198201022025211104",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI IPS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "DARMAWAN, A.Md.Kep",
        "24670140810000133",
        "6203113008980002",
        "199808302025211074",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "DARMAYUDA",
        "24670130810000550",
        "6203012505920003",
        "199205242025211122",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ABDILLAH",
        "24670130810000751",
        "6203011007720007",
        "197207102025211068",
        "Kapuas",
        "SEKOLAH MENENGAH EKONOMI ATAS",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "DAVID, S.T.",
        "24670130810000309",
        "6203021712840004",
        "198412172025211081",
        "Kapuas",
        "D-IV TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "DAVID AKBAR NOGROHO, S.pd.I",
        "24670110810000288",
        "6203032510000003",
        "200010252025211062",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Anjir Serapat Tengah"
    ],
    [
        "MUHAMMAD JAINI",
        "24670130810000531",
        "6203071609000001",
        "200009162025211056",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DAVID PRADANA, S.T",
        "24670130810000161",
        "6203020612920002",
        "199212062025211109",
        "Palangka Raya",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "MUNALISA",
        "24670130820000456",
        "6203044510010004",
        "200001222025212060",
        "Kapuas",
        "MA IPS",
        "Operator Layanan Operasional",
        "Kecamatan Tamban Catur"
    ],
    [
        "DAWAISYA PUTRA, S.H",
        "24670130810000394",
        "6203033005970004",
        "199705302025211072",
        "Ujung Pandang",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "AGMON ANUGERAH MANGORE, S.Pd",
        "24670110810000210",
        "6203073008720004",
        "197208302025211024",
        "Kepulauan Talaud",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 5 Selat Hilir"
    ],
    [
        "NORAIDA, S.M.",
        "24670130820000093",
        "6203015903000005",
        "200003192025212068",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Kelurahan Selat Hulu"
    ],
    [
        "DEBA, S.Pd",
        "24670110810000117",
        "6203112406900004",
        "199006242025211119",
        "Kapuas",
        "S-1 PENDIDIKAN TEKNIK BANGUNAN",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Tengah"
    ],
    [
        "DEBBY PERDANA PUTRA, S.Th",
        "24670110810000063",
        "6203111407950001",
        "199507142025211116",
        "Kapuas",
        "S-1 TEOLOGI AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Tengah"
    ],
    [
        "DEBY, S.Kom.",
        "24670130810000081",
        "6203011409860003",
        "198609142025211090",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "AGUS TIMBANG, SE",
        "24670130810000175",
        "6203011008720005",
        "197208102025211079",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "DEDE HATLIN SANDITO JAYA, S.Kep., Ners",
        "24670140810000112",
        "6203051907950001",
        "199507192025211126",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "DEDEN ANUGRAH SAPUTRA, A.Md.Kep",
        "24670140810000115",
        "6203090704970005",
        "199704072025211097",
        "Banjarmasin",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mantangai"
    ],
    [
        "DEDEN SAPUTRA",
        "24670130810000557",
        "6203010209840007",
        "198409022025211099",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DEDET PRIATNA, S.Pd",
        "24670110810000233",
        "6203052111980003",
        "199811212025211064",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Maju Bersama"
    ],
    [
        "DEDETUHAS PRATAMA RAMBANG, S.Kom",
        "24670130810000196",
        "6203012609940006",
        "199409262025211098",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "DEDI KRISTANTO, ST",
        "24670130810000307",
        "6210030306890003",
        "198906032025211142",
        "Gunung Mas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ANITA",
        "24670130820000587",
        "6203016908720002",
        "197208292025212009",
        "Kapuas",
        "SLTA PENDIDIKAN GURU AGAMA KRISTEN PROTESTAN",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "DEDI NURAHMAN",
        "24670130810000897",
        "6203011010910014",
        "199110102025211254",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "DEDI SAPUTRA, A.Md.Kep",
        "24670120110000776",
        "6203012201910001",
        "199101222025211073",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "DEDI SETIAWAN",
        "24670130810000205",
        "6203050701940004",
        "199410072025211119",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "DEDI WIJAYA, S.Pd.I",
        "24670130810000210",
        "6203082607840002",
        "198407262025211084",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Penata Layanan Operasional",
        "Subbagian Keuangan dan Aset"
    ],
    [
        "BAMBANG KURNIANTO",
        "24670130810000075",
        "6203091309720006",
        "197209132025211035",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "DEDY SUTRISNO B.",
        "24670130810000388",
        "6203101809960002",
        "199609182025211083",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Kelurahan Sei Pasah"
    ],
    [
        "DEGRID OKTRI NURLIANSI, S.E",
        "24670620120000423",
        "6203016310960009",
        "199610232025212118",
        "Kapuas",
        "S-1 MANAJAMEN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "DEDY ERAWAN, S.E",
        "24670130810000021",
        "6203013105720003",
        "197205312025211020",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "RAMINTO",
        "24670130810000770",
        "6203020911000002",
        "200011092025211058",
        "Kapuas",
        "SMK TEKNIK OTOMOTIF",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Hilir"
    ],
    [
        "DEMI",
        "24670130820000531",
        "6203114802930001",
        "199302082025212149",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "EFENDY",
        "24670130810000803",
        "6203013006720001",
        "197206302025211037",
        "Kapuas",
        "SMA A.2/BIOLOGI",
        "Operator Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "DENI ANGGARA",
        "24670130810000038",
        "6203021503980002",
        "199803152025211092",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "DENNY MUSTHAFA, S.Kom",
        "24670130810000384",
        "6203013004930003",
        "199304302025211135",
        "Barito Kuala",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DENNY TRIARDINATA",
        "24670130810000450",
        "6203022912830005",
        "198312292025211074",
        "Kapuas",
        "SLTA",
        "Operator Layanan Operasional",
        "Kelurahan Dahirang"
    ],
    [
        "DEPRIANTO AGUSTINUS, S.Kom",
        "24670130810000149",
        "6203052308950003",
        "199508232025211098",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "DESI",
        "24670130820000133",
        "6203014612960005",
        "199612062025212115",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "DESI ANDRIANI",
        "24670130820000405",
        "6203056912960001",
        "199612292025212109",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "DESI OKFENIA",
        "24670130820000273",
        "6211076610980001",
        "199710262025212108",
        "Pulang Pisau",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "DESSA FITRA ALIF YAGITA, A.md, Keb",
        "24670140820000350",
        "6207015012940001",
        "199412102025212173",
        "Seruyan",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Melati"
    ],
    [
        "RENDY JUANDHA",
        "24670130810000376",
        "6211050110000001",
        "200010012025211047",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "DESY ANDRIANI",
        "24670130820000661",
        "6203125409900002",
        "199009142025212136",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "DESY ENDRI YANI, S.Pd",
        "24670110820000576",
        "6372026412970001",
        "199712242025212124",
        "Banjarbaru",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 TERUSAN MULYA"
    ],
    [
        "DESY MARISA",
        "24670120120001158",
        "6203076402930001",
        "199302242025212106",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Perumahan, Kawasan Permukiman, dan Pertanahan"
    ],
    [
        "DESY PURNAMA",
        "24670620120000185",
        "6203017112930004",
        "199312312025212252",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "DETI ASTUTI",
        "24670130820000466",
        "6203105212950004",
        "199512122025212185",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "DEVI JANDRIATI",
        "24670140820000026",
        "6203117001950003",
        "199501302025212116",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "DEVI PUTRI ILANI",
        "24670130820000463",
        "6203044708970004",
        "199708072025212121",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Kecamatan Tamban Catur"
    ],
    [
        "DEVI SARTIKA, S.Sos",
        "24670130820000065",
        "6203014704890009",
        "199009212025212131",
        "Kapuas",
        "S-1 ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "DEVI WANDASARY, Amd.Keb",
        "24670140820000362",
        "6271036607920008",
        "199207262025212160",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "DEVINDA AYU BUDIHARTI, S.Pd",
        "24301220120095036",
        "6203015607950002",
        "199507162025212143",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "DEVY MAULINA PARDEDE, S.E",
        "24670130820000333",
        "6203016209920005",
        "199209222025212120",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "DEWI, S.Pd",
        "24670110820000624",
        "6307104406880001",
        "198806042025212121",
        "Hulu Sungai Tengah",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sidorejo"
    ],
    [
        "GUNIS SAWON WIJAYA",
        "24670130810000995",
        "6203010804720007",
        "197204082025211057",
        "Cilacap",
        "SD/SEDERAJAT",
        "Pengelola Umum Operasional",
        "Dinas Kesehatan"
    ],
    [
        "DEWI AGUS TINA, S.Pd.I",
        "24670130820000589",
        "6203016008880011",
        "198808202025212135",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "DEWI AMELIA HERLINAWATI G, S.Pd.I",
        "24670110820000295",
        "6203016712900002",
        "199012272025212139",
        "Barito Selatan",
        "S-1 PAI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pulau Mambulau"
    ],
    [
        "DEWI ASTUTI, S.Pd.I",
        "24670110820000309",
        "6203014804880005",
        "198804082025212141",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 7 PULAU KUPANG"
    ],
    [
        "DEWI HANDAYANI, A.md.,AK",
        "24670220120003353",
        "6203017008900008",
        "199008302025212129",
        "Kapuas",
        "S-1 SEMUA JURUSAN",
        "Pengelola Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DEWI HANDAYANI",
        "24670130820000467",
        "6203015802900001",
        "199002182025212102",
        "Kapuas",
        "MA IPA",
        "Operator Layanan Operasional",
        "Kelurahan Selat Utara"
    ],
    [
        "DEWI NUR UTAMI, S.pd.I",
        "24670130820000663",
        "6203096802960001",
        "199602282025212114",
        "Brebes",
        "S-1 PENDIDIKAN GURU PAUD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sari Makmur"
    ],
    [
        "DEWI RIANI",
        "24670130820000418",
        "6203014804850001",
        "198504082025212079",
        "Barito Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "DEWI RISDAYANTI, S.Pd",
        "24670110820000533",
        "6301075101960005",
        "199601112025212123",
        "Tanah Laut",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Terusan Makmur"
    ],
    [
        "DEWI SHINTA, S.Kep.,Ners",
        "24670140820000231",
        "6203074109920002",
        "199209012025212157",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Palingkau"
    ],
    [
        "DEWI SRI, S.PD",
        "24670110820000416",
        "6402065906860002",
        "198606182025212109",
        "Kapuas",
        "AKADEMI BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 KAPUAS HILIR SATU ATAP"
    ],
    [
        "DEWI YANTY, A.Md.Kep",
        "24670140820000078",
        "6203054511930003",
        "199311052025212152",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "DHARMA SAPUTRA",
        "24670130810000763",
        "6203010303860005",
        "198603032025211156",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "KURDI, S.Pd.I",
        "24670110810000030",
        "6203040704720003",
        "197204072025211054",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palampai"
    ],
    [
        "DIAN PUTRI ANGGRAINI, S.Pd",
        "24670110820000664",
        "3404155104990002",
        "199904112025212083",
        "Sleman",
        "S-1 PENDIDIKAN GURU SD",
        "Guru Ahli Pertama",
        "SD Negeri 3 Selat Hilir"
    ],
    [
        "DIANA",
        "24670130820000399",
        "6203015301840001",
        "198401132025212046",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "DIANA FRANITA, S. Pd. I",
        "24670130820000607",
        "6203017011860005",
        "198611302025212076",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Basungkai"
    ],
    [
        "DIANA IVANA",
        "24670130820000013",
        "6203026310810001",
        "198110232025212049",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Selat Hilir"
    ],
    [
        "DICKY PRAMANA",
        "24670130810000601",
        "6211051509960003",
        "199609152025211096",
        "Pulang Pisau",
        "MA IPA",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "DIDI, A.Md.Kep",
        "24670140810000121",
        "6203101803980001",
        "199803182025211065",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Timpah"
    ],
    [
        "DIEN HARIYONO",
        "24670130810000400",
        "6203023005870001",
        "198705302025211113",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "DIKA SAKTI ARNANDO",
        "24670130810000036",
        "6203011709990008",
        "199909172025211065",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "DIKI WIDIANTO, S.Pd",
        "24670110810000091",
        "6203081705020004",
        "200205172025211021",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 3 Tambun Raya"
    ],
    [
        "DIMAN",
        "24670130810000331",
        "6203090512920001",
        "199212052025211107",
        "Kapuas",
        "SMK AGRIBISNIS DAN AGROINDUSTRI",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "DIMANSYAH",
        "24670130810000356",
        "6203010101820026",
        "198407152025211158",
        "Kapuas",
        "SMK PERTANIAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "DIMAS AGUNG NUGROHO",
        "24670130810000634",
        "6203011605970004",
        "199705162025211100",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "RENI",
        "24670130820000104",
        "6203016407000005",
        "200007242025212060",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "DINDA FEBRIANA D.",
        "24670130820000025",
        "6203015702990007",
        "199902172025212072",
        "Kapuas",
        "SMK TEKNIK KOMPUTER DAN JARINGAN",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "DINO TRISNO, S.Pd.I",
        "24670110810000070",
        "6203092801930004",
        "199301282025211091",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 2 DANAU RAWAH"
    ],
    [
        "DIYAH AFSARI, S.Pd",
        "24670110820000371",
        "6203096809880001",
        "198809282025212101",
        "Kapuas",
        "S-1 PENDIDIDKAN OLAH RAGA DAN KESEHATAN",
        "Guru Ahli Pertama",
        "SD NEGERI 1 SEI GAWING"
    ],
    [
        "DODY",
        "24670130810000521",
        "6203010308850008",
        "198508032025211112",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DODY SETIAWAN",
        "24670130810000364",
        "6203012010950007",
        "199510202025211125",
        "Kapuas",
        "PAKET C (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "DOLI",
        "24670130810000512",
        "6203050404860002",
        "198604042025211168",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "DONA KRISTOLELONO, S.Kom",
        "24670130810000060",
        "6203040106840004",
        "198406012025211141",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "UPT Puskesmas Pujon"
    ],
    [
        "DONA RITA, S.Pd",
        "24670130820000507",
        "6203124710980001",
        "199810072025212083",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Mandau Talawang"
    ],
    [
        "DONDOAN RAYNEI",
        "24670130810000430",
        "6271030601960006",
        "199601062025211119",
        "Palangka Raya",
        "SMK TEKNIK OTOMOTIF",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RINALDY HENDRAWAN",
        "24670130810000674",
        "6203011807000005",
        "200007182025211038",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RIYAN DODI PEBRIYANTO",
        "24670130810000627",
        "6203081602000003",
        "200002162025211043",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "DONNY SAPUTRA TINGGAM, S.Kom",
        "24670130810000248",
        "6203011904940004",
        "199404192025211112",
        "Banjarmasin",
        "S-1 TEKNOLOGI INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "DWI BUDIARTO",
        "24670130810000790",
        "6203071610870001",
        "198710162025211118",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "DWI CANDRA MUKTI",
        "24670130810000426",
        "6271031105930004",
        "199305112025211141",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DWI HARTANTY, S.Kom",
        "24670130820000032",
        "6203016801860003",
        "198601282025212076",
        "Kapuas",
        "S-1/A-IV SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "DWI NORMEI CAHYATI",
        "24670130820000555",
        "6203074305970002",
        "199705032025212145",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "DWI PUJIYANTO, S.E",
        "24670130810000098",
        "6203011903810001",
        "198103192025211075",
        "Sragen",
        "S-1 EKONOMI STUDI PEMBANGUNAN",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "DWI PURNAMASARI, A.Md",
        "24670130820000319",
        "6203126006890001",
        "198906202025212146",
        "Kapuas",
        "D-III MANAJEMEN INFORMATIKA",
        "Pengelola Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "DWI RAHMAN NOR HIDAYAT, S.Pd",
        "24670110810000135",
        "6203016511010006",
        "200111252025211034",
        "Palangka Raya",
        "S-1 PENDIDIKAN SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bamban Raya"
    ],
    [
        "DWI SALASIAH, S.Pd",
        "24670110820000463",
        "6203044301950003",
        "199501032025212120",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 2 Tamban Luar"
    ],
    [
        "DWI YANTO",
        "24670130810000744",
        "6203082108830002",
        "198308212025211087",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "DWIAN ARIANTO, S.Kep,Ns",
        "24670140810000083",
        "6271032104930006",
        "199304212025211132",
        "Palangka Raya",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Timpah"
    ],
    [
        "DWIKA NOR RINA",
        "24670130820000241",
        "6203014708940016",
        "199405072025212165",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "DWIKI NUGRAHA KUSUMA",
        "24670130810000967",
        "6203014903960002",
        "199603092025211106",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "DYAH AYU PRAMUJA, A.Md.Keb",
        "24670120120000926",
        "6203096608980002",
        "199808262025212086",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Panamas"
    ],
    [
        "EDDY, S.Pd",
        "24670110810000234",
        "6203091505910005",
        "199105152025211168",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Manusup"
    ],
    [
        "MASRANI",
        "24670130810000542",
        "6203011004720013",
        "197204102025211059",
        "Hulu Sungai Selatan",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "EDO RICO RONALDO",
        "24670720110000905",
        "6271032311960005",
        "199611232025211110",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "EDOARDO, S.AP.",
        "24670130810000115",
        "6203010410950006",
        "199510042025211110",
        "Kapuas",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "EDWIN, S.Sos",
        "24670130810000025",
        "6203021202890002",
        "198902122025211158",
        "Kapuas",
        "S-1 SEMUA JURUSAN",
        "Penata Layanan Operasional",
        "Kecamatan Kapuas Hilir"
    ],
    [
        "MUHAMMAD MALIK",
        "24670130810000619",
        "6203010509780010",
        "197209052025211052",
        "Kapuas",
        "SMA BAHASA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "EDY RACHMAN, S.Pd.I",
        "24670130810000178",
        "6203011811870003",
        "198711182025211098",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Tamban Catur"
    ],
    [
        "EFENDI",
        "24301220110019112",
        "6203013101930001",
        "199301312025211088",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "NUAH TASA",
        "24670130810000783",
        "6203021702720001",
        "197202172025211049",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ORBITO NYANGKAL, S.Pi",
        "24670130810001015",
        "6203140712720002",
        "197212072025211051",
        "Kapuas",
        "S-1 PERIKANAN",
        "Guru Ahli Pertama",
        "SMP Negeri 2 PASAK TALAWANG SATU ATAP"
    ],
    [
        "EFFENDY",
        "24670130810000525",
        "6203011910860008",
        "198610192025211090",
        "Kapuas",
        "SMK PERDAGANGAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "EFRI SUSANTI",
        "24670130820000632",
        "6203084507850004",
        "198604012025212104",
        "Palangka Raya",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "EFRIDA MARCELLA LUMBAN GAOL, S.AB.",
        "24670130820000326",
        "6371016004950013",
        "199504212025212184",
        "Banjarmasin",
        "S-1 ILMU ADMINISTRASI BISNIS",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RIZALDY RAMADAN",
        "24670130810000816",
        "6203012512000001",
        "200012252025211058",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "EKA DEWI SINTA",
        "24670130820000397",
        "6203014110830009",
        "198310012025212079",
        "Gunung Mas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "EKA KURNIA PRASETIAWATI, SH",
        "24670130820000121",
        "6203015306840002",
        "198406132025212073",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "EKA NOVITASARI, S.M",
        "24670130820000141",
        "6203015611930009",
        "199311162025212130",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "EKA PURNAMA SARI",
        "24670130820000448",
        "6372056310880001",
        "198810232025212119",
        "Banjarbaru",
        "SMA ILMU ILMU SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "EKA PUSPITA DEWI, S. Kep",
        "24670130820000278",
        "6203056602950003",
        "199502262025212115",
        "Kapuas",
        "S-1 KEPERAWATAN",
        "Administrator Kesehatan Ahli Pertama",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "EKA RIANA PUTRI, S.Pd",
        "24670110820000290",
        "6203096111010003",
        "200111212025212048",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Dadahup Raya"
    ],
    [
        "EKKY SRIWIYATI",
        "24670130820000061",
        "6203014402870003",
        "198702042025212092",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "EKO BAYU PRABOWO, S.Pd",
        "24670110810000287",
        "6203013003940007",
        "199403302025211099",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 TAMBAN CATUR"
    ],
    [
        "EKO BUDI SETIAWAN",
        "24670130810000543",
        "6203083012850001",
        "198512302025211136",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Basarang"
    ],
    [
        "RADIAH",
        "24670130820000538",
        "6203016308720003",
        "197208232025212022",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "EKO PRASETIO, S.Pd",
        "24670110810000128",
        "6203110312880001",
        "198812032025211114",
        "Kapuas",
        "PENDIDIKAN OLAHRAGA",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Tengah Satu Atap"
    ],
    [
        "ELA VIKA LINAWATI, S.Pd",
        "24670110820000430",
        "6203074604970004",
        "199704062025212128",
        "Tanah Bumbu",
        "S-1 PENDIDIKAN FISIKA",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 DADAHUP"
    ],
    [
        "REPELITA LIUS",
        "24670130810000443",
        "6203012310720001",
        "197210232025211027",
        "Barito Utara",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "ELI DIANA SARI, S.H",
        "24670110820000734",
        "6203086403820003",
        "198203242025212047",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Guru Ahli Pertama",
        "SMP NEGERI 14 MANTANGAI SATU ATAP"
    ],
    [
        "ELI NUR INDAHSARI, S.Pd",
        "24670110820000764",
        "6203095903970002",
        "199703192025212119",
        "Pulang Pisau",
        "S-1 PENDIDIKAN KIMIA",
        "Guru Ahli Pertama",
        "SMP Negeri 6 Mantangai Satu Atap"
    ],
    [
        "ELIANA ELMAWATI",
        "24670130820000355",
        "6203014207900006",
        "199007022025212137",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ELISA, S.Th",
        "24670110820000538",
        "6203114701850002",
        "198501072025212081",
        "Kapuas",
        "S-1 KEPENDETAAN KRISTEN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Karukus"
    ],
    [
        "ELISAE",
        "24670130820000530",
        "6203095405820004",
        "198205142025212066",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ELISE SETIAWATI",
        "24670130820000542",
        "6203024405920002",
        "199205042025212182",
        "Kotabaru",
        "SMK TATA BUSANA",
        "Operator Layanan Operasional",
        "Kelurahan Barimba"
    ],
    [
        "ELIZA",
        "24670130820000049",
        "6203026407930001",
        "199307242025212131",
        "Kapuas",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "ELLY SUBAKTI, A.Md",
        "24670130810000399",
        "6203012806830004",
        "198306282025211073",
        "Kapuas",
        "D-III TEKNIK ELEKTRO",
        "Pengelola Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "ELMI SONDRI, S.Pd",
        "24670110810000130",
        "1409011407840001",
        "198407142025211117",
        "Kuantan Singingi",
        "S-1 PENJASKESREK",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lamunti Baru"
    ],
    [
        "RIDUANSYAH",
        "24670130810000348",
        "6203052411710001",
        "197211242025211021",
        "Kapuas",
        "SMA ILMU ILMU BIOLOGI",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ELSA DAMAYANTI, S.M.",
        "24670130820000323",
        "6203015107990012",
        "199907112025212079",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "ELSA DWI AGUSTIN",
        "24670130820000351",
        "6211054508990004",
        "199908052025212098",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ELSA FREYANTI, S.H",
        "24670130820000050",
        "6203014611930008",
        "199311062025212122",
        "Banjarmasin",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Inspektorat"
    ],
    [
        "ELSA NORFITRIANA, S.Kep.,Ns",
        "24670140820000259",
        "6203095210980002",
        "199810122025212096",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Mantangai"
    ],
    [
        "ELSA PUTRI, S.Pd",
        "24670110820000454",
        "6203096606000004",
        "200006182025212057",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Manusup"
    ],
    [
        "ELVY OLIANTY, SH",
        "24670130820000017",
        "6203014111880003",
        "198811012025212087",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "ELWAN SAPUTRA",
        "24670130810000517",
        "6203031003850002",
        "198503102025211148",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Kelurahan Selat Dalam"
    ],
    [
        "ELY ANGGRAINI, S.Pd",
        "24670110820000292",
        "6203055012940003",
        "199412102025212181",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Dusun"
    ],
    [
        "ELY RACHMAH, S.M",
        "24670130820000213",
        "6203016005900003",
        "199005202025212181",
        "Hulu Sungai Tengah",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "ELY SINTA, S.Pd",
        "24670130820000206",
        "6203064504920003",
        "199204052025212202",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Penata Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "ELYANI",
        "24670130820000139",
        "6203017009880002",
        "198809302025212100",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "ENDAH NOVYANINGTYAS, Amd.Keb",
        "24670140820000149",
        "6203015811900005",
        "199011182025212120",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ENDAH PURNAMA SARI, S.Kep.,Ns",
        "24670140820000250",
        "6203014902900008",
        "199002092025212150",
        "Kapuas",
        "S-1 KEPERAWATAN",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Sei Tatas"
    ],
    [
        "ENDAH SUSILOWATI, S.Pd.I",
        "24670130820000616",
        "6203025704930001",
        "199304172025212130",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ROBEN",
        "24670130810000932",
        "6203012712720006",
        "197212272025211025",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ENDANG YULIYANTI",
        "24670130820000153",
        "6203017007840005",
        "198407302025212049",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Panamas"
    ],
    [
        "ENJEL, A.Md.Kep",
        "24670140810000057",
        "6203092511950002",
        "199511252025211139",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mantangai"
    ],
    [
        "ENMEL JENY FRANSISCA DELLYANTY",
        "24670130820000118",
        "6203015601960004",
        "199601162025212122",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ENNY SULISTYA WATI, A.Md.Keb.",
        "24670140820000334",
        "6309104908940002",
        "199408092025212133",
        "Tabalong",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Talekung Punai"
    ],
    [
        "ENRICKO TRIAWAN",
        "24670130810000134",
        "6203012906940005",
        "199406292025211103",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "EPDALI, S.I.Kom",
        "24670110810000261",
        "6203012906980003",
        "199906082025211073",
        "Kapuas",
        "S-1 ILMU KOMUNIKASI",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Bataguh Satu Atap"
    ],
    [
        "EPIN, S.H.",
        "24670130810000162",
        "6203111606910003",
        "199106162025211175",
        "Kapuas",
        "S-1/A-IV ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "ERA ANGGRAINI",
        "24670130820000132",
        "6203015412940004",
        "199412142025212166",
        "Barito Timur",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SAYUTI",
        "24670130810001016",
        "6203010709720002",
        "197209072025211058",
        "Kapuas",
        "SEKOLAH DASAR",
        "Pengelola Umum Operasional",
        "Dinas Perhubungan"
    ],
    [
        "ERICA NOPRI DAMAYANTI, A.Md.Kep",
        "24670140820000224",
        "6203014406980001",
        "199806042025212114",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Melati"
    ],
    [
        "ERICK ARWANDI, S.Pd",
        "24670130810000410",
        "6203011503870008",
        "198703152025211167",
        "Kapuas",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Penata Layanan Operasional",
        "Bidang Pembinaan PAUD dan DIKMAS"
    ],
    [
        "ERITA",
        "24670130820000582",
        "6203094109910004",
        "198609012025212099",
        "Kapuas",
        "S-1 PG PAUD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sekata Makmur"
    ],
    [
        "ERLINA WAHYU NINGSIH",
        "24670130820000271",
        "6203014611860005",
        "198611062025212093",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ERNAWATI",
        "24670130820000634",
        "6203096605810001",
        "198105262025212039",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ERNI OKTANIA, Amd.Keb",
        "24670140820000367",
        "6210024310930001",
        "199310032025212150",
        "Palangka Raya",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Timpah"
    ],
    [
        "ERNI SETIA NINGSIH",
        "24670130820000034",
        "6203015501830003",
        "198301152025212075",
        "Dharmasraya",
        "SMU IPA",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "ERPINA MARIA, S.Pd.K",
        "24670110820000331",
        "6203114512860001",
        "198612052025212122",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tapen"
    ],
    [
        "ERSA SAHICA, A.Md.Keb",
        "24670140820000070",
        "6203116903990001",
        "199903292025212090",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "SURYADI",
        "24670130810000769",
        "6203010708720011",
        "197208072025211060",
        "Kapuas",
        "SMEA PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ERVINA WIYANA",
        "24670130820000219",
        "6271035709830002",
        "198309172025212064",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "WENDY",
        "24670130810000142",
        "6203022702720001",
        "197202272025211024",
        "Kapuas",
        "D-I ADMINISTRASI KANTOR",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ERWIN RIZKY PRAKOSO",
        "24670130810000631",
        "3515072404860001",
        "198604242025211155",
        "Banjarmasin",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "ERYANTO, S.Kom",
        "24670130810000200",
        "6203012603970005",
        "199703262025211108",
        "Kapuas",
        "S-1 SISTEM INFROMASI",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ESTER LINA BR REGAR, S.Pd",
        "24670110820000515",
        "1206175711000002",
        "200011172025212040",
        "Karo",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BUDI MUFAKAT"
    ],
    [
        "ETER LAMBUT",
        "24670130810000503",
        "6203022906680002",
        "196806292025211006",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ETIE PEBRIANI",
        "24670130820000282",
        "6203025202960002",
        "199602122025212143",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Kelurahan Hampatung"
    ],
    [
        "EVA AMELIA",
        "24670130820000182",
        "6203044301960004",
        "199601032025212117",
        "Kapuas",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "EVA KUMALASARI, S.Pd",
        "24670110820000284",
        "6203076510940001",
        "199410252025212151",
        "Hulu Sungai Utara",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 3 Palingkau Baru"
    ],
    [
        "EVALUASI, SE",
        "24670110820000758",
        "6203115303860003",
        "198603132025212112",
        "Kapuas",
        "S-1 MANAJEMEN EKONOMI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Tengah Satu Atap"
    ],
    [
        "EVI",
        "24670130820000079",
        "6203094506930005",
        "199306052025212208",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ZULKIFLI",
        "24670130810000019",
        "6203030609720004",
        "197209062025211058",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "EVY RULIANA SIMANGUNSONG, S.H",
        "24670130820000264",
        "6203016908860002",
        "198608292025212085",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SINTIA",
        "24670130820000293",
        "6203015808000009",
        "200008182025212060",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "FACHRIZAL ANWARI, AMK",
        "24670140810000062",
        "6304132603910001",
        "199103262025211108",
        "Barito Kuala",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Tamban Baru"
    ],
    [
        "FADLI",
        "24670130810000432",
        "6307090303850001",
        "198503032025211177",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SITI FATIMAH",
        "24670130820000521",
        "6304026506000001",
        "200006252025212061",
        "Barito Kuala",
        "D-III PENDIDIKAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FADLIYANOOR",
        "24670130810000166",
        "6203022504890002",
        "198904252025211125",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "FAHMI HIDAYAT",
        "24670130810000784",
        "6203010811890004",
        "198911082025211120",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "FAHMIANUR, S.Pd",
        "24670110810000072",
        "6203101606980002",
        "199806162025211096",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 7 Timpah Satu Atap"
    ],
    [
        "SUPRIADI",
        "24670130810000903",
        "6203010701000002",
        "200001072025211049",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "FAHRIATI, S.Pd.I",
        "24670130820000597",
        "6203014309880004",
        "198809032025212109",
        "Hulu Sungai Tengah",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sriwidadi"
    ],
    [
        "FAHRINA HIKMAH, S.pd.I",
        "24670110820000712",
        "6203016102010005",
        "200102212025212045",
        "Hulu Sungai Tengah",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Selat Hulu"
    ],
    [
        "FAHRIZAL KAMARULLAH VAHLEFI, S.Kom",
        "24670130810000262",
        "6203010408930001",
        "199308042025211133",
        "Pulang Pisau",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "AGUS NADI",
        "24670130810000652",
        "6203013008730001",
        "197308302025211042",
        "Kapuas",
        "SLTA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FAHRUL",
        "24670130810000648",
        "6203011111970015",
        "199711272025211094",
        "Tanah Bumbu",
        "SMK MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "FAHRUL NOR, ( S.Pd.)",
        "24670110810000075",
        "6203070902950003",
        "199502092025211101",
        "Kapuas",
        "S-1 AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Menteng Raya"
    ],
    [
        "FAHRUL ZAINI",
        "24670130810000244",
        "6203071009840002",
        "198409102025211148",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "APRINENSI",
        "24670130820000651",
        "6203105204730001",
        "197304122025212030",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FAISAL",
        "24670130810000812",
        "6203010208840006",
        "198408022025211125",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "FAISAL SAPUTRA",
        "24670130810000532",
        "6203082406980002",
        "199806242025211089",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "FAIZAL GUSWANDA, S.Kom",
        "24670110810000262",
        "6203081108970002",
        "199708112025211115",
        "Pulang Pisau",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Basarang Satu Atap"
    ],
    [
        "FAJAR DIANSYAH",
        "24670130810000677",
        "6203012803980003",
        "199603282025211099",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "FAJAR ILHAMSYAH, S.Pd",
        "24670110810000227",
        "6203012610990002",
        "199910262025211066",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 4 Pulau Kupang"
    ],
    [
        "FAJAR IRIANTO",
        "24670130810000461",
        "6203011110920002",
        "199210112025211144",
        "Tapin",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "FAJAR RAMADHAN",
        "24670130810000448",
        "6203010210910006",
        "199110022025211152",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "FAJERI, S.Pd.,Gr",
        "24670110810000172",
        "6304040608970001",
        "199708062025211091",
        "Barito Kuala",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 2 Tumbang Manyarung"
    ],
    [
        "FAJRI",
        "24670130810000728",
        "6203012308860005",
        "198708232025211116",
        "Kapuas",
        "PAKET C (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "FAJRIANOR, S.Kom.",
        "24670120110000654",
        "6203072011950002",
        "199511202025211109",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Bidang Pembinaan SMP"
    ],
    [
        "FAJRIN RAHMAN, S.Pd",
        "24670110810000263",
        "6203040107000230",
        "200007012025211061",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BANDAR MEKAR"
    ],
    [
        "FANNY LOBERTONIUS, A.md.Kep",
        "24670140810000120",
        "6203110407980003",
        "199807042025211083",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "SYAFRI CAHYADIANTO",
        "24670130810000810",
        "6203010605000004",
        "200005062025211060",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "FARIDA HANUM BATUBARA",
        "24670130820000095",
        "6203015604820005",
        "198204162025212065",
        "Medan",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "FARIS SANTOSO",
        "24670130810000785",
        "3526081405920004",
        "199205142025211169",
        "Nganjuk",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FATHURRAZAK, S.Pd.I",
        "24670110810000211",
        "6203132905870001",
        "198705292025211108",
        "Kapuas",
        "S-1 PENDIDIKAN GURU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Terusan Baguntan Raya"
    ],
    [
        "FATIMAH, S.Pd.I",
        "24670130820000566",
        "6203087011920001",
        "199211302025212146",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BAMBAN RAYA"
    ],
    [
        "FATIMAH LINA, S.T.",
        "24670130820000276",
        "6203017005910003",
        "199105302025212124",
        "Banjarmasin",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "FATMA MAUDY SAPUTRI, S.Pd",
        "24670110820000557",
        "6203014308990004",
        "199908032025212118",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Sei Kayu"
    ],
    [
        "SYAHMAN MAULANA PUTRA",
        "24670130810000573",
        "6203011512000011",
        "200012152025211045",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "FATMAWATI",
        "24670130820000163",
        "6203015005900011",
        "199005102025212201",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "FATMAWATI, S.Pd",
        "24670110820000407",
        "6203044402990001",
        "199902042025212088",
        "Kapuas",
        "S-1 AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 3 Anjir Mambulau Barat"
    ],
    [
        "FATMAWATI",
        "24670320120000753",
        "6203025309980001",
        "199809132025212103",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FATMAWATI, A.Md.Kep",
        "24670120120000718",
        "6203016108970003",
        "199708212025212096",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "FATURAHMAN SALMANI, S.Kep.,Ns",
        "24670140810000034",
        "6203011509940006",
        "199409152025211115",
        "Hulu Sungai Tengah",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "FEBBY NUARISA, S.E",
        "24670130820000284",
        "6203015101900009",
        "199001112025212146",
        "Barito Timur",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SYAHRIAN RAHDI",
        "24670130810000726",
        "6203010907000003",
        "200007092025211049",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "FEBRI DAMAYADI, S.Pd",
        "24670110810000304",
        "6203110512970002",
        "199712052025211108",
        "Kapuas",
        "S-1 MANAJEMEN PENDIDIKAN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pujon"
    ],
    [
        "FEBRIANTO",
        "24670130810000213",
        "6203012002920007",
        "199202202025211160",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "FEBRIN HARRIE SETIAWAN",
        "24670130810001017",
        "6203020202880004",
        "198802022025211190",
        "Kotawaringin Timur",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "FEBRINA AYU LESTARI",
        "24670130820000312",
        "6203086902920002",
        "199202292025212122",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Penata Layanan Operasional",
        "Subbagian Keuangan dan Aset"
    ],
    [
        "FEBRINA TIARA PUTRI, A.Md.Pjk.",
        "24670130820000157",
        "6203014402970005",
        "199702042025212132",
        "Kapuas",
        "D-III PERPAJAKAN",
        "Pengelola Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "FEBRYAN SAZMI",
        "24670130810000853",
        "6203011402900013",
        "199002142025211130",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "VIA WINATA",
        "24670130820000660",
        "6203015007000007",
        "200007102025212068",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FEPTY INDRIATY, S.E",
        "24670130820000181",
        "6203016602890004",
        "198902262025212093",
        "Kapuas",
        "S-1 EKONOMI DAN MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "HERRY JUNAEDI",
        "24670130810000572",
        "6203012708730004",
        "197308272025211048",
        "Palangka Raya",
        "SMT PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "HERTONO",
        "24670130810000881",
        "6203010310730003",
        "197310032025211036",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Bataguh"
    ],
    [
        "FERRYANTO, S.A.P",
        "24670130810000183",
        "6203011710860005",
        "198610172025211121",
        "Palangka Raya",
        "S-1 ADMINITRASI PUBLIK",
        "Penata Layanan Operasional",
        "Bidang Pembinaan Ketenagaan"
    ],
    [
        "FERY SETIAWAN",
        "24670120110000077",
        "6203012802980003",
        "199802282025211082",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "FIDE DELANO KRESTIN, S.Kom",
        "24670130810000862",
        "6203021102980003",
        "199802112025211087",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana"
    ],
    [
        "FIKAL",
        "24670130810000349",
        "6203060806890003",
        "198906082025211170",
        "Kapuas",
        "SMK PERIKANAN",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "FIKRY ADINATA, S.T",
        "24670130810000283",
        "6371032306870008",
        "198706232025211102",
        "Banjarmasin",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "FINA, S.Pd",
        "24670110820000469",
        "6203115611920003",
        "199302252025212138",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Kota Baru"
    ],
    [
        "FIRMAN DEDE, S.T.",
        "24670130810001007",
        "6203080708930002",
        "199308072025211174",
        "Banjarmasin",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "FIRMANSYAH, S.Pd",
        "24670110810000179",
        "6203092704940006",
        "199404272025211115",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lapetan"
    ],
    [
        "FIRMANSYAH LASA",
        "24670130810000920",
        "6203011501900008",
        "199001152025211154",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "FITRI, S.Pd",
        "24670110820000646",
        "6271036808980006",
        "199808222025212104",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Danau Rawah"
    ],
    [
        "FITRI",
        "24670130820000029",
        "6203055603920001",
        "199203162025212136",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "FITRI UTAMI DEWI, S.Pd.I",
        "24670110820000303",
        "6203016306840005",
        "198406232025212070",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Selat Hulu"
    ],
    [
        "JUMADI IMANUEL SANTOSO, A.Ma.Pd",
        "24670130810000987",
        "6211033107730001",
        "197307312025211025",
        "Palangka Raya",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FITRIA ANGGRENI WULANDARI",
        "24670130820000556",
        "6203054905950003",
        "199505092025212155",
        "Palangka Raya",
        "SMA MATEMATIKA DAN ILMU PENGETAHUAN ALAM",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "FITRIA SARI, S.H",
        "24670130820000131",
        "6203014404920012",
        "199204042025212212",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "FITRIAH, S.Pd",
        "24670130820000565",
        "6203086103910003",
        "199103212025212116",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Suka Maju"
    ],
    [
        "FITRIANI",
        "24670130820000243",
        "6203014204960003",
        "199604022025212138",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "FITRIANI D.L, S.Pd.I",
        "24670110820000446",
        "6203015109860004",
        "198709112025212114",
        "Kapuas",
        "D-IV PENDIDIKAN GURU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pulau Telo Baru"
    ],
    [
        "FITRIANSYAH",
        "24670130810000725",
        "6203011605880002",
        "198805162025211132",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "FITRIYANTI, S.Pd",
        "24670110820000658",
        "6203046012860002",
        "198612202025212112",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 3 Lupak Dalam"
    ],
    [
        "FIZENSA, S.Pd",
        "24670110820000390",
        "6203076111000002",
        "200011212025212056",
        "Kapuas",
        "S-1 PENDIDIKAN SENI DRAMA, TARI DAN MUSIK",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Dadahup"
    ],
    [
        "FONTRY VONDA P. SATU, S.T",
        "24670020110002379",
        "6203011901910004",
        "199101192025211119",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "FOULLINA HANDAYANI",
        "24670130820000016",
        "6203054110910003",
        "199110012025212158",
        "Palangka Raya",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "MUHAMMAD AMBRI",
        "24670130810000434",
        "6203011510730006",
        "197310152025211046",
        "Kapuas",
        "SEKOLAH MENENGAH EKONOMI ATAS",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "FRANKLIN, S.E",
        "24670130810000159",
        "6211051808970001",
        "199708182025211107",
        "Pulang Pisau",
        "S-1 MANAJAMEN",
        "Penata Layanan Operasional",
        "Dinas Perumahan, Kawasan Permukiman, dan Pertanahan"
    ],
    [
        "FRANSISCA YULIA DYAH WULANDARI, S.E",
        "24670130820000092",
        "3306096007870008",
        "198707202025212136",
        "Purworejo",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "FREDDY ALIKA, S.T",
        "24670130810000224",
        "6203010802950002",
        "199502082025211110",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "FREDI NUGRAHA",
        "24670130810000368",
        "6203022505970002",
        "199705252025211121",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "FRENDI ANGGARA",
        "24670130810000518",
        "6203013009930009",
        "199309302025211120",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "FRENTI YURIBKA, S.Pd",
        "24670110820000737",
        "6203015611900003",
        "199011162025212123",
        "Pulang Pisau",
        "S-1 KEGURUAN DAN ILMU PENDIDIKAN GURU SD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Batapah"
    ],
    [
        "FRISTINA NATALIA, S.Sos",
        "24670130820000167",
        "6203026712850002",
        "198512272025212086",
        "Palangka Raya",
        "S-1 ILMU ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "FRITZ GERALDY AFRANDA, S.H.",
        "24670130810000275",
        "6371040505900008",
        "199005052025211233",
        "Banjarmasin",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "FUAD TRY AKBAR, SH",
        "24670130810000937",
        "6203022611930001",
        "199311262025211096",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FUJIANTO, Amd, Kep",
        "24670140810000119",
        "6203010806960004",
        "199606082025211104",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "GANDA ERAMBONO",
        "24670130810000733",
        "6203013006880002",
        "198806302025211117",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "GEDE GUNAWAN, S.Pd",
        "24670110810000193",
        "6203130511850001",
        "198511052025211126",
        "Kapuas",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SMP NEGERI 1 BATAGUH"
    ],
    [
        "YOSUA CHARLEN",
        "24670130810000921",
        "6271032708000003",
        "200008272025211041",
        "Kapuas",
        "SLTA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "YUBILATE",
        "24670130810000255",
        "6203012006000005",
        "200009172025211055",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "YUELDI",
        "24670130810000148",
        "6203080310000002",
        "200010032025211058",
        "Barito Timur",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "GHAUTSUL AZHAM, S.Kom",
        "24670130810000041",
        "6203011809950003",
        "199509182025211112",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "GIDEON DOMINGGO",
        "24670130810000852",
        "6203022007990002",
        "199907202025211070",
        "Kapuas",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "GIMEDI HERDINATAE GONCO, S.Pd",
        "24670110810000224",
        "6203012705880005",
        "198805272025211134",
        "Palangka Raya",
        "S-1/A-IV PENDIDIKAN JASMANI KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Timpah Satu Atap"
    ],
    [
        "GINO SUSANTO, S.Pd",
        "24670110810000079",
        "6203111507880003",
        "198807152025211197",
        "Kapuas",
        "S-1 TEKNOLOGI PENDIDIKAN",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Kapuas Tengah"
    ],
    [
        "GLORIA MEITY, A.md.Keb",
        "24670130820000291",
        "6203016305900005",
        "199005232025212110",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "Dinas Kesehatan"
    ],
    [
        "GOMITRO",
        "24670020110002743",
        "6271040701900002",
        "199001072025211124",
        "Kapuas",
        "SMA ILMU ALAM",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "NASARUDDIN KARYA",
        "24670130810000805",
        "6203012304730001",
        "197304232025211036",
        "Kapuas",
        "SMEA",
        "Operator Layanan Operasional",
        "Kelurahan Selat Hulu"
    ],
    [
        "GUSNESI, S.Kom",
        "24670110820000645",
        "6211035508900002",
        "199008152025212170",
        "Pulang Pisau",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Timpah"
    ],
    [
        "GUSRIN ARIFIANTO",
        "24670130810000424",
        "6203010408920002",
        "199208042025211133",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "GUSTEN MARAW",
        "24670130810000308",
        "6203011008900009",
        "199008102025211190",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "GUSTI OCTAVIANI RUSTINA DEVI",
        "24670130820000250",
        "6371036610940005",
        "199410262025212107",
        "Palangka Raya",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "GUSTIANSYAH",
        "24670130810000291",
        "6203011503870011",
        "198703152025211159",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "NELI WANGI",
        "24670130820000543",
        "6203024305730001",
        "197305032025212033",
        "Kapuas",
        "D-IV PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 5 Pulau Kupang"
    ],
    [
        "HADIJAH, S.Pd",
        "24670110820000425",
        "6203035005010002",
        "200105102025212056",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PAUD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Serapat Tengah"
    ],
    [
        "HADINOR",
        "24670130810000284",
        "6203073011850003",
        "198511302025211118",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "HADITIA WARDANA",
        "24670130810000263",
        "6203080406880001",
        "198806042025211184",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "HADRAN",
        "24670130810000820",
        "6203010211920004",
        "199208082025211189",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "NOPRIYEDI",
        "24670130810001011",
        "6203071911730001",
        "197311192025211028",
        "Kapuas",
        "SLTP/SMP SEDERAJAT",
        "Pengelola Umum Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HAFIZ ANSHARI, S.Pd",
        "24670110810000055",
        "6203072410940003",
        "199410242025211124",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palingkau Sejahtera"
    ],
    [
        "HAGAI SASTRO MARYONO, S.Pd",
        "24670110810000236",
        "6271032903880008",
        "198803292025211116",
        "Gunung Mas",
        "S-1 PENDIDIKAN OLAHRAGA, JASMANI, KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Tengah Satu Atap"
    ],
    [
        "HAI GREISIAS, S.Kom.",
        "24670120110000762",
        "6203010312960010",
        "199612032025211091",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "HAIDIR ALI",
        "24670130810000653",
        "6203013107930004",
        "199307312025211127",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Sei Pasah"
    ],
    [
        "YULANDA MILENIA",
        "24670130820000504",
        "6203095509000001",
        "200003152025212075",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "HALIM NUGRAHA PUTRA, S.E",
        "24670130810000497",
        "6203010106990009",
        "199906012025211082",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HALIMAH, S.Pd",
        "24670110820000596",
        "6308106906980001",
        "199806292025212106",
        "Hulu Sungai Utara",
        "D-IV PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Cemara Labat"
    ],
    [
        "HALISAH, Amd. Keb",
        "24670140820000339",
        "6203077001000001",
        "200001302025212068",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Dadahup"
    ],
    [
        "NUR SUPIAN",
        "24670130810000942",
        "6203013103730002",
        "197303312025211026",
        "Kapuas",
        "SMT PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HAMDAN",
        "24670130810000369",
        "6203010701970004",
        "199701072025211112",
        "Kapuas",
        "SMK TEKNIK KENDARAAN RINGAN",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "HAMDI",
        "24670130810000773",
        "6203012104860010",
        "198604212025211135",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "HAMIDAH",
        "24670130820000102",
        "6203074512960002",
        "199505122025212149",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "HAMIDAH",
        "24670130820000664",
        "6203096910810003",
        "198110292025212052",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HAMIDAH, S.Pd",
        "24670110820000365",
        "6203056703960001",
        "199603272025212123",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BUKIT BATU"
    ],
    [
        "HAMIDAH, AMK",
        "24670140820000285",
        "6203044701930004",
        "199206072025212152",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Tamban Baru"
    ],
    [
        "HANA PRATIWI, S.Pd",
        "24670110820000444",
        "6203075008000006",
        "200008102025212080",
        "Kapuas",
        "S-1 PENDIDIKAN MATEMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Murung"
    ],
    [
        "HANIFAH",
        "24670130820000401",
        "6203076602960003",
        "199602262025212138",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "HANIFATUR RAHMAH KURNIA PUTRI, S.Pd",
        "24670110820000421",
        "6211067108000002",
        "200008312025212051",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 7 Selat Hilir"
    ],
    [
        "HANNA DIANA, S.Pd",
        "24670110820000423",
        "6203014103960005",
        "199603012025212136",
        "Kotawaringin Timur",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 5 PULAU KUPANG"
    ],
    [
        "HARDIATIE, S.Pd",
        "24670110820000716",
        "6203075511940003",
        "199411152025212147",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Dadahup"
    ],
    [
        "REINHARD",
        "24670130810000537",
        "6203010111730001",
        "197311012025211038",
        "Kapuas",
        "SEKOLAH MENENGAH TEKNOLOGI PERTANIAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "HARIANTO",
        "24670130810000084",
        "6203081306900004",
        "199006132025211122",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "HARIS IMAM SAPUTRA, S.Kep, Ns",
        "24670140810000141",
        "6203081005910003",
        "199305102025211160",
        "Pulang Pisau",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "HARIS SAKTI",
        "24670130810000585",
        "6203010110830006",
        "198310012025211106",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "HARISON",
        "24670130810001036",
        "6203010608870010",
        "198706082025211142",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "HARIYADI, A.Md.",
        "24670130810000013",
        "6203013004880003",
        "198804302025211109",
        "Barito Selatan",
        "AKADEMI MANAJEMEN INFORMATIKA DAN KOMPUTER",
        "Pengelola Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "ZAINUL MUTAQIN",
        "24670130810000146",
        "6203011902000005",
        "200002152025211069",
        "Hulu Sungai Selatan",
        "SMK TEKNIK KOMPUTER DAN INFORMATIKA",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "HARRY ARIES SUSANTO",
        "24670130810000600",
        "6203011803840001",
        "198403182025211099",
        "Murung Raya",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HARRY RARIO REPLY, S.T",
        "24670130810000985",
        "6271030201850005",
        "198501022025211128",
        "Palangka Raya",
        "S-1 ARSITEKTUR",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "HARRY SANTOSO, SE",
        "24670130810000059",
        "6203022009830001",
        "198309202025211112",
        "Kapuas",
        "S-1 ILMU EKONOMI MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "HARRYWAN BAHAN",
        "24670130810000238",
        "6203011011870012",
        "198711102025211204",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Kelurahan Barimba"
    ],
    [
        "HARSENSIUS ALFARESS JHENTLY, S.AP",
        "24670020110002487",
        "6203012610940001",
        "199410262025211108",
        "Kapuas",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "HARTATI",
        "24670130820000188",
        "6203014604930004",
        "199304062025212153",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "HARTINI",
        "24670130820000252",
        "6203016008850006",
        "198508202025212097",
        "Enrekang",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "RUBENSI",
        "24670130810001003",
        "6203110405730003",
        "197305042025211087",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RESSA FIRMANDA",
        "24670130810001026",
        "6203032611010001",
        "200111262025211047",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RUSMILA",
        "24670130820000600",
        "6203025002730004",
        "197302102025212036",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Guru Ahli Pertama",
        "SD Negeri 2 Barimba"
    ],
    [
        "SETIAWAN",
        "24670130810000874",
        "6203052011730002",
        "197311202025211041",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HARYADI GUSMAN",
        "24670130810000555",
        "6203012808860007",
        "198608282025211138",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "HARYANOOR PUADY",
        "24670130810000712",
        "6203010203810004",
        "198103022025211094",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "HARYUNDU SAPUTRA",
        "24670130810000241",
        "6203072703880003",
        "198803272025211095",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "HASANAH, S.Pd",
        "24670110820000283",
        "6203016006010003",
        "200106202025212038",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Budi Mufakat"
    ],
    [
        "HASANATUL MUNAWARAH, S.E.",
        "24301220120206149",
        "6203036504980001",
        "199804252025212130",
        "Kapuas",
        "S-1 PERBANKAN SYARIAH",
        "Penata Layanan Operasional",
        "UPT Puskesmas Pujon"
    ],
    [
        "HASBIANOR, A.Md",
        "24670130810000984",
        "6203042210970004",
        "199710222025211087",
        "Kapuas",
        "D-III AKUNTANSI",
        "Pengelola Layanan Operasional",
        "UPT Puskesmas Tamban Baru"
    ],
    [
        "HASYIM ABDILLAH",
        "24670130810001025",
        "6203010304870004",
        "198704052025211159",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SUJOKO",
        "24670130810000996",
        "6203042811730002",
        "197311282025211023",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "UPT Puskesmas Tamban Catur"
    ],
    [
        "HAYATUN NUPUS, S.Pd",
        "24670110820000537",
        "6271015408850005",
        "198508142025212100",
        "Palangka Raya",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Barunang"
    ],
    [
        "HAZAR MAWET KAREL SIKI TARI, S.Pd",
        "24670110810000097",
        "6203112605890001",
        "198905262025211128",
        "Rote Ndao",
        "S-1 SARJANA PENDIDIKAN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Batu Sambung"
    ],
    [
        "HELIWATI",
        "24670130820000613",
        "6203095707870004",
        "198707172025212191",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HELJON JEPTA MIHING",
        "24670130810000867",
        "6203010701900007",
        "199001072025211131",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DODI PURWANSYAH",
        "24670130810000524",
        "6203030807840003",
        "Belum ada nip",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Pengelola Umum Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HENDI MUALAMI",
        "24670130810000553",
        "6203010809880004",
        "198809082025211148",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "TERISIANA FEBRIANTI, A.Ma.",
        "24670130820000057",
        "6205056302730004",
        "197302232025212020",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HENDRATNO",
        "24670130810000950",
        "6203010808820013",
        "198208082025211157",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "HENDRI",
        "24670130810000695",
        "6203011507850006",
        "198507152025211200",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "WELDI SURIADI",
        "24670130810000807",
        "6203060706730001",
        "197306072025211060",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HENDRI FEBRIANSON, SE",
        "24670130810000197",
        "6203020702860003",
        "198602072025211088",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "HENDRI MANTIAS, S.Kep.,Ners",
        "24670140810000122",
        "6203062110950003",
        "199510212025211086",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Danau Rawah"
    ],
    [
        "HENDRI PINARTO",
        "24670130810000536",
        "6203020505890001",
        "198905052025211238",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HENDRIAN TUAH, A.md,",
        "24670130810001013",
        "6203012410820004",
        "198210022025211115",
        "Barito Selatan",
        "D-III AGRIBISNIS",
        "Pengelola Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "HENDRO SAPUTRA, SH",
        "24670130810000002",
        "6203012909890015",
        "198909292025211190",
        "Pulang Pisau",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "YAN LOTHARD",
        "24670130810000719",
        "6206050506730002",
        "197306052025211089",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ABDURRAHMAN",
        "24670320110002116",
        "6203031101010002",
        "200101112025211049",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "HENGKY SUANGGARA",
        "24670130810000140",
        "6203070204840003",
        "198404022025211127",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HENI KORNIAWATI, S.Pd.I",
        "24670130820000626",
        "6203016205910004",
        "199105222025212122",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Guru Ahli Pertama",
        "SD Negeri 2 Murung Keramat"
    ],
    [
        "ZULKISMIAH, S.Sos",
        "24670130820000344",
        "6203024203730003",
        "197303022025212026",
        "Balikpapan",
        "S-1 ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "HENNIE ANANDA, S.pd.I",
        "24670110820000456",
        "6304054208010001",
        "200108022025212031",
        "Banjarmasin",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Tamban Luar"
    ],
    [
        "HENNY ARI SUSANTI, A.Ma.Pd.TK",
        "24670130820000624",
        "6203026405860002",
        "198605242025212071",
        "Kapuas",
        "D-I PAUD",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HENNY OCTANIA",
        "24670130820000353",
        "6203016910820004",
        "198210292025212042",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "HENNY PERAWATI",
        "24670130820000554",
        "6203026007880001",
        "198807202025212137",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ABDUL HAMID",
        "24670130810000218",
        "6203010911740006",
        "197411092025211038",
        "Kapuas",
        "SEKOLAH MENENGAH EKONOMI ATAS",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "AGUS SALIM",
        "24670130810000522",
        "3674040506740005",
        "197406052025211099",
        "Jakarta Selatan",
        "SMA A.2/BIOLOGI",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "HEPPY, S.Pt",
        "24670130820000580",
        "6203025309920002",
        "199209132025212146",
        "Kapuas",
        "S-1 PETERNAKAN",
        "Penata Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "HERDI, A.md,Kep",
        "24670140810000006",
        "6203110604930002",
        "199304062025211158",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Jangkang"
    ],
    [
        "HERDIANA ENGGAR WIDHIASTI, S.Si",
        "24670120120000812",
        "6203017006960007",
        "199606302025212128",
        "Kapuas",
        "S-1 BIOLOGI",
        "Penata Layanan Operasional",
        "Laboratorium Kesehatan Daerah Kabupaten Kapuas"
    ],
    [
        "HERDIANTI, A.Md.Keb",
        "24670140820000345",
        "6203035404940002",
        "199504142025212175",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "HERDYAN AZOKA CHANDRA, S.Pd",
        "24670110810000157",
        "6371010405010008",
        "200105042025211037",
        "Banjarmasin",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD NEGERI 1 TAMBAN MAKMUR"
    ],
    [
        "HERI PURNOMO, S.Kom",
        "24670130810000156",
        "6203012301900007",
        "199001232025211123",
        "Kapuas",
        "D-IV/S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "HERI YANTO",
        "24670130810000488",
        "6203011604850007",
        "198504162025211116",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "HERI YANTO, A.Ma",
        "24670130810001046",
        "6203091804900005",
        "199004182025211117",
        "Kapuas",
        "D-II PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Katimpun"
    ],
    [
        "HERIANTO SOSILO, SKM",
        "24670130810001005",
        "6271031001890008",
        "198901102025211158",
        "Palangka Raya",
        "S-1 KESEHATAN MASYARAKAT",
        "Penata Layanan Operasional",
        "UPT Puskesmas Sei Pinang"
    ],
    [
        "HERIYANTO",
        "24670130810000915",
        "6203020108930003",
        "199308012025211122",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "HERLIADI",
        "24670130810000811",
        "6203080707840001",
        "198407072025211168",
        "Kapuas",
        "SMK SEKRETARIS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HERLIANI, S.Pd",
        "24670110820000412",
        "6203015608000005",
        "200008162025212077",
        "Kapuas",
        "S-1 KEGURUAN DAN ILMU PENDIDIKAN GURU SD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pulau Mambulau"
    ],
    [
        "HERLINA, S.pd.I",
        "24670130820000561",
        "6271034708840003",
        "198408072025212097",
        "Kapuas",
        "S-1 PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tarantang"
    ],
    [
        "HERLINA",
        "24670130820000522",
        "6204064910990002",
        "199910092025212094",
        "Barito Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ARI NAHAN",
        "24670130810000122",
        "6371050404740005",
        "197404042025211103",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "HERMELIATI",
        "24670130820000640",
        "6203014804820003",
        "198204082025212083",
        "Barito Selatan",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Gudang Farmasi Kesehatan"
    ],
    [
        "HERNANDI",
        "24670130810000290",
        "6210020205930003",
        "199305022025211135",
        "Pulang Pisau",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Subbagian Keuangan dan Aset"
    ],
    [
        "HERNIKA",
        "24670130820000320",
        "6210024204930001",
        "199304022025212154",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Hulu"
    ],
    [
        "HERNIKA FITRIANI, S,Pd",
        "24670110820000750",
        "6203035302970001",
        "199702132025212099",
        "Kapuas",
        "S-1 PAUD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palingkau Jaya"
    ],
    [
        "HERO, S.Kep",
        "24670130810000998",
        "6203011304930007",
        "199304132025211132",
        "Kapuas",
        "S-1 KEPERAWATAN",
        "Administrator Kesehatan Ahli Pertama",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "HERO SETIAWAN",
        "24670130810000738",
        "6203011604930005",
        "199304162025211121",
        "Kapuas",
        "KPG PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "BAMBANG SUPRAPTO",
        "24670130810000481",
        "6203010202740008",
        "197402022025211069",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "HERO",
        "24670130810000699",
        "6203011510940006",
        "199410072025211130",
        "Kapuas",
        "SMK PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DEMIWATI",
        "24670130820000549",
        "6203114404740002",
        "197404042025212046",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "H.SUFIANSYAH",
        "24670130810000762",
        "6203011807740008",
        "197407182025211039",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HERVIANSYAH",
        "24670130810000487",
        "6307040502980002",
        "199802052025211080",
        "Hulu Sungai Tengah",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "HERVITHA",
        "24670130820000251",
        "6203024807840005",
        "198407082025212086",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Hampatung"
    ],
    [
        "HERY KRIST NOVARIANTA, S E",
        "24670130810000040",
        "6203011411810001",
        "198111142025211068",
        "Palangka Raya",
        "EKONOMI MANAJEMEN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "HERY PRAMANA",
        "24670130810000589",
        "6203010310890006",
        "198910032025211139",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "HESTI ASTRIA, S.Pd",
        "24670110820000338",
        "6203115704980001",
        "199812012025212088",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Jangkang"
    ],
    [
        "HESTINI",
        "24670130820000214",
        "6271035712890002",
        "198912172025212127",
        "Gunung Mas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "HEVI SUSANTI",
        "24670130820000421",
        "6203015411920005",
        "199211142025212148",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "HADY SANTOSO",
        "24670130810000163",
        "6203010808740019",
        "197408082025211077",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "HIDAYAH, S.Kom",
        "24670130820000101",
        "6203016112900008",
        "199012212025212102",
        "Barito Utara",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "HIDAYANA AS",
        "24670130820000059",
        "6203017001970003",
        "199701302025212100",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "HIDAYATULLAH",
        "24670130810000320",
        "6203082110950004",
        "199510212025211089",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "HENIE",
        "24670130820000656",
        "6203014510740006",
        "197410052025212039",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HILMI",
        "24670130810000143",
        "6203010410930007",
        "199610042025211124",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "HIRMAYA SANTI",
        "24670130820000080",
        "6203014706870003",
        "198706272025212112",
        "Balangan",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "HJ. NORLAILA, S.Pd.I",
        "24670110820000330",
        "6203105906870001",
        "198706192025212113",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tamban Baru Selatan"
    ],
    [
        "HOKLAN LOBERY H.L",
        "24670130810000453",
        "6203092002990004",
        "199902202025211051",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "HORTANELIS",
        "24670130820000125",
        "6203016801880007",
        "198801282025212088",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Bidang Pembinaan SMP"
    ],
    [
        "HUMAIDI",
        "24670130810000006",
        "6203012303950010",
        "199503232025211136",
        "Palangka Raya",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "HUMAIRATUN NISA, S.AP",
        "24670130820000553",
        "6203045508940003",
        "199408152025212141",
        "Kapuas",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HUSAINI",
        "24670130810001001",
        "6203010408930003",
        "199106122025211151",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "HUSNUL HATIMAH, S.Pd",
        "24670110820000335",
        "6203064201020003",
        "200201022025212017",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SMP NEGERI 4 KAPUAS BARAT SATU ATAP"
    ],
    [
        "HUTRI TAMBUNAN, S.Pd",
        "24670110820000550",
        "1202115708990001",
        "199908172025212129",
        "Tapanuli Utara",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lunuk Ramba"
    ],
    [
        "I KETUT SUKARINI, S.Pd.AH.",
        "24670110820000536",
        "6203085812890002",
        "198912182025212127",
        "Pulang Pisau",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sari Makmur"
    ],
    [
        "I KOMANG RATNE, S.pd.I",
        "24670110810000123",
        "6203080309930003",
        "199309212025211127",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bungai Jaya"
    ],
    [
        "I MADE ARTAWAN, S.Pd",
        "24670110810000219",
        "6203082304000003",
        "200004232025211070",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Batu Nindan"
    ],
    [
        "I MADE MURYE",
        "24670130810000095",
        "6203081210820001",
        "198210122025211135",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "HEWI",
        "24670130820000318",
        "6203124203740002",
        "197403022025212023",
        "Kapuas",
        "SLTA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "I NYOMAN WARTA, SH",
        "24670130810000024",
        "6203080911820004",
        "198211092025211101",
        "Kapuas",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "I WAYAN ALEK SADEWO, S.Pd",
        "24670110810000165",
        "6203010709990004",
        "199909072025211066",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Terusan Baguntan Raya"
    ],
    [
        "I WAYAN ASTE, S,Pd.",
        "24670110810000306",
        "6203082005910002",
        "199105202025211159",
        "Kapuas",
        "D-IV PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 1 Selat Dalam"
    ],
    [
        "I WAYAN HERMANTO",
        "24670130810000121",
        "6203081112850001",
        "198511122025211131",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "I WAYAN KRISNA ADIPAYANA, S.Mat",
        "24670110810000249",
        "6203010104980006",
        "199804012025211095",
        "Kapuas",
        "S-1 MATEMATIKA",
        "Guru Ahli Pertama",
        "SMP NEGERI 1 BATAGUH"
    ],
    [
        "I WAYAN PUDITE",
        "24670130810000227",
        "6203082308890002",
        "198908232025211155",
        "Kapuas",
        "SMA ILMU SOSIAL",
        "Operator Layanan Operasional",
        "Bidang Pembinaan SD"
    ],
    [
        "I WAYAN SANA INDRA WINATA, S.Pd",
        "24670110810000175",
        "6203010302990004",
        "199902032025211064",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Terusan Baguntan Raya"
    ],
    [
        "IBAH, S.Pd.I",
        "24670130810000316",
        "6203071303810001",
        "198103132025211100",
        "Kapuas",
        "D-IV AGAMA",
        "Penata Layanan Operasional",
        "Kecamatan Kapuas Hilir"
    ],
    [
        "IBRAHIM",
        "24670130810000119",
        "6203010808940003",
        "199408082025211157",
        "Kapuas",
        "SMK TEKNIK OTOMOTIF",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "JUMBERI",
        "24670130810000949",
        "6203060211740002",
        "197411022025211030",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "KAMALA PUSPAWATI, S.Pd",
        "24670130820000519",
        "6203095907740001",
        "197406162025212037",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD NEGERI 1 TARANTANG"
    ],
    [
        "IDA ANDRIYANI, A.Md. Keb",
        "24670130820000512",
        "6203144202940002",
        "199402022025212168",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Jangkang"
    ],
    [
        "IDA AYU LESTARI",
        "24670130820000051",
        "6203014401920009",
        "199201042025212136",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "IDA MAHDA WAHDA WINATA, S.Pd",
        "24670110820000356",
        "6211026010990001",
        "199910202025212099",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 2 Terusan Raya Hulu"
    ],
    [
        "IDA PRAMITA",
        "24670130820000187",
        "6203014906870007",
        "198706092025212098",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "IDA ROHYANI, S.Pd.I",
        "24670110820000565",
        "3502184508920004",
        "199208052025212201",
        "Ponorogo",
        "S-1 PENDIDIKAN BAHASA ARAB",
        "Guru Ahli Pertama",
        "SD Negeri 3 Terusan Mulya"
    ],
    [
        "IDRUS",
        "24670130810000047",
        "6203020206930004",
        "199306022025211125",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "IDRUS",
        "24670130810000960",
        "6203021707810002",
        "198107172025211111",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "IGA ARISANTI, S.E.I",
        "24670120120001281",
        "6203015106940003",
        "199306112025212139",
        "Kapuas",
        "S-1 PERBANKAN SYARIAH",
        "Penata Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "IIN YULIARTI",
        "24670130820000277",
        "6203015702870004",
        "198702172025212082",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "M. RAMLI",
        "24670130810000540",
        "6203010911740005",
        "197411092025211041",
        "Banjarmasin",
        "SMPS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "IKA KARTIKASARI",
        "24670130820000441",
        "6203065403880002",
        "198803142025212108",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "IKA PARWATI, S. Pd",
        "24670110820000678",
        "6203106412000002",
        "200012242025212041",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Tengah"
    ],
    [
        "IKA WIDIASTUTI",
        "24670130820000552",
        "6203015210850013",
        "198510122025212098",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "IKE VALENTINA",
        "24670130820000361",
        "6203014902890006",
        "198902092025212101",
        "Kapuas",
        "SMK TATA BUSANA",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "IKHSAN DWI BASUKI",
        "24670130810000061",
        "6203011602940010",
        "199402162025211079",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "IKHWAN SAPUTRO",
        "24670130810000054",
        "6203081508930003",
        "199308152025211143",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "IKO",
        "24670130810000302",
        "6203010608840010",
        "198408062025211126",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ILA SURATI, S.E",
        "24670110820000604",
        "6271014505860002",
        "198605052025212196",
        "Kapuas",
        "A-V BIDANG PENDIDIKAN",
        "Guru Ahli Pertama",
        "SMP NEGERI 13 MANTANGAI SATU ATAP"
    ],
    [
        "MOHAMAD AMIN, S.E.",
        "24670130810000809",
        "6203020403740005",
        "197403042025211069",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ILHAM PRIMAYUDI",
        "24670130810000438",
        "6203010606870009",
        "198706062025211222",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "IMAH RAHIMAH",
        "24670130820000471",
        "6203065007950002",
        "199507102025212190",
        "Kapuas",
        "PAKET C (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "IMAM HADI SUCIPTO, S.Pd",
        "24670110810000244",
        "6203011411950010",
        "199511142025211101",
        "Barito Kuala",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Teluk Palinget"
    ],
    [
        "IMAM SAYUTI",
        "24301220110008184",
        "6203011009930012",
        "199309102025211154",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Kelurahan Mambulau"
    ],
    [
        "IMAM WAHYUDI",
        "24670130810000767",
        "6203010402940005",
        "199402042025211124",
        "Grobogan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "IMAN",
        "24670130810000072",
        "6203081507900003",
        "199103052025211161",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI IPS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "IMANUEL, S.T",
        "24670130810000311",
        "6203013108840004",
        "198408312025211071",
        "Banjarmasin",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "IMANUEL",
        "24670130810000460",
        "6203011412890004",
        "198912142025211126",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "IMELDA YUNITA, S.pd.I",
        "24670110820000635",
        "6203054706010001",
        "200106072025212041",
        "Kapuas",
        "PENDIDIKAN SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 3 Saka Mangkahai"
    ],
    [
        "IMING, S.Pd",
        "24670110820000776",
        "6203124511970001",
        "199710052025212137",
        "Murung Raya",
        "S-1 PENDIDIKAN BAHASA SASTRA INDONESIA",
        "Guru Ahli Pertama",
        "SD Negeri 1 Jakatan Pari"
    ],
    [
        "INA ANJELINA, S. Sos",
        "24670130820000085",
        "6203024207910001",
        "199107022025212147",
        "Kapuas",
        "S-1 ILMU PEMERINTAHAN",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "INDAH PERMATASARI",
        "24670130820000154",
        "6203016009920006",
        "199209202025212133",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "AGUSTIA J.MIHING",
        "24670130820000460",
        "6203015908010006",
        "200108192025212037",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "INDAH PUJI ASTUTI, A.Md. Kep",
        "24670140820000216",
        "6203144107890001",
        "198907012025212155",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Lamunti"
    ],
    [
        "INDAH RATNASARI",
        "24670130820000438",
        "6203015209950004",
        "199509122025212140",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "INDRA",
        "24670130810000393",
        "6203022303950002",
        "199503212025211100",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "INDRA ADI KUSUMA, A.Md.Kep",
        "24670140810000126",
        "6203040304000004",
        "200004032025211053",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Tamban Catur"
    ],
    [
        "INDRA ARIEF RIANTO, S.Kom.",
        "24670130810000705",
        "6203021410880002",
        "198810142025211138",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "INDRA RIZKI MAULANA",
        "24670130810000541",
        "6203012408990007",
        "199908242025211051",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "INDRA SAPUTRA",
        "24670130810000926",
        "6203021709840001",
        "198409172025211124",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "INDRA SETIAWAN",
        "24670130810000864",
        "6203021004960004",
        "199604102025211131",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "INDRA WIJAYA, A.md",
        "24670130810000285",
        "6203010108930009",
        "199308012025211119",
        "Banjarmasin",
        "D-III TEKNIK GEODESI",
        "Pengelola Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "INDRASANJAYA SAPUTRA",
        "24670130810000383",
        "6203012511960004",
        "199611252025211113",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Baru"
    ],
    [
        "INDRAWAN",
        "24670130810000606",
        "6203051606990004",
        "199906162025211078",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "INDRI PERMATASARI, S.Pd",
        "24670020120001203",
        "6203014601940010",
        "199401062025212136",
        "Kapuas",
        "S-1 PENDIDIKAN",
        "Penata Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "INDRIANIE, S.Pd",
        "24670110820000267",
        "6271026409850001",
        "198509242025212104",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Marapit"
    ],
    [
        "INTAN MASDINAR SARI, S. Pd.",
        "24670110820000559",
        "6203034106000002",
        "200006012025212074",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Mambulau Barat"
    ],
    [
        "IQLIMA RAHMATUNNISA HEFNY, S.Pd",
        "24670110820000685",
        "6203014602020010",
        "200202062025212019",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 4 Palingkau Lama"
    ],
    [
        "IRA SRININGSIH",
        "24670130820000402",
        "6203015004860004",
        "198604102025212096",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Kelurahan Barimba"
    ],
    [
        "IRA WIDYANTI, S.Pd.I",
        "24670130820000411",
        "6203036301880001",
        "198801232025212068",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Timur"
    ],
    [
        "IRAWAN",
        "24670130810000658",
        "6203010205850015",
        "198505022025211184",
        "Kapuas",
        "SMK MEKANIK OTOMOTIF",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "IRAWANTO, S.Pd",
        "24670110810000126",
        "6203021411910001",
        "199111142025211117",
        "Kapuas",
        "S-1 PENDIDIKAN SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Terusan Raya Hulu"
    ],
    [
        "IREYATI, Amd.Kep",
        "24670140820000062",
        "6203016112910003",
        "199112212025212127",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "IRFAN IBRAHIM",
        "24670320110002029",
        "6203010601970001",
        "199701062025211097",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "IRFAN SUMINTRAPURA",
        "24670130810000516",
        "6203010707910022",
        "199107072025211172",
        "Palangka Raya",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "IRFINA, A.Md.Keb",
        "24670130820000328",
        "6203015507950009",
        "199507152025212168",
        "Kapuas",
        "D-III KEBIDANAN",
        "Pengelola Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "IRIANI LESTARI",
        "24670130820000015",
        "6203105210910002",
        "199110152025212142",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kecamatan Timpah"
    ],
    [
        "IRMARINATA, Amd.Kep",
        "24670140820000028",
        "6203165906910001",
        "199106192025212116",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "MURNIE, S.Ag.",
        "24670110820000695",
        "6203105007740002",
        "197407102025212039",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 1 Timpah"
    ],
    [
        "IRNI, S.Pd",
        "24670110820000436",
        "6203115408940003",
        "199408142025212146",
        "Gunung Mas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Tapen"
    ],
    [
        "IRPAN, S.Pd",
        "24670110810000154",
        "6203062306020001",
        "200206232025211015",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Teluk Palinget"
    ],
    [
        "AHMAD RIFANI",
        "24670130810000645",
        "6203013006010008",
        "200106302025211029",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "IRWAN TAURISA RAHMAN",
        "24670130810000225",
        "6203011107840017",
        "198407112025211105",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "IRWANSYAH",
        "24670130810000408",
        "6203011005920006",
        "198912102025211155",
        "Barito Selatan",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "PEBRUANITA, SE",
        "24670110820000470",
        "6202036702740001",
        "197402272025212008",
        "Seruyan",
        "S-1 MANAGEMENT",
        "Guru Ahli Pertama",
        "SD Negeri 2 Pujon"
    ],
    [
        "RAMINI",
        "24670130820000655",
        "6203014506740005",
        "197406052025212036",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ISHAK PARMANA",
        "24670130810000894",
        "6203080707810002",
        "198106122025211137",
        "Pulang Pisau",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ISKA, S.Pd",
        "24670110820000332",
        "6203094904990004",
        "199806092025212108",
        "Kapuas",
        "S-1 PAI",
        "Guru Ahli Pertama",
        "SD Negeri 2 Selat Hulu"
    ],
    [
        "ROSALINDA",
        "24670130820000574",
        "6203095405740003",
        "197405142025212019",
        "Kapuas",
        "SMEA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SATRIANOR",
        "24670130810000739",
        "6203010709740003",
        "197409072025211054",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ISKANDAR ZULKARNAIN",
        "24670130810000012",
        "6203032810890001",
        "198910282025211158",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ALDI SUBRATA",
        "24670130810000906",
        "6203012106010008",
        "200106212025211034",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "ISNAWATI, A.Md.Keb",
        "24670140820000176",
        "6203035311920003",
        "199211132025212140",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "ISRIANDI",
        "24680120110000614",
        "6203010405920009",
        "199205042025211167",
        "Kapuas",
        "SMA/MA (IPA)",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ANDRE A. RIFANZA",
        "24670130810000446",
        "6203011103010003",
        "200103112025211024",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Kelurahan Hampatung"
    ],
    [
        "ISTIANAH LAELA, S. Pd",
        "24670110820000291",
        "6203017007970002",
        "199707302025212117",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Terusan Baguntan Raya"
    ],
    [
        "ARI CITRA JAYA",
        "24670130810000547",
        "6203050509010002",
        "200109052025211034",
        "Kapuas",
        "SMA/MA (IPA)",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ITA NUR JANNAH, S.Ak",
        "24670130820000255",
        "6203014705950014",
        "199508072025212149",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "ITALIA NOVANTA, S.Mat",
        "24670020120002770",
        "6203015109900002",
        "199009112025212125",
        "Barito Selatan",
        "S-1 MATEMATIKA",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "IWAN",
        "24670130810000787",
        "6203080110880003",
        "198810012025211144",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "IWAN BUDIANTO, S.Sos",
        "24670130810000029",
        "6203080103910003",
        "199103012025211138",
        "Kapuas",
        "S-1 ILMU ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "SITI HADIJAH, S.Pd.I",
        "24670130820000575",
        "6203025108740003",
        "197408112025212016",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 2 Sei Asam"
    ],
    [
        "SRI AYU ARIATI",
        "24670130820000652",
        "6203015210740004",
        "197410122025212029",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "IWAN RIDWAN, S.Pd",
        "24670110810000140",
        "6203161511950001",
        "199511152025211109",
        "Ciamis",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sumber Agung"
    ],
    [
        "SRIMINIATI",
        "24670130820000412",
        "6203075309740001",
        "197409132025212021",
        "Tabalong",
        "SEKOLAH MENENGAH PERTAMA",
        "Pengelola Umum Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "JAINAH, S.Pd",
        "24670110820000334",
        "6203096907000001",
        "200007292025212049",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Manusup Hilir"
    ],
    [
        "JAINAL ABIDIN, S.Pd",
        "24670110810000218",
        "6203030806920002",
        "199206082025211160",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 3 Anjir Mambulau Tengah"
    ],
    [
        "JAINUDIN, A.Md.Kep",
        "24670140810000132",
        "6203010108980011",
        "199808012025211073",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Pengelola Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "JAKA RAHMAT",
        "24670130810000067",
        "6203021410850001",
        "198510142025211104",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "JAMAIN",
        "24670130810000215",
        "6203071804900003",
        "199004182025211111",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "JAMHARI",
        "24670130810000622",
        "6203010906890003",
        "198807282025211124",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "JAMIATURRAHMI, S.Pd",
        "24670110820000747",
        "6203045312910003",
        "199112132025212145",
        "Kapuas",
        "S-1/A-IV PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palampai"
    ],
    [
        "JAMILAH",
        "24670130820000406",
        "6203017110990006",
        "199910312025212065",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Inspektorat"
    ],
    [
        "JAMILAH, S.Pd",
        "24670110820000287",
        "6203055011930004",
        "199311102025212189",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Maju Bersama"
    ],
    [
        "JANO PRISWANTO",
        "24670130810000004",
        "6203061201850002",
        "198501122025211112",
        "Barito Selatan",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "JAWANTI, S.Pd",
        "24670110820000682",
        "6203115601950002",
        "199501162025212126",
        "Kapuas",
        "S-1 PENDIDIKAN KIMIA",
        "Guru Ahli Pertama",
        "SD Negeri 2 Pujon"
    ],
    [
        "SUJALMI",
        "24670130820000388",
        "6203015303740006",
        "197403132025212018",
        "Banyuwangi",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Laboratorium Kesehatan Daerah Kabupaten Kapuas"
    ],
    [
        "JEFRI PRANANDO",
        "24670130810000880",
        "6203012601920003",
        "199201262025211118",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "JELITA, S. Pd",
        "24670110820000503",
        "6203106507860002",
        "198310202025212105",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tumbang Randang"
    ],
    [
        "JEMMY KUSNANDAR",
        "24670620110000314",
        "6371041511900011",
        "199011152025211162",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "JENTA, S.Pd",
        "24670110820000726",
        "6271014503820002",
        "198203052025212080",
        "Gunung Mas",
        "S-1 BIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Kaburan"
    ],
    [
        "JERY HIDAYATULLAH, S.Kom",
        "24670130810000409",
        "6203012307950010",
        "199507232025211112",
        "Kapuas",
        "S-1 KOMPUTER",
        "Penata Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "TRIFINA AMBON",
        "24670130820000644",
        "6203116304740001",
        "197404232025212022",
        "Kapuas",
        "SEKOLAH MENENGAH TEKNOLOGI PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "JHONRETNO",
        "24670130810001023",
        "6203011309890006",
        "198909132025211131",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "JHONTRIS",
        "24670130810000510",
        "6211031005890001",
        "198905102025211207",
        "Pulang Pisau",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Mandau Talawang"
    ],
    [
        "JHOSUA FRISTISON ARTANA, S.M",
        "24670130810000199",
        "6203012508980003",
        "199808252025211062",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "JHUANDI",
        "24670130810000750",
        "6203022503910005",
        "198908012025211175",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DAVIT ADE LUKAS",
        "24300420110027659",
        "6203011207010004",
        "200107122025211027",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "DEA INDRIYANI, S.E",
        "24670130820000155",
        "6203015706010003",
        "200106172025212029",
        "Kapuas",
        "S-1 MANAJEMEN EKONOMI",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "JOHAR LATIFAH",
        "24670130820000330",
        "6203015208900016",
        "199108122025212181",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Murung"
    ],
    [
        "WAHYUDI",
        "24670130810000821",
        "6203082404740003",
        "197404242025211077",
        "Trenggalek",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DELANITA",
        "24670130820000240",
        "6203075411010003",
        "200111142025212021",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "JONATHAN IMANUEL DEVERGIA",
        "24670130810000983",
        "6203010608990003",
        "199908062025211066",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "JONI",
        "24670130810000759",
        "6203010611830001",
        "198311062025211077",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "JONI ISKANDAR",
        "24670130810000568",
        "6203011907880005",
        "198807192025211106",
        "Kapuas",
        "S-1 SEMUA JURUSAN",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "JONI ISMAIL",
        "24670130810000243",
        "6203010306940010",
        "199406032025211152",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Pasak Talawang"
    ],
    [
        "JONI SETIAWAN",
        "24670130810000373",
        "6203011606810004",
        "198203162025211097",
        "Banjarmasin",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "JONI WANTO L. LAJU, S. Pd",
        "24670110810000221",
        "6210042601820001",
        "198201262025211057",
        "Kapuas",
        "S-1 PENDIDIKAN SEJARAH",
        "Guru Ahli Pertama",
        "SMP NEGERI 3 TIMPAH"
    ],
    [
        "JONISON",
        "24670130810000676",
        "6203020606810001",
        "198106062025211153",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "JONNEDY",
        "24670130810000216",
        "6203011103810001",
        "198103112025211064",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "RUSDIAN NUR",
        "24670130810000535",
        "6203011712750003",
        "197512172025211041",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM TINGKAT PERTAMA",
        "Pengelola Umum Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "JOSHUA PUTRA ROBERTO",
        "24670130810000321",
        "6213052303980001",
        "199803232025211116",
        "Barito Utara",
        "SMK TEKNIK OTOMOTIF",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "JUANDA FERINUANDA",
        "24670130810000615",
        "6203011902980002",
        "199802192025211060",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ABU MARWAN",
        "24670130810000861",
        "6203011205750011",
        "197505122025211095",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "JULIANSON SAHIDAR, S.Kep",
        "24670130810000297",
        "6203011907920005",
        "199207192025211137",
        "Kapuas",
        "S-1 KEPERAWATAN",
        "Administrator Kesehatan Ahli Pertama",
        "Dinas Kesehatan"
    ],
    [
        "JULIANTI MILKA, A.Md.Keb",
        "24670140820000150",
        "6203017007940002",
        "199407302025212131",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "JULIANTO, S.Sos.H",
        "24670110810000066",
        "6271011809830003",
        "198309182025211080",
        "Kapuas",
        "S-1 FILSAFAT AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 2 Tapen"
    ],
    [
        "JULITA, Amd.Kep",
        "24670140820000040",
        "6203126007940001",
        "199407202025212165",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "JULKIFLI SETIAWAN",
        "24670130810000171",
        "6203022007850002",
        "198507202025211131",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "JUMADI, S.Pd.I.",
        "24670110810000232",
        "6203050802930002",
        "199302082025211111",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tabore"
    ],
    [
        "AHMAD YANI",
        "24670130810000979",
        "6203011909750005",
        "197505192025211062",
        "Tabalong",
        "KPG PAKET C",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "JUMAIDI",
        "24670130810000999",
        "6203010801850007",
        "198501082025211113",
        "Kapuas",
        "SD/SEDERAJAT",
        "Pengelola Umum Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "AHMADI",
        "24670130810000646",
        "6203090308750002",
        "197508032025211061",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "ANDI FAHRUDIN",
        "24670130810000771",
        "6203011605750004",
        "197505162025211090",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "JUMEDIE",
        "24670130810000526",
        "6203011705850009",
        "198505172025211147",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "JUMIATI, S.Pd.I",
        "24670110820000298",
        "6203016406900001",
        "199006242025212126",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 2 SEI GITA"
    ],
    [
        "JUMIATI, S.Pd.I",
        "24670110820000315",
        "6203016206900009",
        "199006222025212123",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Kuala Satu Atap"
    ],
    [
        "JUMIATI, S.pd.I",
        "24670110820000448",
        "6203055607990006",
        "199907162025212090",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mandomai"
    ],
    [
        "JUNAIDI",
        "24670130810000527",
        "6203022512820003",
        "198212252025211108",
        "Banjarmasin",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "JUNAIDI",
        "24670130810000778",
        "6203011705930007",
        "199305172025211139",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "JUNEDI SUTIAWAN, S.Pd",
        "24670110810000235",
        "6203052006880001",
        "198806202025211162",
        "Kapuas",
        "D-IV PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 4 Saka Mangkahai"
    ],
    [
        "JUNITA FITRI, S.Sos",
        "24670130820000020",
        "6203015706860003",
        "198606172025212089",
        "Kapuas",
        "S-1 ILMU ADMINISTRASI NIAGA",
        "Penata Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "KABRIANTO",
        "24670130810000945",
        "6203011202810009",
        "198102122025211105",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "KADEK JAKA ANDIRA, S.Pd",
        "24670110810000216",
        "6203010812980004",
        "199812082025211067",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Terusan Baguntan Raya"
    ],
    [
        "KALELUNI PUTRI WARDAHANI, S.Pd",
        "24670110820000264",
        "6204064508940007",
        "199408052025212168",
        "Barito Selatan",
        "S-1 PENDIDIKAN GURU OLAHRAGA DAN KESEHATAN",
        "Guru Ahli Pertama",
        "SD Negeri 2 Selat Tengah"
    ],
    [
        "ERAS PANEAH, S.T",
        "24670110810000153",
        "6203112210750001",
        "197510222025211039",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Tengah"
    ],
    [
        "KAMARIYAH",
        "24670130820000422",
        "6203036111860002",
        "198604212025212118",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "KAPRITHA ASI LAMBUT, S.Pd",
        "24670110820000529",
        "6203026004940002",
        "199404202025212163",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 2 Hampatung"
    ],
    [
        "KARDU, A.Md.Kep",
        "24670140810000023",
        "6203010802890008",
        "198902082025211132",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Timpah"
    ],
    [
        "DESVIRA EVADITA",
        "24670130820000459",
        "6203026112010001",
        "200112212025212020",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Sei Pasah"
    ],
    [
        "KARLINA",
        "24670130820000077",
        "6203066810910002",
        "199110282025212149",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "KARLINA PORWANTI",
        "24670130820000451",
        "6203015005900009",
        "199005102025212193",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "KARMILA, S. Kep",
        "24670130820000300",
        "6203024104940003",
        "199404012025212183",
        "Kapuas",
        "S-1 KEPERAWATAN",
        "Administrator Kesehatan Ahli Pertama",
        "UPT Puskesmas Barimba"
    ],
    [
        "FERDINAN",
        "24670130810000554",
        "6203012909750004",
        "197509292025211042",
        "Kapuas",
        "SMEA PERDAGANGAN",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "KARTIKA, S.Pd",
        "24670110820000510",
        "6203094104930001",
        "199102112025212112",
        "Kapuas",
        "S-1 PENDIDIKAN PENJASORKES",
        "Guru Ahli Pertama",
        "SMP Negeri 5 Mantangai Satu Atap"
    ],
    [
        "HARYADI",
        "24670130810000974",
        "6203022210750002",
        "197510222025211036",
        "Kapuas",
        "SEKOLAH MENENGAH TEKNOLOGI PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "KARYADI",
        "24670130810000267",
        "6203012106850004",
        "198506212025211119",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "KASPUL ANWAR, S.Kep.,Ners",
        "24670140810000073",
        "6203031509920001",
        "199209152025211157",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "Dinas Kesehatan"
    ],
    [
        "HENRY, S.Pi",
        "24670130810000091",
        "6203010809750002",
        "197509082025211063",
        "Kapuas",
        "S-1 MANAJEMEN SUMBER DAYA PERAIRAN",
        "Penata Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "KELLY",
        "24670130820000054",
        "6203055907880001",
        "198607192025212093",
        "Pulang Pisau",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "KETI LESTARI, A.Md.Keb",
        "24670140820000355",
        "6203106808000001",
        "200008042025212064",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Timpah"
    ],
    [
        "KHAIRIANSYAH",
        "24670130810000957",
        "6304131007870001",
        "198707102025211178",
        "Barito Kuala",
        "D-III ADMINISTRASI PENDIDIKAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "KHAIRIL AZMI",
        "24670130810000777",
        "6203010703850008",
        "198503072025211109",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HEMAWATI, S.Pd.I",
        "24670110820000118",
        "6203014412870001",
        "Belum ada nip",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SMP NEGERI 4 MANTANGAI SATU ATAP"
    ],
    [
        "KHAIRUDDIN",
        "24670130810000799",
        "6203040605970004",
        "199705062025211082",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "UPT Puskesmas Lupak"
    ],
    [
        "EGGREN PRADANA",
        "24670130810000814",
        "6203012604010002",
        "200104262025211033",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "KHAIRUL RAMADHAN, S.T.",
        "24670130810000392",
        "6203012203910005",
        "199103222025211108",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "KHAIRUNNISA, A. Md Kes",
        "24670120120000921",
        "6203014406970010",
        "199706042025212121",
        "Kapuas",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "KHALIDA NURIAH, S.Pd",
        "24670130820000615",
        "6203015501880011",
        "198801152025212109",
        "Banjarmasin",
        "PGPAUD",
        "Guru Ahli Pertama",
        "SD NEGERI 1 MALUEN"
    ],
    [
        "KHAMARUKMIN MARDIATI, S.M",
        "24670130820000665",
        "6203015408970011",
        "199708142025212124",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "KHATIMATUL HUSNA, S.Pd.I.",
        "24670110820000369",
        "6203047010900003",
        "199010302025212143",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Talekung Punai"
    ],
    [
        "KHOEROTUL MAFTUHAH, S.E",
        "24670120120000890",
        "3322034108910003",
        "199108012025212149",
        "Semarang",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "UPT Puskesmas Selat"
    ],
    [
        "KILA HARMONIKA",
        "24670130820000579",
        "6203106310860001",
        "198610232025212105",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "KISWANTO",
        "24670130810000588",
        "6203010809880015",
        "198809082025211151",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "KITING",
        "24670130810000933",
        "6203072705810001",
        "198105272025211087",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "KORIANTO, S.pd.I",
        "24670110810000155",
        "6203091510000002",
        "200010152025211050",
        "Kapuas",
        "S-1 PENDIDIKAN MATEMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Mantangai"
    ],
    [
        "IRWANSYAH",
        "24670130810000734",
        "6203011011750007",
        "197511102025211093",
        "Kapuas",
        "SMEA PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "FADLI",
        "24670130810000370",
        "6203011205010007",
        "200105122025211042",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "KRISDIANTO",
        "24670130810000702",
        "6203122312830002",
        "198312232025211107",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Hulu"
    ],
    [
        "KRISIA YEMIMA, S.Pd",
        "24670110820000592",
        "6203124109890001",
        "198909012025212143",
        "Barito Utara",
        "D-IV ADMINISTRASI PENDIDIKAN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Pinang"
    ],
    [
        "KRISIANDI K.U. SAWANG",
        "24670130810000717",
        "6203020101960002",
        "199601012025211216",
        "Banjarmasin",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "KRISMANTO",
        "24670130810000101",
        "6203010910910009",
        "199110092025211165",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "KRISTIAN ADINATA",
        "24670130810000640",
        "6213012808860001",
        "198608282025211139",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "KRISTIAN ADY CANDRA",
        "24670130810000039",
        "6203022802830006",
        "198302282025211120",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "KRISTIAN AGUSTINUS, SH",
        "24670130810000198",
        "6203011708890010",
        "198908172025211274",
        "Kapuas",
        "S-1 SARJANA HUKUM",
        "Penata Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "KRISTIAN SEPTIYADI",
        "24670130810000845",
        "6203022409990003",
        "199909242025211075",
        "Kotawaringin Barat",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "KRISTINE NATALINA, S. H",
        "24670130820000259",
        "6203016812870006",
        "198712282025212125",
        "Kapuas",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "KUMALA SARI, S.pd.I",
        "24670130820000295",
        "6203016912930005",
        "199312292025212135",
        "Kapuas",
        "S-1 PENDIDIKAN",
        "Penata Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "KAROLA, S.E",
        "24670130820000331",
        "6203025104750001",
        "197504112025212027",
        "Kapuas",
        "S-1 EKONOMI MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "KURNAIN",
        "24670130810000563",
        "6203030903820003",
        "198203092025211089",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "KURNIA OKTAVIA",
        "24670130820000611",
        "6203094707880007",
        "198807072025212199",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "FAHRIAN",
        "24670130810000925",
        "6203020802010002",
        "200102082025211030",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "KURNIANTO SUHADI",
        "24670130810000425",
        "6203082811900002",
        "199011282025211114",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "KURNIATI, S.Pd.I.",
        "24670110820000092",
        "6211026010910003",
        "199110202025212151",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SMP NEGERI 6 BATAGUH SATU ATAP"
    ],
    [
        "LENSA",
        "24670130810000700",
        "6203022411750002",
        "197511242025211037",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MARANATHA",
        "24670130820000496",
        "6203015906730002",
        "197506192025212028",
        "Barito Selatan",
        "D-III THEOLOGIA",
        "Pengelola Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "KUYUNG",
        "24670130820000605",
        "6203095709680001",
        "196809172025212007",
        "Pulang Pisau",
        "SMKK BUSANA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "LALA TRIANA",
        "24670130820000031",
        "6203014911850002",
        "198511092025212074",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "LAM'AH, A. Md. Keb",
        "24670120120001777",
        "6203096406940001",
        "199406242025212150",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Lamunti"
    ],
    [
        "MARIANA HARAHAP, S.E.",
        "24670130820000372",
        "6203011103750006",
        "197503112025212027",
        "Kotawaringin Timur",
        "S-1 EKONOMI MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "LANA ELIYANA",
        "24670130820000506",
        "6203015709960002",
        "199609172025212134",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Mambulau"
    ],
    [
        "LASMINI",
        "24670130820000375",
        "6203016005840005",
        "198405202025212096",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "LASNO",
        "24670130810000754",
        "6203091408910002",
        "199108142025211139",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "LAURA, S.AP",
        "24670130820000044",
        "6203026411930001",
        "199311242025212117",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Lama"
    ],
    [
        "LEDI",
        "24670130810000043",
        "6203012708820011",
        "198208272025211079",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "LEGAWATI, S.Pd",
        "24670110820000614",
        "6212026805980002",
        "199805282025212113",
        "Murung Raya",
        "S-1 MANAJEMEN PENDIDIKAN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pujon"
    ],
    [
        "LELI, S.Pd",
        "24670110820000639",
        "6203144101990001",
        "199901012025212178",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 2 Jangkang"
    ],
    [
        "MARIATUL KIPTIAH, S.Pd",
        "24670110820000727",
        "6203046005760001",
        "197505202025212034",
        "Kapuas",
        "S-1/A-IV PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BANDAR MEKAR"
    ],
    [
        "LENI RUSMAWATI, S.Pd",
        "24670110820000780",
        "6203094805890002",
        "198905082025212130",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SMP Negeri 5 KAPUAS TENGAH SATU ATAP"
    ],
    [
        "LENI UTARY",
        "24670130820000390",
        "6203025212890003",
        "198912122025212183",
        "Kapuas",
        "SLTA",
        "Operator Layanan Operasional",
        "Kelurahan Dahirang"
    ],
    [
        "LENIE, S,Pd",
        "24670110820000709",
        "6203094507920005",
        "199302232025212140",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Harapan Jaya"
    ],
    [
        "MASRADI",
        "24670130810000513",
        "6203011202750004",
        "197502122025211078",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "LEONAR MARKUS META",
        "24670130810000772",
        "6203012305810005",
        "198105232025211085",
        "Sumba Timur",
        "SMU",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "LESTARI, S. Pd",
        "24670110820000730",
        "6203086203950001",
        "199503222025212126",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD NEGERI 1 MALUEN"
    ],
    [
        "LESTARIYANI",
        "24670130820000413",
        "6203056208930002",
        "199302282025212139",
        "Kapuas",
        "SMA/MA (IPA)",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MAYASISKA, S.Pi",
        "24670110820000385",
        "6203056609750002",
        "197509262025212020",
        "Kapuas",
        "S-1 PERIKANAN",
        "Guru Ahli Pertama",
        "SMP NEGERI 4 KAPUAS BARAT SATU ATAP"
    ],
    [
        "LIA KARLINA AYU, S.T",
        "24670110820000731",
        "6203115601940003",
        "199401162025212121",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Guru Ahli Pertama",
        "SMP NEGERI 3 PASAK TALAWANG SATU ATAP"
    ],
    [
        "LIANI DIAH KESUMA, A.Md. AK",
        "24670620120000503",
        "6203014508980009",
        "199808052025212129",
        "Banjarbaru",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "UPT Puskesmas Palingkau"
    ],
    [
        "FELIX SARDIUS FERNANTO",
        "24670130810000310",
        "6203012906010006",
        "200106292025211037",
        "Banjarmasin",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "LIDIASTUTY, S.E",
        "24670130820000283",
        "6271036107860003",
        "198607212025212107",
        "Palangka Raya",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "LIDYA BETRIANA, S.I.Kom",
        "24670110820000572",
        "6271034208880003",
        "198808022025212128",
        "Palangka Raya",
        "S-1 ILMU KOMUNIKASI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Timpah Satu Atap"
    ],
    [
        "LIDYA ELISABET, S.T.",
        "24670120120001375",
        "6203014608950007",
        "199508062025212134",
        "Palangka Raya",
        "S-1 TEKNIK LINGKUNGAN",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "LIDYA THITIANA, S.Pd",
        "24670110820000715",
        "6203016910920002",
        "199210292025212122",
        "Kapuas",
        "S-1 PENDIDIKAN GURU TAMAN KANAK-KANAK",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lungkuh Layang"
    ],
    [
        "LILAWATI, S.Pd",
        "24670110820000669",
        "6203104607990004",
        "199405182025212169",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Lawang Kajang"
    ],
    [
        "MUHAMAD FADLI FAISAL",
        "24670130810000403",
        "6203022803750001",
        "197503282025211037",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "LILIK WAHYUNI, S.Kep",
        "24670130820000324",
        "6301105201900001",
        "199202152025212154",
        "Kapuas",
        "S-1 KEPERAWATAN",
        "Administrator Kesehatan Ahli Pertama",
        "UPT Puskesmas Tamban Catur"
    ],
    [
        "LILING PUSPITA SARI",
        "24670130820000551",
        "6203024902880002",
        "198802092025212131",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "LILIS",
        "24670130820000069",
        "6203016408900006",
        "199008242025212113",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "LILIS, S.Pd.I",
        "24670110820000007",
        "6203015706890004",
        "198906072025212179",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Pulau Kupang"
    ],
    [
        "LILIUM SESILIA",
        "24670130820000550",
        "6203016607810005",
        "198107262025212039",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "LINA LESTARI, S.Sos",
        "24670110820000671",
        "6271034401930007",
        "199301042025212121",
        "Kapuas",
        "S-1 SOSIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 3 Lawang Kajang"
    ],
    [
        "LINA NORJANNAH, S.Pd",
        "24670110820000648",
        "6271014607000003",
        "200007062025212080",
        "Palangka Raya",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Jangkang"
    ],
    [
        "MUNTI",
        "24670130820000636",
        "6203096901750003",
        "197501292025212018",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "LINDA ARDIANI",
        "24670130820000072",
        "6203055201950001",
        "199501122025212140",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "LINDA ASTUTI",
        "24670130820000081",
        "6203026809890002",
        "198909282025212113",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "LINGSIA WULANDARI, S.M",
        "24670130820000052",
        "6203015409970006",
        "199709142025212121",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "LINI BUDIARTI, A.Md",
        "24670130820000087",
        "6203064602980001",
        "199802062025212108",
        "Kapuas",
        "D-III ADMINISTRASI BISNIS",
        "Pengelola Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "NOOR WIYADI",
        "24670130810000250",
        "6203011902750004",
        "197502192025211039",
        "Kapuas",
        "SMA A.2/BIOLOGI",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "LISA AGUS SUSAN",
        "24670130820000113",
        "6203014308890006",
        "198908032025212145",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "LISA APRILIANI",
        "24670130820000129",
        "6203014204990003",
        "199904022025212094",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "LISA SORAYA, S.Pd",
        "24670110820000498",
        "6203124409970002",
        "199706192025212119",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Hurung Tabengan"
    ],
    [
        "RIADI",
        "24670130810000922",
        "6203012810750005",
        "197510282025211041",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "LISGINA ANISA",
        "24670130820000510",
        "6203065904990006",
        "199904192025212077",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Baru"
    ],
    [
        "LISNA ASYARAH, S.Pd.I",
        "24301220120075096",
        "6203014909920007",
        "199209082025212119",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "LISNAWATI, S.Sos",
        "24670130820000143",
        "6203014202890011",
        "198902022025212189",
        "Kapuas",
        "S-1 ILMU PEMERINTAHAN",
        "Penata Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "LISNAWATI, S.pd",
        "24670110820000451",
        "6203044711920002",
        "199211112025212198",
        "Kapuas",
        "S-1 / AKTA IV PAI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Batanjung"
    ],
    [
        "LIVERSIANA, S.E",
        "24670620120000329",
        "6203017010930002",
        "199310302025212132",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "UPT Puskesmas Melati"
    ],
    [
        "LOLO KARINA NAINGGOLAN, S.Kom",
        "24670110820000670",
        "6203114712920002",
        "199212072025212141",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Tengah"
    ],
    [
        "LUKAS ANDERSON",
        "24670130810000176",
        "6203012901960003",
        "199601292025211110",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "LUKI, S.H",
        "24670130820000321",
        "6271016506920001",
        "199206252025212173",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "LULU RISSI CHAIRUNNISA, A.Md.Kep",
        "24670140820000359",
        "6203024308990004",
        "199908032025212112",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "LUSI MADO HENDRIK",
        "24670130810000737",
        "6203020909830001",
        "198309192025211091",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "GEOFANI MARDI SILVANUS KURNIA",
        "24670130810000203",
        "6203020303010002",
        "200103032025211046",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "M NOOR MUSTAFA",
        "24670130810000788",
        "6203080811930003",
        "199311082025211114",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "GERRY ANDI SAPUTRA",
        "24670130810000177",
        "6203011708010012",
        "200108172025211048",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "M. ARIFIN",
        "24670130810000986",
        "6203090906870004",
        "198706092025211157",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "M. FAJRIN",
        "24670130810000103",
        "6203011603900003",
        "199003162025211125",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "M. IRWAN S, S.E",
        "24670130810000003",
        "6203010605900005",
        "199005262025211102",
        "Kapuas",
        "S-1 EKONOMI MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "HARIYANTI",
        "24670130820000350",
        "6203017009010001",
        "200109302025212029",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "M. KAMIL",
        "24670130810000234",
        "6203041109830001",
        "198309112025211094",
        "Palu",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "M. KHAIR",
        "24670130810000656",
        "6203062705990001",
        "199908142025211072",
        "Kapuas",
        "MA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "M. LOVE",
        "24670130810000452",
        "6306051312860001",
        "198612132025211115",
        "Hulu Sungai Selatan",
        "SMK MANAJEMEN BISNIS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "INDAH PORWANTI",
        "24300420120040132",
        "6203086910010001",
        "200110292025212030",
        "Kapuas",
        "SMK AKUNTANSI DAN KEUANGAN",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "SARINAH, S.Pd.I",
        "24670110820000196",
        "6203044102750004",
        "197502012025212031",
        "Barito Kuala",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 TAMBAN JAYA"
    ],
    [
        "M. RIZAL KURNIAWAN, S.Kom",
        "24670130810000260",
        "6203012108890004",
        "198908212025211138",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "M. RIZKIA RAHMAN, SE",
        "24670130810000345",
        "6371041212880004",
        "198812122025211184",
        "Banjarmasin",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "M. SALIHIN",
        "24670130810000654",
        "6203033008990003",
        "199907032025211065",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Kelurahan Selat Barat"
    ],
    [
        "M. SUKARDI",
        "24670130810000139",
        "6203010104850005",
        "198504012025211140",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "M. SYAHRIL FIKRI, A.Md. Kep.",
        "24670140810000125",
        "6203012309990005",
        "199909232025211059",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI IPS",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "JIMIE OKTAVIAN",
        "24670130810000089",
        "6203012010010008",
        "200110292025211032",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "M.ARSYAD",
        "24670130810000079",
        "6203012611920004",
        "199204262025211125",
        "Hulu Sungai Selatan",
        "SMK TEKNIK KOMPUTER DAN INFORMATIKA",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "M.FAJAR TAUFIK, S.Pi",
        "24670020110000455",
        "6203010809950012",
        "199509082025211099",
        "Kapuas",
        "S-1 PEMANFAATAN SUMBER DAYA PERIKANAN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "JODI PANCARAYANO, S.I.Kom.",
        "24670820110000337",
        "6203013007010004",
        "200107302025211031",
        "Kapuas",
        "S-1 ILMU KOMUNIKASI",
        "Penata Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "SYARKIAH, S.Pd.I",
        "24670110820000288",
        "6203035607750001",
        "197507162025212024",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Serapat Timur"
    ],
    [
        "M.SAINI, S.Pd",
        "24670110810000076",
        "6203040905970002",
        "199607142025211113",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Batanjung"
    ],
    [
        "TRI MULIYANINGSIH",
        "24670130820000393",
        "6203015101750007",
        "197501112025212016",
        "Kapuas",
        "SMEA KEUANGAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MADE ARDIASE, S.E.",
        "24670130810000405",
        "6203011711940003",
        "199411172025211144",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "UPT Puskesmas Terusan Tengah"
    ],
    [
        "KHAIRIYANA, S.Pd.I",
        "24670110820000384",
        "6203076005860004",
        "Belum ada nip",
        "Kapuas",
        "S-1 PAI",
        "Guru Ahli Pertama",
        "SD NEGERI 1 CEMARA LABAT"
    ],
    [
        "YAN MARO",
        "24670130810000647",
        "6203021803750001",
        "197503182025211034",
        "Kapuas",
        "SEKOLAH MENENGAH EKONOMI ATAS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MADE INDRIANI, S.Kom",
        "24670130820000136",
        "6203011509940008",
        "199409152025212148",
        "Kotawaringin Timur",
        "S-1 ILMU KOMPUTER",
        "Penata Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "MADE SULADRA, S.Pd",
        "24670110810000096",
        "6203080801920001",
        "199201082025211122",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bina Karya"
    ],
    [
        "MAHDALENA SHELVIA, S.Kep.,Ners.",
        "24670140820000229",
        "6203016704910005",
        "199104272025212149",
        "Banjarmasin",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Pulau Telo"
    ],
    [
        "YOTO",
        "24670130810001010",
        "6203091304750005",
        "197504132025211047",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "KHUSNUL KHATIMAH, S.Pd",
        "24670110820000677",
        "6203016706980014",
        "Belum ada nip",
        "Banjarmasin",
        "S-1 / AKTA IV BIMBINGAN DAN KONSELING",
        "Guru Ahli Pertama",
        "SD NEGERI 1 JAJANGKIT"
    ],
    [
        "MAHMUDAH, S.Pd.I",
        "24670110820000769",
        "6203015701910012",
        "199101172025212158",
        "Kapuas",
        "S-1 PENDIDIKAN ILMU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Masaran"
    ],
    [
        "MAHMUDI",
        "24670130810000919",
        "6203020104820003",
        "198204012025211107",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MAHMUDIN",
        "24670130810000923",
        "6203070707880010",
        "198807072025211236",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MAHRIFAH, S. Kep",
        "24670130820000296",
        "6203035201930002",
        "199301122025212118",
        "Kapuas",
        "S-1 KEPERAWATAN",
        "Administrator Kesehatan Ahli Pertama",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "MAHYUNITA ARLIANA",
        "24670130820000586",
        "6203035006830008",
        "198306102025212116",
        "Kapuas",
        "PERSAMAAN SLTA",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MAIMUNAH, S.Pd.I",
        "24670110820000270",
        "6203036310870002",
        "198710232025212114",
        "Banjar",
        "S-1 / AKTA IV PAI",
        "Guru Ahli Pertama",
        "SD Negeri 3 Anjir Mambulau Timur"
    ],
    [
        "MAJUNO",
        "24670130810000892",
        "6203072603940001",
        "199404262025211134",
        "Kapuas",
        "SMK AGRIBISNIS PRODUKSI TERNAK",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "YUNIE",
        "24670130820000477",
        "6203026706750002",
        "197502102025212024",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MALINA, S.Pd",
        "24670110820000642",
        "6203096503910001",
        "199003252025212117",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Kapar"
    ],
    [
        "MANUEL DACOSTA, S.T.",
        "24670120110000511",
        "6271010604930006",
        "199304062025211152",
        "Palangka Raya",
        "S-1 ARSITEKTUR",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MAR'ATUS SOLEKAH",
        "24670130820000446",
        "6203054503940003",
        "199403052025212152",
        "Kutai Kartanegara",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ABDUL BASID, S.Pd",
        "24670110810000256",
        "6303052308760008",
        "197608222025211043",
        "Banjar",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lupak Dalam"
    ],
    [
        "ANDI ANANIAS, S.AP",
        "24670130810000147",
        "6203020807760002",
        "197607082025211053",
        "Kapuas",
        "S-1 ILMU ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MARDANI, S.Fil.H",
        "24670110810000238",
        "6203091709900001",
        "199009172025211136",
        "Kapuas",
        "S-1 FILSAFAT AGAMA HINDU",
        "Guru Ahli Pertama",
        "SMP NEGERI 14 MANTANGAI SATU ATAP"
    ],
    [
        "MARDIANUS, S.Pd.AH",
        "24670110810000267",
        "6203102209900003",
        "199009222025211119",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Timpah Satu Atap"
    ],
    [
        "MARDOLEN",
        "24670130810000981",
        "6211051206930001",
        "199306122025211144",
        "Pulang Pisau",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "MARFULIAH DESY ARISANTI, A.Md",
        "24670130820000175",
        "6203036312900004",
        "199012232025212091",
        "Tapin",
        "D-III TEKNIK SIPIL",
        "Pengelola Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MARGARETH EMA KALE PAREHA, S.Si",
        "24670020120002595",
        "6203015703920008",
        "199203172025212118",
        "Palangka Raya",
        "S-1 KIMIA",
        "Penata Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MARGONO, S.Pd",
        "24670110810000201",
        "6271012304880002",
        "198804232025211127",
        "Kapuas",
        "S-1 PENDIDIKAN JASMANI KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Murung Satu Atap"
    ],
    [
        "MARIA LAITARE, S.Kep.,Ners",
        "24670140820000092",
        "6211046209930001",
        "199309202025212146",
        "Pulang Pisau",
        "NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "MARIA ULPAH, S.Pd.I",
        "24670110820000305",
        "6203066608860002",
        "198705132025212120",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP NEGERI 3 DADAHUP"
    ],
    [
        "MARIANA, S.Pd",
        "24670110820000411",
        "6203125303000005",
        "200003132025212084",
        "Kapuas",
        "S-1 GURU KELAS SD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tumbang Sirat"
    ],
    [
        "ASDIANNOOR",
        "24670130810000828",
        "6203010202760004",
        "197602042025211067",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Inspektorat"
    ],
    [
        "AUDAWINA",
        "24670130820000485",
        "6203016111760011",
        "197611212025212024",
        "Kapuas",
        "SMEA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DARYONO",
        "24670130810000599",
        "6203010704760005",
        "197604072025211093",
        "Purworejo",
        "SEKOLAH TEKNOLOGI MENENGAH",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "MARIYANI",
        "24670130820000534",
        "6203086203890003",
        "198903222025212121",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "EFENDI RIANTO",
        "24670130810000673",
        "6203022506760003",
        "197606252025211077",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MARKAMAH",
        "24670130820000346",
        "6203014501860008",
        "198601052025212097",
        "Banjarmasin",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "MARKUS",
        "24670130810000033",
        "6203020203870002",
        "198702022025211197",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "EVITRINITA",
        "24670130820000055",
        "6203027107760002",
        "197607312025212020",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MARKYAH, S.Pd",
        "24670110820000360",
        "6203096104990006",
        "199904212025212119",
        "Kapuas",
        "A-V / AKTA MENGAJAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Manusup Hilir"
    ],
    [
        "FAHRUDIN",
        "24670130810000802",
        "6203010606760008",
        "197606062025211131",
        "Hulu Sungai Selatan",
        "PAKET C",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MARLIANI, S.pd.I",
        "24670110820000578",
        "6203135009680001",
        "196809102025212018",
        "Katingan",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Warna Sari"
    ],
    [
        "MARLINA, S.Sos",
        "24670130820000266",
        "6203075703870003",
        "198704172025212113",
        "Kubu Raya",
        "S-1 ILMU PEMERINTAHAN",
        "Penata Layanan Operasional",
        "Kelurahan Barimba"
    ],
    [
        "MARLINAH",
        "24670130820000545",
        "6271035703870005",
        "198703172025212099",
        "Kotawaringin Barat",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MARLIYANTI",
        "24670130820000537",
        "6203016409890006",
        "198909242025212126",
        "Banjarmasin",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kelurahan Selat Dalam"
    ],
    [
        "MARMI YUNIARTI, S.Sos",
        "24670130820000099",
        "6203025006820001",
        "198206102025212107",
        "Kapuas",
        "S-1 ILMU PEMERINTAHAN",
        "Penata Layanan Operasional",
        "Kecamatan Bataguh"
    ],
    [
        "MARTA KONI BALI, S.Pd.K",
        "24670110820000774",
        "6203147112900001",
        "199012312025212268",
        "Sumba Barat",
        "S-1 KEPENDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Balai Banjang"
    ],
    [
        "FAIDATUL HUSNA, S.Pd.I",
        "24670110820000265",
        "6203035703760001",
        "197603172025212014",
        "Banjarmasin",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Guru Ahli Pertama",
        "SD Negeri 2 Anjir Mambulau Timur"
    ],
    [
        "MARTIN KURNIAWAN, A.Md.Kep",
        "24670140810000065",
        "6203020403880003",
        "198803042025211133",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "MARTINA, S.Pd",
        "24670130820000177",
        "6271036703870003",
        "198703272025212120",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "MARTO",
        "24670130810000887",
        "6203010104900008",
        "199004012025211155",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "MASDAN",
        "24670130810000591",
        "6203060112990006",
        "199912012025211047",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "FERNANDUS",
        "24670130810000608",
        "6203022502760002",
        "197602252025211035",
        "Kapuas",
        "SLTA",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MASLIHAT",
        "24670130820000548",
        "6203144204900001",
        "198907142025212155",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HENSU RIWUN",
        "24670130820000489",
        "6203016107760004",
        "197607212025212028",
        "Pulang Pisau",
        "SMKK",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HIKMAH, S,Pd.I",
        "24670110820000064",
        "6203045012760002",
        "197612102025212040",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 SEI BAKUT"
    ],
    [
        "MASTA KARINA, S.T",
        "24670110810000118",
        "6205050405840002",
        "198405042025211144",
        "Kotawaringin Timur",
        "S-1 TEKNIK SIPIL",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Tengah"
    ],
    [
        "MATIUS EKAHARAP",
        "24670130810000347",
        "6203012304880001",
        "198804232025211123",
        "Barito Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MATNOR",
        "24670130810000753",
        "6203040909990010",
        "199909092025211173",
        "Kapuas",
        "MA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MAULANA IBRAHIM",
        "24670130810000209",
        "6203012507870006",
        "198707252025211148",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "KORNELIUS KAHARAP",
        "24670130810000690",
        "6203022210010004",
        "200110222025211037",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Dahirang"
    ],
    [
        "MAULANA KHAMBALI",
        "24670130810000883",
        "1401100106980005",
        "199806012025211096",
        "Kampar",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "LIDIA",
        "24670130820000527",
        "6203106710010002",
        "200110272025212030",
        "Kapuas",
        "SMK AKUNTANSI DAN KEUANGAN LEMBAGA",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "M. RAMADANI",
        "24670130810000895",
        "6203072411010001",
        "200111242025211033",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "MAWARNI",
        "24670130820000203",
        "6203014301850011",
        "198501032025212081",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MAWARNI SAPUTRI SIAHAAN, A. Md. RO",
        "24670120120000703",
        "6203017006980006",
        "199808302025212092",
        "Kapuas",
        "D-III REFRAKSI OPTISI",
        "Refraksionis Optisien Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MAYA",
        "24670130820000420",
        "6203074510940002",
        "199410292025212119",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Lama"
    ],
    [
        "MAYA ASTRIANA, S.Pd",
        "24670110820000566",
        "6203085306990002",
        "199906132025212101",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 2 Bungai Jaya"
    ],
    [
        "MAYANG SARI, S.E",
        "24670110820000662",
        "6203045906030003",
        "199907152025212116",
        "Kapuas",
        "S-1 EKONOMI SYARIAH",
        "Guru Ahli Pertama",
        "SD NEGERI 1 TAMBAN LUPAK"
    ],
    [
        "JONNIKSO",
        "24670130810000927",
        "6203010508760013",
        "197608052025211079",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MAYATUN NUFUS, S.Pd",
        "24670110820000649",
        "6203015012010004",
        "200112102025212039",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Terusan Mulya"
    ],
    [
        "MEGA MAULIDA RAJAK",
        "24670130820000047",
        "6203015810990003",
        "199910182025212086",
        "Kapuas",
        "SMK AKUNTANSI DAN KEUANGAN",
        "Operator Layanan Operasional",
        "Kecamatan Pasak Talawang"
    ],
    [
        "MEGA MUSTIKA, S.E.",
        "24670130820000091",
        "6203016308920003",
        "199208232025212101",
        "Banjarmasin",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "MARKUS ANIANUS DJUAN DE ROSARI",
        "24670130810000464",
        "6203012504760003",
        "197604252025211053",
        "Flores Timur",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Kecamatan Dadahup"
    ],
    [
        "MEI IKA TINDUH",
        "24670130820000262",
        "6203015405880001",
        "198805142025212138",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "MEI LIANASARI",
        "24670130820000140",
        "6203086605950002",
        "199505152025212206",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MEIDINA EFRITA",
        "24670130820000045",
        "6203014905980003",
        "199805092025212112",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "MEILA, S.Kep",
        "24670130820000573",
        "6271025305930001",
        "199305132025212135",
        "Pulang Pisau",
        "S-1 KEPERAWATAN",
        "Administrator Kesehatan Ahli Pertama",
        "UPT Puskesmas Timpah"
    ],
    [
        "MEILANTIKA PAJJATA, S.H.",
        "24670120120001122",
        "6203016705950007",
        "199505272025212129",
        "Kapuas",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Kelurahan Selat Barat"
    ],
    [
        "MEIMO PRANATA",
        "24670130810000756",
        "6203070705970005",
        "199705072025211090",
        "Kapuas",
        "SMK AGRIBISNIS TANAMAN PANGAN DAN HORTIKULTURA",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MEKO FRIANNATA SIGIT, S.Ars",
        "24670130810001044",
        "6203011905910005",
        "199105192025211132",
        "Kapuas",
        "S-1 ARSITEKTUR",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MELAN YAMANTI",
        "24670130820000004",
        "6203016101880003",
        "198801212025212102",
        "Gunung Mas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "MELANI, S.E",
        "24670620120000345",
        "6203095709920005",
        "199209172025212136",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MELANIA RAHMAWATI",
        "24670130820000306",
        "6203016209990008",
        "199909222025212097",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "UPT Puskesmas Panamas"
    ],
    [
        "MELDA HERLIANTI, A.Md.A.K",
        "24670120120001409",
        "6211065601950001",
        "199501162025212122",
        "Kapuas",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "UPT Puskesmas Panamas"
    ],
    [
        "MELINDA",
        "24670130820000400",
        "6203025304870002",
        "198810032025212131",
        "Bulungan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MELISA MARDINA, S.T.",
        "24670720120001645",
        "6203015603900016",
        "199003162025212126",
        "Kapuas",
        "S-1 TEKNIK PERTAMBANGAN",
        "Penata Layanan Operasional",
        "Subbagian Keuangan dan Aset"
    ],
    [
        "MELKI S. SELAN",
        "24670130810000638",
        "6203012305940007",
        "199405232025211129",
        "Kapuas",
        "SEKOLAH MENENGAH TEOLOGI KRISTEN",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana"
    ],
    [
        "MELKY, S.E.",
        "24670130810000151",
        "6203052504960001",
        "199604252025211113",
        "Kapuas",
        "D-IV EKONOMI AKUNTANSI",
        "Penata Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "MELLIYANI, S.Pd",
        "24670110820000643",
        "6203015508020002",
        "200208152025212009",
        "Kapuas",
        "S-1/A-IV PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 6 Pulau Kupang"
    ],
    [
        "MELLYVERA DWIKE ROSANTI, S.E",
        "24670130820000304",
        "6211015609880002",
        "198809162025212116",
        "Jember",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "MARTHIN",
        "24670130810000231",
        "6203021603760003",
        "197603162025211061",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "MEMO SEPTIAWAN",
        "24670130810000650",
        "6203011809950005",
        "199509182025211113",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Inspektorat"
    ],
    [
        "MERI",
        "24670130820000342",
        "6203166505850001",
        "198505252025212169",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MERIDIANI",
        "24670130820000348",
        "6203096611860001",
        "198611262025212095",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "MERRY CHRISTINA, SH",
        "24670130820000258",
        "6203016412950005",
        "199512242025212154",
        "Palangka Raya",
        "S-1 HUKUM (BERGELAR S.H.)",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MERSIE, S.Pd",
        "24670110820000722",
        "6203105507000004",
        "200003062025212066",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Gawing"
    ],
    [
        "MERY FAZERI",
        "24670130810000755",
        "6203071605910002",
        "199105162025211119",
        "Kapuas",
        "SMA ILMU ALAM",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MERYANA",
        "24670130820000265",
        "6203075605830002",
        "198305162025212082",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "METI, S.Pd",
        "24670110820000757",
        "6203084211940001",
        "199405212025212153",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tumbang Bukoi"
    ],
    [
        "MEYSA NETANIA, S.E",
        "24670220120005261",
        "6203016105970005",
        "199705212025212086",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Kelurahan Selat Hilir"
    ],
    [
        "MEGAWATI",
        "24670130820000290",
        "6203014606760015",
        "197606062025212065",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MUHAMAD FUAD",
        "24670130810001018",
        "6203011201010010",
        "200101122025211039",
        "Kapuas",
        "SMK TEKNIK KOMPUTER DAN JARINGAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MIFTAH HIDAYAT, S.Kom",
        "24670110810000269",
        "6271032408880003",
        "198808242025211137",
        "Palangka Raya",
        "S-1 SISTEM INFORMASI",
        "Guru Ahli Pertama",
        "SMP NEGERI 14 MANTANGAI SATU ATAP"
    ],
    [
        "MIGRAN JISRA PENUAM, S.Pd.K",
        "24670110810000293",
        "5302211905910001",
        "199105192025211133",
        "Timor Tengah Selatan",
        "S-1 PENDDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SMP NEGERI 12 MANTANGAI SATU ATAP"
    ],
    [
        "MELY YUSTIANA",
        "24670130820000368",
        "6203025306760003",
        "197606132025212036",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "MILLA IRONA, S.Pd",
        "24670110820000478",
        "6203104303980002",
        "199803032025212135",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Lawang Kajang"
    ],
    [
        "MILLAH, S.Pd",
        "24670110820000243",
        "6203054111970001",
        "199711012025212111",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Saka Tamiang"
    ],
    [
        "MINA, S.Pd.I",
        "24670110820000122",
        "6203045005880001",
        "199005102025212220",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lupak Dalam"
    ],
    [
        "NORDIANSYAH",
        "24670130810000415",
        "6203100603760001",
        "197603062025211059",
        "Kapuas",
        "SLTA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MIRA MINARTY",
        "24670130820000189",
        "6203016201890002",
        "198901222025212085",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MIRANDA MEILIN",
        "24670130820000048",
        "6203055405980001",
        "199805142025212120",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "MIRAWATI, A.Md.Kep",
        "24670140820000051",
        "6203056803910002",
        "199103282025212112",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "MIRNA, A. Md. Keb",
        "24670120120001607",
        "6203074601960003",
        "199601062025212117",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Sei Tatas"
    ],
    [
        "MIRZA AGUNG JOBELLIANTO, S.Sos",
        "24670130810000251",
        "6371042509840003",
        "198409252025211115",
        "Banjarmasin",
        "S-1 FISIPOL",
        "Penata Layanan Operasional",
        "Bidang Pembinaan SD"
    ],
    [
        "MISHERAWATI, A.Md.Kep",
        "24670130820000483",
        "6203015110890008",
        "198910112025212142",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "MISNA, S.E",
        "24670130820000325",
        "6203095005910003",
        "199105102025212176",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "MISNA, S.Pd",
        "24670110820000766",
        "6203084409020003",
        "200304042025212014",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Tambun Raya"
    ],
    [
        "MISNAH, S.Pd.I",
        "24670110820000556",
        "6203045001860001",
        "198601102025212100",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Baranggau"
    ],
    [
        "MISRAN",
        "24670130810000617",
        "6203040303920003",
        "199203032025211189",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MISRANI WIJAYA",
        "24670130810000847",
        "6203011707910019",
        "199107172025211167",
        "Kapuas",
        "SMP",
        "Pengelola Umum Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MISRIANTHO, S.Pd",
        "24670110810000207",
        "6203112504850002",
        "198504252025211113",
        "Kapuas",
        "S-1 PENDIDIKAN ILMU PENGETAHUAN SOSIAL",
        "Guru Ahli Pertama",
        "SD Negeri 1 Hurung Tampang"
    ],
    [
        "MITA, S.pd.I",
        "24670110820000729",
        "6203104204990001",
        "199904022025212100",
        "Kapuas",
        "S-1/A-IV PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 1 Aruk"
    ],
    [
        "MITA ROSALINA",
        "24670130820000260",
        "6203016907890002",
        "198907292025212135",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MITRIANI, Amd.Kep",
        "24670140820000375",
        "6211075903980001",
        "199803192025212080",
        "Pulang Pisau",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MOCHAMAD YUSUF",
        "24670130810000371",
        "6203012704990002",
        "199904262025211059",
        "Kapuas",
        "SMA/MA (IPA)",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "MOH. ANDI, S.pd.I",
        "24670110810000215",
        "6303080107960249",
        "199607012025211139",
        "Kotabaru",
        "S-1 PENDIDIKAN GEOGRAFI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Basarang"
    ],
    [
        "NOTO SUSANTO",
        "24670130810000593",
        "6203060806760001",
        "197606082025211061",
        "Kapuas",
        "SMT PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MOHAMMAD RAFI'I",
        "24670130810000437",
        "6203011209870007",
        "198709122025211172",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "MONAH, S.Pd",
        "24670110820000708",
        "6304044701970001",
        "199701072025212135",
        "Barito Kuala",
        "S-1 PENDIDIKAN TEKNOLOGI INFORMASI",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Hilir"
    ],
    [
        "MONICA MAGDALENA, S.E",
        "24670620120000528",
        "6203015401980004",
        "199801142025212101",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MONICA MAY SHAILLA, S.H.",
        "24670130820000212",
        "6203016305920010",
        "199205232025212156",
        "Kapuas",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MONIKA, A.Md.Kep.",
        "24670140820000328",
        "6211074107000004",
        "200012312025212050",
        "Pulang Pisau",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "MONIKA INDAH, S.Pd",
        "24670110820000420",
        "6203105008980002",
        "199808102025212115",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SMP Negeri 7 Timpah Satu Atap"
    ],
    [
        "MONIKA MARLENA, S.Kom",
        "24670120120001632",
        "6203016303900008",
        "199003232025212194",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MUCHTAR, A.Md",
        "24670130810000390",
        "6203041503830003",
        "198303152025211166",
        "Kapuas",
        "D-III BUDIDAYA PERIKANAN",
        "Pengelola Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "MUDMAINAH, S.Pd",
        "24670110820000473",
        "6202014305930001",
        "199305032025212158",
        "Kotawaringin Timur",
        "S-1 PENDIDIKAN GURU ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Masaha"
    ],
    [
        "MUHAMAD BANI SETIOKO, S.Pd.,",
        "24670110810000298",
        "3315010110990005",
        "199910012025211070",
        "Grobogan",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tanjung Rendan"
    ],
    [
        "MUHAMAD EFENDI, S.P.d",
        "24670110810000168",
        "6203040810910005",
        "199110082025211114",
        "Kapuas",
        "S-1 PENDIDIKAN EKONOMI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Saka Binjai"
    ],
    [
        "MUHAMAD EFENDI",
        "24670130810000576",
        "6203010103890014",
        "198903012025211144",
        "Kapuas",
        "MA IPS",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MUHAMAD EPENDI",
        "24670130810000789",
        "6203070411860004",
        "198611042025211098",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "NURATI",
        "24670130820000068",
        "6203016212760001",
        "197612222025212031",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "MUHAMAD FAHMI",
        "24670130810000035",
        "6203061010920005",
        "199210102025211238",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "MUHAMAD REZA RAMADAN",
        "24670130810000489",
        "6203011909000005",
        "200112292025211039",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "MUHAMAD ILMI RASYID",
        "24670130810000366",
        "6203021809960001",
        "199609182025211085",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MUHAMAD IRVAN",
        "24670130810000663",
        "6203012501980002",
        "199801252025211084",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MUHAMAD LUTFI",
        "24670130810000959",
        "6203010408950010",
        "199508042025211130",
        "Kotawaringin Barat",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MUHAMAD MUKLIS",
        "24670130810000515",
        "6203081010880006",
        "198810102025211245",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "MUHAMMAD HAPY BADALY",
        "24670130810000722",
        "6203070111010003",
        "200111012025211034",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "MUHAMAD RAMADHANY",
        "24670130810000898",
        "6203011701970003",
        "199701172025211085",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MUTIARA KARTIKA TUNDAN, S.H",
        "24670620120000029",
        "6203016502010004",
        "200102252025212031",
        "Kapuas",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Bidang Pembinaan PAUD dan DIKMAS"
    ],
    [
        "MUHAMAD RIZKI NOR",
        "24670130810000602",
        "6203090905970005",
        "199705092025211098",
        "Kapuas",
        "SLTA ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RACHMAD",
        "24670130810000212",
        "6203011404760003",
        "197604142025211066",
        "Pulang Pisau",
        "SMA A.2/BIOLOGI",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "MUHAMAD YUSUF NAFARIN",
        "24670130810000245",
        "6203011811940004",
        "199411182025211108",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "MUHAMAT HERMAIN",
        "24670130810000456",
        "6203010904990003",
        "199904092025211067",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "MUHAMMAD, S.AP",
        "24670130810000259",
        "6203010910970003",
        "199710092025211110",
        "Kapuas",
        "D-IV ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "MUHAMMAD AGUNG SANTOSO, A.Md.AK",
        "24670120110000376",
        "6203010510950004",
        "199510052025211161",
        "Kapuas",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "UPT Puskesmas Terusan Tengah"
    ],
    [
        "MUHAMMAD AGUNG WIBISONO",
        "24670130810000462",
        "6203013107970004",
        "199707312025211082",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "MUHAMMAD AGUS",
        "24670130810000873",
        "6304051708890004",
        "198908172025211277",
        "Banjarmasin",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "RAHMADI",
        "24670130810000181",
        "6203011304780001",
        "197604132025211058",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MUHAMMAD AMIN",
        "24670130810000909",
        "6203072202970001",
        "199702222025211096",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "MUHAMMAD APRI RAHIM",
        "24670130810000823",
        "6203013004980001",
        "199804302025211089",
        "Kapuas",
        "SMK PERIKANAN",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "MUHAMMAD ARIFIN",
        "24670130810000768",
        "6203012110980001",
        "199810212025211061",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "MUHAMMAD ARYA PUTRA PRATAMA",
        "24670130810000295",
        "6203010101960016",
        "199601012025211215",
        "Palangka Raya",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MUHAMMAD ARYADIE, S.Kep.Ns",
        "24670140810000071",
        "6203020511930003",
        "199311052025211135",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Talekung Punai"
    ],
    [
        "MUHAMMAD AULIA RAHMAN",
        "24670130810000327",
        "6203011512930002",
        "199312152025211147",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Kelurahan Pulau Kupang"
    ],
    [
        "MUHAMMAD AZHAR",
        "24670130810000597",
        "6203012409960001",
        "199609242025211103",
        "Palangka Raya",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MUHAMMAD BAYU SYAFRUDIN, S.E.",
        "24670130810000436",
        "6203010409900008",
        "199009042025211131",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MUHAMMAD BENI",
        "24670130810000482",
        "6203081508820003",
        "198208152025211143",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Basarang"
    ],
    [
        "MUHAMMAD BUDHI HARTONO",
        "24670130810000144",
        "6203012409860007",
        "198609242025211118",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MUHAMMAD DELVIAN NOOR",
        "24670130810000102",
        "6203012507990002",
        "199907252025211063",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MUHAMMAD DONI, S.Pd",
        "24670110810000285",
        "6371040802010003",
        "200102082025211033",
        "Banjarmasin",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Batanjung"
    ],
    [
        "MUHAMMAD EFENDI",
        "24670130810000708",
        "6203010512940006",
        "199412032025211094",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Hilir"
    ],
    [
        "MUHAMMAD FAIZAL",
        "24670130810000691",
        "6203012308990010",
        "199908232025211054",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MUHAMMAD HABIBI",
        "24670130810000815",
        "6203012603980004",
        "199803262025211067",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Inspektorat"
    ],
    [
        "MUHAMMAD HAMDI, S.Pd",
        "24670110810000191",
        "6203102512970002",
        "199712252025211104",
        "Kapuas",
        "S-1 PENDIDIKAN",
        "Guru Ahli Pertama",
        "SMP Negeri 7 Timpah Satu Atap"
    ],
    [
        "NAJAMUDDIN AZMI",
        "24670130810000294",
        "6203072106010002",
        "200106212025211031",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "MUHAMMAD HASBI ASH-SHIDDIQI",
        "24670130810000298",
        "6203042905890002",
        "198905292025211117",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MUHAMMAD HERIANSYAHRI, S.Pd",
        "24670110810000300",
        "6201022903000003",
        "200003292025211047",
        "Kotawaringin Barat",
        "S-1 PENDIDIKAN JASMANI, KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SD Negeri 3 Palingkau Lama"
    ],
    [
        "MUHAMMAD INDRA",
        "24670130810000973",
        "6203021308960002",
        "199608132025211092",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "NOVIA SRI REJEKI",
        "24670130820000021",
        "6203025211010002",
        "200111122025212027",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "OKTAPIANUS",
        "24670130810000111",
        "6203022210010003",
        "200110222025211036",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MUHAMMAD JASMI MUZAIN, S.pd.I",
        "24670110810000162",
        "6203011301020002",
        "200201132025211023",
        "Hulu Sungai Selatan",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Basarang"
    ],
    [
        "MUHAMMAD JUHDI, S.Kep.,Ners",
        "24670140810000081",
        "6203070906890008",
        "198907092025211157",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Palangkau"
    ],
    [
        "MUHAMMAD JUNAIDI",
        "24670130810001030",
        "6203070404940004",
        "199404042025211245",
        "Kapuas",
        "SMK TEKNIK KOMPUTER DAN INFORMATIKA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MUHAMMAD KHAIRANI, ST",
        "24670130810000235",
        "6203012709890003",
        "198909272025211138",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "MUHAMMAD KHAIRUDDIN, S.Pd",
        "24670110810000268",
        "6203012807980002",
        "199807282025211105",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Saka Binjai"
    ],
    [
        "MUHAMMAD KURNAIN",
        "24670130810000507",
        "6203011003810006",
        "198103102025211112",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MUHAMMAD LUTFI",
        "24670130810000724",
        "6203013009930007",
        "199309302025211122",
        "Pulang Pisau",
        "MA IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MUHAMMAD LUTFI ANSYARI",
        "24670020110001474",
        "6203010711980009",
        "199811072025211076",
        "Hulu Sungai Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "MUHAMMAD LUTHFI RAHMANI",
        "24670130810000459",
        "6203010506980009",
        "199806052025211084",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RIANTO",
        "24670130810000714",
        "6203041203760002",
        "197603122025211081",
        "Kapuas",
        "SMT PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MUHAMMAD MAULIDI RAHMAN, S.Pd., Gr",
        "24670110810000237",
        "6304062207010021",
        "200107222025211039",
        "Barito Kuala",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Serapat Tengah"
    ],
    [
        "MUHAMMAD NOR",
        "24670130810000412",
        "6203011611840002",
        "198311162025211074",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Perumahan, Kawasan Permukiman, dan Pertanahan"
    ],
    [
        "MUHAMMAD RIDHO ANSHARI, S.pd.I",
        "24670110810000074",
        "6203012501020006",
        "200201252025211012",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 5 KAPUAS TENGAH SATU ATAP"
    ],
    [
        "MUHAMMAD RIDHO ASY'ARI",
        "24670130810000680",
        "6203010512830008",
        "198912052025211132",
        "Banjarmasin",
        "SMK",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MUHAMMAD RIFA'I",
        "24670130810000666",
        "6203012604890003",
        "198904262025211140",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "MUHAMMAD RIZKI, S. Pd.",
        "24670110810000094",
        "6211021007970002",
        "199706102025211108",
        "Pulang Pisau",
        "S-1 PENDIDIKAN MATEMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Murung"
    ],
    [
        "MUHAMMAD RIZKY NASRULLAH, S.Pd",
        "24670110810000242",
        "6203040902000001",
        "200002092025211049",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD NEGERI 2 BANDAR MEKAR"
    ],
    [
        "MUHAMMAD RUDINI",
        "24670130810000133",
        "6203010603900012",
        "199003062025211137",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "MUHAMMAD RUJI, S.Pd",
        "24670110810000174",
        "6203061204920001",
        "199212062025211119",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mawar Mekar"
    ],
    [
        "MUHAMMAD SA'RANI, S.AP",
        "24670120110000341",
        "6203042807940001",
        "199407282025211101",
        "Kapuas",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "UPT Puskesmas Tamban Baru"
    ],
    [
        "MUHAMMAD SABARUL INSAN",
        "24670130810000688",
        "6211022303810001",
        "198103232025211094",
        "Murung Raya",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "MUHAMMAD SIDIK, S.Pd.I",
        "24670110810000019",
        "6203101909850001",
        "198509192025211107",
        "Kapuas",
        "S-1 PAI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Timpah"
    ],
    [
        "MUHAMMAD SYARKAWI",
        "24670130810000136",
        "6203070910930006",
        "199310092025211102",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Murung"
    ],
    [
        "MUHAMMAD TAMA",
        "24670130810000859",
        "6203011203980002",
        "199808092025211064",
        "Kapuas",
        "SMK AGRIBISNIS PRODUKSI TANAMAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MUHAMMAD TRI FITERIYANOR",
        "24670130810000026",
        "6203012101990003",
        "199901212025211061",
        "Banjarmasin",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "MUHAMMAD WAHYUDI",
        "24670130810000977",
        "6203011203970005",
        "199703122025211112",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MUHAMMAD WAHYUDI, S.Pd.I",
        "24670110810000141",
        "6203132510860002",
        "198610252025211129",
        "Tapin",
        "A-V / AKTA MENGAJAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sidorejo"
    ],
    [
        "MUHAMMAD YAMIN, A.Md.Kep",
        "24670140810000110",
        "6203010101930010",
        "199301302025211093",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "RIMA MANDARALIA",
        "24670130820000620",
        "6203024402760002",
        "197602042025212030",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MUHAMMAD YUSUF",
        "24670130810000877",
        "6203013105830002",
        "198305312025211076",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "MUHAMMAD YUSUF, S.Pd",
        "24670110810000069",
        "6203031506950004",
        "199506152025211140",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Mambulau Timur"
    ],
    [
        "RAHMAH",
        "24670130820000479",
        "6203074112010002",
        "200112012025212031",
        "Kapuas",
        "SLTA UMUM",
        "Operator Layanan Operasional",
        "UPT Puskesmas Palingkau"
    ],
    [
        "MUHAMMAD ZULFITRIANOR, S.Sos",
        "24670130810000382",
        "6203010907890006",
        "198906092025211161",
        "Kapuas",
        "S-1 ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "MUJAHAN",
        "24670130810000982",
        "6203091208990001",
        "199908092025211074",
        "Kapuas",
        "SMK AGRIBISNIS PRODUKSI TANAMAN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MUKTI AZMI, S.T",
        "24670130810000346",
        "6203012611880002",
        "198811262025211106",
        "Banjarmasin",
        "S-1 TEKNIK ELEKTRO",
        "Penata Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "MULDIANSYAH, S.Pd",
        "24670110810000280",
        "6203052709920004",
        "199209272025211152",
        "Kapuas",
        "S-1/A-IV PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 SAKALAGUN"
    ],
    [
        "MULIADY",
        "24670130810000938",
        "6203050407990001",
        "199907042025211084",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MULIANI, S.Pd",
        "24670110820000302",
        "6203166407980001",
        "199807242025212107",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Jangkang"
    ],
    [
        "MULYADI, S.Pd.l",
        "24670110810000032",
        "6203042606890002",
        "198906262025211179",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Kuala"
    ],
    [
        "RIO APRIADI",
        "24670130810000483",
        "6203082604010001",
        "200104262025211032",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Kecamatan Basarang"
    ],
    [
        "RINIE HERLIYANTI",
        "24670130820000354",
        "6203015010760012",
        "197610102025212057",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MURAH KABAH, S.Pd.I",
        "24670110820000293",
        "6203014107870519",
        "199008172025212224",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Sei Jangkit"
    ],
    [
        "RITA KARTIKA",
        "24670130820000403",
        "6203015006760006",
        "197606102025212057",
        "Barito Selatan",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "SABRI",
        "24670130810000629",
        "6203020106760002",
        "197606012025211097",
        "Kapuas",
        "SEKOLAH MENENGAH EKONOMI ATAS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SAHRIYADI",
        "24670130810000312",
        "6203011011760013",
        "197611102025211084",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MURNI",
        "24670130820000547",
        "6203124606960004",
        "199606062025212249",
        "Kapuas Hulu",
        "PERSAMAAN SLTA",
        "Operator Layanan Operasional",
        "Kecamatan Mandau Talawang"
    ],
    [
        "SRI RAHAYU",
        "24670130820000408",
        "6203015810760004",
        "197610182025212021",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Pulau Kupang"
    ],
    [
        "MURSIDAH, S.Pd.I",
        "24670130820000631",
        "6203065507920003",
        "199207152025212148",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Murung"
    ],
    [
        "MURTI, S.Pd",
        "24670110820000610",
        "6203096107840002",
        "198407212025212087",
        "Kapuas",
        "S-1 PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Kapar"
    ],
    [
        "MUSDALIFAH, S. Pd.",
        "24670110820000433",
        "6203015710000006",
        "200010172025212056",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Selat Hilir"
    ],
    [
        "MUSLIHAT",
        "24670130820000539",
        "6203035204800007",
        "198505102025212132",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "MUSLIM",
        "24670130810000545",
        "6203011303820013",
        "198203132025211120",
        "Kapuas",
        "SMU",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MUSTAFA HELMI, S.Pd.I",
        "24670110810000058",
        "6203061908840003",
        "198408192025211102",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 3 Sei Tatas Hilir"
    ],
    [
        "MUSTAJAB",
        "24670130810000797",
        "6203011605910004",
        "199105162025211116",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MUSTAPA",
        "24670130810000499",
        "6203010102910008",
        "199102012025211127",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Kelurahan Selat Utara"
    ],
    [
        "MUSTIKA SARI, S.Kep.,Ners",
        "24670140820000273",
        "6203114701970001",
        "199701072025212133",
        "Pulang Pisau",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Basarang"
    ],
    [
        "MUTIA WAHYU TRIANA",
        "24670130820000261",
        "6203014608920008",
        "199208062025212162",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "SUMARTONO",
        "24670130810000207",
        "6203011212760010",
        "197612122025211091",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RIZAL",
        "24670130810000584",
        "6203020901010003",
        "200101092025211050",
        "Barito Selatan",
        "SMK AKUNTANSI DAN KEUANGAN",
        "Operator Layanan Operasional",
        "Kelurahan Selat Tengah"
    ],
    [
        "NABHAN RASYIDI",
        "24670130810000220",
        "6203011305850004",
        "198605132025211092",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "NABILLA, S.Kep,Ns",
        "24670140820000292",
        "6203014111900004",
        "199011012025212135",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Barimba"
    ],
    [
        "SIMSON LEGIMAY",
        "24300420110088589",
        "6203012605010009",
        "200105262025211036",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "NADIA APRILITA, A.Md.Keb",
        "24670140820000329",
        "6203116404970001",
        "199704242025212174",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "NADIA PEGA, S.pd.I",
        "24670110820000496",
        "6203076612000004",
        "200012262025212066",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Menteng Raya"
    ],
    [
        "NADIATUL HAFIDAH, S.Pd",
        "24670110820000275",
        "6304135206990002",
        "199906122025212102",
        "Barito Kuala",
        "S-1 PENDIDIKAN IPA",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Mambulau Timur"
    ],
    [
        "NADILA RAHMELIA, S.Pd",
        "24670110820000418",
        "6304037006000007",
        "200006302025212062",
        "Banjar",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD NEGERI 4 DANAU RAWAH"
    ],
    [
        "WILLIAM REVINDO",
        "24670130810000278",
        "6203023007010002",
        "200107302025211033",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "NANDA AYU LESTARI NATALIA",
        "24670130820000281",
        "6203016903930002",
        "199403292025212129",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "NANI DEWANTARY, A.Md. Keb",
        "24670140820000142",
        "6203086402880001",
        "198802242025212117",
        "Pulang Pisau",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "NAOMI IMANIAR, S.Psi",
        "24670130820000524",
        "6203025912900003",
        "199012192025212111",
        "Barito Selatan",
        "S-1 PSIKOLOGI",
        "Penata Layanan Operasional",
        "Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana"
    ],
    [
        "SUPAIN",
        "24670130810000704",
        "6203030404800004",
        "197605072025211059",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "NASARUDIN NOOR, S.Kep.,Ns",
        "24670140810000102",
        "6203011804930007",
        "199504182025211124",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Barimba"
    ],
    [
        "NATA SUKMARAGA",
        "24670130810000493",
        "6203012112890003",
        "198912212025211111",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "NATALIA T. BUTAR BUTAR, A.Md.Kep",
        "24670140820000020",
        "6203016612880006",
        "198812262025212098",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Selat"
    ],
    [
        "NATO BRANAWAN, A.Md.Kep",
        "24670140810000091",
        "6203012207970003",
        "199707222025211101",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "Dinas Kesehatan"
    ],
    [
        "NEHEMIA",
        "24670130810000280",
        "6203010311810007",
        "198111032025211062",
        "Gunung Mas",
        "SMK PERTANIAN BUDIDAYA TANAMAN",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "SUYANTO",
        "24670130810000944",
        "6203082801760004",
        "197601282025211033",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "NEMIE",
        "24670130820000621",
        "6203095009860006",
        "198609102025212112",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "URIANTINUS, S.M",
        "24670130810000254",
        "6203012110760003",
        "197610122025211062",
        "Barito Selatan",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "NENGAH SUADA",
        "24670130810000096",
        "6203082006810002",
        "198106202025211101",
        "Kapuas",
        "SMU IPA",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "NENY KUSUMAWATI, S.E",
        "24670130820000343",
        "6203015108920005",
        "199208112025212121",
        "Banjar",
        "S-1 EKONOMI MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "NESINIWATI",
        "24670130820000464",
        "6203016312880002",
        "198812232025212098",
        "Murung Raya",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "NEVA BERLIANA, A.Md. AK",
        "24670140820000357",
        "6203034810990004",
        "199910082025212074",
        "Kapuas",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "WITRIANY MELATI, S.Pd",
        "24670110820000702",
        "6203095509760006",
        "197609152025212036",
        "Kapuas",
        "S-1 GURU PAUD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mantangai Hulu"
    ],
    [
        "NI KETUT KARTINI",
        "24670130820000316",
        "6203086909900001",
        "199009262025212114",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "NI KETUT WANGI, S.Pd",
        "24670110820000601",
        "6203084407840001",
        "198406042025212093",
        "Kapuas",
        "S-1 PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD NEGERI 2 BASARANG JAYA"
    ],
    [
        "NI MADE TRIA MONICA",
        "24670020120003488",
        "6203015109920002",
        "199209112025212152",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "NI NYOMAN MARDIANGSIH, S.pd.I",
        "24670110820000679",
        "6203015502000002",
        "200002152025212074",
        "Kapuas",
        "S-1 PENDIDIKAN MATEMATIKA",
        "Guru Ahli Pertama",
        "SD Negeri 1 Terusan Makmur"
    ],
    [
        "NI PUTU MILA PURWANTI, S.Pd",
        "24670110820000586",
        "6203094706010005",
        "200106072025212042",
        "Poso",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Suka Maju"
    ],
    [
        "NI WAYAN INDRIANI, S.E",
        "24670130820000339",
        "6203084505980003",
        "199805052025212166",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "NI WAYAN NOVIANTI",
        "24670130820000650",
        "6203094711920002",
        "199211072025212147",
        "Kapuas",
        "D-I ILMU PENGETAHUAN ALAM",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NI WAYAN SUTRINI, S.Pd",
        "24670110820000480",
        "6203044502970005",
        "199702052025212113",
        "Kapuas",
        "S-1 KEPENDIDIKAN SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Terusan Makmur"
    ],
    [
        "NI WAYAN YUNI ASTITI, A.Md.AK",
        "24670120120001292",
        "6310085906960001",
        "199606192025212115",
        "Tanah Bumbu",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "UPT Puskesmas Terusan Tengah"
    ],
    [
        "NIA, Amd.Kep",
        "24670140820000276",
        "6271036206920004",
        "199206222025212131",
        "Gunung Mas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "NIBRAS SUCIAMI",
        "24670130820000454",
        "6203015001980009",
        "199801102025212103",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "NICHO WIRAWAN JAYA",
        "24670130810000120",
        "6203022508940002",
        "199408252025211112",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "YANDRIANUS",
        "24670130810000997",
        "6203061109760003",
        "197609112025211066",
        "Kapuas",
        "D-I LINGKUNGAN",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "NIDYA MARGARETH, S.E",
        "24670130820000205",
        "6203025303960002",
        "199603132025212125",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "NIKALIA, S.Pd",
        "24670110820000656",
        "6203106412960001",
        "199612242025212111",
        "Kapuas",
        "PENDIDIKAN GURU AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 1 Danau Pantau"
    ],
    [
        "NIKOLAS ANDRIANUS, S.E",
        "24670130810000256",
        "6271011811900006",
        "199011182025211116",
        "Palangka Raya",
        "S-1 EKONOMI MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "NINA PEBRIANA",
        "24670130820000592",
        "6203094102930001",
        "199302012025212139",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NINDA AYU PRATAMI, S.Tr.Kes",
        "24301120920000105",
        "6203015208980003",
        "199808122025212098",
        "Kapuas",
        "D-IV TERAPI GIGI",
        "Terapis Gigi Dan Mulut Ahli Pertama",
        "UPT Puskesmas Melati"
    ],
    [
        "NINDI, A.md,Kep",
        "24670140810000005",
        "6203120303930005",
        "199303032025211225",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "NINIK WIJAYANTI",
        "24670130820000576",
        "6203175903840001",
        "198403192025212073",
        "Semarang",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NINING SUGIANTI, A.Md.kep",
        "24670140820000282",
        "6203125909920004",
        "199209162025212127",
        "Kapuas Hulu",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "NIRMAYANTI, S.Pd.I",
        "24670130820000622",
        "6203095209860003",
        "198609122025212105",
        "Bojonegoro",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Asam"
    ],
    [
        "NOFRI DWI ISWANTO, S.AN",
        "24670130810000046",
        "6203012211910001",
        "199111222025211114",
        "Kapuas",
        "S-1 ILMU ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Kecamatan Kapuas Hilir"
    ],
    [
        "WISNU ANANDA NICKOLLA, S.Kom",
        "24670130810000070",
        "6203020612010002",
        "200112062025211013",
        "Kapuas",
        "S-1 SARJANA KOMPUTER",
        "Penata Layanan Operasional",
        "Kelurahan Dahirang"
    ],
    [
        "NONI AMALIA",
        "24670130820000317",
        "6203015207970003",
        "199707122025212125",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "NOOR AULIA SAFITRI, S.E.",
        "24670130820000253",
        "6203014603990010",
        "199903062025212086",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Bidang Pembinaan SD"
    ],
    [
        "NOOR FADHILAH, S.Pd.I",
        "24670110820000554",
        "6203015503930004",
        "199303152025212189",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Banama"
    ],
    [
        "NOOR HANIFAH, A.Md.Keb",
        "24670120120001745",
        "6203016607920001",
        "199207262025212156",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Melati"
    ],
    [
        "NOOR HATIAH, A.md,Kep",
        "24670140820000008",
        "6203124303910001",
        "199103032025212177",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "NOOR LAILA, .",
        "24670120120000463",
        "6203016508920004",
        "199208252025212135",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "NOOR MAYASARI",
        "24670130820000202",
        "6203015312840004",
        "198412132025212054",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "YULI HARNITA",
        "24670130820000352",
        "6203115707760002",
        "197607172025212031",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NOORAMADAN",
        "24670130810000929",
        "6203070606850007",
        "198506062025211156",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Murung"
    ],
    [
        "NOORHASANAH",
        "24670130820000305",
        "6203016806880004",
        "198806282025212120",
        "Banjarmasin",
        "MADRASAH ALIYAH BAHASA",
        "Operator Layanan Operasional",
        "Kelurahan Selat Hulu"
    ],
    [
        "YULIUS",
        "24670130810000866",
        "6203030306760005",
        "197606032025211057",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "NOORYANA",
        "24670130820000030",
        "6203016208870009",
        "198708222025212093",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "NOPI SRI MIRAYANI, A. Md.Kep",
        "24670140820000030",
        "6203064811900002",
        "199211082025212143",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Tatas"
    ],
    [
        "NOPIANUR, S.Pd.I",
        "24670110810000109",
        "6203062411890002",
        "198911242025211115",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Kapuas Hulu Satu Atap"
    ],
    [
        "NOPRI ARISKA",
        "24670130810000018",
        "6203091911900003",
        "199011192025211104",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "AGUSTINA MALINDA",
        "24670130820000166",
        "6203017108770005",
        "197708312025212029",
        "Hulu Sungai Selatan",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "NOR ABDI SAPUTRA",
        "24670130810000731",
        "6203052512940002",
        "199312252025211171",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "NOR ANNISA",
        "24670130820000303",
        "6203016606930006",
        "199306262025212179",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "NOR ASYIAH, A,Md.Keb",
        "24670140820000102",
        "6203014504930010",
        "199304052025212177",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Panamas"
    ],
    [
        "NOR BAYAH, S. Pd",
        "24670110820000663",
        "6203044508990003",
        "199908052025212103",
        "Kapuas",
        "S-1 PENDIDIKAN GURU KELAS SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD NEGERI 2 SEI GITA"
    ],
    [
        "NOR FINA DEWI",
        "24670130820000307",
        "6203015505960006",
        "199605152025212149",
        "Kapuas",
        "MADRASAH ALIYAH KEAGAMAAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "NOR JANNAH, S.Pd",
        "24670110820000632",
        "6304085212960003",
        "199612122025212153",
        "Barito Kuala",
        "S-1 TADRIS BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Bataguh"
    ],
    [
        "NOR JANNAH, A.Md.Keb",
        "24670140820000363",
        "6203034308000001",
        "200009162025212041",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "NOR JANNAH, S.Pd",
        "24670110820000468",
        "6203036111010001",
        "200111212025212047",
        "Kapuas",
        "D-IV PENDIDIKAN GURU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bangun Harjo"
    ],
    [
        "NOR SAFITRI, S.Pd",
        "24670110820000779",
        "6203054803000001",
        "200003082025212079",
        "Kapuas",
        "S-1 PENDIDIKAN TEKNOLOGI INFORMASI",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Barat"
    ],
    [
        "NOR SAIDAH",
        "24670130820000646",
        "6203095504980002",
        "199804152025212123",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AHMAD HOLDANI",
        "24670130810000905",
        "6203011805020004",
        "200205182025211020",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "ATENG",
        "24670130810000063",
        "6203111710770002",
        "197710172025211043",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Pasak Talawang"
    ],
    [
        "NORAYATI",
        "24670130820000027",
        "6203035909850002",
        "198509192025212097",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "DAYA",
        "24670130820000495",
        "6203014709770009",
        "197709062025212038",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NORETY, S.Pd.I",
        "24670110820000394",
        "6203016909920004",
        "199204292025212167",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Selat Barat"
    ],
    [
        "NORFAH, S.Pd.I",
        "24301220120113271",
        "6203074311910003",
        "199111032025212139",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 2 Muara Dadahup"
    ],
    [
        "NORHADI FAUJI, S. Kom",
        "24670110810000124",
        "6203011403990006",
        "199905142025211074",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Selat"
    ],
    [
        "NORHALIMAH, S.Pd",
        "24670110820000589",
        "6203055303910006",
        "199103132025212148",
        "Kapuas",
        "D-IV PENDIDIKAN KIMIA",
        "Guru Ahli Pertama",
        "SMP NEGERI 3 KAPUAS BARAT SATU ATAP"
    ],
    [
        "NORHALIMAH",
        "24670130820000067",
        "6203024105950002",
        "199505012025212154",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "NORHAN HIDAYAT, S.AP",
        "24670130810000093",
        "6203012611880009",
        "198811262025211104",
        "Banjarmasin",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "NORHAYATI",
        "24670130820000648",
        "6203064507950004",
        "199412012025212173",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NORHIKMAH, S.I.Pust",
        "24670130820000518",
        "6304156005930001",
        "199305202025212189",
        "Hulu Sungai Utara",
        "S-1 ILMU PERPUSTAKAAN",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NORHIKMAH, A.Md.Keb",
        "24670140820000398",
        "6203016206960007",
        "199606222025212109",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "NORKAMARIAH, S.Kep.,Ners",
        "24670140820000185",
        "6203016209910003",
        "199109222025212137",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "NORLAILA, S.E",
        "24670110820000673",
        "6203035910930001",
        "199210192025212148",
        "Kapuas",
        "S-1 EKONOMI SYARIAH",
        "Guru Ahli Pertama",
        "SMP Negeri 3 BATAGUH"
    ],
    [
        "NORLAILA, S.Pd.I",
        "24670110820000453",
        "6203066012880004",
        "198712202025212137",
        "Kapuas",
        "S-1 / AKTA IV PAI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mawar Mekar"
    ],
    [
        "NORLELA",
        "24670130820000060",
        "6203065507970006",
        "199707152025212124",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "NORLIANA",
        "24670130820000499",
        "6203015602910007",
        "199102162025212114",
        "Hulu Sungai Utara",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Kelurahan Murung Keramat"
    ],
    [
        "NORMIATI.I, S.Pd",
        "24670130820000619",
        "6212014506890001",
        "198906052025212175",
        "Murung Raya",
        "S-1/A-IV PENDIDIKAN GURU TAMAN KANAK-KANAK (PGTK)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bunga Mawar"
    ],
    [
        "NORMILAH, S.Pd",
        "24670130820000567",
        "6203015702900004",
        "198902172025212133",
        "Kapuas",
        "S-1 AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Suka Mukti"
    ],
    [
        "NORMILASARI",
        "24670130820000486",
        "6203016410960005",
        "199610242025212099",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "EDY FITRIAN",
        "24670130810000451",
        "6203011909770005",
        "197709192025211055",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "NORPAH",
        "24670130820000378",
        "6203054111880002",
        "198811012025212091",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ERTISEN",
        "24670130820000635",
        "6203095010770010",
        "197710102025212057",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NOVA",
        "24670130820000200",
        "6203116405960003",
        "199605242025212119",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NOVA",
        "24670130820000407",
        "6203016812880005",
        "198812282025212139",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "NOVA CAHYAWATI, A.Md.Kep",
        "24670140820000386",
        "6211065004990001",
        "199607282025212128",
        "Pulang Pisau",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ARIF RAHMAN",
        "24670130810000628",
        "6203010409020005",
        "200209042025211013",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "NOVA VEBRIANIE, A.Md.Keb",
        "24670140820000347",
        "6203054411970006",
        "199711042025212110",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "NOVAL PRATAMA, S.M.",
        "24670620110000246",
        "6203021011970003",
        "199711102025211123",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Hilir"
    ],
    [
        "NOVI, A.Md.Keb",
        "24670140820000138",
        "6271016302920009",
        "199202232025212131",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "NOVI ERMA, S.Pd",
        "24670110820000499",
        "6203047011880003",
        "198811302025212131",
        "Kapuas",
        "S-1 PG PAUD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Warna Sari"
    ],
    [
        "NOVI PURWASIH, S.Pd",
        "24670110820000692",
        "6203015411880001",
        "198811142025212091",
        "Kapuas",
        "S-1 PENDIDIKAN USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 2 TERUSAN MAKMUR"
    ],
    [
        "NOVI PUSPITA WIDIANANDA, S.Pd",
        "24670130820000108",
        "6203065611920001",
        "199211162025212131",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Murung"
    ],
    [
        "NOVIA DWI PRAYANI, S.Pd",
        "24670110820000314",
        "6203086011990002",
        "199911202025212073",
        "Palangka Raya",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tarung Manuah"
    ],
    [
        "NOVIA RIANTI, S.H.",
        "24670130820000014",
        "6203016111970003",
        "199711212025212108",
        "Kapuas",
        "S-1 HUKUM (BERGELAR S.H.)",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "NOVIA SANTI",
        "24670130820000170",
        "6203015311940003",
        "199411132025212117",
        "Kapuas",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "AURATANIA",
        "24670130820000444",
        "6203075604020002",
        "200204192025212026",
        "Kapuas",
        "SMK KEUANGAN",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "NOVITA SARI, S.Pd",
        "24670110820000404",
        "6271024210900001",
        "199010022025212120",
        "Pulang Pisau",
        "S-1 PENDDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Pitung"
    ],
    [
        "NOVITASARI, S.Pd",
        "24670110820000621",
        "6203094204990004",
        "199904022025212101",
        "Kapuas",
        "S-1 PENDIDIKAN TEKNOLOGI INFORMASI",
        "Guru Ahli Pertama",
        "SMP NEGERI 13 MANTANGAI SATU ATAP"
    ],
    [
        "ERWANDI",
        "24670130810001043",
        "6203021408770002",
        "197708142025211058",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "NUGROHO, S.T",
        "24670130810000300",
        "6371022502910002",
        "199102252025211105",
        "Banjarmasin",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "NUGROHO DWI PUTRANTO, S.H",
        "24670130810000333",
        "6203013003890007",
        "198903302025211110",
        "Kapuas",
        "S-1 HUKUM",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "NUNIK SUTARMI, S.Pd.I",
        "24670130820000557",
        "6203024502860002",
        "198602052025212095",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mambulau"
    ],
    [
        "ISKANDAR",
        "24670130810000882",
        "6203041607770003",
        "197707162025211087",
        "Trenggalek",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "NUR ADINDA WULAN SARI",
        "24670130820000097",
        "6203014103880006",
        "198803012025212121",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "NUR ANNISA DEWI",
        "24670130820000103",
        "6203026009990002",
        "199909202025212080",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "NUR ATHIYA MAULIDAH, A.Md Kep",
        "24670140820000348",
        "6203015107970003",
        "199707112025212107",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Basarang"
    ],
    [
        "NUR BAITI, S.E",
        "24301220920001012",
        "6203015807960001",
        "199607182025212133",
        "Kapuas",
        "S-1 EKONOMI SYARIAH",
        "Penata Layanan Operasional",
        "UPT Puskesmas Mandomai"
    ],
    [
        "NUR FITRIANI LESMANA, S.H",
        "24670130820000137",
        "6203014705890015",
        "198905072025212186",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "NUR HAPIPAH, A.Md.Keb",
        "24670140820000066",
        "6203094301940003",
        "199401032025212129",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Mantangai"
    ],
    [
        "NUR ISNAINI",
        "24670130820000349",
        "6203045505980003",
        "199805152025212148",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "NUR REINA",
        "24670130820000423",
        "6203014810840008",
        "198410082025212074",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "KARTINI",
        "24670130820000437",
        "6203015608770011",
        "197708162025212046",
        "Grobogan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "NUR SYA'BANA PRATAMA, S.Pd",
        "24670110810000163",
        "6371033001930006",
        "199301302025211094",
        "Banjarmasin",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 5 Pulau Kupang"
    ],
    [
        "NUR ZUMURRUDAH LUTHFIYAH, A.Md.Keb.",
        "24670140820000365",
        "6211055902070001",
        "200006242025212075",
        "Pulang Pisau",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "NUR'ID H., S.Sos",
        "24670130810000188",
        "6203011303890003",
        "198903132025211146",
        "Kapuas",
        "S-1 SOSIOLOGI",
        "Penata Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "LISAWATI, S.Pd",
        "24670110820000461",
        "6203015505780011",
        "197705152025212055",
        "Hulu Sungai Utara",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD 2 Bamban Raya"
    ],
    [
        "NURAFNI",
        "24670130820000038",
        "6203014503990015",
        "199903052025212083",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Kecamatan Basarang"
    ],
    [
        "MAHDALENA WARNI",
        "24670130820000541",
        "6203015402770006",
        "197702142025212026",
        "Barito Selatan",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "NURESA OKTAVIA, A.md, Keb",
        "24670140820000240",
        "6203015010990008",
        "199910102025212135",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Palingkau"
    ],
    [
        "NURHAYATI, A.Md.Keb",
        "24670140820000019",
        "6203015511930001",
        "199311152025212128",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "NURIAH, A.Md.Kep",
        "24670140820000036",
        "6203127110940003",
        "199410132025212126",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "MINARLY KUSUMAJAYA",
        "24670130810000379",
        "6271032301770001",
        "197701232025211049",
        "Kapuas",
        "SEKOLAH TEKNOLOGI MENENGAH",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "NURJANNAH, S.Pd",
        "24670110820000672",
        "6308065103990004",
        "199903112025212091",
        "Kapuas",
        "S-1 PGMI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Teluk Palinget"
    ],
    [
        "NURSI YUYUS, S.Pd",
        "24670110820000476",
        "6203114907840002",
        "198407092025212083",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 2 Bajuh"
    ],
    [
        "NURUL AZMI",
        "24670130810000128",
        "6203070704940001",
        "199404072025211133",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Baru"
    ],
    [
        "NURUL BAITI, S.Pd",
        "24670110820000367",
        "6203075705950004",
        "199505172025212141",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Murung"
    ],
    [
        "NURUL FALAH HIDAYAH",
        "24670110820000768",
        "6203095503020003",
        "200203152025212025",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Basuta Raya"
    ],
    [
        "NURUL HASANAH",
        "24670130820000313",
        "6203015505910003",
        "199105152025212219",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "NURYANTI, S.kom",
        "24670110820000552",
        "6371046905910007",
        "199105292025212113",
        "Kapuas",
        "S-1 INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Bataguh"
    ],
    [
        "NYOMAN DWI LIPE, S.Pd",
        "24670110810000246",
        "6203052607930001",
        "199309262025211139",
        "Kapuas",
        "S-1 PENDIDIKAN TEKNIK MESIN",
        "Guru Ahli Pertama",
        "SMP NEGERI 1 KAPUAS BARAT SATU ATAP"
    ],
    [
        "NYOMAN LAGU",
        "24670130810000152",
        "6203081001840001",
        "198401102025211120",
        "Barito Kuala",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "OJEK APRIANTO, S.Pd",
        "24670110810000081",
        "6203120503990004",
        "199905052025211099",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Hanyo"
    ],
    [
        "OKABRIONO, S.Pd",
        "24670110810000260",
        "6271010510840017",
        "198410052025211149",
        "Palangka Raya",
        "S-1 PENDIDIKAN EKONOMI",
        "Guru Ahli Pertama",
        "SD NEGERI 3 DANAU RAWAH"
    ],
    [
        "OKTA SRILESTARI, S.Kep.,Ns",
        "24670140820000267",
        "6203016610840002",
        "198410262025212056",
        "Katingan",
        "D-IV + PROFESI KEPERAWATAN",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "AYU SETYANINGRUM",
        "24670130820000445",
        "6203016006020003",
        "200206202025212018",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "OKTAVIANUS NICHOLAS, S.M.",
        "24670130810000185",
        "6203022110920003",
        "199210212025211107",
        "Kapuas",
        "S-1 MANAJAMEN",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "OKTO BUDI WIJAYA",
        "24670130810000495",
        "6271022810950003",
        "199510282025211138",
        "Barito Utara",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "OKTOPIATI, S.E",
        "24670110820000551",
        "6371044210850009",
        "198510022025212086",
        "Kapuas",
        "S-1 EKONOMI AKUNTANSI",
        "Guru Ahli Pertama",
        "SD Negeri 2 Tapen"
    ],
    [
        "OLIVIA FELY CITA, S.Ak.",
        "24670130820000299",
        "6203017005990002",
        "199905302025212086",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "OMEGA GLORIA, S.Pd",
        "24670110820000327",
        "6203066210990002",
        "199910222025212078",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Pulau Petak"
    ],
    [
        "OPIEYANTO, S.P.d.I",
        "24670130810000792",
        "6203011410880005",
        "198810142025211134",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "NORMILAWATI",
        "24670130820000482",
        "6203016403770002",
        "197703242025212025",
        "Banjarmasin",
        "SEKOLAH MENENGAH TEKNOLOGI PERTANIAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "OSIN, S.Pd",
        "24670110820000321",
        "6211034406920001",
        "199206042025212162",
        "Palangka Raya",
        "S-1 PENDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Teluk Hiri"
    ],
    [
        "OVI YULIANI",
        "24670130820000442",
        "6203014507950011",
        "199507062025212149",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "OVILIA SANTARI, S.T",
        "24670130820000562",
        "6271015010920006",
        "199210102025212298",
        "Palangka Raya",
        "S-1 ARSITEKTUR",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "PADELI",
        "24670130810000801",
        "6203062906810001",
        "198106292025211072",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "PADLI",
        "24670130810000247",
        "6203012711900001",
        "199011272025211103",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "PAHRIAH, S.Pd",
        "24670130820000074",
        "6203065404880001",
        "198804142025212142",
        "Kapuas",
        "D-IV BIMBINGAN DAN KONSELING",
        "Penata Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "PANDEHEN, S.Pd",
        "24670130810000354",
        "6203092403820005",
        "198203242025211080",
        "Kapuas",
        "S-1 ADMINISTRASI PENDIDIKAN",
        "Penata Layanan Operasional",
        "Kecamatan Kapuas Hulu"
    ],
    [
        "PANGKO MALI",
        "24670130810000097",
        "6203011107870008",
        "198707112025211119",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "PARIDA",
        "24670130820000374",
        "6203015903880004",
        "198803192025212103",
        "Kapuas",
        "D-I ADMINISTRASI",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "PASKA HISKIA SOETA, A.md",
        "24670130810000253",
        "6203011004820012",
        "198204102025211132",
        "Kapuas",
        "D-III ILMU ADMINISTRASI NIAGA",
        "Pengelola Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "PATIMAH, S.Pd",
        "24670110820000767",
        "6203075710990003",
        "199909172025212091",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tajepan"
    ],
    [
        "PATMAWATI",
        "24670130820000138",
        "6203016002850011",
        "198502202025212088",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Kelurahan Selat Tengah"
    ],
    [
        "PATMAWATI, S.Pd.I",
        "24670110820000487",
        "6203044505830006",
        "198305052025212135",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BANDAR MEKAR"
    ],
    [
        "PEBRIANA, S.E",
        "24670110820000736",
        "6203015102960001",
        "199602112025212085",
        "Kapuas",
        "S-1 EKONOMI",
        "Guru Ahli Pertama",
        "SMP NEGERI 3 KAPUAS MURUNG SATU ATAP"
    ],
    [
        "PEBRIANA VALENTINA, A.Md.Keb",
        "24670720120001730",
        "6203015402960006",
        "199602142025212123",
        "Tabalong",
        "D-III KEBIDANAN",
        "Pengelola Layanan Operasional",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "PEBRINA, S.M",
        "24670130820000078",
        "6203026702890002",
        "198902272025212111",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "NURIE ATIE",
        "24670130820000469",
        "6203016204770002",
        "197704222025212034",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "PENI, S.Pd",
        "24670130820000583",
        "6203095202870003",
        "198702122025212133",
        "Kapuas",
        "S-1 PGTK/PAUD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sekata Makmur"
    ],
    [
        "PEPRIADI JAYA MASA, Amd. Kep",
        "24670140810000143",
        "6204032402950001",
        "199502242025211122",
        "Tabalong",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Danau Rawah"
    ],
    [
        "PERAWATI",
        "24670130820000201",
        "6203014211850006",
        "198511022025212076",
        "Palangka Raya",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "PERAYULIATI",
        "24670130820000528",
        "6203016507870010",
        "198707252025212114",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RAUDAH",
        "24670130820000465",
        "6203017009910008",
        "197711282025212028",
        "Kapuas",
        "SEKOLAH MENENGAH EKONOMI ATAS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "PERMATASARI",
        "24670130820000162",
        "6203016109860004",
        "198609212025212120",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Selat Dalam"
    ],
    [
        "PERRA RACHMAWATI, A.Md.Keb.",
        "24670140820000112",
        "6203026709980002",
        "199809272025212102",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "PIANA, S. Pd. I",
        "24670110820000395",
        "6203016505880003",
        "198805252025212163",
        "Barito Selatan",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Kayu Bulan"
    ],
    [
        "RIYANI, S. Pd",
        "24670110820000518",
        "6203116812770002",
        "197712282025212031",
        "Kapuas",
        "S-1 PENDIDIKAN JASMANI KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 PASAK TALAWANG"
    ],
    [
        "PINA DEWI SARTIKA",
        "24670130820000292",
        "6211035505890001",
        "198905152025212189",
        "Pulang Pisau",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "PINEHAS UDA, S.Kom",
        "24670130810000141",
        "6203022211980002",
        "199811222025211044",
        "Kapuas",
        "D-IV/S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Kelurahan Barimba"
    ],
    [
        "PIRTA WISNUARTA",
        "24670130810000614",
        "6203040502960002",
        "199602052025211102",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Tamban Catur"
    ],
    [
        "PISTA PANDINI",
        "24670130820000285",
        "6203074209960001",
        "199609022025212113",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "PITMA NARA, S.P",
        "24670110810000245",
        "6205052810860001",
        "198610282025211155",
        "Barito Utara",
        "S-1 AGROTEKNOLOGI",
        "Guru Ahli Pertama",
        "SMP NEGERI 3 TIMPAH"
    ],
    [
        "PITNEGU, S.T.",
        "24670130810000765",
        "6271030606810009",
        "198106062025211149",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "PONIMAN",
        "24670130810000857",
        "6203011812830003",
        "198312182025211089",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "POPPY CHRISTINA MADJIAH, S.Kom",
        "24670130820000008",
        "6203016710940003",
        "199410272025212161",
        "Banjarmasin",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ROYA AGUSTINA, S.Pd",
        "24670110820000326",
        "6203115908770002",
        "197708192025212024",
        "Kapuas",
        "S-1 PENDDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SD Negeri 2 Kaburan"
    ],
    [
        "PRANSI",
        "24670130820000493",
        "6203054309930001",
        "199609032025212138",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "PRIA NITA UTARI, S.Stat",
        "24670130820000158",
        "6203075804950004",
        "199504182025212152",
        "Kapuas",
        "S-1 STATISTIKA",
        "Penata Layanan Operasional",
        "Subbagian Keuangan dan Aset"
    ],
    [
        "SAIFULLAH",
        "24670130810000779",
        "6203011503770002",
        "197703152025211071",
        "Kapuas",
        "SMA ILMU ILMU SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "PRIWATI",
        "24670130820000500",
        "6203015209810003",
        "198109122025212049",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "PUBERSIH.H",
        "24670130820000474",
        "6203015805870009",
        "198705182025212128",
        "Kapuas",
        "SMA ILMU SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "PUNGKAS MAHARPA, S.T.",
        "24670130810000353",
        "6203014406870004",
        "198706042025211157",
        "Tapin",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "PUNGKI, SE",
        "24670110820000542",
        "6210027110950001",
        "199510312025212108",
        "Kapuas",
        "S-1 EKONOMI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Hulu Satu Atap"
    ],
    [
        "PURNAMA, A.Md. Keb",
        "24670140820000115",
        "6203036108940001",
        "199408212025212114",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "PURWADI",
        "24670130810000890",
        "6203010505820018",
        "198205052025211191",
        "Demak",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "PUSPA KENCANA, S.Kep.Ners",
        "24670120120001366",
        "6203016703950001",
        "199503272025212128",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Talekung Punai"
    ],
    [
        "PUSPITA SARI, S.pd.I",
        "24670110820000761",
        "6271016503900002",
        "199003252025212120",
        "Palangka Raya",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tambun Raya"
    ],
    [
        "PUTERI DINI DWI HERELI, S.Kom",
        "24670130820000294",
        "6203014312930004",
        "199312032025212149",
        "Palangka Raya",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "PUTRA BAYU",
        "24670130810000900",
        "6203012110880002",
        "198810212025211087",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "PUTRA NATA, A.md,Kep",
        "24670140810000004",
        "6203122010910002",
        "199210202025211157",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "AZURRA BARDA CAHYANI",
        "24670130820000419",
        "6203016601020005",
        "200201262025212017",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "PUTRY AGUSTIN",
        "24670130820000517",
        "6203016008940001",
        "199408202025212154",
        "Barito Kuala",
        "SMK TEKNIK KOMPUTER DAN INFORMATIKA",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SELVI YANTHIE",
        "24670130820000195",
        "6203015909770007",
        "197709192025212030",
        "Kapuas",
        "SMEA KEUANGAN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "R. PURBO KUSMONO, S.AN",
        "24670130810000885",
        "3305091304860002",
        "198604132025211113",
        "Kebumen",
        "S-1 ILMU ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "RABIYATUL ADAWIYAH",
        "24301220920000560",
        "6203075310990003",
        "199910132025212074",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Murung"
    ],
    [
        "RABUT, S.Pd",
        "24670110810000275",
        "6203121203870002",
        "198703122025211173",
        "Kapuas",
        "D-IV PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Sei Pinang"
    ],
    [
        "SISWADI",
        "24670130810000571",
        "6203012611770002",
        "197711262025211040",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RACHMAD IRAWAN, S.Kom.",
        "24670130810000313",
        "6203011501950001",
        "199503152025211117",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "RACHMAD RUSIADY, S.Pd",
        "24670110810000182",
        "6203043012900002",
        "199012312025211300",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lupak Dalam"
    ],
    [
        "SMIRNA",
        "24670130820000526",
        "6203016702770002",
        "197702272025212030",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RADIAH INDAH SARI, S.Pd",
        "24301220120018357",
        "6203015704970007",
        "199704172025212141",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Muara Dadahup"
    ],
    [
        "RADU, S.Pd",
        "24670110810000087",
        "6271030507890005",
        "198907052025211192",
        "Kapuas",
        "D-IV PENDIDIKAN JASMANI KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SD NEGERI 2 SAKA MANGKAHAI"
    ],
    [
        "BERRY MIKHAEL",
        "24670130810000698",
        "6271030602020009",
        "200202062025211026",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RAFIKA PERMATA INDAH SARI, S.Kom",
        "24670130820000073",
        "6203024808940002",
        "199408082025212210",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RAFLI, S.Pd",
        "24670110810000078",
        "6203101411940001",
        "199411142025211115",
        "Kapuas",
        "S-1 AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Penda Muntei"
    ],
    [
        "RAHAYU ERNAWATI, S. Pd",
        "24670020120001410",
        "6203015908910004",
        "199108192025212160",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Penata Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RAHMAD HARIANTO",
        "24670130810000214",
        "6203021106910003",
        "199206112025211137",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RAHMAD HIDAYAT, A.Md.Kep",
        "24670120110000506",
        "6307052602950001",
        "199502262025211107",
        "Hulu Sungai Tengah",
        "AKADEMI KEPERAWATAN",
        "Pengelola Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "RAHMAD KELANA SAPUTRA K, A.md",
        "24670130810001004",
        "6203010603830006",
        "198303062025211121",
        "Kapuas",
        "D-III ELEKTRO",
        "Pengelola Layanan Operasional",
        "UPT Puskesmas Selat"
    ],
    [
        "RAHMAD RAMADHAN",
        "24670130810000683",
        "6203010305920018",
        "199205032025211157",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SRI DEWITO",
        "24670130810000869",
        "6203012510770003",
        "197710252025211044",
        "Kapuas",
        "SMEA ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RAHMADINUR, S.Pd.I",
        "24670110810000073",
        "6307030101850004",
        "198501012025211313",
        "Hulu Sungai Tengah",
        "S-1 MANAJEMEN KEPENDIDIKAN ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 2 BANDAR MEKAR"
    ],
    [
        "DINA NOOR APRILIA",
        "24670130820000387",
        "6203014704020004",
        "200204072025212014",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RAHMAH AYU WIDIASTUTI, S.Farm",
        "24670120120001433",
        "6203014505980006",
        "199805052025212155",
        "Kapuas",
        "S-1 FARMASI",
        "Administrator Kesehatan Ahli Pertama",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "RAHMAH DANIA, S.Pd",
        "24670110820000262",
        "6271034201970008",
        "199701022025212104",
        "Kapuas",
        "S-1 PENDIDIKAN (TADRIS) BIOLOGI",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Tengah"
    ],
    [
        "RAHMAN WAHYUDI, S.pd.I",
        "24670110810000181",
        "6304061707900003",
        "199007172025211195",
        "Barito Kuala",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Serapat Timur"
    ],
    [
        "RAHMAT FAUZAN",
        "24670130810000735",
        "6203012912830001",
        "198312292025211080",
        "Palangka Raya",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RAHMAT MURJIANSYAH",
        "24670130810000421",
        "6203050410870003",
        "198710042025211109",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RAHMAT SUPIANI, S.Pd",
        "24670110810000148",
        "6203090411990002",
        "199911052025211063",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Kapar"
    ],
    [
        "RAHMAWATI, A. Md. Keb",
        "24670140820000397",
        "6203074406990002",
        "199906042025212112",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "SUPANDI",
        "24670130810000567",
        "6203022104730002",
        "197704212025211080",
        "Kapuas",
        "SMEA KEUANGAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RAHMAWATI",
        "24670130820000046",
        "6203016808900004",
        "199008282025212171",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RAHMAWATI",
        "24670130820000275",
        "6203085701960002",
        "199601172025212121",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "SUSANA SUSANTHY SARY, S.T",
        "24670130820000654",
        "6203014509770006",
        "197709052025212031",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "RAHMI RUMILIA, S.Pd",
        "24670110820000593",
        "6203064201900001",
        "199001022025212129",
        "Kapuas",
        "D-IV PENDIDIKAN GURU ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 2 Teluk Palinget"
    ],
    [
        "RAHMIATI",
        "24670130820000533",
        "6203064908970002",
        "199709232025212108",
        "Kapuas",
        "MA IPA",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "RAHMIATY, S.AP",
        "24680720120000437",
        "6203016702960003",
        "199602272025212131",
        "Kapuas",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RAHMITA, S.M.",
        "24670130820000236",
        "6203074703900007",
        "199103072025212125",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Bidang Pembinaan SMP"
    ],
    [
        "RAHMITA",
        "24670130820000657",
        "6203014707870005",
        "198707072025212219",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RAHMIYATI, S.Pd.I",
        "24670110820000488",
        "6203044511910003",
        "199111052025212165",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pematang"
    ],
    [
        "RAKMADANI APLIANOR, S. Kom",
        "24670130810000511",
        "6271030204910002",
        "199104022025211169",
        "Palangka Raya",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Bidang Pelayanan Terpadu Satu Pintu"
    ],
    [
        "RAKUTIH",
        "24670130810000056",
        "6203060705910002",
        "199105072025211126",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RAMA PRATAMA REYNALDI",
        "24670130810000286",
        "6203011301970008",
        "199701132025211107",
        "Barito Selatan",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "RAMADANI",
        "24670130810000609",
        "6203012802940009",
        "199402282025211148",
        "Kapuas",
        "SMA/MA (IPA)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RAMADHAN, S.AP.",
        "24670130810000305",
        "6203011908910003",
        "199108192025211122",
        "Kapuas",
        "S-1 ILMU ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "RAMIDAH, Ramidah, A.Md.Keb",
        "24670140820000169",
        "6203015309910003",
        "199109132025212108",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "RAMINA, S.Pd",
        "24670110820000513",
        "6203106711000001",
        "200011272025212068",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Lungkuh Layang"
    ],
    [
        "UDEN ABADI",
        "24670130810000796",
        "6203080501770003",
        "197701052025211073",
        "Kapuas",
        "SLTA",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "DONI SANTOSO",
        "24670130810000604",
        "6203010504020006",
        "200204052025211015",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Timur"
    ],
    [
        "RAMITA, S.Pi",
        "24670720120000246",
        "6206045201950001",
        "199501122025212136",
        "Katingan",
        "S-1 BUDIDAYA PERAIRAN",
        "Guru Ahli Pertama",
        "SMP NEGERI 1 MANDAU TALAWANG"
    ],
    [
        "RARA APRITYA, S.Kep.,Ns",
        "24670140820000013",
        "6203016004910004",
        "199204202025212155",
        "Kapuas",
        "NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "RASAPATI MARZRIUS, S.H",
        "24670130810000352",
        "6203082703840002",
        "198403272025211097",
        "Palangka Raya",
        "S-1/A-IV ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "RASIDAH, S.Pd",
        "24670130820000647",
        "6203026112920001",
        "199212212025212127",
        "Kapuas",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD Negeri 1 Saka Batur"
    ],
    [
        "RASYIDAH, A.Md",
        "24670130820000096",
        "6203014611950004",
        "199511062025212134",
        "Kapuas",
        "D-III MANAJEMEN ADMINISTRASI",
        "Pengelola Layanan Operasional",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "RATI BETANIA",
        "24670130820000327",
        "6203014609850002",
        "198509062025212088",
        "Kapuas",
        "SMK TATA BUSANA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RATIH PURWASIH, S.Pd",
        "24670110820000502",
        "6271035903930010",
        "199303192025212143",
        "Kapuas",
        "S-1 PENDIDIKAN FISIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Kapuas Tengah Satu Atap"
    ],
    [
        "RATIH PURWATI, S.Pd",
        "24670130820000568",
        "6203057010940004",
        "199410302025212138",
        "Kapuas",
        "S-1 PAI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Kalampan"
    ],
    [
        "RATNA",
        "24670130820000106",
        "6203025106910001",
        "199206062025212224",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RATNA JUWITA, S. AP",
        "24670130820000235",
        "6203014304910008",
        "199104032025212163",
        "Kapuas",
        "S-1 ILMU ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RATNA SARI, S.Pd.I",
        "24670130820000577",
        "6203015811900010",
        "199011182025212124",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 8 Pulau Kupang"
    ],
    [
        "RATNA YANI, S.Kep., Ns",
        "24670140820000243",
        "6203016709930003",
        "199309272025212147",
        "Kapuas",
        "NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "RATNAWATI",
        "24670130820000040",
        "6203015906840004",
        "198406192025212073",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "RAUDAH",
        "24670130820000005",
        "6203014302850001",
        "198502032025212089",
        "Kapuas",
        "SMK SEKRETARIS",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "UMAR HADI WIJAYA",
        "24670130810000548",
        "6203012107770002",
        "197707212025211058",
        "Kapuas",
        "SMEA MANAJEMEN PEMASARAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RAUDAH, S. Pd. I",
        "24670110820000301",
        "6203066609910001",
        "199109262025212126",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Guru Ahli Pertama",
        "SD Negeri 1 Teluk Palinget"
    ],
    [
        "RAUDAH, Amd.Keb",
        "24670140820000352",
        "6203015601950003",
        "199501162025212123",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "WALBER",
        "24670130810000720",
        "6203011111770003",
        "197711112025211079",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RAYANTI PERTIWI, S. Tr. Keb",
        "24670120120000905",
        "6203014801960003",
        "199601082025212121",
        "Kapuas",
        "D-IV KEBIDANAN",
        "Bidan Ahli Pertama",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RAYMUNDUS DE SIMSON SAN SALTVATORE B, S.IP",
        "24670110810000061",
        "6203091010970007",
        "199710102025211152",
        "Kapuas",
        "S-1 ILMU PEMERINTAHAN",
        "Guru Ahli Pertama",
        "SMP NEGERI 1 MANTANGAI SATU ATAP"
    ],
    [
        "RAYTOVELO, SE",
        "24670130810000239",
        "6271031103870007",
        "198703112025211161",
        "Kapuas",
        "S-1 EKONOMI AKUTANSI",
        "Penata Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "FATMAH",
        "24670130820000436",
        "6203025808020001",
        "200208182025212027",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RECZI QORINA AUGANIA RUNUK, A.Md.Kes",
        "24670620120000398",
        "6203016503980009",
        "199803252025212103",
        "Kapuas",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "UPT Puskesmas Melati"
    ],
    [
        "REDI",
        "24670130810000236",
        "6271010804890003",
        "198904182025211143",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "REDY HARTANTO, Amd.Kep",
        "24670140810000136",
        "6203010902920007",
        "199202092025211120",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "REGGA TUAPATINAYA, A.Md.Keb",
        "24670120120000777",
        "6203016909960008",
        "199609292025212158",
        "Banjar",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Lamunti"
    ],
    [
        "REGINA",
        "24670130820000502",
        "6203115205970003",
        "199704152025212111",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Hulu"
    ],
    [
        "YUDA EKA SANTOSA",
        "24670130810000240",
        "6203071707770004",
        "197707172025211098",
        "Kapuas",
        "SMK KEUANGAN",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "YUNEDIE, S.Pi",
        "24670130810000137",
        "6203012002770005",
        "197702202025211043",
        "Kapuas",
        "S-1 BUDIDAYA PERAIRAN",
        "Penata Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "REKNO WINARSIH, S.Pd",
        "24670110820000387",
        "6303025905010003",
        "200105192025212035",
        "Sukoharjo",
        "S-1 PENDIDIKAN MATEMATIKA",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 KAPUAS HILIR"
    ],
    [
        "RELYANO",
        "24670130810000838",
        "6203011102890005",
        "198902112025211140",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Perencanaan Pembangunan, Penelitian dan Pengembangan Daerah"
    ],
    [
        "ABDUL AZIZ",
        "24670130810000662",
        "6203011110780005",
        "197810112025211056",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RENA",
        "24670130820000529",
        "6203164808880001",
        "198802232025212119",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kecamatan Pasak Talawang"
    ],
    [
        "RENALDY, S.Kep.,Ns",
        "24670140810000079",
        "6203021110950003",
        "199510112025211125",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "RENALIA, S.Pd",
        "24670110820000665",
        "6213106004010001",
        "200104202025212043",
        "Barito Timur",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Budi Mufakat"
    ],
    [
        "RENDO, S.Kep., Ners.",
        "24670140810000104",
        "6203101111950002",
        "199511112025211140",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Timpah"
    ],
    [
        "RENDRA FAJA'ARDISURYA",
        "24670130810000360",
        "6203130506840001",
        "198406052025211152",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "FEBBY YOLANDA",
        "24670130820000396",
        "6203025902020001",
        "200202192025212015",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "HENDRY SATRIALDI",
        "24670130810000766",
        "6203022412020002",
        "200212242025211023",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RENI KRISTILA, S.Sos.",
        "24670130820000208",
        "6203016509870002",
        "198709252025212095",
        "Barito Utara",
        "S-1 ILMU ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "RENIE AGUSTINAWATI, Amd.Keb",
        "24670140820000148",
        "6203024108960002",
        "199608012025212156",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Barimba"
    ],
    [
        "RENSI ANDIKA, S.Pd",
        "24670110820000325",
        "6203115408980001",
        "199808142025212091",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Tengah Satu Atap"
    ],
    [
        "RENY ANGGRENY, S.E,.M.AP",
        "24670130820000207",
        "6203016008870001",
        "198708202025212102",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "REPA LINA",
        "24670130820000289",
        "6211044709980001",
        "199709072025212131",
        "Kapuas",
        "SMK AGRIBISNIS PERIKANAN",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "ADJI ANDI ALFIAN",
        "24670130810000603",
        "6203011606780001",
        "197806162025211079",
        "Kutai Kartanegara",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "REPIULO",
        "24670130810000969",
        "6204041510850003",
        "198510152025211153",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RESA, S.E",
        "24670120120000510",
        "6203070805960003",
        "199605082025212143",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "RETI MULAINI",
        "24670130820000515",
        "6203015910870003",
        "198710192025212090",
        "Barito Utara",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RETNO, Amd.keb",
        "24670140820000133",
        "6203116210930002",
        "199310222025212122",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "RETNO DEWIWAHYUNI AKIK, S.Pd.I",
        "24670110820000543",
        "6203015511920010",
        "199211152025212163",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Selat Hilir"
    ],
    [
        "RETTY ELLYSABECT",
        "24670130820000238",
        "6203016609920002",
        "199209262025212141",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "REVI RIDUAN",
        "24670130810001038",
        "6203013001810004",
        "198101302025211058",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "REXSI PURWANTO, S.E",
        "24670130810000380",
        "6203110406920003",
        "199206042025211128",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "UPT Puskesmas Pujon"
    ],
    [
        "REYDA RAHMADITA PUTRI",
        "24301020120039518",
        "6203016406990007",
        "199906242025212064",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "REZA JUAN RIAZI, S.H",
        "24670130810000191",
        "6203011207920006",
        "199207122025211131",
        "Kapuas",
        "D-IV ILMU HUKUM",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "REZA MAULANA",
        "24670130810000504",
        "6203012207980004",
        "199807222025211083",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "REZKI FITRIA, S.Pd",
        "24670130820000183",
        "6203014304930007",
        "199304032025212141",
        "Kapuas",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Penata Layanan Operasional",
        "Subbagian Keuangan dan Aset"
    ],
    [
        "REZKY REZITA, S.Pd",
        "24670110820000285",
        "6271044603960001",
        "199603062025212146",
        "Palangka Raya",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Hulu Satu Atap"
    ],
    [
        "RHOBERTSA, S.Pi",
        "24670110810000265",
        "6203011306940004",
        "199406132025211173",
        "Kapuas",
        "S-1 TEKNOLOGI HASIL PERIKANAN",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Murung Satu Atap"
    ],
    [
        "RIA ANGGRAINI, A.Md.Kep",
        "24670120120001410",
        "6203016703940001",
        "199403272025212122",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pulau Telo"
    ],
    [
        "RIA FEBRIASTIKA, A.Md.Kep",
        "24670140820000281",
        "1803146202890003",
        "198902222025212127",
        "Bandar Lampung",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Lamunti"
    ],
    [
        "RIA HANDAYANI, S.Kom",
        "24670130820000220",
        "6203014312870007",
        "198712032025212104",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "RIA ISNAWATI, A.Md.Kep",
        "24670140820000219",
        "6203085001910003",
        "199101102025212152",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Basarang"
    ],
    [
        "RIA NURWULAN SETIAWATI, S.T.",
        "24670120120001383",
        "6203015712970004",
        "199712172025212087",
        "Palangka Raya",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RIAASTRI AZARIAH, S. Pd",
        "24670130820000633",
        "6203136806860001",
        "198606282025212079",
        "Kapuas",
        "S-1 PENDIDIKAN SOSIOLOGI",
        "Guru Ahli Pertama",
        "SMP NEGERI 1 TAMBAN CATUR"
    ],
    [
        "AGUS SALIM",
        "24670130810000782",
        "6203012711780007",
        "197811272025211049",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RIAN HIDAYAT",
        "24670130810000560",
        "6203012507810003",
        "198107252025211101",
        "Banjarmasin",
        "SMK PERTANIAN",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RIAN PRANATA",
        "24670020110000476",
        "3602191304900001",
        "199004132025211136",
        "Serang",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RIANA ANGGRAINI, S.pd.I",
        "24670110820000634",
        "6203044110970004",
        "199710012025212153",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pematang"
    ],
    [
        "RIANALDO FELANOSA, S.T.",
        "24670130810000315",
        "6203012111910003",
        "199111212025211107",
        "Banjarmasin",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RIANI NOVIYANTI HAKE, S.KM",
        "24670130820000623",
        "5371045211870006",
        "198711122025212127",
        "Kupang",
        "S-1 KESEHATAN MASYARAKAT",
        "Penata Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "AHMAD",
        "24670130810000992",
        "6203012702780003",
        "197802272025211039",
        "Kapuas",
        "SMP PAKET B",
        "Pengelola Umum Operasional",
        "Dinas Perhubungan"
    ],
    [
        "RIANTY, S.Pd",
        "24670110820000393",
        "6271024206900001",
        "199006022025212126",
        "Katingan",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SMP NEGERI 3 PASAK TALAWANG SATU ATAP"
    ],
    [
        "RICA KOSMIRAWATY",
        "24670130820000612",
        "6203095207830005",
        "198307122025212104",
        "Kapuas",
        "D-III ADMINISTRASI",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RICCA PAULINA, S.Kep., Ners",
        "24681120120001061",
        "6203016007920008",
        "199207202025212164",
        "Hulu Sungai Utara",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Panamas"
    ],
    [
        "RICKI MARCORIUS",
        "24670130810000086",
        "6203050803980001",
        "199803082025211089",
        "Kapuas",
        "SMK TEKNIK KONSTRUKSI KAYU",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RICKY AFRIANTO",
        "24670130810000359",
        "6203010604940007",
        "199404062025211105",
        "Tapin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RICKY ISKANDAR",
        "24670130810000265",
        "6203010706920008",
        "199206072025211146",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Inspektorat"
    ],
    [
        "RIDA HANDAYANI",
        "24670130820000156",
        "6203016608940003",
        "199408262025212147",
        "Kapuas",
        "SMK TEKNIK KOMPUTER DAN INFORMATIKA",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RIDA HIDAYATULLAH, S.Pd",
        "24670110810000253",
        "6203030708950004",
        "199508072025211146",
        "Kapuas",
        "S-1 PENDIDIKAN MATEMATIKA",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Serapat Timur"
    ],
    [
        "RIDHA HARTANTO, S.Pd.I",
        "24670110810000247",
        "6203041205900004",
        "199005122025211189",
        "Kapuas",
        "D-IV PENDIDIKAN GURU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI1 SIDOMULYO"
    ],
    [
        "RIDUAN",
        "24670130810000055",
        "6203080703830002",
        "198303072025211150",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RIDUAN",
        "24670130810000964",
        "6203010610840002",
        "198410062025211126",
        "Kapuas",
        "SMK SEKRETARIS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RIDUANSYAH, S.Kep,.Ns",
        "24670140810000030",
        "6203012411910005",
        "199111242025211119",
        "Kapuas",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Selat"
    ],
    [
        "APRI YOKO",
        "24670130810000850",
        "6203050104780003",
        "197804012025211087",
        "Palangka Raya",
        "SMU",
        "Operator Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "RIFKY FIRMAN PUTRA, S.Sos",
        "24670130810001006",
        "6203012007970001",
        "199707202025211095",
        "Kapuas",
        "S-1 ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "UPT Puskesmas Barimba"
    ],
    [
        "RIKA, S.Pd.AH",
        "24670110820000383",
        "6203106911890002",
        "198911292025212138",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 3 Dadahup"
    ],
    [
        "RIKA, S.Pd.I",
        "24670110820000408",
        "6203016205860002",
        "198605222025212078",
        "Kapuas",
        "S-1 PENDIDIKAN GURU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Mandomai"
    ],
    [
        "RIKA ASTARI, S.Pd",
        "24670110820000344",
        "6203124511980001",
        "199811052025212113",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Rahung Bungai"
    ],
    [
        "RIKA FRISKA, A.Md.Kep",
        "24670140820000378",
        "6203095202990001",
        "199902122025212075",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Palangkau"
    ],
    [
        "RIKI ALFIANSYAH, A.Md.Kep",
        "24670140810000054",
        "6203092804970006",
        "199704282025211099",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mantangai"
    ],
    [
        "RIKI JANUARDI",
        "24670130810000693",
        "6203012301930004",
        "199301232025211115",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RIKSAN PASARIBU, S.Pd",
        "24670110810000279",
        "6211065803890001",
        "198903182025211134",
        "Pulang Pisau",
        "S-1/A-IV PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Menteng Raya"
    ],
    [
        "RIMA APRILINI, S.I.P",
        "24670130820000007",
        "6204056804960002",
        "199604282025212167",
        "Barito Selatan",
        "S-1 ILMU PEMERINTAHAN",
        "Penata Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "ARDIANSYAH",
        "24670130810000758",
        "6203011705780001",
        "197805172025211072",
        "Kapuas",
        "SMK AKUNTANSI DAN KEUANGAN",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RIMANIA KUSMI WULANSARI, S.S.",
        "24670110820000450",
        "6203095505950005",
        "199505152025212216",
        "Magelang",
        "S-1 SASTRA INGGRIS",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Mantangai"
    ],
    [
        "RINA",
        "24670130820000429",
        "6203026804910002",
        "199204282025212182",
        "Kapuas",
        "D-I ADMINISTRASI DAN KEUANGAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RINA, S.Pd",
        "24670110820000429",
        "6203075811990001",
        "199911182025212079",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 6 Mantangai"
    ],
    [
        "RINA FAUZIAH, S. Ak",
        "24670130820000135",
        "6371036609890012",
        "198909262025212128",
        "Banjarmasin",
        "S-1 AKUNTANSI EKONOMI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RINA RIZQI, S.Pd.I.",
        "24670130820000639",
        "6203015905910003",
        "199305192025212169",
        "Kapuas",
        "S-1 / AKTA IV PAI",
        "Guru Ahli Pertama",
        "SD NEGERI 4 ANJIR MAMBULAU TIMUR"
    ],
    [
        "RINALDI ARIA CHANDRA, S.Pd",
        "24670110810000259",
        "6203100701000002",
        "200001072025211050",
        "Palangka Raya",
        "S-1 PENDIDIKAN EKONOMI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Timpah"
    ],
    [
        "RINALDI MAULANA, S.I.KOM",
        "24670130810000457",
        "6203011904980005",
        "199804192025211073",
        "Kapuas",
        "S-1 ILMU KOMUNIKASI",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ISTIANA SALUM",
        "24670130820000433",
        "6203015712020008",
        "200212172025212016",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RINI, Amd.Kep",
        "24670140820000159",
        "6210024905910007",
        "199105092025212184",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Jangkang"
    ],
    [
        "RINI, Amd, Keb",
        "24670140820000289",
        "6203115006920003",
        "199206102025212204",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "ARIPIN",
        "24670130810000502",
        "6203010305780012",
        "197805032025211083",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "RINITA, S.Pd",
        "24670110820000372",
        "6203016904970002",
        "199704292025212121",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP NEGERI 14 MANTANGAI SATU ATAP"
    ],
    [
        "JOJO",
        "24670130810000947",
        "6203032504020001",
        "200204252025211021",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RINTO",
        "24670130810000314",
        "6203010911820002",
        "198211092025211102",
        "Kapuas",
        "D-I KOMPUTER",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RINTO",
        "24670130810000458",
        "6203012111830003",
        "198311212025211071",
        "Kapuas",
        "D-I ADMINISTRASI KANTOR",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RINTO, A. Md",
        "24670130810000065",
        "6203011002810001",
        "198102102025211112",
        "Kapuas",
        "AKADEMI MANAJEMEN INFORMATIKA DAN KOMPUTER",
        "Pengelola Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "M.RIDADDIN",
        "24670130810000961",
        "6203012408020004",
        "200208242025211015",
        "Kapuas",
        "MADRASAH ALIYAH MATEMATIKA DAN IPA",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "MAULANA IQBAL",
        "24670130810000795",
        "6203080405020005",
        "200205042025211022",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RIO SAPUTRA",
        "24670130810000109",
        "6203022808920003",
        "199208282025211177",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RIONO SAPUTRA, S.H.",
        "24670130810000577",
        "6203013011870005",
        "198711302025211124",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Penata Layanan Operasional",
        "Kecamatan Kapuas Hulu"
    ],
    [
        "RIRIN",
        "24670130820000089",
        "6203014310920008",
        "199210032025212131",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "RIRIN, S.Pd",
        "24670110820000524",
        "6203095703870005",
        "198703172025212096",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 3 Pujon"
    ],
    [
        "RIRIN FEBRIANI",
        "24670130820000430",
        "6203095102930004",
        "199302112025212126",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "RISKI PADLI",
        "24670130810000367",
        "6203010108890004",
        "198908012025211182",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "RISKY",
        "24670130820000280",
        "6203012012910005",
        "199112202025212125",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "MAULIDATUL HASANAH",
        "24670130820000488",
        "6203155505020001",
        "200205152025212014",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Dadahup"
    ],
    [
        "RISMA FITRIYANA",
        "24670120120001770",
        "6203014506970009",
        "199706062025212143",
        "Tapin",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RISMA SANTI, S.Pd",
        "24670110820000379",
        "6371024711000007",
        "200011072025212053",
        "Banjarmasin",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Kaladan Jaya"
    ],
    [
        "RISNA, S.M.",
        "24670130820000107",
        "6203084506980003",
        "199702102025212127",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "RISNA NOVITA, S.P",
        "24670130820000435",
        "6203076703980006",
        "199803272025212110",
        "Kapuas",
        "S-1 AGRIBISNIS PERTANIAN",
        "Penata Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "RISNAWATI, S.E",
        "24670110820000732",
        "6203094607960006",
        "199607062025212161",
        "Kapuas",
        "S-1 MANAJEMEN EKONOMI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Belawang"
    ],
    [
        "RISNO EFENDI",
        "24670130810000402",
        "6203053008850003",
        "198508302025211087",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RISTATIE, S.Pd",
        "24670110820000765",
        "6203114505990004",
        "199905052025212148",
        "Kapuas",
        "D-IV PENDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Kapuas Tengah Satu Atap"
    ],
    [
        "RISWANDI, S.Kom",
        "24670110810000290",
        "6203030409940001",
        "199409042025211136",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Timur"
    ],
    [
        "BONISON",
        "24670130810000884",
        "6203022906780002",
        "197806292025211051",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "RITA PURWANINGSIH, A.Md. Keb",
        "24670140820000130",
        "6203046608940001",
        "199408262025212146",
        "Pulang Pisau",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Tamban Catur"
    ],
    [
        "DEGUS HARIYANTO",
        "24670130810000138",
        "6203012008780007",
        "197808202025211072",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RITNA WATI, S.Pd",
        "24670110820000345",
        "6203056301980001",
        "199801232025212087",
        "Kapuas",
        "S-1 PENDDIDIKAN AGAMA KRISTEN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mandomai"
    ],
    [
        "RIVIA NORHANIVA, S.E.",
        "24670130820000199",
        "6203016606950015",
        "199506262025212185",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "RIVKA ANETA. A, S.Pd",
        "24670110820000746",
        "6203055805900003",
        "199005182025212126",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mandomai"
    ],
    [
        "RIWANDI, S.Pd",
        "24670110810000095",
        "6203052901910002",
        "199101292025211103",
        "Gunung Mas",
        "S-1 PENDIDIKAN JASMANI, KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pantai"
    ],
    [
        "RIWANDY KESDIONO, S.Pd",
        "24670130810000282",
        "6203021111870003",
        "198711112025211181",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Penata Layanan Operasional",
        "Bidang Pembinaan PAUD dan DIKMAS"
    ],
    [
        "MUHAMMAD ZAINI, A.Md.Ak.",
        "24670130810000509",
        "6203070806020007",
        "200206082025211012",
        "Kapuas",
        "D-III AKUNTANSI",
        "Pengelola Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RIYAN HIDAYAT",
        "24670130810000193",
        "6203012701890007",
        "198901272025211122",
        "Palangka Raya",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "RIYAN RAMADHANA",
        "24670130810000418",
        "6203011302940003",
        "199402132025211096",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "DIAN ANGGRIYANI",
        "24670130820000428",
        "6203017112820006",
        "197812312025212116",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RIZA, S.Pd",
        "24670110820000641",
        "6203094507920006",
        "198908062025212113",
        "Kapuas",
        "S-1 ADMINISTRASI PENDIDIKAN",
        "Guru Ahli Pertama",
        "SD Negeri 1 Danau Pantau"
    ],
    [
        "NABILLA SHAFA NOVIANA",
        "24670130820000395",
        "6203015811040003",
        "200211182025212021",
        "Kapuas",
        "SMA MATEMATIKA DAN ILMU PENGETAHUAN ALAM",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RIZAL SAFWAN ANSHORI, S.Kom",
        "24670130810000422",
        "6203011409960002",
        "199609142025211109",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "NOVA RINA",
        "24670130820000373",
        "6203016107020005",
        "200206212025212011",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RIZALI ILMI",
        "24670130810000281",
        "6203031302920001",
        "199202132025211114",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RIZALI NOOR",
        "24670130810000164",
        "6203031704920001",
        "199204172025211133",
        "Banjarmasin",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Timur"
    ],
    [
        "RIZKA DYAH RAMADHANI, AMd.Kep",
        "24670140820000003",
        "6203015205860010",
        "198605122025212119",
        "Sidoarjo",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Tatas"
    ],
    [
        "RIZKA GIANY",
        "24670130820000114",
        "6203016208970004",
        "199708222025212115",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "RIZKA KHALISA, S.M",
        "24670020120001848",
        "6203010408980006",
        "199808042025212123",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "UPT Puskesmas Pulau Telo"
    ],
    [
        "RIZQAN AKMAL",
        "24670110810000248",
        "6304030902000001",
        "200002092025211048",
        "Banjarmasin",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Serapat Tengah"
    ],
    [
        "RIZQI AYU AGHNI OKTAVIA, S.Pd",
        "24670110820000519",
        "6203044910940004",
        "199410092025212140",
        "Barito Kuala",
        "S-1 PENDIDIKAN GEOGRAFI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palingkau Baru"
    ],
    [
        "ROBBI ANNOOR",
        "24670130810000132",
        "6203012509900001",
        "199009252025211132",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "ROBBY ANTHONNY, A.Md",
        "24670130810000032",
        "6203010710820007",
        "198210072025211113",
        "Kapuas",
        "D-III MANAJEMEN INFORMATIKA",
        "Pengelola Layanan Operasional",
        "Bidang Pembinaan Ketenagaan"
    ],
    [
        "ROBBY HARTONO, S.pd.I",
        "24670110810000184",
        "6203093011970004",
        "199712302025211059",
        "Kapuas",
        "S-1 PENDIDIKAN TEKNOLOGI INFORMASI",
        "Guru Ahli Pertama",
        "SMP NEGERI 1 MANTANGAI SATU ATAP"
    ],
    [
        "ROBBY SANTOSO",
        "24670130810000620",
        "6203012509910004",
        "199109252025211128",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ROBBY YUSUF, A.Md.Kep",
        "24670140810000137",
        "6203050203950001",
        "199503022025211139",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "EDINOOR SULAIMAN, A. Md",
        "24670130810000005",
        "6203021110780001",
        "197810112025211054",
        "Barito Utara",
        "AKADEMI MANAJEMEN INFORMATIKA DAN KOMPUTER",
        "Pengelola Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "ROBI KRISTANTO YAHYA, A.Md.Kep",
        "24670140810000131",
        "6203061212930002",
        "199612122025211148",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ROBINTANG MAHARANI BAKKARA, S.pd.I",
        "24670110820000609",
        "1208164707000001",
        "200007072025212079",
        "Simalungun",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Teluk Palinget"
    ],
    [
        "ROBY, A.Md.Kep",
        "24670140810000142",
        "6203092108930003",
        "199308212025211127",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Dadahup"
    ],
    [
        "RODYMAN",
        "24670130810000037",
        "6203012106860002",
        "198706212025211119",
        "Kotawaringin Barat",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "ROHANA.AR, S.Pd",
        "24670130820000404",
        "6203016305920008",
        "199205232025212169",
        "Barito Kuala",
        "SLTA/SMA SEDERAJAT",
        "Guru Ahli Pertama",
        "SD NEGERI 1 MANGGALA PERMAI"
    ],
    [
        "ELGEN TANANO",
        "24670130810001031",
        "6203020909780002",
        "197809092025211088",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RAFI SAPUTRA",
        "24670130810000684",
        "6203012909020002",
        "200209292025211024",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ROLANA, S.Kep.,Ners",
        "24670140820000227",
        "6203016111950002",
        "199511212025212142",
        "Kotawaringin Timur",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Pulau Telo"
    ],
    [
        "ROLLY",
        "24670130810000477",
        "6203012101820002",
        "198201212025211083",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "ROLLY HANDOYO",
        "24670130810000833",
        "6203081111850004",
        "198511112025211133",
        "Kapuas",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ELPINA",
        "24670130820000606",
        "6203056812780001",
        "197812282025212039",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ROMANO",
        "24670130810000501",
        "6203011504850003",
        "198504152025211144",
        "Kapuas",
        "D-I PENDIDIKAN KOMPUTER",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ROMANSYAH",
        "24670130810000546",
        "6203011008880007",
        "198808102025211236",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ROMITA PURNAMA SARI, S.Pd",
        "24670110820000625",
        "6203177006890001",
        "198906302025212126",
        "Murung Raya",
        "S-1 PENDIDIKAN SEJARAH",
        "Guru Ahli Pertama",
        "SD Negeri 1 Masupa Ria"
    ],
    [
        "ROMONDO TEGUH",
        "24670130810000806",
        "6203022606830001",
        "198306262025211130",
        "Kapuas",
        "SMU",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RONA WIDIAWATI, S.Pd",
        "24670110820000600",
        "6271034711960006",
        "199611072025212138",
        "Kapuas",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD Negeri 1 Hurung Tabengan"
    ],
    [
        "RONI HIDAYAT",
        "24670130810000844",
        "6203010810890004",
        "198909082025211150",
        "Kapuas",
        "MA IPS",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "RONI SETIAWAN, S.AP",
        "24670130810000492",
        "6203010508910006",
        "199108052025211157",
        "Kapuas",
        "S-1 ADMINITRASI PUBLIK",
        "Penata Layanan Operasional",
        "Kecamatan Bataguh"
    ],
    [
        "RORI WORKA, S.Pd",
        "24670110810000178",
        "6203090802930001",
        "199302082025211113",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Kapar"
    ],
    [
        "HATNI",
        "24670130810000975",
        "6203061603730001",
        "197804102025211099",
        "Banjarmasin",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HENDRU",
        "24670130810000846",
        "6203051312780002",
        "197812132025211061",
        "Kapuas",
        "SMT PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "ROYIMAN MOHAIDIN",
        "24670130810000682",
        "6203020508950003",
        "199508052025211120",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "I NYOMAN PASEK BAGIARTA",
        "24670130810000538",
        "6203011211780008",
        "197811122025211063",
        "Buleleng",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RUDIANSYAH",
        "24670130810000611",
        "6203012010830002",
        "198310202025211106",
        "Kapuas",
        "SMEA KOPERASI",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RUDIYANATA",
        "24670130810000270",
        "6203011701940003",
        "199401172025211106",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "RUDY IRAWAN",
        "24670130810001040",
        "6203012510830001",
        "198310252025211084",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "RUFNI PEBRIANI, S.Pd",
        "24670110820000558",
        "6203014902900017",
        "199002092025212154",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Maluen"
    ],
    [
        "RUMILAH",
        "24670130820000383",
        "6203016501840003",
        "198401252025212050",
        "Karawang",
        "MADRASAH ALIYAH BAHASA",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "RUPIAH, A.Md.Keb",
        "24670140820000336",
        "6203055812980001",
        "199810012025212106",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "IBRAMSYAH",
        "24670130810000872",
        "6203070709780001",
        "197809072025211074",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RUSDIANA, S.Kep.,Ners",
        "24670140820000331",
        "6203115509840001",
        "198409152025212084",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Pujon"
    ],
    [
        "RUSITA",
        "24670130820000369",
        "6203016312840003",
        "198412232025212062",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "RUSLI",
        "24670130810000836",
        "6203016505920007",
        "199202052025211154",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "IRMAS SARI, S.Pd.I",
        "24670110820000005",
        "6203066708780001",
        "197808272025212032",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 2 Banama"
    ],
    [
        "JOHN BRORY, S.E.",
        "24670130810000168",
        "6301062406780002",
        "197806242025211058",
        "Kapuas",
        "S-1 EKONOMI PEMBANGUNAN",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "RUSMINI",
        "24670130820000126",
        "6203014406880012",
        "198806042025212114",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RUSMITA",
        "24670130820000159",
        "6203014905920006",
        "199205092025212177",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "RUSNA YULIDA",
        "24670130820000490",
        "6203015408920006",
        "199208142025212165",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "RUSTAM EFENDI, S.Pi",
        "24670130810000184",
        "6203012512940008",
        "199412252025211125",
        "Kapuas",
        "S-1 BUDIDAYA PERAIRAN",
        "Penata Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "RUSTINATI, S.Pd.I",
        "24670110820000772",
        "6203086907870001",
        "198707292025212087",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Sido Mulyo"
    ],
    [
        "RUT SISTER",
        "24670130820000381",
        "6203035908990004",
        "199908192025212086",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "RYAN RINALDI",
        "24670130810000623",
        "6203012606970004",
        "199706262025211110",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SABAR TRI ATMAJA",
        "24670130810000581",
        "6203010411990003",
        "199911092025211062",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "LIA, S.E",
        "24670130820000063",
        "6471056612780009",
        "197812262025212028",
        "Palangka Raya",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SAFRI",
        "24670130810000077",
        "6203012008930010",
        "199308202025211126",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Inspektorat"
    ],
    [
        "SAFRIL, S.Kep.,Ns.",
        "24670140810000138",
        "6271032111970001",
        "199711212025211094",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Pujon"
    ],
    [
        "SAHDAN",
        "24670130810000665",
        "6203011212950008",
        "199512122025211191",
        "Kapuas",
        "SMK AKUNTANSI DAN KEUANGAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "LILI YATI, S.Pd",
        "24670130820000578",
        "6203074603780003",
        "197803062025212040",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SAIDILAH",
        "24670130810000936",
        "6203031004810003",
        "198104102025211109",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MADE FRANDI WINATA, S.Pd",
        "24670110810000195",
        "6210010609780001",
        "197809062025211065",
        "Kapuas",
        "S-1/A-IV PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 2 Pujon"
    ],
    [
        "MARLIANCE",
        "24670130820000494",
        "6203026809780001",
        "197809282025212030",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SAIPUL RAHMAN, S.Pd.I",
        "24670130810000335",
        "6203011609850008",
        "198509162025211113",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "SAKTI LARASATI ENDIS PURWITO, SM",
        "24670620120000198",
        "6203015709980002",
        "199809172025212076",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SALASIAH, S.Pd",
        "24670110820000475",
        "6203045907860002",
        "198607192025212091",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Simpang Bunga Tanjung"
    ],
    [
        "SALDI, A.Md.Kep",
        "24670140810000047",
        "6203050206980004",
        "199806022025211072",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "SALIE, S.Pd",
        "24670110820000706",
        "6271034401840007",
        "198401042025212072",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Teluk Hiri"
    ],
    [
        "SALMA SAFITRI, S.pd.I",
        "24670110820000328",
        "6203016910020002",
        "200210292025212015",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Maluen"
    ],
    [
        "SALMAH, S.Pd",
        "24670110820000377",
        "6203054310010004",
        "200110032025212034",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 PENDA KATAPI"
    ],
    [
        "SALWA HASANAH, S.Pd",
        "24670110820000618",
        "6203045409000002",
        "200009142025212052",
        "Kapuas",
        "A-V BIDANG PENDIDIKAN",
        "Guru Ahli Pertama",
        "SMP Negeri 9 Bataguh Satu Atap"
    ],
    [
        "SAMIAH",
        "24670130820000314",
        "6203076212900001",
        "199012222025212120",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Baru"
    ],
    [
        "SAMPANI NGULAHNI",
        "24670130820000211",
        "6203024907860003",
        "198607092025212102",
        "Barito Selatan",
        "SMK PARIWISATA",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "SAMPRI, S.Kep.Ns",
        "24670130810001012",
        "6203110206950004",
        "199506022025211116",
        "Kapuas",
        "NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Jangkang"
    ],
    [
        "SAMSUDIN, S.Pd.I",
        "24670110810000144",
        "6203012909860003",
        "198609292025211136",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 5 Selat Hulu"
    ],
    [
        "SAMSUDIN NOOR",
        "24670130810000935",
        "6203071010910010",
        "199110102025211256",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SANDO",
        "24670130810000416",
        "6203012409900006",
        "199109242025211116",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "SANDRA ESTER",
        "24670130820000642",
        "6204055506850002",
        "198307152025212070",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SANDY SAPUTRA, S.AP",
        "24670130810000355",
        "6203071503900005",
        "199003152025211146",
        "Kapuas",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Dinas Perumahan, Kawasan Permukiman, dan Pertanahan"
    ],
    [
        "SANO KARIANTO",
        "24670130810000475",
        "6203021204860004",
        "198604122025211149",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MADE CLARA SEPTIANA, S.Pd",
        "24670110820000619",
        "6203084409960001",
        "199609042025212120",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD NEGERI 2 TERUSAN RAYA HULU"
    ],
    [
        "MILAWATI",
        "24670130820000603",
        "6203024905780004",
        "197805092025212041",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUA",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SANTIA, S.Pd",
        "24670130820000627",
        "6203085201950001",
        "199501122025212138",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 4 Tambun Raya"
    ],
    [
        "SAPRUDINOR",
        "24670130810000843",
        "6203033006960002",
        "199606302025211122",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SAPRULLAH, S.Pd.I",
        "24670110810000150",
        "6203012908900002",
        "199008292025211132",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 3 Mantangai"
    ],
    [
        "SARBANI, S.Pd",
        "24670110810000176",
        "6203041402840002",
        "198402142025211119",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Baranggau"
    ],
    [
        "SARDI SATRIAWAN",
        "24670130810000381",
        "6203011010940004",
        "199410102025211266",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "SARDIMAN, S.Pd.I",
        "24670110810000114",
        "6211011906910003",
        "199106192025211137",
        "Pulang Pisau",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Hulu"
    ],
    [
        "SARIE",
        "24670130820000514",
        "6203014412840002",
        "198412042025212052",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SARIFAH, S.Pd",
        "24670110820000440",
        "6203086012830001",
        "198510202025212088",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sumber Mulya"
    ],
    [
        "MUHAMMAD YUSRO",
        "24670130810000893",
        "6203012201780002",
        "197801222025211048",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SARINAH",
        "24670130820000062",
        "6203066705950003",
        "199505272025212133",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "SARIPIPA",
        "24670130820000516",
        "6203014212810001",
        "198112022025212053",
        "Gunung Mas",
        "SMK PERIKANAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SARISNA, S.Kep.,Ners",
        "24670140820000284",
        "6203066404930002",
        "199304242025212175",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Palingkau"
    ],
    [
        "SARJONI",
        "24670130810000271",
        "6203051104980003",
        "199804112025211075",
        "Kapuas",
        "SMK TEKNIK KONSTRUKSI KAYU",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "SARKANI, A.Md",
        "24670130810000145",
        "6203012106840007",
        "198406212025211108",
        "Kapuas",
        "D-III MANAJEMEN INFORMATIKA DAN KOMPUTER",
        "Pengelola Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "SARLI",
        "24670130820000584",
        "6203094703840004",
        "198403072025212088",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SARUJI",
        "24670130810000863",
        "6203062202940002",
        "199401222025211075",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "SATRIA",
        "24670130810000318",
        "6203012709970004",
        "199709272025211095",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "SATRIA PRATAMA",
        "24670130810000910",
        "6203022209860004",
        "198609222025211106",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "MUTIARA",
        "24670130820000599",
        "6204056812810002",
        "197803172025212035",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "PIEN UYEN, A.Md",
        "24670130820000628",
        "6203016012780004",
        "197812202025212035",
        "Pulang Pisau",
        "D-III MANAGEMEN INFORMATIKA",
        "Pengelola Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "REMIE",
        "24670130820000511",
        "6203114705800003",
        "197805072025212038",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SEFRIYANIE, S.Pd",
        "24301220120112917",
        "6203017006900007",
        "199006302025212138",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Penata Layanan Operasional",
        "Bidang Pembinaan SMP"
    ],
    [
        "SELWI",
        "24670130810000633",
        "6203011109780005",
        "197809112025211071",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Bidang Pembinaan Ketenagaan"
    ],
    [
        "SELA ANGGERAINI, S. Pd.",
        "24670110820000312",
        "6203065203010001",
        "200103122025212039",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bunga Mawar"
    ],
    [
        "SELAWATI, S.Pd",
        "24670110820000667",
        "6203105209970001",
        "199911212025212074",
        "Barito Selatan",
        "D-I PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SMP Negeri 7 Timpah Satu Atap"
    ],
    [
        "SELLIN, A.md,Kep",
        "24670140820000387",
        "6203025407980001",
        "199807142025212105",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SELPI",
        "24670130820000002",
        "6203104406930002",
        "199306042025212157",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kecamatan Timpah"
    ],
    [
        "SINARTO",
        "24670130810000879",
        "6203021709780001",
        "197809172025211069",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SELVY DEWINA",
        "24670130820000190",
        "6203014202950001",
        "199502022025212177",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SIONNATA, S.E.",
        "24670130820000347",
        "6203015706780007",
        "197806272025212045",
        "Barito Selatan",
        "D-IV/S-1 EKONOMI PEMBANGUNAN",
        "Penata Layanan Operasional",
        "UPT Puskesmas Selat"
    ],
    [
        "SENDI, S.Pd",
        "24670110810000080",
        "6203100108990002",
        "199908012025211075",
        "Kapuas",
        "D-I PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 2 Lungkuh Layang"
    ],
    [
        "SOLEHA INDRIANI",
        "24670130820000088",
        "6203017103780006",
        "197803312025212024",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "SEPRI WAHYU MASMIRI",
        "24670130820000149",
        "6203016009850009",
        "198509202025212077",
        "Gunung Mas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SEPRIAN NURIFANGGA",
        "24670130810000334",
        "6203011909910006",
        "199109192025211136",
        "Palangka Raya",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SEPRIANTO SAPUTRA",
        "24670130810000559",
        "6203051109990002",
        "199909112025211082",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "SEPTHIAN HARTATO",
        "24670130810000709",
        "6203010809950013",
        "199509082025211100",
        "Kapuas",
        "SMK TATA NIAGA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SEPTIAN, A.md",
        "24670130810000104",
        "6203051209900002",
        "199009122025211123",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "SEPTIAN PRAYOGA, S. Kom",
        "24670120110000742",
        "6203011309920003",
        "199209132025211144",
        "Kapuas",
        "S-1 KOMPUTER SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "SEPTIANA ASTUTIE, S. Pd",
        "24670130820000558",
        "6203084309980002",
        "199809032025212097",
        "Hulu Sungai Tengah",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Penata Layanan Operasional",
        "Bidang Pembinaan PAUD dan DIKMAS"
    ],
    [
        "SEPTIANAE, S.Th",
        "24670110820000657",
        "6211035709970001",
        "199709172025212120",
        "Pulang Pisau",
        "S-1 TEOLOGI",
        "Guru Ahli Pertama",
        "SD NEGERI 1 HUMBANG RAYA"
    ],
    [
        "SEPTIANI DARA, S.Pd",
        "24670110820000352",
        "6203054509980002",
        "199809052025212074",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Katunjung"
    ],
    [
        "SEPTIARINDA, S.Kep.,Ners",
        "24670140820000401",
        "6203104509930002",
        "199309052025212149",
        "Palangka Raya",
        "S-1 KEPERAWATAN + NERS + STR",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Danau Rawah"
    ],
    [
        "SERVIA DEVI ANGGIRIANY, A.Md.Keb",
        "24670120120000803",
        "6203016805960005",
        "199605282025212132",
        "Kapuas",
        "D-III KEBIDANAN",
        "Pengelola Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SESKO OKTA GARSIA, A.Md.Kep",
        "24670140810000129",
        "6203012110950009",
        "199510212025211088",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pulau Telo"
    ],
    [
        "SUFIAN NOOR, SH",
        "24670130810000444",
        "6203010304780008",
        "197804032025211101",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "SETIAWAN",
        "24670130810000044",
        "6203010901830007",
        "198301092025211086",
        "Kapuas",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SUHAIMI",
        "24670130810000686",
        "6203010105780009",
        "197805012025211093",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SETO INDRA KURNIA, A. Md",
        "24670130810000110",
        "6203011312870004",
        "198712132025211110",
        "Kapuas",
        "D-III ADVERTISING",
        "Pengelola Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "SETYAWAN",
        "24670130810000404",
        "6203010109820003",
        "198209012025211116",
        "Ponorogo",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "SEWATI, A. Md.Kep",
        "24670140820000082",
        "6203095310830002",
        "198310132025212071",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Lamunti"
    ],
    [
        "SHELLA CITRA OKTAVIANI, S.Ak",
        "24670130820000076",
        "6203016310980010",
        "199810232025212058",
        "Hulu Sungai Selatan",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "SHELVIA HENDRIANI AS, S.pd.I",
        "24670110820000374",
        "6203036605020001",
        "200205262025212025",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Anjir Serapat Baru"
    ],
    [
        "SHOFI RAMADHANA",
        "24670130810000071",
        "6203071709820006",
        "198207172025211142",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "SHOLEHAH FARIDATON JANNAH, S.Pd",
        "24670110820000694",
        "6203014902010005",
        "200102092025212062",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Selat Utara"
    ],
    [
        "SIGIT SUPRIANTO",
        "24670130810000062",
        "6203080812940003",
        "199412082025211122",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RAZJASA DHARMA PUTRA DAUD",
        "24670130810000476",
        "6203010403020010",
        "200203042025211023",
        "Kapuas",
        "SMK AKUNTANSI DAN KEUANGAN",
        "Operator Layanan Operasional",
        "Kecamatan Pasak Talawang"
    ],
    [
        "SINARTO",
        "24670130810000034",
        "6203010805960007",
        "199605082025211119",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "TAUFIK",
        "24670130810000860",
        "6203011708790006",
        "197808172025211144",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "SINDY SEPALAN",
        "24670130820000100",
        "6203016209890007",
        "198909222025212087",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SINDY YULESI, S.T.P.",
        "24670220120003641",
        "6203016207970004",
        "199707222025212100",
        "Kapuas",
        "S-1 TEKNOLOGI INDUSTRI PERTANIAN",
        "Penata Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "SINGENG, S.Kep.,Ners",
        "24670140810000058",
        "6203110911920001",
        "199209112025211154",
        "Kapuas",
        "PROFESI NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Jangkang"
    ],
    [
        "SINSON",
        "24670130810000706",
        "6311072304830001",
        "198304232025211096",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MAHMUD JAUHARI, S.Pd",
        "24670130810000989",
        "6203042706920002",
        "Belum ada nip",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Guru Ahli Pertama",
        "SD NEGERI 2 TERUSAN RAYA BARAT"
    ],
    [
        "RISMA",
        "24670130820000416",
        "6203065107020002",
        "200207112025212015",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "UJANG SUHENDRI",
        "24670130810000723",
        "6203010412780005",
        "197812042025211049",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SIPRA, Amd.Kep.",
        "24670140820000005",
        "6203125404930001",
        "199304142025212166",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Pinang"
    ],
    [
        "SISAE BUNGA ASMARA, A.Md.A.K",
        "24670120120000328",
        "6203015309990004",
        "199909132025212059",
        "Kapuas",
        "D-III ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SISCA TRIMERRY ANGGREANY",
        "24670130820000443",
        "6203017012810001",
        "198112302025212045",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SISILIA, S.E.",
        "24670130820000224",
        "6203015909880002",
        "198809192025212137",
        "Palangka Raya",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "SISKA DWI WIDIASTUTI, S.E.",
        "24670130820000164",
        "6203014205870007",
        "198705022025212145",
        "Semarang",
        "S-1 AKUNTANSI EKONOMI",
        "Penata Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "WALTHO",
        "24670130810000865",
        "6203012008780004",
        "197808202025211075",
        "Kapuas",
        "SMU",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SITI AINUR AMINAH, S.Pd",
        "24670110820000363",
        "6203096207980002",
        "199807222025212089",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Manyahi"
    ],
    [
        "SITI AMINAH, S.Pd.I",
        "24670130820000645",
        "6203037007870001",
        "198707302025212101",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lunuk Ramba"
    ],
    [
        "WIDYA SARI MASDIPURA, SE",
        "24670130820000094",
        "6203016912780008",
        "197812292025212039",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "ROLA DAMAYANTI",
        "24670130820000540",
        "6203056612020001",
        "200212262025212012",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SITI FATIMAH, S.Pd",
        "24670110820000273",
        "6203145307860001",
        "198507132025212092",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 3 Lupak Dalam"
    ],
    [
        "SITI FATIMAH H.SYAMSUDIN, S. Hut",
        "24670130820000311",
        "6203016009810002",
        "198109202025212048",
        "Kapuas",
        "S-1 MANAJEMEN HUTAN",
        "Penata Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "YARDI",
        "24670130810000952",
        "6203071605780002",
        "197805162025211074",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Murung"
    ],
    [
        "SITI HASANAH, S.Pd",
        "24670110820000676",
        "6203085605950001",
        "199504162025212131",
        "Kapuas",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Mampai"
    ],
    [
        "SITI JUBAIDAH, S.Pd.I",
        "24301220120184338",
        "6203015905930004",
        "199305192025212168",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SITI NUR SHIFA",
        "24670130820000481",
        "6203026510020001",
        "200210252025212018",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SITI KAMARIAH",
        "24670130820000329",
        "6203105704890002",
        "198904172025212143",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SITI KAMARIAH, S.pd.I",
        "24670130820000070",
        "6203015510900004",
        "199010152025212173",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Penata Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "SITI KHADIJAH",
        "24670130820000593",
        "6203044804890002",
        "198904082025212158",
        "Banjar",
        "SMP PAKET B",
        "Pengelola Umum Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SITI MARNI, S.Pd",
        "24670110820000688",
        "6203035510960001",
        "199510152025212168",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Pulau Kupang"
    ],
    [
        "SITI MAULINA, S.Pd",
        "24670110820000506",
        "6203015207990006",
        "199907122025212103",
        "Kapuas",
        "S-1 TADRIS BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD Negeri 2 Pulau Kupang"
    ],
    [
        "SITI MUNAWARAH, S.Pd.I",
        "24670110820000447",
        "6203036905920001",
        "199205292025212140",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Guru Ahli Pertama",
        "SD Negeri 3 Basarang"
    ],
    [
        "AGUS FITHRY",
        "24670130810000030",
        "6203012408790005",
        "197908242025211063",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "SONDANG JULIANA PANGARIBUAN",
        "24670130820000458",
        "6203015312020004",
        "200212132025212013",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SITI PATIMAH, A.Md.Keb",
        "24670140820000294",
        "6304044909910001",
        "199109092025212184",
        "Barito Kuala",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "SITI RAHMANIAH, S.Pd",
        "24670110820000296",
        "6203016004970004",
        "199805092025212114",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Terusan Raya"
    ],
    [
        "SITI ROSAIDAH, A.Md.Kep",
        "24670140820000230",
        "6203094406900001",
        "199006042025212177",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Lamunti"
    ],
    [
        "SITI ULULIAH, S.Pd",
        "24670110820000282",
        "6401066504960001",
        "199604252025212144",
        "Paser",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 3 BANDAR MEKAR"
    ],
    [
        "SITY KHADIJAH, Amd.Keb",
        "24670140820000145",
        "6203046208930001",
        "199308222025212135",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SLAMET RINGKAP, S.Sos",
        "24670130810000076",
        "6203010104890003",
        "198904012025211163",
        "Kapuas",
        "S-1/A-IV ILMU PEMERINTAHAN",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SLAMET RIYADI",
        "24670130810000337",
        "6203010504900005",
        "199104052025211151",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "AHMAD IWANSYAH",
        "24670130810000636",
        "6203012510790011",
        "197910252025211066",
        "Banjarmasin",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SNEZA AYU FEBRIANI, S.Sos",
        "24670130820000148",
        "6203014802890003",
        "198902082025212107",
        "Kapuas",
        "S-1 ILMU ADMINISTRASI NEGARA",
        "Penata Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "SOLEHA",
        "24670130820000602",
        "6203096808880009",
        "198808282025212165",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AHMAD KURNIA",
        "24670130810000480",
        "6203012210790002",
        "197910222025211047",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "SOLIHIN",
        "24670130810000899",
        "6203162805890001",
        "198905282025211116",
        "Kapuas",
        "SMK BUDIDAYA TANAMAN",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "TEGUH PRASETYO",
        "24670130810000842",
        "6203011703020004",
        "200203172025211016",
        "Kapuas",
        "D-I ILMU PENGETAHUAN ALAM",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MANDRA H. SEM",
        "24670130810001002",
        "6203102101760002",
        "Belum ada nip",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SORAYA APRILIANI",
        "24670130820000425",
        "6203025704970002",
        "199704172025212140",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "CAROLINA DEASY, A.Md",
        "24670130820000036",
        "6203015012790009",
        "197912102025212062",
        "Kapuas",
        "D-III TEKNIK PERTAMBANGAN",
        "Pengelola Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SOVIA RAHMAH RUSITA, S.pd.I",
        "24670110820000739",
        "6203094208990001",
        "199908022025212097",
        "Kapuas",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lamunti Permai"
    ],
    [
        "EKO BUDIYANTO",
        "24670130810000839",
        "6203012111790004",
        "197911212025211062",
        "Kendal",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SRI AYUANITA, S.Pd.AH",
        "24670110820000310",
        "6271037001910006",
        "199101302025212095",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 3 Lungkuh Layang"
    ],
    [
        "SRI BINTANG MARINA BR. SIAHAAN, AMd.Keb",
        "24670120120001616",
        "1209226003970001",
        "199703202025212115",
        "Asahan",
        "S-1 SEMUA JURUSAN",
        "Bidan Terampil",
        "UPT Puskesmas Jangkang"
    ],
    [
        "FRAN SALUNDIK, S.T",
        "24670130810000401",
        "6203011406790005",
        "197906142025211105",
        "Palangka Raya",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SRI HANDAYANI, S.Pd",
        "24670110820000637",
        "6203094208970005",
        "199708022025212113",
        "Jayapura",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sekata Makmur"
    ],
    [
        "SRI HARTATI, Amd.Keb",
        "24670140820000127",
        "6203124909910002",
        "199309092025212177",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Sei Pinang"
    ],
    [
        "SRI INDRIANI",
        "24670130820000617",
        "6203096611960005",
        "199611262025212148",
        "Tuban",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "HARIANTO",
        "24670130810000913",
        "6203010504790016",
        "197904052025211101",
        "Kapuas",
        "SMEA KEUANGAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "HENDRA TOKANDAYA SAU",
        "24670130810000552",
        "6203010212790009",
        "197912022025211061",
        "Poso",
        "SMK MEKANIK OTOMOTIF",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "HERMANUS, S.T",
        "24670130810000659",
        "6203010603790010",
        "197903062025211069",
        "Pulang Pisau",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SRI RAHAYU, S.Pd.I.",
        "24670130820000598",
        "6203025911900002",
        "199011192025212127",
        "Kapuas",
        "S-1 PAI",
        "Penata Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SRI RAHAYU",
        "24670130820000389",
        "6203015409970005",
        "199709142025212119",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Panamas"
    ],
    [
        "IKA AUSTRALINA",
        "24670130820000497",
        "6203014202790011",
        "197902022025212062",
        "Hulu Sungai Tengah",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana"
    ],
    [
        "SRI RATU, A.Md.Keb",
        "24670140820000158",
        "6203014708900006",
        "199308072025212139",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Basarang"
    ],
    [
        "IRWANSYAH",
        "24670130810000008",
        "6203010807790003",
        "197907082025211078",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "SRI WAHYUNI",
        "24670130820000439",
        "6203114509880002",
        "198809052025212135",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SRI WAHYUNI, S,Pd",
        "24670110820000751",
        "6203014103950008",
        "199603012025212133",
        "Kapuas",
        "S-1 PGSD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palingkau Baru"
    ],
    [
        "SRI WAHYUNI",
        "24670130820000384",
        "6203014104850007",
        "198504012025212094",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "SRIMILUWATI",
        "24670130820000508",
        "6203016807870002",
        "198707282025212112",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "IWAN HARTAWAN",
        "24670130810000692",
        "6203012110790004",
        "197910212025211070",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SRIWARDINA, S.Pd.I",
        "24301220120112701",
        "6203016012890019",
        "198912202025212158",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Kapuas Hulu Satu Atap"
    ],
    [
        "STEVIANO SEBASTIAN TUNDAN",
        "24670130810000664",
        "6203011009970008",
        "199709102025211112",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "STEYLI VIFRI RARANTA",
        "24670130820000178",
        "6203016207830002",
        "198307222025212066",
        "Minahasa",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SUBAHAN",
        "24670130810000590",
        "6203012603940001",
        "199403262025211116",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "JABIR",
        "24670130810000195",
        "6203041006790004",
        "197906102025211129",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Kuala"
    ],
    [
        "SUBHAN, SE",
        "24670130810000252",
        "6203080205830001",
        "198305022025211149",
        "Kapuas",
        "S-1 SEMUA JURUSAN",
        "Penata Layanan Operasional",
        "UPT Puskesmas Basarang"
    ],
    [
        "SUBHAN",
        "24670130810000528",
        "6203010301840003",
        "198401032025211122",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SUBHAN TAMIMI",
        "24670130810000237",
        "6203012704920005",
        "199204272025211122",
        "Kapuas",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "SUBIHI RAMADHAN",
        "24670130810001009",
        "6203021511930001",
        "199311152025211127",
        "Kapuas",
        "D-I ILMU PENGETAHUAN ALAM",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "SUBLI",
        "24670130810000696",
        "6203010210910017",
        "199110022025211140",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Panamas"
    ],
    [
        "SUCI WULANDARI",
        "24670130820000270",
        "6203015302950002",
        "199502132025212119",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "SUCIATI PRAMADANI",
        "24670130820000041",
        "6203015805850002",
        "198505182025212085",
        "Banjarmasin",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "JAYA",
        "24670130810000849",
        "6203021008790004",
        "197908102025211115",
        "Barito Timur",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "SUGENG SUKRIYANTO, S.Pd.I",
        "24670110810000098",
        "6203062512880001",
        "198812252025211146",
        "Pulang Pisau",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SD Negeri 1 Warga Mulya"
    ],
    [
        "SUGIANOR, A.md kep",
        "24670140810000109",
        "6203010106890007",
        "198906012025211178",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "SUGIARIANTO",
        "24670130810000951",
        "6203011409820004",
        "198209142025211100",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "SUGIYONO",
        "24670130810000474",
        "6203092704920003",
        "199204272025211123",
        "Kapuas",
        "SMK AGRIBISNIS TANAMAN PANGAN DAN HORTIKULTURA",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "SUHAIBATUL ASLAMIYAH",
        "24670130820000476",
        "6304135807930002",
        "199201182025212124",
        "Barito Kuala",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "JHONNY",
        "24670130810000319",
        "6302062411790002",
        "197911242025211068",
        "Kapuas",
        "SMU IPA",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "JUMARI",
        "24670130810000741",
        "6203010204790010",
        "197904022025211077",
        "Kapuas",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SUHARTINI NINGSIH",
        "24670130820000173",
        "6203016806890004",
        "198906282025212123",
        "Banjarbaru",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "KUSMARANTI",
        "24670130820000591",
        "6203027011790003",
        "197911302025212038",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "KUSNADI",
        "24670130810000752",
        "6203010612790003",
        "197912062025211070",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Baru"
    ],
    [
        "SUKARTO FETRIANO",
        "24670130810000223",
        "6203072107930004",
        "199307212025211115",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "SUKMA RAGA, A.Md.Kep",
        "24670140810000028",
        "6203052206910001",
        "199106222025211111",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mandomai"
    ],
    [
        "SUKRIS NOMODI, Amd.Kep",
        "24670140810000135",
        "6210111609940001",
        "199409162025211110",
        "Gunung Mas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Timpah"
    ],
    [
        "SULAIMAN",
        "24670130810000991",
        "6203010707900005",
        "199007072025211201",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SULASTRI, S.E",
        "24670130820000124",
        "6203014303910016",
        "199103032025212176",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SULATIE, A.Md.Kep.",
        "24670140820000390",
        "6203116205980001",
        "199805222025212096",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "SULISTIA RAHMAH, S.pd.I",
        "24670110820000531",
        "6371034602000008",
        "200002062025212055",
        "Kotawaringin Timur",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palingkau Sejahtera"
    ],
    [
        "SUMADI",
        "24670130810000829",
        "6203012910880008",
        "198610292025211098",
        "Pulang Pisau",
        "SMK BISNIS DAN MANAJEMEN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SUMANTO, S.kep",
        "24670130810000988",
        "6203100303920002",
        "199303032025211227",
        "Kapuas",
        "S-1 KEPERAWATAN",
        "Administrator Kesehatan Ahli Pertama",
        "Dinas Kesehatan"
    ],
    [
        "LINAE",
        "24670130820000520",
        "6203016301790003",
        "197901232025212031",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SUMIATI",
        "24670130820000037",
        "6203014801830003",
        "198301082025212064",
        "Kapuas",
        "D-III AGRIBISNIS PERTANIAN",
        "Pengelola Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SUMIATI, S.Pd",
        "24670110820000553",
        "5206124101011017",
        "200101012025212094",
        "Bima",
        "S-1 PENDIDIKAN GURU MADRASAH IBTIDAIYAH",
        "Guru Ahli Pertama",
        "SD Negeri 5 Selat Hilir"
    ],
    [
        "SUNARDIE, S.Kom",
        "24670130810000332",
        "6211072908830001",
        "199308272025211120",
        "Pulang Pisau",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SUNARTI, A.Md.Kep",
        "24670140820000017",
        "6203016409830003",
        "198309242025212062",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Melati"
    ],
    [
        "SUNARTI ANGGRAINI, S.pd.I",
        "24670110820000300",
        "6203045506930004",
        "199306152025212173",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lupak Timur"
    ],
    [
        "SUNARTIE, AMK",
        "24670140820000307",
        "6203126512890001",
        "198912252025212199",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Pinang"
    ],
    [
        "SUNARY NUUR MUDMAINAH, A.Md.Keb",
        "24670140820000332",
        "6203114411000004",
        "200011042025212058",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "SUNARYO",
        "24670130810000273",
        "6203011707830004",
        "198307172025211146",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SUNDARI",
        "24670130820000358",
        "6203015509960003",
        "199609152025212115",
        "Barito Utara",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "M.RUSDI S.HUT, S.Hut",
        "24670110810000289",
        "6203041712790002",
        "197912172025211079",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Lupak Dalam"
    ],
    [
        "M.SALEH",
        "24670130810000804",
        "6203071905780001",
        "197906042025211084",
        "Hulu Sungai Utara",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "SUPARDI",
        "24670130810000433",
        "6203011709880003",
        "198809172025211122",
        "Gunung Mas",
        "SMK PERTANIAN BUDIDAYA TANAMAN",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "SUPARDIE",
        "24670130810000971",
        "6203022005680001",
        "196805202025211030",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SUPIANSYAH",
        "24670130810001021",
        "6203010408820004",
        "198208042025211135",
        "Kapuas",
        "SMK SEKRETARIS",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "SUPIYANI",
        "24670130810000022",
        "6203012202810001",
        "198102222025211088",
        "Barito Kuala",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "SUPONO AJIE",
        "24670130810000953",
        "6203012311810001",
        "198111232025211053",
        "Kapuas",
        "SEKOLAH MENENGAH TEKNOLOGI HASIL PERTANIAN",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "TIARA ASTIGINI",
        "24670130820000424",
        "6203075808020001",
        "200209182025212017",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Palingkau Lama"
    ],
    [
        "SUPRIANSYAH",
        "24670130810001019",
        "6203083007920002",
        "199207302025211115",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SUPRIANTO",
        "24670130810000825",
        "6203081906900004",
        "199006192025211120",
        "Kapuas",
        "MADRASAH ALIYAH IPA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SUPRIHATIN, S.Pd",
        "24670110820000636",
        "6203094401820001",
        "198304012025212084",
        "Trenggalek",
        "S-1 PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Manyahi"
    ],
    [
        "SUPRIYONO, S.Pd.I",
        "24670110810000038",
        "6203091805850001",
        "198505182025211116",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Mantangai"
    ],
    [
        "SUPRIYONO",
        "24670130810000817",
        "6203012510900013",
        "199010252025211122",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SURIADI",
        "24670130810000848",
        "6203071911850002",
        "198511192025211095",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SURIANIE",
        "24670130820000341",
        "6203145012920001",
        "199212102025212181",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SURYA M. IKBAL",
        "24670130810000094",
        "6203051009840004",
        "198409102025211154",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SURYADI",
        "24670130810000819",
        "6203021205980002",
        "199805122025211079",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SURYADI",
        "24670130810000105",
        "6203012704870004",
        "198704272025211127",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "MAKDALENA",
        "24670130820000359",
        "6203015204790008",
        "197904122025212061",
        "Barito Timur",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "SUSANA, S.M",
        "24670130820000221",
        "6203014212860002",
        "198612022025212118",
        "Barito Selatan",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "MUHAMAD SYAFI'E",
        "24670130810000800",
        "6203011910790006",
        "197910192025211059",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SUSANDRI BERKAT HAWINI, S.pd.I",
        "24670110820000562",
        "6213056009960002",
        "199609202025212131",
        "Barito Timur",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Pujon"
    ],
    [
        "SUSANTHI, S.Pd",
        "24670110820000666",
        "6271014608830007",
        "198308062025212083",
        "Palangka Raya",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tumbang Nusa"
    ],
    [
        "SUSANTI, S. Pd",
        "24670110820000484",
        "6203074906000001",
        "200006092025212067",
        "Palangka Raya",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palangkau Baru"
    ],
    [
        "SUSANTI, S.Pd.I",
        "24670110820000426",
        "6203015404920009",
        "199204142025212183",
        "Kapuas",
        "S-1 PAI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bina Jaya"
    ],
    [
        "SUSANTY ERIANY",
        "24670130820000123",
        "6203014408840008",
        "198408042025212083",
        "Palangka Raya",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "SUSANTY REZEKI",
        "24670130820000042",
        "6203016707880009",
        "198807272025212153",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "SUSETYO, S.Pd.I",
        "24670130810001000",
        "6203070307860004",
        "198607032025211168",
        "Kulon Progo",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Penata Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "SUSI MIALIDUINA",
        "24670130820000231",
        "6203016709810002",
        "198109272025212034",
        "Barito Utara",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "SUSIANI",
        "24670130820000302",
        "6203015909860007",
        "198609192025212104",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "SUSILAWATI",
        "24670130820000357",
        "6203024408940001",
        "199408042025212143",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "UPT Puskesmas Anjir Serapat"
    ],
    [
        "SUSNAWANINGSIH, SE",
        "24670130820000160",
        "6203015008860007",
        "198608102025212148",
        "Yogyakarta",
        "S-1 MANAJEMEN APOTEK DAN FARMASI",
        "Penata Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SUSUR DEBORAH",
        "24670130820000532",
        "6203025503900001",
        "199003152025212177",
        "Kotawaringin Timur",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SUTARMO",
        "24670130810000669",
        "6203082302810005",
        "198102232025211064",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Basarang"
    ],
    [
        "MURAI",
        "24670130820000198",
        "6203026402790001",
        "197902242025212033",
        "Palangka Raya",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Pariwisata, Kebudayaan, Kepemudaan dan Olahraga"
    ],
    [
        "SUTRISNO",
        "24670130810000917",
        "6203081006910001",
        "199106102025211188",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "NIDYA KIRANA ROMZAH, S.E",
        "24670130820000217",
        "6203016104790005",
        "197904212025212080",
        "Yogyakarta",
        "S-1 EKONOMI AKUNTANSI",
        "Penata Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "WAHYU HADANI",
        "24670130810000965",
        "6203013004010006",
        "200205012025211028",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "YULIANA",
        "24670130820000409",
        "6203065208020002",
        "200208122025212020",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "SYAHRIAL KURNIAWAN",
        "24670130810000924",
        "6203012201980003",
        "199801222025211086",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "AELAYA FRIDA PUTRI",
        "24670130820000449",
        "6203015007030005",
        "200307102025212012",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SYAHRIL ANWAR",
        "24670130810000626",
        "6203010903850004",
        "198503092025211090",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "SYAHROL",
        "24670130810000793",
        "6203050904850003",
        "198205012025211117",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "SYAHRUDIN NOOR, S.pd.I",
        "24670110810000189",
        "6203052408930003",
        "199308242025211102",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sei Dusun"
    ],
    [
        "SYAHRUL GUNAWAN",
        "24670130810000219",
        "6271023009950002",
        "199509302025211104",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "SYAIFUL ANWAR",
        "24670130810000886",
        "6203030901920002",
        "199201092025211124",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SYAMSUDIN",
        "24670130810001035",
        "6203011905880001",
        "198805192025211120",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI IPS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "NOORHAYATI, S.Pd.AUD",
        "24670110820000602",
        "6203015110790001",
        "197910112025212051",
        "Kapuas",
        "S-1 PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 6 Selat Hilir"
    ],
    [
        "SYLVI WULANDARI, S.Kep.,Ns",
        "24670140820000323",
        "6203105009990003",
        "199810092025212092",
        "Kapuas",
        "NERS",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Timpah"
    ],
    [
        "TAISYIR RIJANI",
        "24670130810000868",
        "6203071012850007",
        "198512102025211145",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "TANDAWATI",
        "24670130820000643",
        "6203086405810002",
        "198105242025212046",
        "Kotawaringin Timur",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "TANTI WULANDARI, S.T.",
        "24670130820000470",
        "6203014410970005",
        "199610042025212122",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Perumahan, Kawasan Permukiman, dan Pertanahan"
    ],
    [
        "TASYA MEILIANY ANDINA, S.P.",
        "24670130820000382",
        "6203015305980003",
        "199805132025212115",
        "Banjarmasin",
        "SLTA/SMA SEDERAJAT",
        "Penata Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "PUTU KARTIKA",
        "24670130810000129",
        "6203083004790002",
        "197904302025211059",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "RAHMAWATI",
        "24670130820000560",
        "6203036808790002",
        "197908282025212069",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "TAUFIK RAHMAN, S.Kom",
        "24670110810000159",
        "6203091206980004",
        "199806122025211108",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Mantangai"
    ],
    [
        "TAUFIKKURRAHMAN",
        "24670130810000180",
        "6203040912810004",
        "198112092025211059",
        "Kapuas",
        "SMU IPA",
        "Operator Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "TAUFIQ",
        "24670130810000494",
        "6203012506830007",
        "198306252025211117",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "AYU ANDANI",
        "24670130820000505",
        "6203024902030001",
        "200302092025212020",
        "Kapuas",
        "D-I ILMU PENGETAHUAN ALAM",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "RUSDI S.",
        "24670130810000479",
        "6203030501790001",
        "197901052025211091",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "SAIPUL ANWAR",
        "24670130810000074",
        "6203012806790005",
        "197906282025211049",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "THOMAS ARIJAL",
        "24670130810000549",
        "6203010305810004",
        "198105032025211133",
        "Kapuas",
        "SMK PERTANIAN DAN KEHUTANAN",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "TIA SAFITRI",
        "24670130820000105",
        "6203015501990003",
        "199901152025212076",
        "Kapuas",
        "SMA/MA (IPA)",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "IRPANI",
        "24670130810000570",
        "6203010706040007",
        "200306072025211015",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "TIARMA RENOVA NABABAN, S.Pd",
        "24670110820000406",
        "1207324509890003",
        "198909052025212138",
        "Deli Serdang",
        "S-1 PENDIDIKAN SEJARAH",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tumbang Puroh"
    ],
    [
        "TIAS AKTAFIA ANGRAINI, A.md.Kep",
        "24670140820000220",
        "6203014406930012",
        "199310042025212124",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Selat"
    ],
    [
        "TIMAH",
        "24670130820000386",
        "6203015212930004",
        "199312122025212197",
        "Banjar",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "TIMPUNG",
        "24670130820000371",
        "6203016006820008",
        "198206202025212081",
        "Palangka Raya",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "TINA MURTINI",
        "24670130820000222",
        "6203016611900004",
        "199011262025212115",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Sosial"
    ],
    [
        "TIRA AISA, AMK",
        "24670140820000205",
        "6203094205920002",
        "199205022025212220",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Mantangai"
    ],
    [
        "TITA NATASARI, S.E",
        "24670110820000405",
        "6203096807990001",
        "199907282025212102",
        "Kapuas",
        "D-IV/S-1 EKONOMI",
        "Guru Ahli Pertama",
        "SMP NEGERI 12 MANTANGAI SATU ATAP"
    ],
    [
        "SANTI",
        "24670130820000022",
        "6203014607790008",
        "197907062025212061",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "TITIN KARTINI, S.Pd",
        "24670110820000342",
        "6203074306950003",
        "199506032025212152",
        "Samarinda",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Menteng Raya"
    ],
    [
        "TIWI ANTIKA",
        "24670130820000625",
        "6211076408890001",
        "198907042025212147",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "TOMI SAPUTRA",
        "24670320110001783",
        "6203012505980007",
        "199805252025211087",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "TOMMY",
        "24670130810000916",
        "6203022202860001",
        "198602222025211100",
        "Banjarmasin",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "TOMY SAPUTRA",
        "24670130810000343",
        "6203051010950001",
        "199510102025211194",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "TONI",
        "24670130810000742",
        "6203023012890001",
        "198912302025211116",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "TONNY HARYONO",
        "24670130810000358",
        "6203010905820001",
        "198205092025211132",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "TONY",
        "24670130810001047",
        "6203011206820020",
        "198206122025211164",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "TONY SETIAWAN",
        "24670130810000419",
        "6203031108910004",
        "199208112025211131",
        "Kapuas",
        "D-I ADMINISTRASI",
        "Operator Layanan Operasional",
        "UPT Puskesmas Lamunti"
    ],
    [
        "TRANS MUBAROHKIM, S.AP",
        "24670130810000276",
        "6203011009940008",
        "199409102025211127",
        "Kapuas",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "TRI ANGGRAHA",
        "24670130810000441",
        "6203010512890006",
        "198912052025211130",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Pemadam kebakaran dan Penyelamatan"
    ],
    [
        "TRI AYU WAHYUNI",
        "24670130820000269",
        "6203015605940002",
        "199405162025212138",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "SAYANG HARWARTO",
        "24670130810000505",
        "6203012106790011",
        "197906212025211072",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "TRI PERMATA KASANAH, S.Kom",
        "24670110820000351",
        "6203034303000003",
        "200003032025212094",
        "Kapuas",
        "D-IV/S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Selat"
    ],
    [
        "TRI PRASETIA",
        "24670130810000747",
        "6203010309980004",
        "199809032025211068",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "TRI REJEKI NINGSIH, S.Pd",
        "24670110820000569",
        "6205055201960004",
        "199601122025212125",
        "Barito Utara",
        "S-1 PENDIDIDKAN OLAH RAGA DAN KESEHATAN",
        "Guru Ahli Pertama",
        "SD Negeri 3 Tamban Luar"
    ],
    [
        "TRI SUDARTO",
        "24670130810000958",
        "6203070104910006",
        "199104012025211146",
        "Tulang Bawang",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Dadahup"
    ],
    [
        "TRI SULISTYOWATI, S.Kom",
        "24670130820000071",
        "6203016109860006",
        "198609212025212118",
        "Kapuas",
        "S-1 SISTEM INFROMASI",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "TRI WAHYUNI",
        "24670130820000536",
        "6203014504860008",
        "198604052025212119",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "TRIAS PARAMITA",
        "24670130820000110",
        "6203135005900001",
        "199005102025212203",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "TRICIA AMELOKA, S.Kom",
        "24670110820000545",
        "6203015505940008",
        "199405152025212193",
        "Kapuas",
        "S-1 SISTEM INFROMASI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Selat"
    ],
    [
        "SENTA",
        "24670130820000194",
        "6203144104790001",
        "197904012025212041",
        "Kapuas",
        "SMU IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "TRIONO",
        "24670130810000710",
        "6203081111910003",
        "199111112025211194",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "SETIA BUNGA INDA",
        "24670130820000461",
        "6203055412790001",
        "197912142025212030",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "TRISNA DWIYANTO PUTRA, S.E",
        "24670220110004016",
        "6203011704970001",
        "199704172025211098",
        "Ciamis",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "TRISNAWATI",
        "24670130820000455",
        "6211024211910002",
        "199111122025212143",
        "Pulang Pisau",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Basarang"
    ],
    [
        "TRISNAWATI",
        "24670130820000487",
        "6203016912890002",
        "198912292025212116",
        "Kapuas",
        "SMK ADMINISTRASI PERKANTORAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "TRIWIRA",
        "24670130810000727",
        "6203020708980002",
        "199808072025211081",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Hilir"
    ],
    [
        "TRY WIDYASAPUTRA, A.Md",
        "24670130810000001",
        "6203011808930005",
        "199308182025211153",
        "Kapuas",
        "D-III ADMINISTRASI BISNIS",
        "Pengelola Layanan Operasional",
        "Badan Kesatuan Bangsa dan Politik"
    ],
    [
        "TURRUS PERDANA G. BANJANG",
        "24670130810000066",
        "6203010210910005",
        "199210022025211156",
        "Pulang Pisau",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Inspektorat"
    ],
    [
        "TUTI LESTARI",
        "24670130820000662",
        "6203016802820005",
        "198202282025212071",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "TUTUT WIDIARTI, S.Pd",
        "24670110820000263",
        "6203016909990005",
        "199909292025212104",
        "Kapuas",
        "S-1 PENDIDIKAN GURU AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 2 Selat Tengah"
    ],
    [
        "SITI ASIAH",
        "24670130820000609",
        "6203044409790001",
        "197909042025212047",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "UJANG",
        "24670130810000939",
        "6203050406860001",
        "198606042025211155",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "TAUFIK RAHMAN",
        "24670130810000491",
        "6203081603790003",
        "197911102025211102",
        "Kapuas",
        "PAKET C",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "WAHYUDI",
        "24670130810000968",
        "6203012906790019",
        "197906292025211053",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "UNAL SAPUTRA, Amd.Kep",
        "24670140810000001",
        "6211042801920001",
        "199201282025211104",
        "Pulang Pisau",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Pinang"
    ],
    [
        "UNANG SEPTIO",
        "24670130810000508",
        "6203011809880001",
        "198809182025211119",
        "Banyumas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "UNTUNG ANWAR",
        "24670130810000167",
        "6203010601920003",
        "199201062025211132",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Bataguh"
    ],
    [
        "YOHANES DECA HARYANTO.S, S.Hut",
        "24670130810000099",
        "6203012605790002",
        "197905262025211081",
        "Kapuas",
        "S-1 KEHUTANAN (MANAJEMEN KEHUTANAN)",
        "Penata Layanan Operasional",
        "Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah"
    ],
    [
        "UTOMO SAPUTRA, S.pd.I",
        "24670130810000500",
        "6203012810860002",
        "198610282025211147",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "VANESA AGUSTIN",
        "24670130820000066",
        "6203015308990001",
        "199908132025212083",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "VANNY MONICA SEPTEMERY, S.I.P",
        "24670130820000174",
        "6203014809940008",
        "199409082025212153",
        "Palangka Raya",
        "S-1 SOSIAL DAN POLITIK",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "VELIA HAYATI, S.pd.I",
        "24670110820000597",
        "3209176106990008",
        "199906212025212094",
        "Cirebon",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Selat Tengah"
    ],
    [
        "VERONIKA PANGARIBUAN, A.md",
        "24670130820000192",
        "6203025811910002",
        "199111182025212129",
        "Kapuas",
        "D-III MANAJEMEN INFORMATIKA",
        "Pengelola Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "VERONNICA. T",
        "24670130820000098",
        "6203025910960002",
        "199510192025212090",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Penanaman Modal dan PTSP"
    ],
    [
        "VERRY LIHARDI GARIDING",
        "24670130810000116",
        "6203013107850003",
        "198507312025211099",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "VIA, S.Ag.",
        "24670110820000585",
        "6203116606010003",
        "200206182025212017",
        "Kapuas",
        "S-1 FILSAFAT AGAMA HINDU",
        "Guru Ahli Pertama",
        "SD Negeri 2 Kaburan"
    ],
    [
        "KARINA APRILIA",
        "24670130820000453",
        "6203016104030006",
        "200304212025212015",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "VICTOR KURNIA",
        "24670130810000306",
        "6203012207880003",
        "198807222025211118",
        "Palangka Raya",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "VIKO, S.Pd",
        "24670110810000131",
        "6211051006920001",
        "199206102025211177",
        "Pulang Pisau",
        "S-1 PENDIDIKAN JASMANI KESEHATAN DAN REKREASI",
        "Guru Ahli Pertama",
        "SD Negeri 3 Anjir Kalampan"
    ],
    [
        "VINAE YULIANTI",
        "24670130820000197",
        "6203014405900010",
        "199005042025212167",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "VIO ALVARO",
        "24300420110026964",
        "6205010201990001",
        "199901022025211066",
        "Palangka Raya",
        "SMK TEKNIK BODI OTOMOTIF",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "VIRA YUNIAR, S.E",
        "24670110820000564",
        "6210025606990001",
        "199906162025212117",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Guru Ahli Pertama",
        "SD NEGERI 2 TUMBANG MUROI"
    ],
    [
        "VIRGA ROYZA ADENATA, S.Pd",
        "24670110810000203",
        "6203012209980001",
        "199809222025211056",
        "Kapuas",
        "S-1 TEKNOLOGI PENDIDIKAN",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Mantangai"
    ],
    [
        "VIRLIANA KHAIRUNNISA, S.pd.I",
        "24670110820000382",
        "6203065610970002",
        "199710162025212130",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Handiwong"
    ],
    [
        "MONALISA, S.Pd",
        "24670110820000380",
        "6203084607990004",
        "Belum ada nip",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Sumber Agung"
    ],
    [
        "VIVI AGUSTINA",
        "24670130820000165",
        "6203016208870006",
        "198708222025212100",
        "Kapuas",
        "SMK MULTIMEDIA",
        "Operator Layanan Operasional",
        "Subbagian Keuangan dan Aset"
    ],
    [
        "WAHYU D, A.Md.Kep",
        "24670140810000108",
        "6203071110940002",
        "199410112025211137",
        "Barito Selatan",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Tatas"
    ],
    [
        "M.AMIN BADALI",
        "24670130810000798",
        "6203030903050001",
        "200309102025211007",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "WAHYU KURNIAWAN, S.H",
        "24670130810000533",
        "6203012307930004",
        "199307232025211142",
        "Kapuas",
        "S-1 HUKUM (BERGELAR S.H.)",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "WAHYU MAULIDI YANTI, S.E",
        "24670130820000216",
        "6203012607960007",
        "199607262025212097",
        "Kapuas",
        "S-1 MANAJAMEN",
        "Penata Layanan Operasional",
        "Bidang Pembinaan Ketenagaan"
    ],
    [
        "WAHYU PRIYONO, S.E.",
        "24670130810000395",
        "6203014601950008",
        "199501062025211105",
        "Kapuas",
        "S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "WAHYU RETNO NINGTIYAS",
        "24670130820000440",
        "3524095111960003",
        "199611112025212161",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "WAHYU SRI NINGSIH",
        "24670130820000392",
        "6203024909940001",
        "199409092025212172",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "WAHYU SUGIANTO",
        "24670130810000746",
        "6203011705860010",
        "198606162025211188",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "WAHYU WIDODO",
        "24670130810000667",
        "6203012905950002",
        "199505292025211140",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "WAHYU YUSNI RAHMAN, S. Pd.",
        "24670110810000059",
        "6203060210910003",
        "199110022025211148",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 3 Sei Tatas Hilir"
    ],
    [
        "ZAINAL ABADIN",
        "24670130810000856",
        "6203011307790005",
        "197907132025211083",
        "Kapuas",
        "SMK MANAJEMEN BISNIS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "MUHAMMAD ARSYAD",
        "24670130810000901",
        "6203010101800030",
        "198001012025211255",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "WAHYUDI",
        "24670130810000465",
        "6203011907860007",
        "198607192025211126",
        "Kapuas",
        "SMK SEKRETARIS",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "WAHYUDINNOR",
        "24670130810000672",
        "6203012201850002",
        "198501222025211088",
        "Kapuas",
        "MADRASAH ALIYAH BAHASA",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "WAHYUDINOR",
        "24670130810000624",
        "6203011111900009",
        "199011112025211144",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "WAHYUNI",
        "24670130810000165",
        "6203081508850003",
        "198508152025211168",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "WAHYUNI CAHAYA APRILIA",
        "24670130820000279",
        "6203014504980004",
        "199804052025212116",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "ABDUL HAKIM",
        "24670130810000671",
        "6203010202770011",
        "198007052025211166",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Kelurahan Murung Keramat"
    ],
    [
        "WALJHON PAMUNGKAS, S.AN",
        "24670130810000028",
        "6203012606940011",
        "199406262025211162",
        "Kapuas",
        "S-1 ADMINITRASI NEGARA",
        "Penata Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "ANANG AMINUDDIN, S.Pd",
        "24670110810000173",
        "6271031709800006",
        "198009172025211074",
        "Jombang",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 TERUSAN MAKMUR"
    ],
    [
        "WANYO",
        "24670130810000786",
        "6203012909900003",
        "198905292025211119",
        "Kapuas",
        "PERSAMAAN SLTA (PAKET C)",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "WARDIYANSYAH",
        "24670130810000966",
        "6203011003930014",
        "199303102025211166",
        "Kapuas",
        "SMU IPA",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "WARNITI",
        "24670130820000432",
        "6203015810980003",
        "199210182025212151",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "WARSIKUN",
        "24670130810000908",
        "6203012809880005",
        "198809282025211098",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ARBAINAH",
        "24670130820000468",
        "6203014101800014",
        "198001012025212149",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ARMAN",
        "24670130810000639",
        "6203010101800021",
        "198001012025211247",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Selat Dalam"
    ],
    [
        "BATMAN",
        "24670130810000187",
        "6203011707800009",
        "198007172025211117",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "WAYAN EKA SAPTA JAYA, S. Pd,",
        "24670110810000213",
        "6203041712960002",
        "199612172025211097",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bandaraya"
    ],
    [
        "WELAHESTIANI, A.Md.Keb",
        "24670140820000330",
        "6203086302930001",
        "199302232025212147",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Danau Rawah"
    ],
    [
        "CISNA HELDAYATI",
        "24670130820000075",
        "6203026703800001",
        "198003272025212050",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "WELSY, A.Md.Keb",
        "24670140820000075",
        "6271036311950006",
        "199511232025212136",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "WEMPRI ROMANSYAH",
        "24670130810000016",
        "6203021511860001",
        "198611152025211114",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "DEDI MULYANA",
        "24670130810001033",
        "3272050707800901",
        "198007072025211134",
        "Sukabumi",
        "SMU IPA",
        "Operator Layanan Operasional",
        "Kecamatan Selat"
    ],
    [
        "WENDY PRANATA",
        "24670130810000721",
        "6203111602960006",
        "199602162025211077",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Tengah"
    ],
    [
        "WIDYA AFRIANI WINARSIH",
        "24670130820000180",
        "6203017004900001",
        "199004302025212123",
        "Palangka Raya",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "WIDYA HAPSARI PUTRI",
        "24670130820000242",
        "6203045910970002",
        "199710192025212100",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "WIDYA PRATIWI",
        "24670130820000379",
        "6203015808880003",
        "198806182025212135",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Kelurahan Selat Barat"
    ],
    [
        "ENDANG SUNARNI, S.E.",
        "24670130820000111",
        "6203016009800006",
        "198009202025212045",
        "Ponorogo",
        "D-IV/S-1 MANAJEMEN",
        "Penata Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "PUTRI AURA ASIA",
        "24670130820000426",
        "6203015712030001",
        "200312172025212011",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "WILSON HARYES",
        "24670130810000496",
        "6203051905840001",
        "198405162025211074",
        "Kapuas",
        "SEKOLAH MENENGAH UMUM",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "WINARNO, Amd.Kep",
        "24670140810000029",
        "6203112408940005",
        "199408242025211125",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Jangkang"
    ],
    [
        "WINNY OCTAVIANA DONARTI, S.Ak",
        "24670130820000345",
        "6203016410990005",
        "199910242025212081",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "WISNU ADI SULISTYO, S.Pd",
        "24670110810000120",
        "6203011705990003",
        "199905172025211100",
        "Pulang Pisau",
        "S-1 PENDIDIKAN JASMANI",
        "Guru Ahli Pertama",
        "SD Negeri 4 Selat Hilir"
    ],
    [
        "SITI JULAEHA",
        "24670130820000472",
        "6203015505030009",
        "200305162025212010",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "WITRI MEILYNA RAFIANI, S.E.",
        "24670130820000161",
        "6203025705880002",
        "198805172025212126",
        "Kapuas",
        "S-1 MANAJEMEN EKONOMI",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "FITRIA AN'NISA, SH",
        "24670130820000145",
        "6203016911800006",
        "198011292025212036",
        "Palangka Raya",
        "S-1 HUKUM UMUM",
        "Penata Layanan Operasional",
        "Dinas Pertanian"
    ],
    [
        "WIWI ARDIATI, S.Pd",
        "24670110820000544",
        "6203016104890003",
        "198904172025212149",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Saka Tamiang"
    ],
    [
        "HAMDAH",
        "24670130820000594",
        "6203045211800003",
        "198011122025212052",
        "Kapuas",
        "MADRASAH ALIYAH NEGERI",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "WIWIN",
        "24670130820000410",
        "6203026708940001",
        "199408272025212151",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HERO SUSILO",
        "24670130810000398",
        "6203011104800004",
        "198004112025211059",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Satuan Polisi Pamong Praja"
    ],
    [
        "WIWIN MEIKA SINTA, S.pd.I",
        "24670110820000317",
        "6211056705960003",
        "199605272025212139",
        "Palangka Raya",
        "AKADEMI BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD Negeri 1 Masaran"
    ],
    [
        "WIWIT SEPTETI, A.Md.Keb",
        "24670140820000340",
        "6203115009950003",
        "199509102025212141",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "WIZAR UTAMI",
        "24670130820000601",
        "6203014408990010",
        "199908042025212090",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "WULAN RATNA SARI, A.Md.Kep",
        "24670140820000389",
        "6203045204980005",
        "199804122025212101",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Lupak"
    ],
    [
        "WULANDARI, S.Kom",
        "24670110820000760",
        "6211055506970005",
        "199704302025212111",
        "Pulang Pisau",
        "S-1 SISTEM INFROMASI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Timpah Satu Atap"
    ],
    [
        "WULANDARI, S.Pd",
        "24670130820000337",
        "6203026909990001",
        "199909292025212106",
        "Kapuas",
        "S-1 PENDIDIKAN GURU ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 4 Anjir Mambulau Timur"
    ],
    [
        "WULANDARI PUSPITASARI, S.T.",
        "24697120120000406",
        "6203016603920003",
        "199403262025212115",
        "Kapuas",
        "S-1 TEKNIK PERTAMBANGAN",
        "Penata Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "YAHYA VRIYANTO, S.H",
        "24670130810000268",
        "6203011212820009",
        "198212122025211187",
        "Palangka Raya",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "YAMANI, S.Pd",
        "24670110810000122",
        "6203040509000003",
        "200009052025211037",
        "Kapuas",
        "S-1 PENDIDIKAN OLAHRAGA",
        "Guru Ahli Pertama",
        "SD Negeri 2 Lupak Dalam"
    ],
    [
        "ILAWANSI",
        "24670130820000629",
        "6203094812800003",
        "198012082025212049",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "JUANDI",
        "24670130810001020",
        "6203011009800005",
        "198009102025211120",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "LISA",
        "24670130820000256",
        "6271034712800004",
        "198012072025212055",
        "Kapuas",
        "SMA IPA",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "YANA",
        "24670130820000501",
        "6203015008940014",
        "199408102025212150",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "MARIO",
        "24670130810000660",
        "6203010604800007",
        "198004062025211088",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "YANTI, S.Sos.",
        "24670110820000668",
        "6203125208980001",
        "199808122025212105",
        "Kapuas",
        "S-1 ILMU ADMINISTRASI NEGARA",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Kapuas Hulu Satu Atap"
    ],
    [
        "YANTY",
        "24670130820000590",
        "6203054805850003",
        "198505082025212083",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MICKY DAVIS",
        "24670130810000976",
        "6203021303800003",
        "198003132025211090",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "YASINTHA NGEO",
        "24670130820000010",
        "6203016204820003",
        "198204222025212051",
        "Ngada",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "YATNO ERIYANTO",
        "24670130810000112",
        "6203012006910006",
        "199106202025211124",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Transmigrasi dan Tenaga Kerja"
    ],
    [
        "YAUMI FATMASARI, S.Tr.A.K",
        "24670120120001059",
        "6203014806970006",
        "199706082025212112",
        "Kapuas",
        "D-IV ANALIS KESEHATAN",
        "Pranata Laboratorium Kesehatan Ahli Pertama",
        "UPT Puskesmas Dadahup"
    ],
    [
        "YEFRIN TINDJABATE",
        "24670130820000237",
        "6203015106860001",
        "198606112025212103",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Perumahan, Kawasan Permukiman, dan Pertanahan"
    ],
    [
        "YENI APRINA, A.Md.Keb",
        "24670140820000084",
        "6203114804990004",
        "199904082025212092",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Pujon"
    ],
    [
        "YENI WULANDARI, S.Pd",
        "24670110820000308",
        "6203116306010003",
        "200106232025212027",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Balai Banjang"
    ],
    [
        "YENIE, A.Md.Kep",
        "24670140820000380",
        "6203096608950008",
        "199508262025212128",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Melati"
    ],
    [
        "YEPIN",
        "24670130810000582",
        "6203020108860003",
        "198608012025211157",
        "Kapuas",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "YERI WARTIN, S.H.",
        "24670130810000427",
        "6203012206850003",
        "198506222025211121",
        "Kapuas",
        "S-1 ILMU HUKUM",
        "Penata Layanan Operasional",
        "Dinas Pemberdayaan Masyarakat dan Desa"
    ],
    [
        "YESI GASELA, S.E",
        "24670130820000658",
        "6203094107930151",
        "199312212025212118",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "UPT Puskesmas Mantangai"
    ],
    [
        "YESICA NATALINA, S.AP",
        "24670130820000204",
        "6203027012870003",
        "198712302025212112",
        "Kapuas",
        "S-1 ADMINISTRASI PUBLIK",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "YETI SISNAWATI, S.Pd",
        "24670110820000281",
        "6203055205900002",
        "198909072025212169",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Teluk Hiri"
    ],
    [
        "YETTO",
        "24670130810000170",
        "6203011406900002",
        "199006142025211157",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Komunikasi, Informatika, Persandian dan Statistik"
    ],
    [
        "YEYEN SUTRISNA DEWI, S.Pd",
        "24670110820000599",
        "6271036608970007",
        "199708262025212104",
        "Gunung Mas",
        "S-1 PENDIDIKAN FISIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Timpah Satu Atap"
    ],
    [
        "YOELVA GIOVANNY ELIZABETH SARAGIH, S.Keb",
        "24670140820000319",
        "6203014207960008",
        "199607022025212128",
        "Banjarmasin",
        "S-1 KEBIDANAN",
        "Administrator Kesehatan Ahli Pertama",
        "UPT Puskesmas Pulau Kupang"
    ],
    [
        "YOGA PRATAMA",
        "24670130810000835",
        "6203061910940001",
        "199410192025211125",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Kecamatan Pulau Petak"
    ],
    [
        "YOGI MAHENDRI, S.Kom",
        "24670130810000323",
        "6203041607920001",
        "199207162025211142",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Subbagian Umum, Kepegawaian dan Tugas Pembantuan"
    ],
    [
        "YOGI PRADITIA, S.Pd",
        "24670110810000185",
        "6203102801990001",
        "199901282025211048",
        "Kapuas",
        "S-1 PENDIDIKAN TEKNIK MESIN",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Timpah Satu Atap"
    ],
    [
        "MURHAD",
        "24670130810000748",
        "6203011712800001",
        "198012172025211080",
        "Kapuas",
        "SMK SEKRETARIS",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "YOHANES SULKANEDI KANCANA, A.md Kep",
        "24670140810000086",
        "6203010907900004",
        "199007092025211160",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Barimba"
    ],
    [
        "YOLITHA BELLA CHRISTINE",
        "24670130820000366",
        "6309066512960005",
        "199612252025212138",
        "Tabalong",
        "SLTA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Penanggulangan Bencana Daerah"
    ],
    [
        "YONIE",
        "24670130820000082",
        "6203017001890006",
        "198901302025212124",
        "Gunung Mas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "ADRIAN",
        "24670130810000808",
        "6203020111040002",
        "200411012025211001",
        "Kapuas",
        "SMA/MA (IPA)",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "NUNING",
        "24670130820000336",
        "6203084404800001",
        "198004042025212067",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "AHMAD SAIDI",
        "24670130810000740",
        "6203010703040006",
        "200403072025211008",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "PERI SUPRANOTO",
        "24670130810000730",
        "6203012312800007",
        "198012232025211085",
        "Kapuas",
        "MADRASAH ALIYAH",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "YUDA YANSAH",
        "24670130810000632",
        "6203010606860011",
        "198606062025211233",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "HAIRROLLAH",
        "24670130810000854",
        "6203010812040005",
        "200412082025211002",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "YUGI HARRYANTA, S.E",
        "24670130810000011",
        "6203011702810005",
        "198102172025211062",
        "Kotawaringin Timur",
        "S-1 MANAJAMEN",
        "Penata Layanan Operasional",
        "Dinas Kependudukan dan Pencatatan Sipil"
    ],
    [
        "YUHENI",
        "24670130820000480",
        "6203015606850004",
        "198506162025212115",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Selat Tengah"
    ],
    [
        "YUHESTI, S.pd.I",
        "24670110820000755",
        "6203045704990002",
        "199904172025212084",
        "Kapuas",
        "D-IV PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Pulau Telo Baru"
    ],
    [
        "NOFRIYAN K.U. SAWANG",
        "24670130810000651",
        "6203022011040002",
        "200411202025211001",
        "Kapuas",
        "SMA IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "YULANDA RISWAN, S.pd.I",
        "24670110810000127",
        "6203042110990002",
        "199910212025211072",
        "Hulu Sungai Tengah",
        "S-1 PENDIDIKAN JASMANI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tamban Lupak"
    ],
    [
        "PORDAYANTI",
        "24670130820000315",
        "6203026608800003",
        "198008262025212046",
        "Palangka Raya",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "YULI MARIANA, S.pd.I",
        "24670110820000535",
        "6271016404860007",
        "198604242025212135",
        "Kotawaringin Timur",
        "S-1 PGSD",
        "Guru Ahli Pertama",
        "SD Negeri 4 Selat Hilir"
    ],
    [
        "YULIA",
        "24670130820000492",
        "6211035810960002",
        "199610182025212137",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "YULIA ANDRIANI",
        "24670130820000247",
        "6203014107890366",
        "198907012025212157",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "UPT Puskesmas Panamas"
    ],
    [
        "YULIA ARTHA, Amd.Kep",
        "24670140820000325",
        "6271035107970001",
        "199707112025212106",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Pinang"
    ],
    [
        "YULIA INDAH MAWARNI, S.Pd",
        "24670110820000268",
        "6371034302010012",
        "200102032025212024",
        "Banjarmasin",
        "S-1 PENDIDIKAN SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Barimba"
    ],
    [
        "YULIAN DENI DEVILLA, S.Kep.,Ners",
        "24670120120000286",
        "6203014201930006",
        "199301022025212168",
        "Kapuas",
        "D-IV + PROFESI KEPERAWATAN",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "RINNY",
        "24670130820000376",
        "6203015703040009",
        "200403172025212008",
        "Palangka Raya",
        "SMK MANAJEMEN PERKANTORAN",
        "Operator Layanan Operasional",
        "Badan Kepegawaian dan Pengembangan Sumber Daya Manusia"
    ],
    [
        "YULIANA RATNASARI, S.T.",
        "24670130820000610",
        "6203025009910002",
        "199109102025212154",
        "Kapuas",
        "S-1 TEKNIK SIPIL",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "YULIANA YAYU, S.Kom",
        "24670110820000745",
        "6203114712840002",
        "198805042025212158",
        "Kapuas",
        "D-IV SISTEM INFORMASI",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Tengah Satu Atap"
    ],
    [
        "YULIANI",
        "24670130820000394",
        "6203014702850007",
        "198210112025212061",
        "Barito Kuala",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Perhubungan"
    ],
    [
        "YULIANI",
        "24670130820000024",
        "6203014611860009",
        "198610062025212125",
        "Kapuas",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Badan Pendapatan Daerah"
    ],
    [
        "YULIANI, S.Pd",
        "24670110820000517",
        "6203056110860002",
        "198610212025212079",
        "Kapuas",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Masupa Ria"
    ],
    [
        "YULIANI ELPA PIRGIN, S.Akun",
        "24670130820000179",
        "6203026407940001",
        "199407242025212155",
        "Kapuas",
        "S-1 AKUNTANSI",
        "Penata Layanan Operasional",
        "Dinas Kesehatan"
    ],
    [
        "YULIANTI",
        "24670130820000035",
        "6203016407850003",
        "198507242025212069",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kelurahan Pulau Kupang"
    ],
    [
        "YULIANTI LESTARI, S.Sos",
        "24670110820000615",
        "6203106007940001",
        "199307202025212168",
        "Kapuas",
        "S-1 SOSIOLOGI",
        "Guru Ahli Pertama",
        "SD NEGERI 1 PETAK PUTI"
    ],
    [
        "ROHAPIJAH, S.Pd",
        "24670110820000660",
        "6203044301800003",
        "198001032025212054",
        "Kapuas",
        "S-1 PENDIDIKAN GURU ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lupak Timur"
    ],
    [
        "YULISA, S.Kom",
        "24670110820000640",
        "6271054704940002",
        "199404072025212151",
        "Palangka Raya",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Hulu"
    ],
    [
        "YULITA, Amd.Kep",
        "24670140820000318",
        "6203126006980001",
        "199806202025212114",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Sei Hanyo"
    ],
    [
        "YULITA M. SUJAT, S.E",
        "24670130820000356",
        "6271036612850004",
        "198512262025212070",
        "Kapuas",
        "S-1/A-IV EKONOMI AKUNTANSI",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "YULITAE KIRANAE, S.Kom",
        "24670130820000210",
        "6203016907950005",
        "199507292025212129",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ROLY ISKANDAR",
        "24670130810000520",
        "6203010610810003",
        "198010062025211079",
        "Kapuas",
        "SMU",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "YULIYANA, SE",
        "24670130820000225",
        "6203066603950004",
        "199503262025212114",
        "Hulu Sungai Tengah",
        "S-1 SARJANA EKONOMI",
        "Penata Layanan Operasional",
        "Badan Keuangan dan Aset Daerah"
    ],
    [
        "YULLY NATALYA, S.Pd",
        "24670110820000567",
        "6271036612870004",
        "198712262025212110",
        "Pulang Pisau",
        "S-1 / AKTA IV PGMI/SD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Danau Pantau"
    ],
    [
        "YUNANDO, A.Md. Kep",
        "24670140810000060",
        "6203012001940007",
        "199401202025211113",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Pulau Telo"
    ],
    [
        "YUNATA KRISTINE, A.Md.Kep",
        "24670140820000326",
        "6203104608980003",
        "199808062025212081",
        "Kapuas",
        "D-III KEPERAWATAN",
        "Perawat Terampil",
        "UPT Puskesmas Timpah"
    ],
    [
        "SOSILAWATI",
        "24670130820000544",
        "6203014508800007",
        "198008052025212064",
        "Pulang Pisau",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "YUNGKI PRANATA",
        "24670130810000774",
        "6203101212920004",
        "199212122025211181",
        "Kapuas",
        "SMA/MA (IPA DAN IPS)",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "YUNI",
        "24670130820000115",
        "6203026411900001",
        "199011242025212101",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "YUNI ERLINA, S.Pd",
        "24670110820000741",
        "6203015506980003",
        "199806152025212113",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bangun Harjo"
    ],
    [
        "YUNI KRISTIANTI",
        "24670130820000267",
        "6203026405880002",
        "198805242025212116",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "YUNI MAULITA SARI, S.pd.I",
        "24670110820000376",
        "6203055806010002",
        "200106182025212038",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD Negeri 2 Saka Mangkahai"
    ],
    [
        "YUNI PESPA RIANI, S.Pd",
        "24670110820000442",
        "6271035306930004",
        "199306132025212153",
        "Palangka Raya",
        "S-1 PENDIDIKAN GEOGRAFI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Lawang Kajang"
    ],
    [
        "SRI MEGAH, SE",
        "24670110820000707",
        "6271015008800003",
        "198008102025212077",
        "Kapuas",
        "S-1 EKONOMI PEMBANGUNAN",
        "Guru Ahli Pertama",
        "SMP Negeri 4 Timpah Satu Atap"
    ],
    [
        "YUNIKO, S.E.",
        "24670130810000158",
        "6211052701980001",
        "199801272025211083",
        "Pulang Pisau",
        "S-1 MANAJAMEN",
        "Penata Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "YUNISA NOFIANA",
        "24670130820000268",
        "6203014511970005",
        "199711052025212116",
        "Kapuas",
        "SMK AKUNTANSI DAN KEUANGAN",
        "Operator Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "YUNITA, A.Md. Keb",
        "24670120120001580",
        "6203013003950005",
        "199503302025212119",
        "Kapuas",
        "D-III KEBIDANAN",
        "Bidan Terampil",
        "UPT Puskesmas Palingkau"
    ],
    [
        "YUNITA DINI DIANTY G., S.Kom",
        "24670130820000134",
        "6203026806900002",
        "199006282025212117",
        "Kapuas",
        "S-1 SISTEM INFORMASI",
        "Penata Layanan Operasional",
        "Dinas Kearsipan dan Perpustakaan"
    ],
    [
        "YUNITA MADELIA PRANSISKA, S. Pd",
        "24670110820000573",
        "6271035706980005",
        "199806172025212113",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA HINDU",
        "Guru Ahli Pertama",
        "SMP Negeri 1 Kapuas Hulu"
    ],
    [
        "YUNNI ANTI, S.Pd",
        "24670110820000580",
        "6212054906980003",
        "199806092025212109",
        "Murung Raya",
        "S-1 PENDIDIKAN GURU KELAS SEKOLAH DASAR",
        "Guru Ahli Pertama",
        "SD Negeri 1 Jangkang"
    ],
    [
        "YUSA",
        "24670130820000450",
        "6203114403950001",
        "199504022025212160",
        "Kapuas",
        "SMK AKUNTANSI",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "YUSERAN FAUZI",
        "24670130810000902",
        "6203012903840009",
        "198403292025211094",
        "Hulu Sungai Selatan",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Pekerjaan Umum dan Penataan Ruang"
    ],
    [
        "SUBAHAN",
        "24670130810001024",
        "6203011709800003",
        "198009172025211073",
        "Kapuas",
        "SMK",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "YUTINAH, S.pd.I",
        "24670110820000402",
        "6203115207870002",
        "198607122025212124",
        "Kapuas",
        "D-IV PENDIDIKAN EKONOMI",
        "Guru Ahli Pertama",
        "SD Negeri 1 Balai Banjang"
    ],
    [
        "YUYUN, S.Tr.Kep",
        "24670140820000400",
        "6203117007980004",
        "199801302025212096",
        "Kapuas",
        "D-IV KEPERAWATAN",
        "Perawat Ahli Pertama",
        "UPT Puskesmas Jangkang"
    ],
    [
        "ZAHRA NUR ALIVIA, S.Pd",
        "24670110820000466",
        "6271036004010003",
        "200104202025212042",
        "Palangka Raya",
        "S-1 PGSD (PENDIDIKAN GURU SEKOLAH DASAR)",
        "Guru Ahli Pertama",
        "SD Negeri 1 Tumbang Mangkutup"
    ],
    [
        "ZAHRATUN NISA, S.Pd",
        "24670110820000626",
        "6203016207000007",
        "200007222025212057",
        "Kapuas",
        "S-1 GURU PAUD",
        "Guru Ahli Pertama",
        "SD Negeri 1 Bunga Mawar"
    ],
    [
        "SUHARTATIK",
        "24670130820000525",
        "6203015109800009",
        "198009112025212044",
        "Kapuas",
        "SEKOLAH MENENGAH KEJURUAN",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "ZAINI AKHLAK",
        "24670130810000530",
        "6203010812850003",
        "198411052025211124",
        "Kapuas",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "ZAINI HP, S.Pd.I.",
        "24670110810000051",
        "6203041305880004",
        "198805132025211147",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Kuala"
    ],
    [
        "RIO ANSYARI",
        "24670130810000760",
        "6203012809040005",
        "200409282025211001",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ZALI RAHMAN",
        "24670130810000303",
        "6203011608860008",
        "198608162025211153",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "UPT Puskesmas Panamas"
    ],
    [
        "ZAQLI JURDAN, S.E",
        "24670130810000630",
        "6203010501980005",
        "199801052025211106",
        "Kapuas",
        "S-1 EKONOMI",
        "Penata Layanan Operasional",
        "Sekretariat Dewan Perwakilan Rakyat Daerah"
    ],
    [
        "ZAUHARI ARIFIN",
        "24670130810000718",
        "6203010810820008",
        "198210082025211122",
        "Hulu Sungai Tengah",
        "SMA PAKET C",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "ZUBAIDAH, S.Pd",
        "24670110820000361",
        "6203064604970002",
        "199704062025212127",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD Negeri 1 Palangkai"
    ],
    [
        "ZULHAMSYAH",
        "24670130810000417",
        "6203010512850005",
        "198512052025211127",
        "Kapuas",
        "MADRASAH ALIYAH IPS",
        "Operator Layanan Operasional",
        "RSUD dr. H. Soemarno Sosroatmodjo"
    ],
    [
        "TITI MOLIYANI",
        "24670130820000563",
        "6203095012800004",
        "198012102025212063",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "ZULKIPLI",
        "24670130810000578",
        "6203010308970005",
        "199708032025211095",
        "Kapuas",
        "SMA ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "WIWI KUSMAWATI",
        "24670130820000572",
        "6203024609800004",
        "198006062025212075",
        "Kapuas",
        "PAKET C ILMU PENGETAHUAN SOSIAL",
        "Operator Layanan Operasional",
        "Dinas Perdagangan, Perindustrian, Koperasi dan Usaha Kecil Menengah"
    ],
    [
        "WIWIN EMELIA",
        "24670130820000571",
        "6203094208800006",
        "198008022025212042",
        "Kapuas",
        "D-I IPS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "DEWI NOR LINDAWATI, S.Kom",
        "24670110820000563",
        "6203046805940002",
        "199405282025212148",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 TAMBAN CATUR"
    ],
    [
        "NATALIA PRANATA, S.Sos",
        "24670110820000630",
        "6211044801960001",
        "Belum ada nip",
        "Pulang Pisau",
        "S-1 SOSIOLOGI",
        "Guru Ahli Pertama",
        "SMP Negeri 2 Kapuas Tengah Satu Atap"
    ],
    [
        "FARIED JANUAR, S.Pd",
        "24670110810000192",
        "6203032401910001",
        "199101242025211111",
        "Kapuas",
        "S-1 PENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD NEGERI 1 ANJIR MAMBULAU BARAT"
    ],
    [
        "JULIANES",
        "24670130810000605",
        "6203050707960001",
        "199504062025211160",
        "Kapuas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Kecamatan Kapuas Barat"
    ],
    [
        "KEREN HAPUKH MEIRYSARY, S.Kom",
        "24670130820000209",
        "6203014905950004",
        "199505092025212170",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "PERLIANTI, S.Pd",
        "24670110820000723",
        "6205024402880002",
        "Belum ada nip",
        "Kapuas",
        "S-1 PENDIDIKAN BIOLOGI",
        "Guru Ahli Pertama",
        "SD NEGERI 2 BARUNANG"
    ],
    [
        "M. IQBAL KILABSA, S.Kom",
        "24670130810000523",
        "6203012611880001",
        "198811262025211115",
        "Kapuas",
        "S-1 TEKNIK INFORMATIKA",
        "Penata Layanan Operasional",
        "Dinas Ketahanan Pangan dan Perikanan"
    ],
    [
        "RIBKA GRACE KELLY MANAFE, S.M",
        "24670620120000267",
        "6203026608980003",
        "Belum ada nip",
        "Palangka Raya",
        "S-1 MANAJAMEN",
        "Penata Layanan Operasional",
        "Sekretariat Daerah Kabupaten Kapuas"
    ],
    [
        "MASITA",
        "24670130820000596",
        "6203086101800002",
        "198001212025212040",
        "Gunung Mas",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "MASNAH, S.Pd",
        "24670110820000455",
        "6203075010940006",
        "199410102025212270",
        "Kapuas",
        "S-1 PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI",
        "Guru Ahli Pertama",
        "SD NEGERI 2 MUARA DADAHUP"
    ],
    [
        "MASTIKA",
        "24670130820000641",
        "6203017012770004",
        "197712302025212034",
        "Barito Timur",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "SANTA MARIA SINAGA, SP.d",
        "24670110820000696",
        "1207287011890003",
        "Belum ada nip",
        "Deli Serdang",
        "S-1 KEPENDIDIKAN BAHASA INGGRIS",
        "Guru Ahli Pertama",
        "SD NEGERI 1 BASUTA RAYA"
    ],
    [
        "MUHAMMAD FADHILAH, S.pd.I",
        "24670110810000133",
        "6203032912020001",
        "200212292025211016",
        "Mekkah",
        "S-1 GURU PENDIDIKAN AGAMA ISLAM",
        "Guru Ahli Pertama",
        "SD NEGERI 1 PALINGKAU LAMA"
    ],
    [
        "SINTA WULANDARI, S.Pd",
        "24670110820000410",
        "6211026905010004",
        "200002222025212089",
        "Pulang Pisau",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD NEGERI 1 TERUSAN RAYA HULU"
    ],
    [
        "SOPIA, S.pd.I",
        "24670110820000428",
        "6203047105990001",
        "199905312025212085",
        "Kapuas",
        "S-1 PENDIDIKAN GURU SEKOLAH DASAR (PGSD)",
        "Guru Ahli Pertama",
        "SD NEGERI 1 SEI TERAS"
    ],
    [
        "RATNO TUAH, S.P.",
        "24670110810000052",
        "6203052611750001",
        "197511262025211032",
        "Kapuas",
        "S-1 BUDIDAYA PERTANIAN (AGRONOMI)",
        "Guru Ahli Pertama",
        "SMP NEGERI 1 MANDAU TALAWANG SATU ATAP"
    ],
    [
        "SULIS ELIANA, S.Pd.I.",
        "24670110820000445",
        "6211014410900002",
        "Belum ada nip",
        "Pulang Pisau",
        "S-1 PENDIDIKAN AGAMA ISLAM ( TARBIYAH )",
        "Guru Ahli Pertama",
        "SMP NEGERI 2 TAMBAN CATUR"
    ],
    [
        "SARTIYAH",
        "24670130820000653",
        "6203094707800003",
        "198006072025212064",
        "Cilacap",
        "SEKOLAH MENENGAH ATAS",
        "Operator Layanan Operasional",
        "Dinas Pendidikan"
    ],
    [
        "STELLA SULING, S.Psi",
        "24670130820000298",
        "6203014508970009",
        "199708052025212115",
        "Palangka Raya",
        "SLTA/SMA SEDERAJAT",
        "Operator Layanan Operasional",
        "Dinas Lingkungan Hidup dan Kehutanan"
    ],
    [
        "VITA ALVIANA, S.Pd",
        "24670130820000630",
        "6203015310940003",
        "Belum ada nip",
        "Kapuas",
        "S-1 PENDIDIKAN AGAMA ISLAM/TARBIYAH",
        "Guru Ahli Pertama",
        "SD NEGERI 2 MAJU BERSAMA"
    ]
];
    }


}