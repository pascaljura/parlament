<?php
include '../assets/php/config.php';
session_start();
ob_start();
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
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">

    <?php
    $headerText = '<i class="fa fa-heart blue"></i>・Přihlášení';
    $headerText1 = '<i class="fa fa-heart blue"></i>・Nový zápis';
    $headerText2 = '<i class="fa fa-heart blue"></i>・O nás';
    ?>
    <style>
        .form-input-wrapper {
            position: relative;
        }

        .password-input {
            padding-right: 30px;
            width: 100%;
            box-sizing: border-box;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 32%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        table {
            width: 100%;
            max-height: 400px;
            border-collapse: collapse;
            font-family: 'Roboto', Calibri, sans-serif;
            font-weight: 500;
            font-size: 16px;
            text-align: center;
        }

        table thead {
            background-color: #5481AA;
            color: #ffffff;
            border: 2px solid black;
            /* Ohraničení pro hlavičku */
            position: sticky;
            top: 0;
            z-index: 10;
        }

        table tbody td,
        table tbody th {
            border: 1px solid black;
            /* Ohraničení pro tělo tabulky */
        }

        td:first-child,
        th:first-child {
            position: sticky;
            left: 0;
            z-index: 5;
            background-color: #5481AA;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: rgba(85, 172, 238, 0.25);
        }

        th {
            padding: 10px;
        }

        td {
            padding: 5px;
        }

        table tr:hover {
            background-color: #5481AA;
            color: #ffffff
        }


        /* Kontejner pro tabulku, umožní horizontální posouvání */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            max-width: 100%;
        }

        /* Styl pro tabulku uvnitř table-wrapper */
        .table-wrapper table {
            width: 100%;
            min-width: 600px;
            /* Ujistí se, že tabulka nebude příliš úzká */
        }

        /* Layout pro větší obrazovky - seznam žáků vedle tabulky */
        .layout-container {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: nowrap;
        }

        /* Seznam žáků styl */
        .student-list-container {
            background: #f9f9f9;
            border: 1px solid #ddd;
            max-height: 400px;
            padding-top: 10px;
            padding-bottom: 10px;
            padding-right: 5px;
            padding-left: 25px;
            border-radius: 8px;
            overflow-y: auto;
        }

        /* Mobilní zařízení - seznam žáků pod tabulkou */
        @media (max-width: 768px) {

            /* Kontejner pro tabulku, umožní horizontální posouvání */
            .table-wrapper {
                width: 40%;
                overflow-x: auto;
                max-width: 100%;
            }

            .layout-container {
                flex-direction: column;
                gap: 0;
            }

            .student-list-container {
                order: 2;
                margin-top: 10px;
                max-width: 100%;
            }
        }


        ol {
            margin: 0;
            padding: 0;
        }

        button.end {
            background-color: rgb(255, 203, 70);
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        button.end:hover {
            background-color: rgb(255, 183, 0);
        }

        button.delete {
            background-color: #ff4848;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        button.delete:hover {
            background-color: rgb(255, 0, 0);
        }

        button.qr {
            background-color: rgb(255, 255, 255);
            color: black;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        button.qr:hover {
            background-color: rgb(196, 255, 255);

        }
    </style>
</head>
<?php

// Kontrola, zda je uživatel již přihlášen
if (isset($_SESSION['idusers'])) {
    // Kontrola přihlášení


    // Získání id uživatele ze session
    $idusers = $_SESSION['idusers'];

    // Kontrola přístupu na základě sloupce parlament_access_admin
    $stmtAccess = $conn->prepare("SELECT parlament_access_admin FROM users_alba_rosa_parlament WHERE idusers = ?");
    $stmtAccess->bind_param("i", $idusers);
    $stmtAccess->execute();
    $stmtAccess->bind_result($parlament_access_admin);
    $stmtAccess->fetch();
    $stmtAccess->close();

    // Pokud není přístup povolen (parlament_access_admin != 1)
    if ($parlament_access_admin != '1') { ?>
        <div id="calendar">
            <div style="color: #FF0000; margin-bottom: 5px;"><b>Chybí oprávnění</b></div>
            <?php
    } else {
        // Načtení prezencí s formátováním datumu
        $attendances = [];
        $sqlattendances = "
    SELECT 
        idattendances_list_parlament, 
        DATE_FORMAT(datetime, '%d.%m.%Y %H:%i:%s') AS datetime, 
        idnotes_parlament,
        active
    FROM 
        attendances_list_alba_rosa_parlament 
    ORDER BY idattendances_list_parlament ASC";

        $resultattendances = $conn->query($sqlattendances);
        if ($resultattendances) {
            while ($rowattendances = $resultattendances->fetch_assoc()) {
                $attendances[] = $rowattendances;
            }
        }

        // Načtení všech zápisů pro dropdown (také formátování data)
        $notes = [];
        $sql = "SELECT idnotes_parlament, DATE_FORMAT(date, '%d.%m.%Y') AS date FROM notes_alba_rosa_parlament";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $notes[] = $row;
            }
        }
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header("Location: ./");
            exit();
        }
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
            $sql = "INSERT INTO notes_alba_rosa_parlament (idusers, date, notes, document_number) VALUES (?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("isss", $_SESSION['idusers'], $date, $notes, $document_number);

                if ($stmt->execute()) {
                    header("Location: ./?message=Uloženo.");
                    exit();
                } else {
                    echo "Chyba při ukládání záznamu: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Chyba při přípravě dotazu: " . $conn->error;
            }
        }


        ?>
            <div id="loading-overlay">
                <div class="loader"></div>
            </div>

            <body>
                <div id="calendar">
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
                        }

                        // Výstup zprávy s ikonou a třídou
                        echo '<div onclick="removeQueryString()" class="' . $message_class . '" style="cursor: pointer;">';
                        echo '<i class="fa ' . $message_icon . '" style="margin-right: 5px;"></i> ' . htmlspecialchars($message);
                        echo '</div>';
                    }


                    ?>

                    <?php

                    // Získání dat z tabulky
                    $query = "SELECT * FROM other_alba_rosa_parlament WHERE idother_parlament = 3";
                    $result = mysqli_query($conn, $query);

                    if ($result) {
                        $row = mysqli_fetch_assoc($result);

                        // Kontrola hodnoty id
                        if ($row['active'] == 1) {
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

                    ?>
                    <div class="table-heading">
                        <h2> <?php echo $headerText2; ?> </h2>
                    </div>
                    <p>
                        Školní parlament na Purkyňce je skupina studentů z každé třídy, kteří zastupují zájmy žáků a podílejí se
                        na chodu školy. Jeho hlavní funkce jsou:<br>
                    <ul>
                        <li><b>Zastupování žáků</b>: Předává názory a přání studentů vedení školy.</li>
                        <li><b>Řešení problémů</b>: Pomáhá řešit otázky týkající se školního prostředí.</li>
                        <li> <b>Organizace akcí</b>: Podílí se na plánování soutěží, tematických dnů nebo charitativních
                            sbírek.</li>
                        <li> <b>Spolupráce s vedením</b>: Pravidelně komunikuje s ředitelem a učiteli.</li>
                    </ul>
                    Zapojení do parlamentu rozvíjí komunikační a organizační dovednosti, umožňuje ovlivňovat dění ve škole a
                    být v obraze ohledně plánovaných změn nebo akcí. Je to příležitost pro studenty, kteří chtějí něco
                    změnit a aktivně se podílet na životě školy.


                    </p>
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
                                echo '<div class="table-heading"><h3>';
                                echo '<i class="fa fa-heart blue"></i>・Zápisy・' . $year;
                                echo '</h3></div>';
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
                    <div class="table-heading">
                        <h2> <?php echo $headerText1; ?> </h>
                    </div>
                    <?php
                    if ($sql != "" && $conn->query($sql) === TRUE) {
                        echo '<div style="color: #008000; margin-bottom: 5px;"><i class="fa fa-check"></i> Zápis byl úspěšně uložen.</div>';
                    } elseif ($sql != "") {
                        echo "Chyba: " . $sql . "<br>" . $conn->error;
                    }
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
                            underline bold italics = __***underline bold italics***__ (podtržený text + tučný text + kurzíva)
                        </p>
                    </div>
                    <div class="table-heading">
                        <h2><i class="fa fa-heart blue"></i>・Správa schůzí</h2>
                    </div>
                    <div class="button-container" id="buttonContainer">
                        <form action="create_attendance_list.php" method="post">
                            <button type="submit" style="margin: 10px 0 10px 0;">Zahájit schůzi</button>
                        </form>
                    </div>
                    <div class="button-container" id="buttonContainer">
                        <div class="layout-container">
                            <form action="save_attendance_links.php" method="post">
                                <div class="table-wrapper">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>ID<br>Prezenční<br>listiny</th>
                                                <th style="white-space: nowrap;">Datum a čas</th>
                                                <th style="white-space: nowrap;">Přiřazený zápis</th>
                                                <th style="white-space: nowrap;">Stav</th>
                                                <th style="white-space: nowrap;">Akce</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($attendances as $attendance): ?>
                                                <tr onclick="loadStudents(<?= $attendance['idattendances_list_parlament'] ?>)"
                                                    style="cursor: pointer;">
                                                    <td
                                                        style="white-space: nowrap; color: white; background-color: #5481AA; border: 1px solid black;">
                                                        <?= htmlspecialchars($attendance['idattendances_list_parlament']) ?>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <?= htmlspecialchars($attendance['datetime']) ?>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <select name="notes[<?= $attendance['idattendances_list_parlament'] ?>]">
                                                            <option value="" selected disabled>-- Vyberte zápis --</option>
                                                            <?php foreach ($notes as $note): ?>
                                                                <option value="<?= $note['idnotes_parlament'] ?>"
                                                                    <?= ($note['idnotes_parlament'] == $attendance['idnotes_parlament']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($note['date']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <?= $attendance['active'] == '1' ? '<span style="color: black; background-color: #70B95E; border-radius: 5px; padding: 5px 10px;">Probíhá</span>' : '<span style="background-color: #ff4848; color: white; border-radius: 5px; padding: 5px 10px;">Ukončeno</span>' ?>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <div style="display: flex; gap: 10px; justify-content: center;">
                                                            <a
                                                                href="attendance_list_actions.php?action=delete&idattendances_list_parlament=<?= $attendance['idattendances_list_parlament'] ?>">
                                                                <button type="button" class="delete">Smazat</button>
                                                            </a>
                                                            <a
                                                                href="attendance_list_actions.php?action=end&idattendances_list_parlament=<?= $attendance['idattendances_list_parlament'] ?>">
                                                                <button type="button" class="end">Ukončit</button>
                                                            </a>
                                                            <a href="attendance_list_actions.php?action=qr&idattendances_list_parlament=<?= $attendance['idattendances_list_parlament'] ?>"
                                                                target="_blank">
                                                                <button type="button" class="qr">QR Kód</button>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" style="margin: 10px 0 10px 0;">Uložit změny</button>
                            </form>

                            <div class="student-list-container" id="studentListContainer"
                                style="display: none; max-width: 40%; overflow-x: auto; width: 40%;">
                                <ol></ol>
                            </div>
                        </div>
                    </div>
                    <hr style="border-top: 1px solid black;border-bottom: none;">
                    <div class="button-container" id="buttonContainer">
                        <form method="post">
                            <button type="submit" name="logout"><i class="fa fa-sign-out"></i> Odhlásit
                                se</button>
                        </form>
                    </div><br>




                    <?php
    }
} else {


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $enteredUsername = $_POST["username"];
        $enteredPassword = $_POST["password"];

        // Připravíme SQL dotaz pro získání hesla a přístupu na základě uživatelského jména
        $stmt = $conn->prepare("SELECT idusers, password, parlament_access_admin FROM users_alba_rosa_parlament WHERE email = ?");
        $stmt->bind_param("s", $enteredUsername);
        $stmt->execute();
        $stmt->store_result();

        // Pokud najdeme uživatele, získáme jeho údaje
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($idusers, $hashedPassword, $parlamentAccess);
            $stmt->fetch();

            // Kontrola, zda má uživatel přístup do parlamentu
            if ($parlamentAccess !== '1') {
                $loginError = "Chybí oprávnění.";
            } else {
                // Ověření hesla pomocí password_verify
                if (password_verify($enteredPassword, $hashedPassword)) {
                    $_SESSION['idusers'] = $idusers;
                    header("Location: ./");
                    exit();
                } else {
                    $loginError = "Nesprávné přihlašovací údaje.";
                }
            }
        } else {
            $loginError = "Uživatel nenalezen.";
        }

        $stmt->close();
    }





    ?>
                <div id="loading-overlay">
                    <div class="loader"></div>
                </div>

                <body>
                    <div id="calendar">
                        <div class="table-heading">
                            <h2> <?php echo $headerText; ?> </>
                        </div>
                        <?php
                        if (isset($loginError)) {
                            echo '<div style="color: #FF0000; margin-bottom: 5px;"><b>' . $loginError . '<b></div>';
                        }
                        ?>
                        <div class="button-container" id="buttonContainer">
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="loginForm"
                                style="width: 80%; max-width: 400px; margin-bottom: 20px;">
                                <label for="username" style="font-size: 16px; margin-bottom: 8px;">Uživatelské
                                    jméno:</label>
                                <input type="text" name="username" required
                                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                                <label for="password" style="font-size: 16px; margin-bottom: 8px;">Heslo:</label>
                                <div class="form-input-wrapper">
                                    <input type="password" name="password" id="txt_pwd" required
                                        style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                                    <span class="toggle-password" id="toggle_pwd" onclick="togglePassword()"><i
                                            class="fa fa-eye"></i></span>
                                </div>
                                <button type="submit"><i class="fa fa-sign-in" aria-hidden="true"></i>
                                    Přihlásit se
                                </button>
                            </form>
                        </div>
                        <hr color="black" style="height: 2px; border: none;" />


                    <?php }
