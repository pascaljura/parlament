<?php
include './assets/php/config.php';

if (isset($_GET['idnotes']) && is_numeric($_GET['idnotes'])) {
    $idnotes = $_GET['idnotes'];

    // Získání záznamu ze schůze
    $result = "SELECT z.*, u.username 
FROM notes_alba_rosa_parlament z
LEFT JOIN users_alba_rosa u ON z.idusers = u.idusers
WHERE z.idnotes = ?";

    // Příprava připraveného dotazu
    $stmt = $conn->prepare($result);

    // Bind parametr (parametr typu i = integer, s = string)
    $stmt->bind_param("i", $idnotes);

    // Vykonání dotazu
    $stmt->execute();

    // Výsledek dotazu
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $datum = date('d.m.Y', strtotime($row['datum']));
        $directoryName = date('d_m_Y', strtotime($row['datum']));
        $zapis = $row['zapis'];
        $username = $row['username'];
        $idusers = $row['idusers'];
        $cislo_dokumentu = $row['cislo_dokumentu']; // předpokládám, že idusers je sloupec v tabulce zapis

        $zapis = str_replace("=", "<br>", $zapis);
        $zapis = preg_replace('/(?<=^|<br>)(?![\w])--/', '&#160;&#160;&#9702;', $zapis);
        $zapis = preg_replace('/(?<=^|<br>)(?![\w])-(?!-)/', '&#8226;', $zapis);

        function ziskatTextVLomitkach($zapis)
        {
            $textInLomitka = "";
            if (preg_match('/\/\/([^\/]+)\/\//', $zapis, $matches)) {
                $textInLomitka = $matches[1];
            }
            return $textInLomitka;
        }

        $textInLomitkach = ziskatTextVLomitkach($zapis);
        $zapis = preg_replace('/\/\/([^\/]+)\/\//', '<div style="color: #3e6181; font-weight: bold; font-size: 20px;">$1</div>', $zapis);
        $zapis = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<b><i>$1</i></b>', $zapis);
        $zapis = preg_replace('/\*\*([^*]+)\*\*/', '<b>$1</b>', $zapis);
        $zapis = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $zapis);
        $zapis = preg_replace('/~~([^~]+)~~/', '<strike>$1</strike>', $zapis);
        $zapis = preg_replace('/__([^_]+)__/', '<u>$1</u>', $zapis);

        // Získání jména uživatele na základě idusers
        $resultUser = $conn->query("SELECT username FROM users_alba_rosa WHERE idusers = $idusers");
        if ($resultUser->num_rows > 0) {
            $rowUser = $resultUser->fetch_assoc();
            $userName = $rowUser['username'];
        } else {
            $userName = 'Neznámý uživatel';
        }

    } else {
        echo "Záznam s idnotes $idnotes nebyl nalezen.";
        exit();
    }
} else {
    echo "Chybějící nebo neplatné idnotes v URL.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="favicon.ico" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
</head>

<body>
    <div id="loading-overlay">
        <div class="loader"></div>
    </div>
    <div id="calendar"
        style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
        <div class="table-heading" style="text-align: center;">
            <?php echo '<img src="./assets/img/purkynka_logo.png" width="200px" height="80">'; ?>
        </div>
        <table>
            <tr style="border-top: 1px solid black;">
                <td>Číslo dokumentu: <?php echo "$cislo_dokumentu / $datum"; ?></td>
                <td style="text-align: center;"></td>
                <td style="text-align: right;">Počet příloh: 0</td>
            </tr>
            <tr>
                <td>Dokument</td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <h3 style="font-size: 25px;">
            Záznam z jednání dne <?php echo "$datum"; ?>
        </h3>
        <div class="button-container" id="buttonContainer" style=" font-family: Calibri, sans-serif;">
            <pre style="white-space: break-spaces; font-family: Calibri, sans-serif;"><?php echo $zapis; ?></pre>
        </div>

        <h> V Brně dne <?php echo "$datum"; ?> <br>
            Zástupci školního Parlamentu<br>
            Zapsal: <?php echo "$username"; ?><br>
            Ověřila: Mgr. Denisa Gottwaldová <br><br></h>
        <table style="border: none;">
            <tr>
                <td><?php echo "$cislo_dokumentu Záznam z jednání dne $datum"; ?></td>
                <td style="text-align: right;"></td>
            </tr>
        </table>
        <br>
        <div style="display: flex; justify-content: space-between;">
            <div class="table-heading button-container">
                <?php
                echo '<button onclick="window.open(\'./zapis_pdf.php?idnotes=' . $idnotes . '\', \'_blank\')">';
                echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> Stáhnout PDF';
                echo '</button>';
                echo '<button onclick="window.open(\'./zapis_docx.php?idnotes=' . $idnotes . '\', \'_blank\')">';
                echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> Stáhnout DOCX';
                echo '</button>';
                ?>
            </div>
        </div>
        <br>
        <?php

        // Získání dat z tabulky
        $query = "SELECT text FROM other_alba_rosa_parlament WHERE idother = 1";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $text = $row['text'];

            // Výpis HTML s dynamickým obsahem
            echo "$text";
        } else {
            echo 'Chyba při získávání dat z databáze: ' . mysqli_error($conn);
        }

        ?>
    </div>
    <script src="./assets/js/script.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
</body>

</html>