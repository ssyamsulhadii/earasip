<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

}