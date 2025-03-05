<?php include '../assets/php/config.php';
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


    } else {
        // Uživatel nenalezen (může být smazán), odhlásíme ho
        header("Location: ./logout.php");
        exit();
    }

    $stmt->close();
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
    <link rel="manifest" href="./assets/json/manifest.json">
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">

    <?php
    $headerText1 = '<i class="fa fa-heart blue"></i>・Nový zápis';
    ?>

</head>
<?php

$sql = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Získání vstupů od uživatele
    $date = $_POST["date"];
    $notes = $_POST["notes"];
    $notes = str_replace(array("\n", "\r"), '=', $notes);

    // Načtení posledního čísla dokumentu podle data
    $sql_last_doc = "SELECT document_number FROM notes_alba_rosa_parlament ORDER BY date DESC LIMIT 1";
    $result = $conn->query($sql_last_doc);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Extrahuje poslední část čísla dokumentu (poslední dvě číslice)
        $last_number = (int) substr($row['document_number'], -2);
        $new_number = str_pad($last_number + 1, 2, "0", STR_PAD_LEFT);  // Zvýší o 1 a doplní nuly
        $document_number = "18.02." . $new_number;
    } else {
        $document_number = "18.02.01"; // První záznam, pokud není žádný předchozí
    }

    // Připravení SQL dotazu s parametry
    $sql = "INSERT INTO notes_alba_rosa_parlament (idusers_parlament, date, notes, document_number) VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isss", $_SESSION['idusers_parlament'], $date, $notes, $document_number);

        if ($stmt->execute()) {
            header("Location: ./?message=Zápis byl uloženo&message_type=success-message");
            exit();
        } else {
            header("Location: ./?message=CHyba při vkládání zápisu&message_type=error-message");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: ./?message=Chyba při přípravě dotazu&message_type=error-message");
        exit();
    }
}


?>
<div id="loading-overlay">
    <div class="loader"></div>
</div>

