<?php
// Podmíněné načtení konfiguračního souboru
$cestaKeKonfiguraci = '../assets/php/config.php';
if (file_exists($cestaKeKonfiguraci)) {
    include $cestaKeKonfiguraci;
} else {
    die("Konfigurační soubor nebyl nalezen.");
}

require_once './vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// Ověření, zda je id_zapis nastaveno a je platné
if (isset($_GET['id_zapis']) && filter_var($_GET['id_zapis'], FILTER_VALIDATE_INT)) {
    $id_zapis = $_GET['id_zapis'];

    // Příprava dotazu pro načtení detailů dokumentu a jména uživatele z databáze
    $stmt = $conn->prepare("
        SELECT z.*, u.username 
        FROM zapis_alba_rosa_parlament z
        LEFT JOIN users_alba_rosa u ON z.idusers = u.idusers
        WHERE z.idzapis = ?
    ");
    $stmt->bind_param("i", $id_zapis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $radek = $result->fetch_assoc();
        $cislo_dokumentu = htmlspecialchars($radek['cislo_dokumentu']);
        $datum = date('dmY', strtotime($radek['datum']));
        $zapis = htmlspecialchars($radek['zapis']);
        $jmeno = htmlspecialchars($radek['username']);

        // Nahrazení odrážek
        $zapis = str_replace("=", "<br>", $zapis);
        $zapis = preg_replace('/(?<=^|<br>)(?![\w])--/', "<br>  ○ ", $zapis);
        $zapis = preg_replace('/(?<=^|<br>)(?![\w])-(?!-)/', "<br>• ", $zapis);

        // Extrakce stylovaného textu
        preg_match_all('/\/\/([^\/]+)\/\//', $zapis, $title);
        preg_match_all('/\*\*\*([^*]+)\*\*\*/', $zapis, $bolitalic);
        preg_match_all('/\*\*([^*]+)\*\*/', $zapis, $bold);
        preg_match_all('/\*([^*]+)\*/', $zapis, $italic);
        preg_match_all('/~~([^~]+)~~/', $zapis, $strike);
        preg_match_all('/__([^_]+)__/', $zapis, $underline);

        // Odebrání formátovacích značek, aby neovlivňovaly finální text
        $zapis = preg_replace('/\/\/([^\/]+)\/\//', '<t>', $zapis);
        $zapis = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<bi>', $zapis);
        $zapis = preg_replace('/\*\*([^*]+)\*\*/', '<b>', $zapis);
        $zapis = preg_replace('/\*([^*]+)\*/', '<i>', $zapis);
        $zapis = preg_replace('/~~([^~]+)~~/', '<s>', $zapis);
        $zapis = preg_replace('/__([^_]+)__/', '<u>', $zapis);

    } else {
        echo "Dokument nebyl nalezen.";
        exit();
    }
    $stmt->close();
} else {
    echo "Neplatný nebo chybějící parametr id_zapis.";
    exit();
}

// Vytvoření nové instance PhpWord
$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Calibri');

// Definování sekce
$section = $phpWord->addSection([
    'marginTop' => 1000,
    'marginBottom' => 1000,
    'marginLeft' => 1000,
    'marginRight' => 1000,
    'pageNumberingStart' => 1
]);

// Přidání hlavičky
$hlavicka = $section->addHeader();
$hlavicka->addImage('../assets/img/purkynka_logo.png', [
    'width' => 320,
    'height' => 103,
    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
]);

// Vytvoření tabulky v hlavičce
$tableHeader = $hlavicka->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'width' => 100 * 100]);
$tableHeader->addRow();
$tableHeader->addCell(4000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])->addText(
    "Číslo dokumentu: $cislo_dokumentu/$datum",
    ['size' => 9]
);
$tableHeader->addCell(2000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])->addText(
    "Počet stran: {PAGE} z {NUMPAGES}",
    ['size' => 9],
    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
);
$tableHeader->addCell(2000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])->addText(
    "Počet příloh: 0",
    ['size' => 9],
    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]
);

// Přidání obsahu s dynamickým formátováním
$section->addText("Záznam z jednání dne " . date('d.m.Y', strtotime($radek['datum'])), ['size' => 22, 'bold' => true]);

// Zpracování formátování
$zapisRun = $section->addTextRun(['size' => 11]);
$textParts = preg_split('/(<br>|<t>|<bi>|<b>|<i>|<s>|<u>)/', $zapis, -1, PREG_SPLIT_DELIM_CAPTURE);
$prewPart = null;
foreach ($textParts as $part) {

    if (strlen($part) > 1 && $part != $prewPart) {
        //echo "<script>console.log(`$part`)</script>";
        if ($part === '<br>') {
            $zapisRun->addTextBreak();
        } elseif ($part === '<t>') {
            $zapisRun->addText(array_shift($title[1]), ['size' => 20, 'bold' => true]);
        } else if ($part === '<bi>') {
            $zapisRun->addText(array_shift($bolitalic[1]), ['bold' => true, 'italic' => true]);
        } elseif ($part === '<b>') {
            $zapisRun->addText(array_shift($bold[1]), ['bold' => true]);
        } elseif ($part === '<i>') {
            $zapisRun->addText(array_shift($italic[1]), ['italic' => true]);
        } elseif ($part === '<s>') {
            $zapisRun->addText(array_shift($strike[1]), ['strikethrough' => true]);
        } elseif ($part === '<u>') {
            $zapisRun->addText(array_shift($underline[1]), ['underline' => true]);
        } else {
            $zapisRun->addText($part);
        }
        $prewPart = $part;
    }
}

$section->addText("V Brně dne " . date('d.m.Y', strtotime($radek['datum'])), ['size' => 11]);
$section->addText("Zástupci školního Parlamentu", ['size' => 11]);
$section->addText("Zapsal: $jmeno", ['size' => 11]);
$section->addText("Ověřila: Mgr. Denisa Gottwaldová", ['size' => 11]);

// Přidání zápatí
$zapati = $section->addFooter();
$tableFooter = $zapati->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'width' => 100 * 100]);
$tableFooter->addRow();
$tableFooter->addCell(4000)->addText("$cislo_dokumentu Záznam z jednání dne " . date('d.m.Y', strtotime($radek['datum'])), ['size' => 9]);
$tableFooter->addCell(2000)->addText("Stránka {PAGE} z {NUMPAGES}", ['size' => 9], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);

// Uložení souboru jako DOCX
$nazevSouboru = 'zapis-ze-schuze-' . date('d-m-Y', strtotime($radek['datum'])) . '.docx';
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