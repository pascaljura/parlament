<?php
// Include configuration file conditionally
$configPath = './assets/php/config.php';
if (file_exists($configPath)) {
    include $configPath;
} else {
    die("Configuration file not found.");
}

require_once './vendor/autoload.php';

use Mpdf\Mpdf;
$mpdf = new Mpdf(['default_font' => 'calibri']);

// Check if id_zapis is set and valid
if (isset($_GET['id_zapis']) && filter_var($_GET['id_zapis'], FILTER_VALIDATE_INT)) {
    $id_zapis = $_GET['id_zapis'];

    // Use a prepared statement to retrieve document details from the database
    $stmt = $conn->prepare("SELECT cislo_dokumentu, datum, zapis FROM zapis WHERE id_zapis = ?");
    $stmt->bind_param("i", $id_zapis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cislo_dokumentu = htmlspecialchars($row['cislo_dokumentu']); // Sanitize output
        $datum = date('dmY', strtotime($row['datum']));
        $zapis = htmlspecialchars($row['zapis']); // Sanitize or format as needed

        // Nahrazení a formátování textu
        $zapis = str_replace("=", "<br>", $zapis);
        $zapis = str_replace("<br>--", "<br>&#160;&#160;&#9702;", $zapis);
        $zapis = str_replace("<br>-", "<br>&#8226;", $zapis);

        function ziskatTextVLomitkach($zapis)
        {
            $textInLomitka = "";
            if (preg_match('/\/\/([^\/]+)\/\//', $zapis, $matches)) {
                $textInLomitka = $matches[1];
            }
            return $textInLomitka;
        }

        $textInLomitkach = ziskatTextVLomitkach($zapis);
        $zapis = preg_replace('/\/\/([^\/]+)\/\//', '<div style="color: #3e6181; font-weight: bold; font-size: 14pt;">$1</div>', $zapis);
        $zapis = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<b><i>$1</i></b>', $zapis);
        $zapis = preg_replace('/\*\*([^*]+)\*\*/', '<b>$1</b>', $zapis);
        $zapis = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $zapis);
        $zapis = preg_replace('/~~([^~]+)~~/', '<strike>$1</strike>', $zapis);
        $zapis = preg_replace('/__([^_]+)__/', '<u>$1</u>', $zapis);

    } else {
        echo "Document not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "Invalid or missing id_zapis parameter.";
    exit();
}
$mpdf->showImageErrors = true;
// Header HTML
$headerHtml = '
<div style="text-align: center;">
<img src="./assets\img\purkynka_logo.png" style="width: 8.98cm; height: 2.88cm;">
    <table style="width: 100%; font-size: 9pt; border-top: 2px solid black; border-collapse: collapse;">
        <tr>
            <td style="text-align: left;">Číslo dokumentu: ' . $cislo_dokumentu . '/' . $datum . '</td>
            <td style="text-align: center;">Počet stran: 1</td>
            <td style="text-align: right;">Počet příloh: 0</td>
        </tr>
        <tr>
            <td>Dokument</td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>';

// Body HTML
$bodyHtml = '
<div style="font-size: 22pt; padding-top: 120px;">
    Záznam z jednání dne ' . date('d-m-Y', strtotime($row['datum'])) . '
</div>
<div style="font-size: 11pt; margin-top: 5pxx;">
    ' . nl2br($zapis) . '<br><br>

    V Brně dne ' . date('d.m.Y', strtotime($row['datum'])) . '<br>Zástupci školního Parlamentu
</div>';

// Footer HTML
$footerHtml = '
<table style="width: 100%; font-size: 9pt; border-collapse: collapse;">
    <tr>
        <td style="text-align: left;">
            Záznam z jednání dne ' . date('d-m-Y', strtotime($row['datum'])) . '
        </td>
        <td style="text-align: right;">
            Stránka {PAGENO} z {nbpg}
        </td>
    </tr>
</table>';

// Configure mPDF with header, body, and footer
$mpdf->SetHTMLHeader($headerHtml);
$mpdf->SetHTMLFooter($footerHtml);
$mpdf->WriteHTML($bodyHtml);

// Output PDF
$mpdf->Output('zapis_ze_schuze.pdf', 'I');
?>