<body>
    <div id="calendar">
        <div class="overlay" id="overlay" onclick="closeAllMenus()"></div>

        <nav>

            <!-- User Icon (vlevo na mobilu, vpravo na desktopu) -->
            <div class="user-icon" onclick="toggleUserMenu(event)">
                                <?php if (!empty($username_parlament)) { ?>
<i class="fa fa-user" style="color: #70B95E;"></i>
  <?php } else { ?>
<i class="fa fa-user" style="color: #3C3C3B;"></i>
  <?php } ?>
            </div>

            <!-- Navigation Links (vlevo na PC) -->
            <div class="nav-links">
                <a href="../">Domů</a>
                <a href="../notes" class="active">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                    <a href="../attendances">Prezenční listiny</a>
                <?php } ?>
            </div>

            <!-- Hamburger Menu Icon (vpravo na mobilu) -->
            <div class="hamburger" onclick="toggleMobileMenu(event)">
                <i class="fa fa-bars"></i>
            </div>

            <!-- User Dropdown Menu -->
            <div class="user-dropdown" id="userDropdown">
                <?php if (!empty($username_parlament)) { ?>
                    <p>Přihlášen jako: <b><?php echo $username_parlament; ?></b></p>
                    <a href="../logout.php">Odhlásit se</a>
                <?php } else { ?>
                    <a href="../login.php">Přihlásit se</a>
                <?php } ?>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu" id="mobileMenu">
                <a href="../">Domů</a>
                <a href="../notes" class="active">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                    <a href="../attendances">Prezenční listiny</a>
                <?php } ?>
            </div>
        </nav>
        <?php
        if (isset($_GET['message']) && isset($_GET['message_type'])) {
            $message = $_GET['message'];
            $message_type = $_GET['message_type'];

            // Určení třídy a ikony podle typu zprávy
            if ($message_type == 'success-message') {
                $message_class = 'success-message';
                $message_icon = 'fa-check';
            } elseif ($message_type == 'error-message') {
                $message_class = 'error-message';
                $message_icon = 'fa-times';
            } elseif ($message_type == 'info-message') {
                $message_class = 'info-message';
                $message_icon = 'fa-info-circle';
            }

            // Výstup zprávy s ikonou a třídou
            echo '<div onclick="removeQueryString()" class="' . $message_class . '" style="cursor: pointer;">';
            echo '<i class="fa ' . $message_icon . '" style="margin-right: 5px;"></i> ' . htmlspecialchars($message);
            echo '</div>';
        }
        if (isset($add_notes) && $add_notes == '1') { ?>
            <div class="table-heading">
                <h2> <?php echo $headerText1; ?> </>
            </div>
            <?php
            $currentDate = date('Y-m-d');
            ?>
            <div style="display: flex; flex-direction: column;">
                <form method="post" id="myForm" style="max-width: 100%; margin-bottom: 5px;">
                    <label for="date" style="font-size: 16px; margin-bottom: 8px;">Datum:</label>
                    <input type="date" name="date" id="dateInput"
                        style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;"
                        value="<?php echo $currentDate; ?>" required>
                    <label for="notes" style="font-size: 16px; margin-bottom: 8px;">Záznam:</label>
                    <div style="display: flex; flex-direction: column;">
                        <textarea name="notes" id="notesInput" rows="10"
                            style="padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; white-space: nowrap;"
                            required></textarea>
                    </div>
                    <div class="button-container" id="buttonContainer">
                        <button type="submit" onclick="smazatZLocalStorage()">
                            <i class="fa fa-save"></i> Uložit
                        </button>
                    </div>
                </form>
                <p> main title = //main title// (Tučný modrý text na středu stránky) <br>
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
                    underline bold italics = __***underline bold italics***__ (podtržený text + tučný text +
                    kurzíva)
                </p>
            </div>
        <?php } ?>
        <div class="button-container" id="buttonContainer">
            <?php
            // Inicializujeme prázdné pole pro seskupení dat podle roků
            $grouped_data = [];

            // Načteme data z databáze
            $result = $conn->query("SELECT idnotes_parlament, date FROM notes_alba_rosa_parlament ORDER BY date DESC");

            if ($result->num_rows > 0) {
                // Projdeme všechny záznamy
                while ($row = $result->fetch_assoc()) {
                    $idnotes_parlament = $row['idnotes_parlament'];
                    $date = $row['date'];
                    $year = date('Y', strtotime($date)); // Extrahujeme rok
            
                    // Vytvoříme skupiny podle roku
                    if (!isset($grouped_data[$year])) {
                        $grouped_data[$year] = []; // Pokud rok ještě neexistuje, vytvoříme prázdné pole
                    }

                    // Přidáme záznam do pole příslušného roku
                    $grouped_data[$year][] = [
                        'idnotes_parlament' => $idnotes_parlament,
                        'date' => date('d.m.Y', strtotime($date))
                    ];
                }

                // Vypíšeme data podle roku
                foreach ($grouped_data as $year => $items) {
                    echo '<div class="year-container">';
                    echo '<div class="table-heading"><h2>';
                    echo '<i class="fa fa-heart blue"></i>・Zápisy・' . $year;
                    echo '</h2></div>';
                    echo '<div class="button-container">'; // Používáme tvůj existující styl pro tlačítka
                    foreach ($items as $item) {
                        echo '<a href="./show_notes.php?idnotes_parlament=' . $item['idnotes_parlament'] . '" target="_blank">';
                        echo '<button>';
                        echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . $item['date'];
                        echo '</button>';
                        echo '</a>';
                    }
                    echo '</div>'; // Uzavřeme kontejner pro tlačítka
                    echo '</div>'; // Uzavřeme kontejner pro rok
                }
            } else {
                echo "Žádná data nebyla nalezena.";
            }
            ?>
        </div>
        <br>

        <?php

        // Získání dat z tabulky
        $query = "SELECT text FROM other_alba_rosa_parlament WHERE idother_parlament = 1";
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
<script>

    // Funkce pro ukládání dat do local storage
    function ulozitDoLocalStorage() {
        const date = document.getElementById('dateInput').value;
        const notes = document.getElementById('notesInput').value;
        if (date.trim() !== '') {
            localStorage.setItem('date', date);
        }
        if (notes.trim() !== '') {
            localStorage.setItem('notes', notes);
        }
    }

    // Funkce pro mazání dat z local storage
    function smazatZLocalStorage() {
        localStorage.removeItem('date');
        localStorage.removeItem('notes');
    }

    // Zavolání funkce pro načtení dat při načtení stránky
    window.onload = function () {
        const date = localStorage.getItem('date');
        const notes = localStorage.getItem('notes');
        if (date) {
            document.getElementById('dateInput').value = date;
        }
        if (notes) {
            document.getElementById('notesInput').value = notes;
        }
    };

    // Zavolání funkce pro ukládání dat při jakékoli změně v polích formuláře
    document.getElementById('dateInput').addEventListener('input', ulozitDoLocalStorage);
    document.getElementById('notesInput').addEventListener('input', ulozitDoLocalStorage);

    // Zavolání funkce pro uložení dat při načtení stránky
    ulozitDoLocalStorage();
</script>

</html>
<?php
ob_end_flush();
?>