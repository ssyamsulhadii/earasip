<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: 210mm 330mm;
            /* F4 Indonesia */
            margin-top: 15mm;
            margin-bottom: 15mm;
            margin-left: 17mm;
            margin-right: 17mm;
            footer: html_tteFooter;
        }

        body {
            font-family: 'cambria,bookman';
            font-size: 11pt;
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
            width: 180px;
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
            <img src="{{ public_path('img/logo-garuda.png') }}" style="width:85px;">
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

    @yield('content')
</body>

</html>
