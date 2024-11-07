<?php
include '../assets/php/config.php';
session_start();
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
    <?php
    $headerText = '&#x1F499;・Přihlášení';
    $headerText1 = '&#x1F499;・Nový zápis・2023/2024';
    $footerText = '&#x1F499;・Aktuálně・2023/2024';
    ?>
</head>
<?php

// Kontrola, zda je uživatel již přihlášen
if (isset($_SESSION['id_users'])) {

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: ./");
        exit();
    }
    $sql = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Získání vstupů od uživatele
        $datum = $_POST["datum"];
        $zapis = $_POST["zapis"];
        $zapis = str_replace(array("\n", "\r"), '=', $zapis);

        // Načtení posledního čísla dokumentu podle data
        $sql_last_doc = "SELECT cislo_dokumentu FROM zapis ORDER BY datum DESC LIMIT 1";
        $result = $conn->query($sql_last_doc);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Extrahuje poslední část čísla dokumentu (poslední dvě číslice)
            $last_number = (int) substr($row['cislo_dokumentu'], -2);
            $new_number = str_pad($last_number + 1, 2, "0", STR_PAD_LEFT);  // Zvýší o 1 a doplní nuly
            $cislo_dokumentu = "18.02." . $new_number;
        } else {
            $cislo_dokumentu = "18.02.01"; // První záznam, pokud není žádný předchozí
        }

        // Připravení SQL dotazu s parametry
        $sql = "INSERT INTO zapis (id_users, datum, zapis, cislo_dokumentu) VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isss", $_SESSION['id_users'], $datum, $zapis, $cislo_dokumentu);

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
        <div id="loading-icon"></div>
    </div>

    <body>
        <div id="calendar">
            <?php
            if (isset($_GET['message'])) {
                echo '<div onclick="removeQueryString()" class="success-message" style="cursor: pointer;">';
                echo '<i class="fa fa-check" style="margin-right: 5px;"></i> ' . htmlspecialchars($_GET['message']);
                echo '</div>';
            }
            ?>
            <div class="button-container" id="buttonContainer">
                <?php
                // Inicializujeme prázdné pole pro seskupení dat podle roků
                $grouped_data = [];

                // Načteme data z databáze
                $result = $conn->query("SELECT id_zapis, datum FROM zapis ORDER BY datum DESC");

                if ($result->num_rows > 0) {
                    // Projdeme všechny záznamy
                    while ($row = $result->fetch_assoc()) {
                        $id_zapis = $row['id_zapis'];
                        $datum = $row['datum'];
                        $year = date('Y', strtotime($datum)); // Extrahujeme rok
            
                        // Vytvoříme skupiny podle roku
                        if (!isset($grouped_data[$year])) {
                            $grouped_data[$year] = []; // Pokud rok ještě neexistuje, vytvoříme prázdné pole
                        }

                        // Přidáme záznam do pole příslušného roku
                        $grouped_data[$year][] = [
                            'id_zapis' => $id_zapis,
                            'datum' => date('d.m.Y', strtotime($datum))
                        ];
                    }

                    // Vypíšeme data podle roku
                    foreach ($grouped_data as $year => $items) {
                        echo '<div class="year-container">';
                        echo '<div class="table-heading"><b>';
                        echo '&#x1F499;・Zápisy・' . $year;
                        echo '</b></div>';
                        echo '<div class="button-container">'; // Používáme tvůj existující styl pro tlačítka
                        foreach ($items as $item) {
                            echo '<a href="./show_zapis.php?id_zapis=' . $item['id_zapis'] . '" target="_blank">';
                            echo '<button>';
                            echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . $item['datum'];
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
                <b> <?php echo $headerText1; ?> </b>
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
                <form method="post" id="myForm" style="width: 80%; max-width: 400px; margin-bottom: 5px;">
                    <label for="datum" style="font-size: 16px; margin-bottom: 8px;">Datum:</label>
                    <input type="date" name="datum" id="datumInput"
                        style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;"
                        value="<?php echo $currentDate; ?>" required>
                    <label for="zapis" style="font-size: 16px; margin-bottom: 8px;">Záznam:</label>
                    <div style="display: flex; flex-direction: column;">
                        <textarea name="zapis" id="zapisInput" rows="4"
                            style="width: 80%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; white-space: nowrap;"
                            required></textarea>
                    </div>
                    <div class="button-container" id="buttonContainer">
                        <button type="submit" onclick="smazatZLocalStorage()">
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

            // Získání dat z tabulky
            $query = "SELECT * FROM other WHERE id_other = 3";
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

            ?>
            <div class="button-container" id="buttonContainer">
                <form method="post">
                    <button type="submit" name="logout">Odhlásit
                        se</button>
                </form>
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



        <?php

} else {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $enteredUsername = $_POST["username"];
        $enteredPassword = $_POST["password"];

        // Připravíme SQL dotaz pro získání id_users na základě uživatelského jména a hesla
        $stmt = $conn->prepare("SELECT id_users FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $enteredUsername, $enteredPassword);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id_users);
            $stmt->fetch();
            $_SESSION['id_users'] = $id_users;
            header("Location: ./");
            exit();
        } else {
            $loginError = "Nesprávné přihlašovací údaje.";
        }
        $stmt->close();
    }

    ?>
        <div id="loading-overlay">
            <div id="loading-icon"></div>
        </div>

        <body>
            <div id="calendar">
                <div class="table-heading">
                    <b> <?php echo $headerText; ?> </b>
                </div>
                <?php
                if (isset($loginError)) {
                    echo '<div style="color: #FF0000; margin-bottom: 5px;">' . $loginError . '</div>';
                }
                ?>
                <div class="button-container" id="buttonContainer">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="loginForm"
                        style="width: 80%; max-width: 400px; margin-bottom: 20px;">
                        <label for="username" style="font-size: 16px; margin-bottom: 8px;">Uživatelské jméno:</label>
                        <input type="text" name="username" required
                            style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                        <label for="password" style="font-size: 16px; margin-bottom: 8px;">Heslo:</label>
                        <input type="password" name="password" required
                            style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                        <button type="submit">
                            Přihlásit
                        </button>
                    </form>
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

        <?php }
?>
    </body>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
    <script src="../assets/js/script.js">
    </script>
    <script>
        // Funkce pro ukládání dat do local storage
        function ulozitDoLocalStorage() {
            const datum = document.getElementById('datumInput').value;
            const zapis = document.getElementById('zapisInput').value;
            if (datum.trim() !== '') {
                localStorage.setItem('datum', datum);
            }
            if (zapis.trim() !== '') {
                localStorage.setItem('zapis', zapis);
            }
        }

        // Funkce pro mazání dat z local storage
        function smazatZLocalStorage() {
            localStorage.removeItem('datum');
            localStorage.removeItem('zapis');
        }

        // Zavolání funkce pro načtení dat při načtení stránky
        window.onload = function () {
            const datum = localStorage.getItem('datum');
            const zapis = localStorage.getItem('zapis');
            if (datum) {
                document.getElementById('datumInput').value = datum;
            }
            if (zapis) {
                document.getElementById('zapisInput').value = zapis;
            }
        };

        // Zavolání funkce pro ukládání dat při jakékoli změně v polích formuláře
        document.getElementById('datumInput').addEventListener('input', ulozitDoLocalStorage);
        document.getElementById('zapisInput').addEventListener('input', ulozitDoLocalStorage);

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