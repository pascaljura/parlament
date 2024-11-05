<?php
include './assets/php/config.php';

if (isset($_GET['id_zapis']) && is_numeric($_GET['id_zapis'])) {
    $id_zapis = $_GET['id_zapis'];

    // Získání záznamu ze schůze
    $result = $conn->query("SELECT * FROM zapis WHERE id_zapis = $id_zapis");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $datum = date('d.m.Y', strtotime($row['datum']));
        $directoryName = date('d_m_Y', strtotime($row['datum']));
        $zapis = $row['zapis'];
        $id_users = $row['id_users']; // předpokládám, že id_users je sloupec v tabulce zapis

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
        $zapis = preg_replace('/\/\/([^\/]+)\/\//', '<div style="color: #3e6181; font-weight: bold; text-align: center; font-size: 34px">$1</div>', $zapis);
        $zapis = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<b><i>$1</i></b>', $zapis);
        $zapis = preg_replace('/\*\*([^*]+)\*\*/', '<b>$1</b>', $zapis);
        $zapis = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $zapis);
        $zapis = preg_replace('/~~([^~]+)~~/', '<strike>$1</strike>', $zapis);
        $zapis = preg_replace('/__([^_]+)__/', '<u>$1</u>', $zapis);

        // Získání jména uživatele na základě id_users
        $resultUser = $conn->query("SELECT name FROM users WHERE id_users = $id_users");
        if ($resultUser->num_rows > 0) {
            $rowUser = $resultUser->fetch_assoc();
            $userName = $rowUser['name'];
        } else {
            $userName = 'Neznámý uživatel';
        }

    } else {
        echo "Záznam s id_zapis $id_zapis nebyl nalezen.";
        exit();
    }
} else {
    echo "Chybějící nebo neplatné id_zapis v URL.";
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
    <div id="calendar"
        style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
        <div class="table-heading" style="text-align: center;">
            <?php echo '<img src="favicon.ico" width="180px" height="200">'; ?>
        </div>
        <div style="display: flex; flex-direction: column; font-size: 24px;">
            <div style="color: #000; font-family: sans-serif;">
                <?php echo "<u>" . $datum . " | Zapsal: <b>" . $userName . "</b></u>" ?>
            </div>
        </div>
        <div class="button-container" id="buttonContainer" style="font-size: 24px; font-family: sans-serif;">
            <pre style="overflow: auto; font-family: sans-serif;"><?php echo $zapis; ?></pre>
        </div>

        
        <div style="display: flex; justify-content: space-between;">
            <div class="table-heading button-container">
                <?php
                echo '<button onclick="downloadPDF(\'' . $directoryName . '\')">';
                echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . ' Stáhnout PDF';
                echo '</button>';
                echo '<button onclick="downloadWORD(\'' . $directoryName . '\')">';
                echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . ' Stáhnout DOCX';
                echo '</button>';
                ?>
            </div>
        </div>
        <br>
        <?php

        // Získání dat z tabulky
        $query = "SELECT text FROM other WHERE id_other = 1";
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
    <script>
        function downloadPDF(directoryName) {
            var link = document.createElement('a');
            link.href = './' + directoryName + '/zapis_ze_schuze_' + directoryName + '.pdf';
            link.download = 'zapis_se_schuze_' + directoryName + '.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        function downloadWORD(directoryName) {
            var link = document.createElement('a');
            link.href = './' + directoryName + '/zapis_ze_schuze_' + directoryName + '.docx';
            link.download = 'zapis_se_schuze_' + directoryName + '.docx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
</body>

</html>