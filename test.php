<?php
require_once __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

try {
    $mpdf = new Mpdf();
    $mpdf->WriteHTML('<h1>Hello World</h1>');
    $mpdf->Output();
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
}
