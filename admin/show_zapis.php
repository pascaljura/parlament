<?php
include '../assets/php/config.php';
session_start();
// Kontrola přihlášení
if (!isset($_SESSION['idusers'])) {
    header("Location: ./index.php");
    exit();
} else {

    // Získání id uživatele ze session
    $idusers = $_SESSION['idusers'];

    // Kontrola přístupu na základě sloupce parlament_access
    $stmtAccess = $conn->prepare("SELECT parlament_access FROM users_alba_rosa WHERE idusers = ?");
    $stmtAccess->bind_param("i", $idusers);
    $stmtAccess->execute();
    $stmtAccess->bind_result($parlament_access);
    $stmtAccess->fetch();
    $stmtAccess->close();
}
?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
</head>
<div id="loading-overlay">
    <div class="loader"></div>
</div>

<body>
    <?php
    // Pokud není přístup povolen (parlament_access != 1)
    if ($parlament_access != '1') { ?>
        <div id="calendar">
            <div style="color: #FF0000; margin-bottom: 5px;"><b>Chybí oprávnění</b></div>
            <?php
    } else {
        if (isset($_GET['idzapis']) && is_numeric($_GET['idzapis'])) {
            $idzapis = $_GET['idzapis'];
            // Získání záznamu ze schůze
            $result = "SELECT z.*, u.username 
     FROM zapis_alba_rosa_parlament z
     LEFT JOIN users_alba_rosa u ON z.idusers = u.idusers
     WHERE z.idzapis = ?";

            // Příprava připraveného dotazu
            $stmt = $conn->prepare($result);

            // Bind parametr (parametr typu i = integer, s = string)
            $stmt->bind_param("i", $idzapis);

            // Vykonání dotazu
            $stmt->execute();
            // Výsledek dotazu
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $datum = date('d.m.Y', strtotime($row['datum']));
                $directoryName = date('d_m_Y', strtotime($row['datum']));
                $idusers = $row['idusers'];
                $zapis = $row['zapis'];
                $username = $row['username'];
                $cislo_dokumentu = $row['cislo_dokumentu'];

                $zapis = str_replace("=", "<br>", $zapis);
                $zapis = preg_replace('/(?<=^|<br>)(?![\w])--/', '&#160;&#160;&#9702;', $zapis);
                $zapis = preg_replace('/(?<=^|<br>)(?![\w])-(?!-)/', '&#8226;', $zapis);





                function getSklonovanyText($text)
                {
                    $posledniZnak = mb_substr($text, -1);
                    switch ($posledniZnak) {
                        case 'a':
                        case 'í':
                            return $text;
                        default:
                            return $text;
                    }
                }
                function ziskatTextVLomitkach($zapis)
                {
                    $textInLomitka = "";
                    if (preg_match('/\/\/([^\/]+)\/\//', $zapis, $matches)) {
                        $textInLomitka = $matches[1];
                    }
                    return $textInLomitka;
                }
                $textInLomitkach = ziskatTextVLomitkach($zapis);
                $zapis = preg_replace('/\/\/([^\/]+)\/\//', '<div style="color: #3e6181; font-weight: bold; font-size: 20px">$1</div>', $zapis); // custom style
                $zapis = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<b><i>$1</i></b>', $zapis); // bold italics
                $zapis = preg_replace('/\*\*([^*]+)\*\*/', '<b>$1</b>', $zapis); // bold
                $zapis = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $zapis); // italics
                $zapis = preg_replace('/~~([^~]+)~~/', '<strike>$1</strike>', $zapis); // strikeout
                $zapis = preg_replace('/__([^_]+)__/', '<u>$1</u>', $zapis); // underline
    
                $resultUser = $conn->query("SELECT username FROM users_alba_rosa WHERE idusers = $idusers");
                if ($resultUser->num_rows > 0) {
                    $rowUser = $resultUser->fetch_assoc();
                    $userName = $rowUser['username'];
                } else {
                    $userName = 'Neznámý uživatel';
                }
            } else {
                echo "Záznam s idzapis $idzapis nebyl nalezen.";
                exit();
            }
        } else {
            echo "Chybějící nebo neplatné idzapis v URL.";
            exit();
        }
        ?>

            <div id="calendar"
                style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
                <div class="table-heading" style="text-align: center;">
                    <?php echo '<img src="../assets/img/purkynka_logo.png" width="200px" height="80">'; ?>
                </div>
                <table>
                    <tr style="border-top: 1px solid black;">
                        <td>Číslo dokumentu: <?php echo "$cislo_dokumentu / " . date('dmY', strtotime($row['datum'])); ?>
                        </td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: right;">Počet příloh: 0</td>
                    </tr>
                    <tr>
                        <td>Dokument</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <h3 style="font-size: 25px;padding: unset;margin: unset;">
                    Záznam z jednání dne <?php echo "$datum"; ?>
                </h3>
                <div class="button-container" id="buttonContainer" style=" font-family: Calibri, sans-serif;">
                    <pre style="white-space: break-spaces;  font-family: Calibri, sans-serif;"><?php echo $zapis; ?></pre>
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
                        echo '<button onclick="window.open(\'../zapis_pdf.php?idzapis=' . $idzapis . '\', \'_blank\')">';
                        echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> Stáhnout PDF';
                        echo '</button>';
                        echo '<button onclick="window.open(\'../zapis_docx.php?idzapis=' . $idzapis . '\', \'_blank\')">';
                        echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> Stáhnout DOCX';
                        echo '</button>';
                        echo '<a href="./edit_zapis.php?idzapis=' . $idzapis . '">';
                        echo '<button>';
                        echo '<i class="fa fa-pencil" aria-hidden="true"></i> ' . ' Upravit zápis';
                        echo '</button>';
                        echo '</a>';
                        echo '<button onclick="deleteZapis(' . $idzapis . ')">';
                        echo '<i class="fa fa-trash" aria-hidden="true"></i> ' . ' Odstranit zápis';
                        echo '</button>';
                        ?>
                    </div>
                </div>
                <hr color="black" style="height: 2px; border: none;" />
                <?php
    }
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
        <script src="../assets/js/script.js">    </script>
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
        <script>
            function deleteZapis(idzapis) {
                if (confirm("Opravdu chcete smazat tento zápis?")) {
                    // Vytvoření instance XMLHttpRequest objektu
                    var xhttp = new XMLHttpRequest();
                    // Nastavení metody a URL pro požadavek
                    xhttp.open("POST", "delete_zapis.php", true);
                    // Nastavení hlavičky požadavku
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    // Nastavení callback funkce pro zpracování odpovědi
                    xhttp.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            // Zpracování odpovědi
                            if (this.responseText === "success") {
                                // Pokud je odpověď "success", přesměrujeme uživatele na main.php
                                window.location.replace("./?message=Zápis+byl+úspěšně+smazán.");
                            } else {
                                // Pokud je odpověď něco jiného než "success", zobrazíme chybovou zprávu
                                alert("Nastala chyba při mazání zápisu.");
                            }
                        }
                    };
                    // Odeslání požadavku s id záznamu
                    xhttp.send("idzapis=" + idzapis);
                }
            }



        </script>

</body>

</html>