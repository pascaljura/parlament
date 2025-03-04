<?php
// Podmíněné načtení konfiguračního souboru
$cestaKeKonfiguraci = '../assets/php/config.php';
if (file_exists($cestaKeKonfiguraci)) {
    include $cestaKeKonfiguraci;
} else {
    die("Konfigurační soubor nebyl nalezen.");
}

require_once '../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// Ověření, zda je idnotes_parlament nastaveno a je platné
if (isset($_GET['idnotes_parlament']) && filter_var($_GET['idnotes_parlament'], FILTER_VALIDATE_INT)) {
    $idnotes_parlament = $_GET['idnotes_parlament'];

    // Příprava dotazu pro načtení detailů dokumentu a jména uživatele z databáze
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
        $radek = $result->fetch_assoc();
        $document_number = htmlspecialchars($radek['document_number']);
        $date = date('dmY', strtotime($radek['date']));
        $notes = htmlspecialchars($radek['notes']);
        $jmeno = htmlspecialchars($radek['username']);

        // Nahrazení odrážek
        $notes = str_replace("=", "<br>", $notes);
        $notes = preg_replace('/(?<=^|<br>)(?![\w])--/', "<br>  ◦", $notes);
        $notes = preg_replace('/(?<=^|<br>)(?![\w])-(?!-)/', "<br>•", $notes);

        // Extrakce stylovaného textu
        preg_match_all('/\/\/([^\/]+)\/\//', $notes, $title);
        preg_match_all('/\*\*\*([^*]+)\*\*\*/', $notes, $bolitalic);
        preg_match_all('/\*\*([^*]+)\*\*/', $notes, $bold);
        preg_match_all('/\*([^*]+)\*/', $notes, $italic);
        preg_match_all('/~~([^~]+)~~/', $notes, $strike);
        preg_match_all('/__([^_]+)__/', $notes, $underline);

        // Odebrání formátovacích značek, aby neovlivňovaly finální text
        $notes = preg_replace('/\/\/([^\/]+)\/\//', '<t>', $notes);
        $notes = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<bi>', $notes);
        $notes = preg_replace('/\*\*([^*]+)\*\*/', '<b>', $notes);
        $notes = preg_replace('/\*([^*]+)\*/', '<i>', $notes);
        $notes = preg_replace('/~~([^~]+)~~/', '<s>', $notes);
        $notes = preg_replace('/__([^_]+)__/', '<u>', $notes);

    } else {
        echo "Dokument nebyl nalezen.";
        exit();
    }
    $stmt->close();
} else {
    echo "Neplatný nebo chybějící parametr idnotes_parlament.";
    exit();
}

// Vytvoření nové instance PhpWord
$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Calibri');
$phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language("cs-CZ"));
// Definování sekce
$section = $phpWord->addSection([
    'marginTop' => 1000,
    'marginBottom' => 1000,
    'marginLeft' => 1000,
    'marginRight' => 1000,
    'pageNumberingStart' => 1
]);

$zahlavi = $section->addHeader();

// Přidání obrázku přímo do stávající sekce (na střed)
$zahlavi->addImage('../assets/img/purkynka_logo1.png', [
    'width' => 430,
    'height' => 76,
    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
]);

// Vytvoření tabulky přímo ve stávající sekci
$table = $zahlavi->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'width' => 100 * 100]);

// Přidání řádku
$table->addRow();

// Přidání první buňky: Číslo dokumentu
$table->addCell(4000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])->addText(
    "Číslo dokumentu: $document_number/$date",
    ['size' => 9]
);

// Přidání druhé buňky: Počet stran
$table->addCell(2000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])->addText(
    "Počet stran: {PAGE}",
    ['size' => 9],
    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
);

// Přidání třetí buňky: Počet příloh
$table->addCell(2000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])->addText(
    "Počet příloh: 0",
    ['size' => 9],
    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]
);
// Přidání řádku
$table->addRow();
// Přidání první buňky: Číslo dokumentu bez horního ohraničení
$table->addCell(4000)->addText(
    "Dokument",
    ['size' => 9]
);


// Přidání obsahu s dynamickým formátováním
$section->addText("Záznam z jednání dne " . date('d.m.Y', strtotime($radek['date'])), ['size' => 22, 'bold' => true]);

// Zpracování formátování
$notesRun = $section->addTextRun(['size' => 11]);
$textParts = preg_split('/(<br>|<t>|<bi>|<b>|<i>|<s>|<u>)/', $notes, -1, PREG_SPLIT_DELIM_CAPTURE);
$prewPart = null;
foreach ($textParts as $part) {

    if (strlen($part) > 1 && $part != $prewPart) {
        
        if ($part === '<br>') {
            $notesRun->addTextBreak();
        } elseif ($part === '<t>') {
            $notesRun->addText(array_shift($title[1]), [
                'size' => 14,
                'bold' => true,
                'color' => '3e6181'
            ]);
        } else if ($part === '<bi>') {
            $notesRun->addText(array_shift($bolitalic[1]), ['bold' => true, 'italic' => true]);
        } elseif ($part === '<b>') {
            $notesRun->addText(array_shift($bold[1]), ['bold' => true]);
        } elseif ($part === '<i>') {
            $notesRun->addText(array_shift($italic[1]), ['italic' => true]);
        } elseif ($part === '<s>') {
            $notesRun->addText(array_shift($strike[1]), ['strikethrough' => true]);
        } elseif ($part === '<u>') {
            $notesRun->addText(array_shift($underline[1]), ['underline' => true]);
        } else {
            $notesRun->addText($part);
        }
        $prewPart = $part;
    }
}

$section->addText("V Brně dne " . date('d.m.Y', strtotime($radek['date'])), ['size' => 11]);
$section->addText("Zástupci školního Parlamentu", ['size' => 11]);
$section->addText("Zapsal: " . $jmeno, ['size' => 11]);
$section->addText("Ověřila: Mgr. Denisa Gottwaldová", ['size' => 11]);

// Přidání zápatí
$zapati = $section->addFooter();
$tableFooter = $zapati->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'width' => 100 * 100]);
$tableFooter->addRow();
$tableFooter->addCell(4000)->addText("$document_number Záznam z jednání dne " . date('d.m.Y', strtotime($radek['date'])), ['size' => 9]);
$tableFooter->addCell(2000)->addText("Strana {PAGE} z {NUMPAGES}", ['size' => 9], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);

// Uložení souboru jako DOCX
$nazevSouboru = 'notes-ze-schuze-' . date('d-m-Y', strtotime($radek['date'])) . '.docx';
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="' . $nazevSouboru . '"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save("php://output");
exit();

?>