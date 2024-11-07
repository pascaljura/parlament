<?php
include '../assets/php/config.php';
session_start();
if (!isset($_SESSION['id_users'])) {
    header("Location: ./index.php");
    exit();
}
function ziskatTextVLomitkach($zapis)
{
    $textInLomitka = "";
    if (preg_match('/\/\/([^\/]+)\/\//', $zapis, $matches)) {
        $textInLomitka = $matches[1];
    }
    return $textInLomitka;
}
function nahraditMarkdown($text)
{
    return $text;
}
if (isset($_GET['id_zapis']) && is_numeric($_GET['id_zapis'])) {
    $id_zapis = $_GET['id_zapis'];

    $result = $conn->query("SELECT * FROM zapis WHERE id_zapis = $id_zapis");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $datum = date('Y-m-d', strtotime($row['datum']));
        $zapis = $row['zapis'];
        $zapis = str_replace("=", "\n", $zapis);
        $textInLomitkach = ziskatTextVLomitkach($zapis);
        $zapis = nahraditMarkdown($zapis);
    } else {
        echo "Záznam s id_zapis $id_zapis nebyl nalezen.";
        exit();
    }
} else {
    echo "Chybějící nebo neplatné id_zapis v URL.";
    exit();
}
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_zapis = $_POST["id_zapis"];
    $datum = $_POST["datum"];
    $zapisText = $_POST["zapis"];
    $zapisText = str_replace(["\r\n", "\r", "\n"], "=", $zapisText);
    $zapisText = nahraditMarkdown($zapisText);
    $sql = "UPDATE zapis SET datum='$datum', zapis='$zapisText' WHERE id_zapis = $id_zapis";
    if ($conn->query($sql) === TRUE) {
        header("Location: show_zapis.php?id_zapis=$id_zapis");
        exit();
    } else {
        echo "Chyba při aktualizaci záznamu: " . $conn->error;
    }
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
    <meta content="https://www.alba-rosa.cz/parlament/favicon.ico" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
</head>

<body>
    <div id="calendar"
        style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
        <div class="table-heading">
            <?php echo '&#x1F499;・ Úprava zápisu・2023/2024'; ?>
        </div>
        <form action="" method="post" id="myForm" style="width: 80%; max-width: 400px; margin-bottom: 5px;">
            <input type="hidden" name="id_zapis" value="<?php echo $id_zapis; ?>">
            <label for="datum" style="font-size: 16px; margin-bottom: 8px;">Datum:</label>
            <input type="date" name="datum" id="datum" value="<?php echo $datum; ?>"
                style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;"
                required>
            <label for="zapis" style="font-size: 16px; margin-bottom: 8px;">Zápis:</label>
            <textarea name="zapis" id="zapis" rows="10"
                style="width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; white-space: nowrap;"
                required><?php echo $zapis; ?></textarea>
        </form>
        <div class="button-container" id="buttonContainer">
            <button type="submit" form="myForm">Uložit změny</button>
            <a href="show_zapis.php?id_zapis=<?php echo $id_zapis; ?>"><button>Opustit stránku beze změn</button></a>
        </div>
        <hr color="#3e6181" style="height: 2px; border: none;" />
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
</body>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
<script src="../assets/js/script.js">
</script>

</html>