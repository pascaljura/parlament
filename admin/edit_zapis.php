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

    // Kontrola přístupu na základě sloupce parlament_access_admin_admin
    $stmtAccess = $conn->prepare("SELECT parlament_access_admin_admin FROM users_alba_rosa WHERE idusers = ?");
    $stmtAccess->bind_param("i", $idusers);
    $stmtAccess->execute();
    $stmtAccess->bind_result($parlament_access_admin_admin);
    $stmtAccess->fetch();
    $stmtAccess->close();
}
// Pokud není přístup povolen (parlament_access_admin_admin != 1)
if ($parlament_access_admin_admin != '1') { ?>
    <div id="calendar">
        <div style="color: #FF0000; margin-bottom: 5px;"><b>Chybí oprávnění<b></div>
    </div>
    <?php
} else {
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
    if (isset($_GET['idnotes']) && is_numeric($_GET['idnotes'])) {
        $idnotes = $_GET['idnotes'];

        $result = $conn->query("SELECT * FROM notes_alba_rosa_parlament WHERE idnotes = $idnotes");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $datum = date('Y-m-d', strtotime($row['datum']));
            $cislo_dokumentu = $row['cislo_dokumentu']; // Načtení čísla dokumentu
            $zapis = $row['zapis'];
            $zapis = str_replace("=", "\n", $zapis);
            $textInLomitkach = ziskatTextVLomitkach($zapis);
            $zapis = nahraditMarkdown($zapis);
        } else {
            echo "Záznam s idnotes $idnotes nebyl nalezen.";
            exit();
        }
    } else {
        echo "Chybějící nebo neplatné idnotes v URL.";
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
        $idnotes = $_POST["idnotes"];
        $datum = $_POST["datum"];
        $cislo_dokumentu = $_POST["cislo_dokumentu"]; // Načtení čísla dokumentu
        $zapisText = $_POST["zapis"];
        $zapisText = str_replace(["\r\n", "\r", "\n"], "=", $zapisText);
        $zapisText = nahraditMarkdown($zapisText);

        // Aktualizace záznamu v databázi včetně čísla dokumentu
        $sql = "UPDATE notes_alba_rosa_parlament SET datum='$datum', cislo_dokumentu='$cislo_dokumentu', zapis='$zapisText' WHERE idnotes = $idnotes";
        if ($conn->query($sql) === TRUE) {
            header("Location: show_zapis.php?idnotes=$idnotes");
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
    <div id="loading-overlay">
                <div class="loader"></div>
            </div>

        <div id="calendar"
            style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
            <div class="table-heading">
                <?php echo '&#x1F499;・ Úprava zápisu・2023/2024'; ?>
            </div>
            <form action="" method="post" id="myForm" style="max-width: 100%; margin-bottom: 5px; ">
                <input type="hidden" name="idnotes" value="<?php echo $idnotes; ?>">

                <label for="datum" style="font-size: 16px; margin-bottom: 8px;">Datum:</label>
                <input type="date" name="datum" id="datum" value="<?php echo $datum; ?>"
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: Calibri, sans-serif;"
                    required>

                <!-- Nové pole pro Číslo dokumentu -->
                <label for="cislo_dokumentu" style="font-size: 16px; margin-bottom: 8px;">Číslo dokumentu:</label>
                <input type="text" name="cislo_dokumentu" id="cislo_dokumentu" value="<?php echo $cislo_dokumentu; ?>"
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: Calibri, sans-serif;"
                    required>

                <label for="zapis" style="font-size: 16px; margin-bottom: 8px;">Zápis:</label>
                <textarea name="zapis" id="zapis" rows="10"
                    style="width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; white-space: nowrap; font-family: Calibri, sans-serif;"
                    required><?php echo $zapis; ?></textarea>
            </form>

            <div class="button-container" id="buttonContainer">
                <button type="submit" form="myForm"><i class="fa fa-save"></i> Uložit změny</button>
                <a href="show_zapis.php?idnotes=<?php echo $idnotes; ?>"><button><i class="fa fa-sign-out"></i> Opustit
                        stránku beze změn</button></a>
            </div>
            <hr color="black" style="height: 2px; border: none;" />
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
}
?>
    </div>
</body>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
<script src="../assets/js/script.js">
</script>

</html>