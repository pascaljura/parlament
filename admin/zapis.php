<?php
include '../config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./index.php");
    exit();
}
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM zapis WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $datum = date('d.m.Y', strtotime($row['datum']));
        $directoryName = date('d_m_Y', strtotime($row['datum']));
        $zapis = $row['zapis'];
        $zapis = str_replace("=", "<br>", $zapis);
        $zapis = str_replace("--", "  o", $zapis);
        $zapis = str_replace("-", "•", $zapis);
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
        $zapis = preg_replace('/\/\/([^\/]+)\/\//', '<div style="color: #3e6181; font-weight: bold; text-align: center; font-size: 34px">$1</div>', $zapis); // custom style
        $zapis = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<b><i>$1</i></b>', $zapis); // bold italics
        $zapis = preg_replace('/\*\*([^*]+)\*\*/', '<b>$1</b>', $zapis); // bold
        $zapis = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $zapis); // italics
        $zapis = preg_replace('/~~([^~]+)~~/', '<strike>$1</strike>', $zapis); // strikeout
        $zapis = preg_replace('/__([^_]+)__/', '<u>$1</u>', $zapis); // underline
    } else {
        echo "Záznam s ID $id nebyl nalezen.";
        exit();
    }
} else {
    echo "Chybějící nebo neplatné ID v URL.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<div id="loading-overlay">
    <div id="loading-icon"></div>
</div>

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../logo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../style.css">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="../logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
</head>

<body>
    <div id="calendar"
        style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
        <div class="table-heading" style="text-align: center;">
            <?php echo '<img src="../logo.png" width="140px" height="200">'; ?>
        </div>
        <div class="button-container" id="buttonContainer" style="font-size: 24px; font-family: sans-serif;">
            <pre style="overflow: auto; font-family: sans-serif;"><?php echo $zapis; ?></pre>
        </div>
        <div style="display: flex; flex-direction: column;">
            <div style="color: #000; font-family: sans-serif;">
                <hr color="#3e6181" style="height: 20px; border: none;" />
                <?php
                if (!empty($textInLomitkach)) {
                    $textSklon = getSklonovanyText($textInLomitkach);
                    echo '<div style="color: #000; font-family: sans-serif; font-size: 24px;">' . $textSklon . '</div>';
                } else {
                    echo '<div style="color: #000; font-family: sans-serif; font-size: 24px;">' . "Týdenní schůze školního Parlamentu" . '</div>';
                }
                ?>
            </div>
            <div style="color: #000; font-family: sans-serif; font-size: 24px;">
                <?php echo $datum; ?>
            </div>
        </div>

        <hr color="#3e6181" style="height: 2px; border: none;" />
        <div style="display: flex; justify-content: space-between;">
            <div class="table-heading button-container">
                <?php
                echo '<button onclick="downloadPDF(\'' . $directoryName . '\')">';
                echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . ' Stáhnout PDF';
                echo '</button>';
                echo '<button onclick="downloadWORD(\'' . $directoryName . '\')">';
                echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . ' Stáhnout DOCX';
                echo '</button>';
                echo '<a href="./edit_zapis.php?id=' . $id . '">';
                echo '<button>';
                echo '<i class="fa fa-pencil" aria-hidden="true"></i> ' . ' Upravit zápis';
                echo '</button>';
                echo '</a>';
                echo '<button onclick="deleteZapis(' . $id . ')">';
                echo '<i class="fa fa-trash" aria-hidden="true"></i> ' . ' Odstranit zápis';
                echo '</button>';
                ?>
            </div>
        </div>
        <hr color="#3e6181" style="height: 2px; border: none;" />
        <?php
        include '../config.php'; // Připojení k databázi
        
        // Získání dat z tabulky
        $query = "SELECT text FROM other WHERE id = 1";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $text = $row['text'];

            // Výpis HTML s dynamickým obsahem
            echo "$text";
        } else {
            echo 'Chyba při získávání dat z databáze: ' . mysqli_error($conn);
        }

        // Uzavření připojení k databázi
        mysqli_close($conn);
        ?>
    </div>
    <script src="../script.js">    </script>
    <script>
        function downloadPDF(directoryName) {
            var link = document.createElement('a');
            link.href = '../' + directoryName + '/zapis_ze_schuze_' + directoryName + '.pdf';
            link.download = 'zapis_se_schuze_' + directoryName + '.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        function downloadWORD(directoryName) {
            var link = document.createElement('a');
            link.href = '../' + directoryName + '/zapis_ze_schuze_' + directoryName + '.docx';
            link.download = 'zapis_se_schuze_' + directoryName + '.docx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        function deleteZapis(id) {
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
                            window.location.replace("main.php?message=Zápis+byl+úspěšně+smazán.");
                        } else {
                            // Pokud je odpověď něco jiného než "success", zobrazíme chybovou zprávu
                            alert("Nastala chyba při mazání zápisu.");
                        }
                    }
                };
                // Odeslání požadavku s id záznamu
                xhttp.send("id=" + id);
            }
        }



    </script>

</body>

</html>