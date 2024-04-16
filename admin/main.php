<?php
include '../config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./index.php");
    exit();
}
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
$sql = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datum = $_POST["datum"];
    $zapis = $_POST["zapis"];
    $zapis = str_replace(array("\n", "\r"), '', $zapis);
    $sql = "INSERT INTO zapis (datum, zapis) VALUES ('$datum', '$zapis')";
    if ($conn->query($sql) === TRUE) {
        header("Location: ./main.php");
        exit();
    } else {
        echo "Chyba: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../logo.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <?php
    $headerText1 = '&#x1F499;・Nový zápis・2023/2024';
    $headerText2 = '&#x1F499;・Zápisy・2023/2024';
    $footerText = '&#x1F499;・Aktuálně・2023/2024';
    ?>
</head>
<div id="loading-overlay">
    <div id="loading-icon"></div>
</div>

<body>
    <div id="calendar">
        <?php
        // Zpráva o úspěchu
        if (isset($_GET['message'])) {
            echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
        }
        ?>

        <div class="table-heading">
            <?php echo $headerText2; ?>
        </div>
        <div class="button-container" id="buttonContainer">
            <?php
            include '../config.php';
            $result = $conn->query("SELECT id, datum FROM zapis ORDER BY datum DESC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $datum = date('d.m.Y', strtotime($row['datum']));
                    echo '<a href="./zapis.php?id=' . $id . '" target="_blank">';
                    echo '<button>';
                    echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . $datum;
                    echo '</button>';
                    echo '</a>';
                }
            } else {
                echo "Žádná data nebyla nalezena.";
            }
            $conn->close();
            ?>
        </div>
        <div class="table-heading">
            <?php echo $headerText1; ?>
        </div>
        <?php
        if ($sql != "" && $conn->query($sql) === TRUE) {
            echo '<div style="color: #008000; margin-bottom: 5px;">Zápis byl úspěšně uložen.</div>';
        } elseif ($sql != "") {
            echo "Chyba: " . $sql . "<br>" . $conn->error;
        }
        ?>
        <div style="display: flex; flex-direction: column;">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="myForm"
                style="width: 80%; max-width: 400px; margin-bottom: 5px;">
                <label for="datum" style="font-size: 16px; margin-bottom: 8px;">Datum:</label>
                <input type="date" name="datum" required
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                <label for="zapis" style="font-size: 16px; margin-bottom: 8px;">Záznam:</label>
                <div style="display: flex; flex-direction: column;">
                    <textarea name="zapis" rows="4" required
                        style="width: 80%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; white-space: nowrap;"></textarea>
                </div>
                <div class="button-container" id="buttonContainer">
                    <button type="submit">
                        Uložit
                    </button>
                </div>
            </form>
            main title = //main title// (Tučný modrý text na středu stránky) <br>
            odrížka = -odrážka <br>
            pododrážka = --podorážka<br>
            header = /header/ (Tučný modrý nadpis uprostřed a zároveň zápatí)<br>
            italics = *italics* (kurzíva)<br>
            bold = **bold** (tučný text)<br>
            bold italics = ***bold italics*** (tučný text + kurzíva)<br>
            strikeout = ~~strikeout~~ (přešktrnuté)<br>
            underline = __underline__ (podtržený text)<br>
            underline italics = __*underline italics*__ (podtržený text + kurzíva)<br>
            underline bold = __**underline bold**__ (podtržený text + tučný text)<br>
            underline bold italics = __***underline bold italics***__ (podtržený text + tučný text + kurzíva)
        </div>
        <?php
        include '../config.php'; // Připojení k databázi
        
        // Získání dat z tabulky
        $query = "SELECT * FROM other WHERE id = 3";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            // Kontrola hodnoty id
            if ($row['aktivni'] == 1) {
                $phpCode = $row['text'];

                // Vyhodnocení PHP kódu
                ob_start();
                eval ('?>' . $phpCode);
                $text = ob_get_clean();

                // Výpis HTML s dynamickým obsahem
                echo $text;
            } else {
            }
        } else {
            echo 'Chyba při získávání dat z databáze: ' . mysqli_error($conn);
        }

        // Uzavření připojení k databázi
        mysqli_close($conn);
        ?>
        <div class="button-container" id="buttonContainer">
            <form method="post">
                <button type="submit" name="logout">Odhlásit
                    se</button>
            </form>
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
    <script src="../script.js">
    </script>
</body>

</html>
