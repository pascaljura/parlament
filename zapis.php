<?php
include './assets/php/config.php';
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM zapis WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $datum = date('d.m.Y', strtotime($row['datum']));
        $directoryName = date('d_m_Y', strtotime($row['datum']));
        $zapis = $row['zapis'];
        $zapis = str_replace("=", "<br>", $zapis);
        $zapis = str_replace("<br>--", "<br>&#160;&#160;&#9702;", $zapis);
        $zapis = str_replace("<br>-", "<br>&#8226;", $zapis);
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
        <div class="button-container" id="buttonContainer" style="font-size: 24px; font-family: sans-serif;">
            <pre style="overflow: auto;  font-family: sans-serif;"><?php echo $zapis; ?></pre>
        </div>
        <hr color="#3e6181" style="height: 20px; border: none;" />
        <div style="display: flex; flex-direction: column; font-size: 24px;">
            <div style="color: #000; font-family: sans-serif;">
                <?php
                if (!empty($textInLomitkach)) {
                    $textSklon = getSklonovanyText($textInLomitkach);
                    echo '<div style="color: #000; font-family: sans-serif;">' . $textSklon . '</div>';
                } else {
                    echo '<div style="color: #000; font-family: sans-serif; ">' . "Týdenní schůze školního Parlamentu" . '</div>';
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
                ?>
            </div>
        </div>
        <hr color="#3e6181" style="height: 2px; border: none;" />
        <?php
        
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

        ?>
    </div>
    <script src="./assets/js/script.js">
    </script>
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
</body>

</html>