<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P3I-BKPSDM | Production</title>
    Memuat Tailwind CSS untuk styling responsif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script>
        // Konfigurasi Tailwind untuk menggunakan font Inter dan warna kustom
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#4f46e5', // Indigo-600
                        'secondary': '#6b7280', // Gray-500
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }

        // Fungsi Simulasi Pencarian
        function handleSearch(event) {
            event.preventDefault(); // Mencegah submit form default

            const keyword = document.getElementById('search-keyword').value.trim();
            const resultsDiv = document.getElementById('search-results');
            const searchStatus = document.getElementById('search-status');
            const initialMessage = document.getElementById('initial-message');

            searchStatus.textContent = "Mencari...";
            resultsDiv.innerHTML = '';
            resultsDiv.classList.add('hidden');
            initialMessage.classList.add('hidden'); // Sembunyikan pesan awal saat mencari

            // Simulasi proses loading
            setTimeout(() => {
                let simulatedResults = [];
                const lowerKeyword = keyword.toLowerCase();

                if (lowerKeyword.includes('laporan')) {
                    simulatedResults = [{
                            title: "Laporan Keuangan Kuartal Terakhir",
                            type: "Dokumen",
                            date: "2025-09-30"
                        },
                        {
                            title: "Ringkasan Laporan Penjualan Harian",
                            type: "Data",
                            date: "2025-11-28"
                        }
                    ];
                } else if (lowerKeyword.includes('pengguna') || lowerKeyword.includes('user')) {
                    simulatedResults = [{
                            title: "Daftar Pengguna Aktif",
                            type: "Data",
                            date: "2025-11-25"
                        },
                        {
                            title: "Analisis Aktivitas Pengguna Premium",
                            type: "Laporan",
                            date: "2025-10-01"
                        }
                    ];
                } else if (lowerKeyword.includes('proyek') || lowerKeyword.includes('project')) {
                    simulatedResults = [{
                            title: "Dokumentasi Proyek X",
                            type: "Dokumen",
                            date: "2025-07-01"
                        },
                        {
                            title: "Rencana Anggaran Proyek Baru",
                            type: "Keuangan",
                            date: "2025-12-05"
                        }
                    ];
                } else if (keyword === "") {
                    // Jika keyword kosong, berikan hasil umum atau pesan
                    searchStatus.textContent = `Masukkan kata kunci untuk memulai pencarian.`;
                    resultsDiv.innerHTML = `
                        <div class="text-center p-6 bg-blue-50 rounded-xl border border-blue-200">
                            <p class="text-lg font-medium text-blue-800">Kata kunci tidak boleh kosong.</p>
                            <p class="text-sm text-blue-600 mt-1">Silakan masukkan apa yang ingin Anda cari (cth: Laporan, Pengguna).</p>
                        </div>
                    `;
                    resultsDiv.classList.remove('hidden');
                    return; // Hentikan fungsi jika kosong
                } else {
                    // Default hasil (jika keyword tidak cocok dengan kriteria simulasi)
                    simulatedResults = [{
                            title: `Arsip Hasil Pencarian untuk: ${keyword}`,
                            type: "Dokumen",
                            date: "2025-01-01"
                        },
                        {
                            title: `Catatan Historis Mengenai ${keyword}`,
                            type: "Data",
                            date: "2024-12-15"
                        }
                    ];
                }

                if (simulatedResults.length > 0) {
                    searchStatus.textContent = `Ditemukan ${simulatedResults.length} hasil untuk "${keyword}".`;

                    const resultsHtml = simulatedResults.map(item => `
                        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                            <p class="text-sm font-semibold text-primary">${item.type}</p>
                            <h3 class="text-lg font-bold text-gray-800 mt-1">${item.title}</h3>
                            <p class="text-sm text-gray-500 mt-2">Diperbarui: ${item.date}</p>
                            <button class="mt-3 text-sm font-medium text-primary hover:text-indigo-700">Lihat Detail &rarr;</button>
                        </div>
                    `).join('');

                    resultsDiv.innerHTML = resultsHtml;
                    resultsDiv.classList.remove('hidden');

                } else {
                    searchStatus.textContent = `Tidak ada hasil yang ditemukan untuk "${keyword}".`;
                    resultsDiv.innerHTML = `
                        <div class="text-center p-6 bg-yellow-50 rounded-xl border border-yellow-200">
                            <p class="text-lg font-medium text-yellow-800">Coba kata kunci lain.</p>
                            <p class="text-sm text-yellow-600 mt-1">Simulasi tidak menemukan data yang cocok dengan kriteria Anda.</p>
                        </div>
                    `;
                    resultsDiv.classList.remove('hidden');
                }

            }, 1000); // Tunda 1 detik untuk simulasi koneksi
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen p-4 sm:p-8 font-sans">

    <div class="max-w-4xl mx-auto">

        Header Form Pencarian
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 border-b pb-2">Penarikan Data SK PPPK Paruh Waktu</h1>
            <p class="text-gray-500 mt-2">Cukup masukkan kata kunci <b>No Peserta</b> dan <b>NIK Peserta</b> untuk
                menemukan data Anda.</p>
        </header>

        Form Pencarian
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-gray-100">
            <form id="search-form" method="GET" action="{{ route('search') }}">
                <div class="flex-grow">
                    <h3 class="mb-3">Kata Kunci Peserta</h3>

                    {{-- No Peserta --}}
                    <div class="relative">
                        <input type="number" name="no_peserta" value="{{ old('no_peserta', request('no_peserta')) }}"
                            placeholder="No Peserta"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150 text-gray-800">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>

                        @error('no_peserta')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIK --}}
                    <div class="relative mt-2">
                        <input type="number" name="nik" value="{{ old('nik', request('nik')) }}"
                            placeholder="NIK Peserta"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150 text-gray-800">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>

                        @error('nik')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="w-full md:w-auto mt-3">
                    <button type="submit"
                        class="w-full md:w-auto px-6 py-2 bg-primary text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition duration-150">
                        <i class="fas fa-search mr-2"></i> Cari Data
                    </button>
                </div>
            </form>
        </div>

        Area Hasil Pencarian
        <div class="mt-8">
            @if (isset($has_search) && $has_search)
                {{-- saat ada pencarian --}}
                @if ($result)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- CARD DATA PESERTA --}}
                        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col">
                            <p class="text-sm font-semibold text-primary">No Peserta</p>
                            <h3 class="text-lg font-bold">{{ $result->username }}</h3>
                            <p class="text-gray-700">{{ $result->nama }}</p>

                            <a href="{{ asset('dokumen-sk/PW' . $result->username . '_SK.pdf') }}" target="_blank"
                                class="mt-4 self-end px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                <i class="fas fa-download mr-1"></i> Download SK
                            </a>
                            <a href="{{ asset('SPP/PW' . $result->username . '.pdf') }}" target="_blank"
                                class="mt-4 self-end px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                <i class="fas fa-download mr-1"></i> Download SPP
                            </a>
                            <a href="{{ route('cetak.spk', ['no_peserta' => $result->username, 'nik' => $result->nik]) }}"
                                target="_blank"
                                class="mt-4 self-end px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                <i class="fas fa-download mr-1"></i> Cetak SPK</a>
                        </div>

                        {{-- CARD INFORMASI --}}
                        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
                            <p class="text-sm font-semibold text-primary mb-2">Informasi</p>

                            <ul class="text-gray-700 text-sm leading-relaxed">
                                <li>Jika saat download SK tampil halaman <strong>404</strong>,</li>
                                <li>berarti <strong>SK masih proses usul ke BKN</strong>.</li>
                            </ul>
                        </div>

                    </div>
                @else
                    {{-- pencarian tapi data tidak ada --}}
                    <div class="text-center p-6 bg-yellow-50 rounded-xl border border-yellow-200">
                        <p class="text-lg font-medium text-yellow-800">Tidak ditemukan.</p>
                        <p class="text-sm text-yellow-600 mt-1">Silakan cek no peserta / nik peserta.</p>
                    </div>
                @endif

                <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-gray-100 mt-3">
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50"
                            role="alert">
                            {{ session('success') }}
                        </div>
                    @endif


                    <form id="search-form" method="POST" action="{{ route('upload.spk') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $result->username }}" name="nopeserta">
                        <div class="flex-grow">
                            <h3 class="mb-3">Upload SPK Yang sudah dit TTD dengan <b>Materai tempel/elektronik</b>
                            </h3>
                            <div class="relative mt-2">
                                <input type="file" name="spk_final" value="{{ old('nik', request('nik')) }}"
                                    placeholder="NIK Peserta"
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary transition duration-150 text-gray-800">

                                @error('spk_final')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="w-full md:w-auto mt-3">
                            <button type="submit"
                                class="w-full md:w-auto px-6 py-2 bg-primary text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition duration-150">Simpan
                            </button>
                        </div>
                    </form>
                </div>


            @endif
        </div>
    </div>

</body>

</html>