?>
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
                function togglePassword() {
                    var passwordInput = document.getElementById("txt_pwd");
                    var icon = document.getElementById("toggle_pwd").querySelector("i");
                    if (passwordInput.type === "password") {
                        passwordInput.type = "text";
                        icon.classList.remove("fa-eye");
                        icon.classList.add("fa-eye-slash");
                    } else {
                        passwordInput.type = "password";
                        icon.classList.remove("fa-eye-slash");
                        icon.classList.add("fa-eye");
                    }
                }
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
                function loadStudents(attendanceId) {
                    const container = document.getElementById('studentListContainer');
                    const list = container.querySelector('ol');

                    // Vyprázdnění obou
                    list.innerHTML = '';

                    // Zobrazení celého containeru (byl původně skrytý)
                    container.style.display = 'block';

                    fetch('fetch_students.php?id=' + attendanceId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                list.style.display = 'block'; // Zobrazí seznam

                                data.forEach((student, index) => {
                                    const li = document.createElement('li');
                                    li.textContent = `${student.username} - ${student.time}`;
                                    list.appendChild(li);
                                });
                            } else {
                                list.style.display = 'block'; // Skryje seznam

                                // Vytvoří li pro "Žádní studenti nenalezeni"
                                const li = document.createElement('li');
                                li.textContent = 'Žádní studenti nenalezeni.';
                                list.appendChild(li);
                            }
                        })
                        .catch(err => {
                            console.error('Chyba při načítání studentů:', err);
                            alert('Nepodařilo se načíst studenty.');
                        });
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

                function removeQueryString() {
                    // Zkontrolujeme, jestli URL obsahuje otazník
                    if (window.location.href.indexOf('?') > -1) {
                        // Získáme část URL před otazníkem
                        const newUrl = window.location.href.split('?')[0];

                        // Nastavíme novou URL bez query stringu
                        window.history.pushState({}, document.title, newUrl);

                        // Obnovíme stránku, aby se URL aktualizovala
                        location.reload();
                    }
                }
            </script>

</html>
<?php
ob_end_flush();

?>