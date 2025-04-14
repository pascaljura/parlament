<?php
// Include configuration file conditionally
$configPath = '../assets/php/config.php';
if (file_exists($configPath)) {
    include $configPath;
} else {
    die("Configuration file not found.");
}

require_once '../vendor/autoload.php';

use Mpdf\Mpdf;
$mpdf = new Mpdf(['default_font' => 'calibri']);

// Check if idnotes_parlament is set and valid
if (isset($_GET['idnotes_parlament']) && filter_var($_GET['idnotes_parlament'], FILTER_VALIDATE_INT)) {
    $idnotes_parlament = $_GET['idnotes_parlament'];

    // Use a prepared statement to retrieve document details and user username from the database
    $stmt = $conn->prepare("
        SELECT z.*, u.username 
        FROM notes_alba_rosa_parlament z
        LEFT JOIN users_alba_rosa_parlament u ON z.idusers_parlament = u.idusers_parlament
        WHERE z.idnotes_parlament = ?
    ");
    $stmt->bind_param("i", $idnotes_parlament);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $document_number = htmlspecialchars($row['document_number']); // Sanitize output
        $date = date('dmY', strtotime($row['date']));
        $notes = htmlspecialchars($row['notes']); // Sanitize or format as needed
        $username = htmlspecialchars($row['username']); // Retrieve and sanitize user's username

        // Nahrazení a formátování textu
        $notes = str_replace("=", "<br>", $notes);
        $notes = str_replace("<br>--", "<br>&#160;&#160;&#9702;", $notes);
        $notes = str_replace("<br>-", "<br>&#8226;", $notes);

        function ziskatTextVLomitkach($notes)
        {
            $textInLomitka = "";
            if (preg_match('/\/\/([^\/]+)\/\//', $notes, $matches)) {
                $textInLomitka = $matches[1];
            }
            return $textInLomitka;
        }

        $textInLomitkach = ziskatTextVLomitkach($notes);
        $notes = preg_replace('/\/\/([^\/]+)\/\//', '<div style="color: #3e6181; font-weight: bold; font-size: 14pt;">$1</div>', $notes);
        $notes = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<b><i>$1</i></b>', $notes);
        $notes = preg_replace('/\*\*([^*]+)\*\*/', '<b>$1</b>', $notes);
        $notes = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $notes);
        $notes = preg_replace('/~~([^~]+)~~/', '<strike>$1</strike>', $notes);
        $notes = preg_replace('/__([^_]+)__/', '<u>$1</u>', $notes);

    } else {
        echo "Document not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "Invalid or missing idnotes_parlament parameter.";
    exit();
}
$mpdf->showImageErrors = true;

// Body HTML
$bodyHtml = '
<div style="text-align: center;">
<img src="../assets\img\purkynka_logo.png" style="width: 8.98cm; height: 2.88cm;">
    <table style="width: 100%; font-size: 9pt; border-top: 2px solid black; border-collapse: collapse;">
        <tr>
            <td style="text-align: left;">Číslo dokumentu: ' . $document_number . '/' . $date . '</td>
            <td style="text-align: center;">Počet stran: 1</td>
            <td style="text-align: right;">Počet příloh: 0</td>
        </tr>
        <tr>
            <td>Dokument</td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>
<div style="font-size: 22pt; padding-top: 5px;">
    Záznam z jednání dne ' . date('d.m.Y', strtotime($row['date'])) . '
</div>
<div style="font-size: 11pt; margin-top: 5pxx;">
    ' . nl2br($notes) . '<br><br>

    V Brně dne ' . date('d.m.Y', strtotime($row['date'])) . '<br>Zástupci školního Parlamentu<br>Zapsal: ' . $username . '<br>Ověřila: Mgr. Denisa Gottwaldová
</div>';

// Footer HTML
$footerHtml = '
<table style="width: 100%; font-size: 9pt; border-collapse: collapse;">
    <tr>
        <td style="text-align: left;"> ' .
    $document_number . ' Záznam z jednání dne ' . date('d.m.Y', strtotime($row['date'])) . '
        </td>
        <td style="text-align: right;">
            Strana {PAGENO} z {nbpg}
        </td>
    </tr>
</table>';

// Configure mPDF with header, body, and footer and title
$mpdf->SetTitle('Alba-rosa.cz | Parlament na Purkyňce');
$mpdf->SetHTMLFooter($footerHtml);
$mpdf->WriteHTML($bodyHtml);

// Output PDF
$mpdf->Output('notes-ze-schuze-' . date('d-m-Y', strtotime($row['date'])) . '.pdf', 'I');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <link rel="manifest" href="./assets/json/manifest.json">
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
</head>
<body>
<script src="./assets/js/script.js">    </script>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
</body>
</html>