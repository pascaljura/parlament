<?php
require('../tfpdf/tfpdf.php');

// Include configuration file conditionally
$configPath = '../assets/php/config.php';
if (file_exists($configPath)) {
    include $configPath;
} else {
    die("Configuration file not found.");
}

// Start output buffering to prevent premature output
ob_start();

// Check if idnotes_parlament is set and valid
if (isset($_GET['idnotes_parlament']) && filter_var($_GET['idnotes_parlament'], FILTER_VALIDATE_INT)) {
    $idnotes_parlament = $_GET['idnotes_parlament'];

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
        $document_number = $row['document_number'];
        $date = date('dmY', strtotime($row['date']));
        $date_human = date('d.m.Y', strtotime($row['date']));
        $notes = $row['notes'];
        $username = $row['username'];

        // Nahrazení a formátování textu
        $notes = str_replace("=", "\n", $notes);
        $notes = str_replace("\n--", "\n  ◦", $notes);
        $notes = str_replace("\n-", "\n•", $notes);

        function ziskatTextVLomitkach($notes)
        {
            $textInLomitka = "";
            if (preg_match('/\/\/([^\/]+)\//', $notes, $matches)) {
                $textInLomitka = $matches[1];
            }
            return $textInLomitka;
        }

        $textInLomitkach = ziskatTextVLomitkach($notes);

        $notes = preg_replace('/\/\/([^\/]+)\//', "[\1]", $notes);

        $notes = preg_replace_callback('/\*\*\*([^*]+)\*\*\*/', function ($matches) {
            return chr(2) . $matches[1] . chr(3);
        }, $notes);

        $notes = preg_replace_callback('/\*\*([^*]+)\*/', function ($matches) {
            return chr(4) . $matches[1] . chr(5);
        }, $notes);

        $notes = preg_replace_callback('/\*([^*]+)\*/', function ($matches) {
            return chr(6) . $matches[1] . chr(7);
        }, $notes);

    } else {
        echo "Document not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "Invalid or missing idnotes_parlament parameter.";
    exit();
}

$pdf = new tFPDF('P', 'mm', 'A4');
$pdf->SetMargins(10, 10);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();
$pdf->SetTitle('Záznam ze schůze');

$pdf->AddFont('TimesNewRoman', '', 'times.ttf', true);
$pdf->AddFont('TimesNewRoman', 'B', 'timesbd.ttf', true);
$pdf->AddFont('TimesNewRoman', 'I', 'timesi.ttf', true);
$pdf->AddFont('TimesNewRoman', 'BI', 'timesbi.ttf', true);
$pdf->SetFont('TimesNewRoman', '', 12);

$pdf->Image('../assets/img/purkynka_logo.png', ($pdf->GetPageWidth() - 90) / 2, 10, 90);
$pdf->Ln(35);

$pdf->SetFont('TimesNewRoman', '', 10);
$pdf->Cell(63, 6, "Číslo dokumentu: $document_number/$date", 0, 0, 'L');
$pdf->Cell(63, 6, "Počet stran: 1", 0, 0, 'C');
$pdf->Cell(63, 6, "Počet příloh: 0", 0, 1, 'R');
$pdf->Ln(5);

$pdf->SetFont('TimesNewRoman', 'B', 14);
$pdf->Cell(0, 10, "Záznam z jednání dne $date_human", 0, 1);
$pdf->Ln(2);

$pdf->SetFont('TimesNewRoman', '', 11);
$lines = explode("\n", $notes);
foreach ($lines as $line) {
    $i = 0;
    $buffer = '';
    $fontStyle = '';
    $pdf->SetFont('TimesNewRoman', '', 11);

    while ($i < strlen($line)) {
        $char = $line[$i];
        switch ($char) {
            case chr(2):
                if ($buffer !== '') {
                    $pdf->SetFont('TimesNewRoman', $fontStyle, 11);
                    $pdf->MultiCell(0, 6, $buffer);
                    $buffer = '';
                }
                $fontStyle = 'BI';
                break;
            case chr(4):
                if ($buffer !== '') {
                    $pdf->SetFont('TimesNewRoman', $fontStyle, 11);
                    $pdf->MultiCell(0, 6, $buffer);
                    $buffer = '';
                }
                $fontStyle = 'B';
                break;
            case chr(6):
                if ($buffer !== '') {
                    $pdf->SetFont('TimesNewRoman', $fontStyle, 11);
                    $pdf->MultiCell(0, 6, $buffer);
                    $buffer = '';
                }
                $fontStyle = 'I';
                break;
            case chr(3):
            case chr(5):
            case chr(7):
                if ($buffer !== '') {
                    $pdf->SetFont('TimesNewRoman', $fontStyle, 11);
                    $pdf->MultiCell(0, 6, $buffer);
                    $buffer = '';
                }
                $fontStyle = '';
                break;
            default:
                $buffer .= $char;
        }
        $i++;
    }
    if ($buffer !== '') {
        $pdf->SetFont('TimesNewRoman', $fontStyle, 11);
        $pdf->MultiCell(0, 6, $buffer);
    }
}

$pdf->Ln(5);
$pdf->SetFont('TimesNewRoman', '', 11);
$pdf->Cell(0, 5, "V Brně dne $date_human", 0, 1);
$pdf->Cell(0, 5, "Zástupci školního Parlamentu", 0, 1);
$pdf->Cell(0, 5, "Zapsal: $username", 0, 1);
$pdf->Cell(0, 5, "Ověřila: Mgr. Denisa Gottwaldová", 0, 1);

// Vlastní zápatí se dvěma sloupci
$pdf->SetY(-20);
$pdf->SetFont('TimesNewRoman', '', 9);
$pdf->Cell(95, 5, "$document_number Záznam z jednání dne $date_human", 0, 0, 'L');
$pdf->Cell(95, 5, "Strana " . $pdf->PageNo(), 0, 0, 'R');

ob_end_clean();
$pdf->Output('I', 'notes-ze-schuze-' . date('d-m-Y', strtotime($row['date'])) . '.pdf');
?>