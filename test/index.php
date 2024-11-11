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

        // Nahrazení a formátování textu
        $zapis = str_replace("=", "\n", $zapis);
        $zapis = str_replace("\n--", "\n  ○", $zapis);
        $zapis = str_replace("\n-", "\n•", $zapis);

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
$sekce = $phpWord->addSection();

// Přidání hlavičky
$hlavicka = $sekce->addHeader();

// Přidání obrázku do hlavičky, zarovnaného na střed
$hlavicka->addImage('../assets/img/purkynka_logo.png', [
    'width' => 320,
    'height' => 103,
    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER // Zarovnání na střed
]);

// Vytvoření tabulky v hlavičce s dvěma řádky a třemi sloupci
$tableHeader = $hlavicka->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'width' => 100 * 100]); // Tabulka na střed přes celou šířku stránky
$tableHeader->addRow();
$tableHeader->addCell(4000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])->addText("Číslo dokumentu: $cislo_dokumentu/$datum", ['size' => 9]);

$tableHeader->addCell(2000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])
    ->addText("Počet stran: 1", ['size' => 9, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

$tableHeader->addCell(2000, ['borderTopSize' => 6, 'borderTopColor' => '000000'])->addText("Počet příloh: 0", ['size' => 9, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
$tableHeader->addRow();
$tableHeader->addCell(4000)->addText("Dokument", ['size' => 9]);
$tableHeader->addCell(2000)->addText("", ['size' => 9, 'alignment' => 'center']);
$tableHeader->addCell(2000)->addText("", ['size' => 9, 'alignment' => 'right']);

// Přidání obsahu dokumentu
$sekce->addText("Záznam z jednání dne " . date('d.m.Y', strtotime($radek['datum'])), ['size' => 22]);
$sekce->addText($zapis, ['size' => 11]);
$sekce->addText("V Brně dne " . date('d.m.Y', strtotime($radek['datum'])), ['size' => 11]);
$sekce->addText("Zástupci školního Parlamentu", ['size' => 11]);
$sekce->addText("Zapsal: $jmeno", ['size' => 11]);
$sekce->addText("Ověřila: Mgr. Denisa Gottwaldová", ['size' => 11]);

// Vytvoření tabulky v zápatí, která bude také přes celou šířku stránky
$zapati = $sekce->addFooter();
$tableFooter = $zapati->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'width' => 100 * 100]); // Tabulka na střed přes celou šířku stránky
$tableFooter->addRow();
$tableFooter->addCell(4000, ['alignment' => 'left'])->addText("$cislo_dokumentu Záznam z jednání dne " . date('d.m.Y', strtotime($radek['datum'])), ['size' => 9]);

// Vložení čísla stránky a celkového počtu stránek
$tableFooter->addCell(2000, ['alignment' => 'right'])->addText("Stránka {PAGE} z {NUMPAGES}", ['size' => 9]);

// Uložení souboru
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