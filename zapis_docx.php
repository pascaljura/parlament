<?php
// Include configuration file conditionally
$configPath = './assets/php/config.php';
if (file_exists($configPath)) {
    include $configPath;
} else {
    die("Configuration file not found.");
}

require_once './vendor/autoload.php';



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
        $zapis = str_replace("=", "\n", $zapis);
        $zapis = str_replace("<br>--", "\n\u2022", $zapis);
        $zapis = str_replace("<br>-", "\n\u2022", $zapis);

        function ziskatTextVLomitkach($zapis)
        {
            $textInLomitka = "";
            if (preg_match('/\/\/([^\/]+)\/\//', $zapis, $matches)) {
                $textInLomitka = $matches[1];
            }
            return $textInLomitka;
        }

        $textInLomitkach = ziskatTextVLomitkach($zapis);
        $zapis = preg_replace('/\/\/([^\/]+)\/\//', '<strong style="color: #3e6181;">$1</strong>', $zapis);
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
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// Create a new PHPWord object
$phpWord = new PhpWord();

// Add a section for the document content
$section = $phpWord->addSection();

// Add header content
$headerText = 'Číslo dokumentu: ' . $cislo_dokumentu . '/' . $datum . "\n";
$headerText .= 'Počet stran: 1' . "\n";
$headerText .= 'Počet příloh: 0' . "\n";
$headerText .= 'Dokument' . "\n";

// Add header to the document
$section->addText($headerText, ['bold' => true, 'size' => 12]);

// Add body content
$bodyText = 'Záznam z jednání dne ' . date('d-m-Y', strtotime($row['datum'])) . "\n\n";
$bodyText .= $zapis . "\n\n";
$bodyText .= 'V Brně dne ' . date('d.m.Y', strtotime($row['datum'])) . "\n";
$bodyText .= 'Zástupci školního Parlamentu';

// Add body to the document
$section->addText($bodyText, ['size' => 12]);

// Footer text
$footerText = 'Záznam z jednání dne ' . date('d-m-Y', strtotime($row['datum'])) . "\n";
$footerText .= 'Stránka {PAGE_NUM} z {PAGE_COUNT}';

// Add footer to the document
$section->addText($footerText, ['italic' => true, 'size' => 10]);

// Save the DOCX file
$fileName = 'zapis_ze_schuze.docx';
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

// Save the file to the output stream
$phpWord->save('php://output', 'Word2007');
?>