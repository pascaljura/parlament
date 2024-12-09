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
    <?php
    $headerText = '&#x1F499;・Přihlášení';
    $headerText1 = '&#x1F499;・Nový zápis';
    $headerText2 = '&#x1F499;・O nás';
    $footerText = '&#x1F499;・Aktuálně';
    ?>
</head>
<?php

// Kontrola, zda je uživatel již přihlášen
if (isset($_SESSION['idusers'])) {
    // Kontrola přihlášení


    // Získání id uživatele ze session
    $idusers = $_SESSION['idusers'];

    // Kontrola přístupu na základě sloupce parlament_access
    $stmtAccess = $conn->prepare("SELECT parlament_access FROM users_alba_rosa WHERE idusers = ?");
    $stmtAccess->bind_param("i", $idusers);
    $stmtAccess->execute();
    $stmtAccess->bind_result($parlament_access);
    $stmtAccess->fetch();
    $stmtAccess->close();

    // Pokud není přístup povolen (parlament_access != 1)
    if ($parlament_access != '1') { ?>
        <div id="calendar">
            <div style="color: #FF0000; margin-bottom: 5px;"><b>Chybí oprávnění</b></div>
            <?php
    } else {
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
            $sql_last_doc = "SELECT cislo_dokumentu FROM zapis_alba_rosa_parlament ORDER BY datum DESC LIMIT 1";
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
            $sql = "INSERT INTO zapis_alba_rosa_parlament (idusers, datum, zapis, cislo_dokumentu) VALUES (?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("isss", $_SESSION['idusers'], $datum, $zapis, $cislo_dokumentu);

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
                    if (isset($_GET['message'])) {
                        echo '<div onclick="removeQueryString()" class="success-message" style="cursor: pointer;">';
                        echo '<i class="fa fa-check" style="margin-right: 5px;"></i> ' . htmlspecialchars($_GET['message']);
                        echo '</div>';
                    }
                    ?>
                   
                    <?php

                    // Získání dat z tabulky
                    $query = "SELECT * FROM other_alba_rosa_parlament WHERE idother = 3";
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
                     <div class="table-heading">
                        <b> <?php echo $headerText2; ?> </b>
                    </div>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin consectetur fringilla sapien, at dapibus
                        est. Fusce nec leo eu arcu convallis ultrices in sit amet leo. Cras rhoncus feugiat purus eu cursus.
                        Nullam eget tempus dui. Donec nibh nibh, sollicitudin a posuere congue, ultricies quis tellus.
                        Pellentesque dui ex, sollicitudin sed tincidunt vel, eleifend vel urna. Nulla facilisi. Sed odio dolor,
                        consequat sit amet aliquet non, faucibus quis lacus. Fusce dui orci, eleifend non dictum eu, sodales
                        eget mi. Integer sed rhoncus odio, a venenatis ipsum. Cras id vehicula nibh, tincidunt mattis augue.
                        Aenean volutpat odio et arcu blandit commodo. Vestibulum blandit sagittis magna a tristique. Ut porta
                        sit amet ex eget tempor. <br><br>

                        Integer elementum rutrum tincidunt. Vivamus a pharetra quam. Vivamus sit amet enim ut sem lobortis
                        blandit. Nunc at leo mauris. Maecenas congue sem ante, a imperdiet lacus porttitor non. Integer
                        vestibulum tristique velit, id eleifend dui. Cras fringilla orci a nibh cursus blandit. Mauris ac libero
                        faucibus, cursus quam volutpat, blandit sem. Phasellus vestibulum in odio vitae interdum. Fusce sem
                        dolor, blandit a ullamcorper in, eleifend eget erat. In dolor tortor, scelerisque eu massa non,
                        malesuada blandit urna. Duis hendrerit eleifend pellentesque. Nullam varius erat non erat maximus, sed
                        fermentum felis pulvinar. Vestibulum et nunc neque.<br><br>

                        Vivamus ut risus sem. Sed vitae leo arcu. Praesent condimentum tristique arcu vitae aliquam. Aliquam
                        accumsan massa ac diam pellentesque, non mattis odio tristique. Etiam sit amet dignissim felis. Nam orci
                        metus, scelerisque a odio rhoncus, hendrerit porttitor risus. Orci varius natoque penatibus et magnis
                        dis parturient montes, nascetur ridiculus mus. Duis iaculis dui et diam hendrerit porta. Nam at diam a
                        neque dignissim tristique faucibus id massa.<br><br>

                        Aliquam erat volutpat. Curabitur ullamcorper ultrices porta. In ultricies bibendum semper. Pellentesque
                        varius vehicula luctus. Etiam dignissim nisl vel sem auctor laoreet. Suspendisse quis augue efficitur,
                        cursus leo in, dignissim diam. Proin metus quam, finibus semper eleifend sed, sollicitudin et dolor.
                        Aliquam sit amet nulla id elit viverra venenatis a vel felis. In accumsan rutrum nisi ac mollis.
                        Pellentesque condimentum rutrum ante, ac placerat diam consequat ac. Quisque dictum ex at quam maximus
                        fermentum. Sed tristique sodales consequat. Morbi eget dolor ac arcu varius tincidunt non sed turpis.
                        Aenean ut varius mi, eu mollis metus. Donec tristique imperdiet enim, non dictum turpis scelerisque at.
                        <br><br>
                        Nullam aliquet fermentum ex, nec euismod urna venenatis eu. Nulla facilisi. Lorem ipsum dolor sit amet,
                        consectetur adipiscing elit. In ac velit ac sapien cursus facilisis. Sed at ultrices purus. Mauris vitae
                        eros eget lectus faucibus semper non nec arcu. Nam turpis mauris, molestie nec augue in, bibendum tempor
                        mi. Vivamus porta mattis nisl ac commodo. Nulla finibus tincidunt blandit. Donec a nulla placerat tortor
                        euismod blandit. Mauris vitae dignissim felis, in tincidunt dui. Fusce a sagittis augue.


                    </p>
                    <div class="button-container" id="buttonContainer">
                        <?php
                        // Inicializujeme prázdné pole pro seskupení dat podle roků
                        $grouped_data = [];

                        // Načteme data z databáze
                        $result = $conn->query("SELECT idzapis, datum FROM zapis_alba_rosa_parlament ORDER BY datum DESC");

                        if ($result->num_rows > 0) {
                            // Projdeme všechny záznamy
                            while ($row = $result->fetch_assoc()) {
                                $idzapis = $row['idzapis'];
                                $datum = $row['datum'];
                                $year = date('Y', strtotime($datum)); // Extrahujeme rok
                
                                // Vytvoříme skupiny podle roku
                                if (!isset($grouped_data[$year])) {
                                    $grouped_data[$year] = []; // Pokud rok ještě neexistuje, vytvoříme prázdné pole
                                }

                                // Přidáme záznam do pole příslušného roku
                                $grouped_data[$year][] = [
                                    'idzapis' => $idzapis,
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
                                    echo '<a href="./show_zapis.php?idzapis=' . $item['idzapis'] . '" target="_blank">';
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
                        <form method="post" id="myForm" style="max-width: 100%; margin-bottom: 5px;">
                            <label for="datum" style="font-size: 16px; margin-bottom: 8px;">Datum:</label>
                            <input type="date" name="datum" id="datumInput"
                                style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;"
                                value="<?php echo $currentDate; ?>" required>
                            <label for="zapis" style="font-size: 16px; margin-bottom: 8px;">Záznam:</label>
                            <div style="display: flex; flex-direction: column;">
                                <textarea name="zapis" id="zapisInput" rows="10"
                                    style="padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; white-space: nowrap;"
                                    required></textarea>
                            </div>
                            <div class="button-container" id="buttonContainer">
                                <button type="submit" onclick="smazatZLocalStorage()">
                                    <i class="fa fa-save"></i> Uložit
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

                    <div class="button-container" id="buttonContainer">
                        <form method="post">
                            <button type="submit" name="logout"><i class="fa fa-sign-out"></i> Odhlásit
                                se</button>
                        </form>
                    </div>
                    <hr color="black" style="height: 2px; border: none;" />




                    <?php
    }
} else {


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $enteredUsername = $_POST["username"];
        $enteredPassword = $_POST["password"];

        // Připravíme SQL dotaz pro získání hesla a přístupu na základě uživatelského jména
        $stmt = $conn->prepare("SELECT idusers, password, parlament_access FROM users_alba_rosa WHERE email = ?");
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
                            <b> <?php echo $headerText; ?> </b>
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
                                <input type="password" name="password" required
                                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
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
                    ?>
                </div>
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
<?php
ob_end_flush();

?>