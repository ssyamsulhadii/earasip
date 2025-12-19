<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: 210mm 330mm;
            /* F4 Indonesia */
            margin-top: 43mm;
            margin-bottom: 15mm;
            margin-left: 17mm;
            margin-right: 17mm;
            footer: html_tteFooter;
        }

        body {
            font-family: 'cambria,bookman';
            font-size: 11.5pt;
            line-height: 1.4;
        }

        h2,
        h3,
        h4 {
            text-align: center;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .label {
            width: 270px;
            vertical-align: top;
        }

        .text-justify {
            text-align: justify;
        }

        .indent {
            text-indent: 30px;
        }
    </style>
</head>

<body>
    <!-- HEADER UNTUK SATU HALAMAN -->
    <htmlpageheader name="logoHeader">
        <div style="text-align: center;">
            <img src="{{ public_path('img/header-sekda.png') }}">
        </div>
    </htmlpageheader>
    <!-- FOOTER UNTUK SEMUA HALAMAN -->
    <htmlpagefooter name="tteFooter" style="display:none">
        <div style="text-align: center; font-size: 9pt; color: #000000;">
            <i>Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik
                yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE),
                Badan Siber dan Sandi Negara (BSSN).</i>
        </div>
    </htmlpagefooter>

    <sethtmlpageheader name="logoHeader" value="on" show-this-page="1" />
    <h4 style="text-decoration: underline;">SURAT PERINTAH PENUGASAN</h4>
    <div style="text-align: center">Nomor : 800/189/P3I/BKPSDM/2025</div>

    <table style="margin-top: 20px">
        <tr>
            <td style="vertical-align: top;">
                <strong>I. DASAR</strong>
            </td>
        </tr>
        <tr>
            <td style="padding-left: 14px;">Keputusan Bupati Kapuas Nomor : 800.1.2.5/167/P3I/BKPSDM/2025 Tanggal 31
                Oktober 2025 </td>
        </tr>
    </table>
    <table style="margin-top: 20px">
        <tr>
            <td style="vertical-align: top;">
                <strong>I. DIPERINTAHKAN KEPADA : </strong>
            </td>
        </tr>
        <tr>
            <td style="padding-left: 10px;">
                <table>
                    <tr>
                        <td class="label">Nama</td>
                        <td>: {{ $result->nama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor Induk PPPK Paruh Waktu</td>
                        <td>: {{ $result->nip }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tempat Tanggal Lahir</td>
                        <td>: {{ $result->tempat_lahir }}, {{ $tanggal_lahir }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kebutuhan Jabatan</td>
                        <td>: {{ $result->jabatan }}</td>
                    </tr>
                </table>
                <table style="margin-top: 20px;">
                    <tr>
                        <td style="vertical-align: top">1.</td>
                        <td style="text-align: justify; padding-bottom: 10px">
                            Untuk berangkat ke {{ $result->unit_kerja }} selambat-lambatnya 2 minggu
                            sejak dikeluarkannya Surat Perintah Penugasan ini.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">2.</td>
                        <td style="text-align: justify; padding-bottom: 10px">
                            Setelah tiba di tempat penugasan, supaya melapor kepada Kepala {{ $result->unit_kerja }}
                            untuk menerima penugasan selanjutnya.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">3.</td>
                        <td style="text-align: justify; padding-bottom: 10px">
                            Biaya perjalanan ke tempat penugasan dibebankan kepada Pegawai Pemerintah dengan
                            Perjanjian Kerja Paruh Waktu yang bersangkutan.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">4.</td>
                        <td style="text-align: justify; padding-bottom: 10px">
                            Surat Perintah Penugasan ini supaya dijalankan dengan penuh tanggung jawab.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="margin-top: 70px; margin-left: 28em">
        <tr>
            <td style="width: 50%; text-align: left; vertical-align: top; text-align: left">
                <div style="margin-bottom: 2px;">Kuala Kapuas, 26 November 2025 Sekretaris Daerah,</div>
                <div>
                    <img src="{{ public_path('img/tte-spp-sekda.jpg') }}" style="width:110px; margin-left: -6px;">
                </div>
                <div style="font-weight: bold; margin-bottom: 3px;">USIS I. SANGKAI</div>
                <div style="margin-bottom: 3px;">Pembina Utama Madya (IV/d)</div>
                <div>NIP. 197501181999031006</div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 70px">
        <tr>
            <td style="vertical-align: top;">
                <strong><i>Tembusan</i></strong> disampaikan kepada Yth :
            </td>
        </tr>
        <tr>
            <td style="padding-left: 10px;">
                <table>
                    <tr>
                        <td>1.</td>
                        <td>Kepala BKAD Kabupaten Kapuas di Kuala Kapuas.</td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td>Inspektur Kabupaten Kapuas di Kuala Kapuas.</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>Kepala Unit Kerja terkait yang dipandang perlu.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
