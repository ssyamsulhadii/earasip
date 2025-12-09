<?php

namespace App\Services;

use Mpdf\Mpdf;
// bookman_osr.ttf
class ServicePdf
{
  public function generate($html, $filename = 'document.pdf', $output = 'I')
  {

    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new \Mpdf\Mpdf([
      'mode' => 'utf-8',
      'format' => [210, 330], // F4 Indonesia
      'margin_top' => 15,
      'margin_bottom' => 15,
      'margin_left' => 20,
      'margin_right' => 20,
      'fontDir' => array_merge($fontDirs, [
        public_path('fonts'),
      ]),
      'fontdata' => $fontData + [
        'cambria' => [
          'R' => 'cambria.ttf',
        ],
      ],
      'default_font' => 'cambria',
    ]);

    $mpdf->WriteHTML($html);

    return $mpdf->Output($filename, $output);
  }

  public function save($html, $relativePath)
  {
    $mpdf = new Mpdf([
      'mode' => 'utf-8',
      'format' => [210, 330], // F4 Indonesia
      'margin_top' => 15,
      'margin_bottom' => 15,
      'margin_left' => 20,
      'margin_right' => 20,
    ]);

    $mpdf->WriteHTML($html);

    $fullPath = storage_path('app/public/' . $relativePath);
    $dir = dirname($fullPath);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $mpdf->Output($fullPath, \Mpdf\Output\Destination::FILE);
    return $fullPath;
  }
}
