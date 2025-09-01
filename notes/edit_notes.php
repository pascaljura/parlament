<?php
include '../assets/php/config.php';
session_start();
ob_start();

if (isset($_SESSION['idusers_parlament'])) {
    $userId = $_SESSION['idusers_parlament'];

    $stmt = $conn->prepare("SELECT * FROM users_alba_rosa_parlament WHERE idusers_parlament = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($userData = $result->fetch_assoc()) {
        // Uložení do proměnných
        $idusers_parlament = $userData['idusers_parlament'];
        $email_parlament = $userData['email'];
        $username_parlament = $userData['username'];
        $parlament_access_admin = $userData['parlament_access_admin'];
        $parlament_access_user = $userData['parlament_access_user'];
        // Nové sloupce (práva a přístupy)
        $add_notes = $userData['add_notes'];
        $delete_notes = $userData['delete_notes'];
        $edit_notes = $userData['edit_notes'];
        $start_attendances = $userData['start_attendances'];
        $end_attendances = $userData['end_attendances'];
        $delete_attendances = $userData['delete_attendances'];
        $qr_attendances = $userData['qr_attendances'];
        $select_idnotes_parlament = $userData['select_idnotes_parlament'];
        $show_attendances = $userData['show_attendances'];
        $admin = $userData['admin'];
    } else {
        // Uživatel nenalezen (může být smazán), odhlásíme ho
        header("Location: ../logout.php");
        exit();
    }
    $latestClassLabel = null;
    if (!empty($idusers_parlament)) {
        if (
            $stc = $conn->prepare("
        SELECT class_year, class_name
        FROM classes_alba_rosa_parlament
        WHERE idusers_parlament = ?
        ORDER BY class_year DESC, idclass_parlament DESC
        LIMIT 1
    ")
        ) {
            $stc->bind_param("i", $idusers_parlament);
            $stc->execute();
            $rc = $stc->get_result();
            if ($c = $rc->fetch_assoc()) {
                $y = (int) $c['class_year'];
                $className = trim((string) $c['class_name']);
                $latestClassLabel = ($y > 0 ? ($y . '/' . ($y + 1)) : '') . ($className !== '' ? ' – ' . $className : '');
            }
            $stc->close();
        }
    }
    $stmt->close();
} else {
    // Pokud není uživatel přihlášený, přesměrujeme na login stránku
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Parlament na Purkyňce</title>
    <link rel="manifest" href="../assets/json/manifest.json">
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- OG Metadata -->
    <meta property="og:title" content="Parlament na Purkyňce" />
    <meta property="og:url" content="https://www.alba-rosa.cz/parlament/" />
    <meta property="og:image" content="https://www.alba-rosa.cz/parlament/logo.png" />
    <meta property="og:description"
        content="Školní parlament na Purkyňce je skupina studentů z každé třídy, kteří zastupují zájmy žáků a podílejí se na chodu školy. Organizuje akce, řeší problémy a komunikuje s vedením školy." />
    <meta name="theme-color" content="#5481aa" data-react-helmet="true" />

    <!-- Meta description pro SEO -->
    <meta name="description"
        content="Školní parlament na Purkyňce je skupina studentů z každé třídy, kteří zastupují zájmy žáků a podílejí se na chodu školy. Organizuje akce, řeší problémy a komunikuje s vedením školy. Zapojení rozvíjí komunikační a organizační dovednosti a umožňuje ovlivnit dění ve škole." />

    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>

    <div id="calendar"
        style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
        <?php
        // Pokud nemá uživatel oprávnění (parlament_access_admin != 1) nebo (edit_notes != 1), přesměrujeme ho nebo zobrazíme chybovou hlášku
        if ($parlament_access_admin != '1' || $edit_notes != '1') {

            echo '<div class="error-message">';
            echo '<i class="fa fa-times" style="margin-right: 5px;"></i> Chybí oprávnění';
            echo '</div>';
        } else {
            function ziskatTextVLomitkach($notes)
            {
                $textInLomitka = "";
                if (preg_match('/\/\/([^\/]+)\/\//', $notes, $matches)) {
                    $textInLomitka = $matches[1];
                }
                return $textInLomitka;
            }
            function nahraditMarkdown($text)
            {
                return $text;
            }
            if (isset($_GET['idnotes_parlament']) && is_numeric($_GET['idnotes_parlament'])) {
                $idnotes_parlament = $_GET['idnotes_parlament'];

                $result = $conn->query("SELECT * FROM notes_alba_rosa_parlament WHERE idnotes_parlament = $idnotes_parlament");
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $date = date('Y-m-d', strtotime($row['date']));
                    $document_number = $row['document_number']; // Načtení čísla dokumentu
                    $notes = $row['notes'];
                    $notes = str_replace("=", "\n", $notes);
                    $textInLomitkach = ziskatTextVLomitkach($notes);
                    $notes = nahraditMarkdown($notes);
                } else {
                    echo "Záznam s idnotes_parlament $idnotes_parlament nebyl nalezen.";
                    exit();
                }
            } else {
                echo "Chybějící nebo neplatné idnotes_parlament v URL.";
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
                $idnotes_parlament = $_POST["idnotes_parlament"];
                $date = $_POST["date"];
                $document_number = $_POST["document_number"]; // Načtení čísla dokumentu
                $notesText = $_POST["notes"];
                $notesText = str_replace(["\r\n", "\r", "\n"], "=", $notesText);
                $notesText = nahraditMarkdown($notesText);

                // Aktualizace záznamu v databázi včetně čísla dokumentu
                $sql = "UPDATE notes_alba_rosa_parlament SET date='$date', document_number='$document_number', notes='$notesText' WHERE idnotes_parlament = $idnotes_parlament";
                if ($conn->query($sql) === TRUE) {
                    header("Location: show_notes.php?idnotes_parlament=$idnotes_parlament");
                    exit();
                } else {
                    echo "Chyba při aktualizaci záznamu: " . $conn->error;
                }
            }

            ?>





            <div class="table-heading">
                <h2> <?php echo '<i class="fa fa-heart blue"></i>・Úprava zápisu'; ?></h2>
            </div>
            <form action="" method="post" id="myForm" style="max-width: 100%; margin-bottom: 5px; ">
                <input type="hidden" name="idnotes_parlament" value="<?php echo $idnotes_parlament; ?>">

                <label for="date" style="font-size: 16px; margin-bottom: 8px;">Datum:</label>
                <input type="date" name="date" id="date" value="<?php echo $date; ?>"
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: Calibri, sans-serif;"
                    required>

                <!-- Nové pole pro Číslo dokumentu -->
                <label for="document_number" style="font-size: 16px; margin-bottom: 8px;">Číslo dokumentu:</label>
                <input type="text" name="document_number" id="document_number" value="<?php echo $document_number; ?>"
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-family: Calibri, sans-serif;"
                    required>

                <label for="notes" style="font-size: 16px; margin-bottom: 8px;">Zápis:</label>
                <textarea name="notes" id="notes" rows="10"
                    style="width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; white-space: nowrap; font-family: Calibri, sans-serif;"
                    required><?php echo $notes; ?></textarea>
            </form>
            <p> main title = //main title// (Tučný modrý text na středu stránky) <br>
                odrížka = -odrážka <br>
                pododrážka = --podorážka<br>
                italics = *italics* (kurzíva)<br>
                bold = **bold** (tučný text)<br>
                bold italics = ***bold italics*** (tučný text + kurzíva)<br>
                strikeout = ~~strikeout~~ (přešktrnuté)<br>
                underline = __underline__ (podtržený text)<br>
                underline italics = __*underline italics*__ (podtržený text + kurzíva)<br>
                underline bold = __**underline bold**__ (podtržený text + tučný text)<br>
                underline bold italics = __***underline bold italics***__ (podtržený text + tučný text + kurzíva)
            </p>
            <div class="button-container" id="buttonContainer">
                <button type="submit" form="myForm"><i class="fa fa-save"></i> Uložit změny</button>
                <a href="show_notes.php?idnotes_parlament=<?php echo $idnotes_parlament; ?>"><button><i
                            class="fa fa-sign-out"></i> Opustit
                        stránku beze změn</button></a>
            </div>
            <?php
        }


        ?>
        <div id="footer-text"></div>
    </div>
</body>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
<script src="../assets/js/script.js">
</script>

</html>
<?php
ob_end_flush();
?>