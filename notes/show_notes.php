<?php
include '../assets/php/config.php';
session_start();
ob_start();
if (isset($_SESSION['idusers'])) {
    $userId = $_SESSION['idusers'];

    $stmt = $conn->prepare("SELECT * FROM users_alba_rosa_parlament WHERE idusers = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($userData = $result->fetch_assoc()) {
        // Uložení do proměnných
        $idusers_parlament = $userData['idusers'];
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <link rel="manifest" href="./assets/json/manifest.json">
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
</head>
<div id="loading-overlay">
    <div class="loader"></div>
</div>

<body>
    <?php

    if (isset($_GET['idnotes_parlament']) && is_numeric($_GET['idnotes_parlament'])) {
        $idnotes_parlament = $_GET['idnotes_parlament'];
        // Získání záznamu ze schůze
        $result = "SELECT z.*, u.username 
     FROM notes_alba_rosa_parlament z
     LEFT JOIN users_alba_rosa_parlament u ON z.idusers = u.idusers
     WHERE z.idnotes_parlament = ?";

        // Příprava připraveného dotazu
        $stmt = $conn->prepare($result);

        // Bind parametr (parametr typu i = integer, s = string)
        $stmt->bind_param("i", $idnotes_parlament);

        // Vykonání dotazu
        $stmt->execute();
        // Výsledek dotazu
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $date = date('d.m.Y', strtotime($row['date']));
            $directoryName = date('d_m_Y', strtotime($row['date']));
            $idusers = $row['idusers'];
            $notes = $row['notes'];
            $username = $row['username'];
            $document_number = $row['document_number'];

            $notes = str_replace("=", "<br>", $notes);
            $notes = preg_replace('/(?<=^|<br>)(?![\w])--/', '&#160;&#160;&#9702;', $notes);
            $notes = preg_replace('/(?<=^|<br>)(?![\w])-(?!-)/', '&#8226;', $notes);





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
            function ziskatTextVLomitkach($notes)
            {
                $textInLomitka = "";
                if (preg_match('/\/\/([^\/]+)\/\//', $notes, $matches)) {
                    $textInLomitka = $matches[1];
                }
                return $textInLomitka;
            }
            $textInLomitkach = ziskatTextVLomitkach($notes);
            $notes = preg_replace('/\/\/([^\/]+)\/\//', '<div style="color: #3e6181; font-weight: bold; font-size: 20px">$1</div>', $notes); // custom style
            $notes = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<b><i>$1</i></b>', $notes); // bold italics
            $notes = preg_replace('/\*\*([^*]+)\*\*/', '<b>$1</b>', $notes); // bold
            $notes = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $notes); // italics
            $notes = preg_replace('/~~([^~]+)~~/', '<strike>$1</strike>', $notes); // strikeout
            $notes = preg_replace('/__([^_]+)__/', '<u>$1</u>', $notes); // underline
    
            $resultUser = $conn->query("SELECT username FROM users_alba_rosa_parlament WHERE idusers = $idusers");
            if ($resultUser->num_rows > 0) {
                $rowUser = $resultUser->fetch_assoc();
                $userName = $rowUser['username'];
            } else {
                $userName = 'Neznámý uživatel';
            }
        } else {
            echo "Záznam s idnotes_parlament $idnotes_parlament nebyl nalezen.";
            exit();
        }
    } else {
        echo "Chybějící nebo neplatné idnotes_parlament v URL.";
        exit();
    }
    ?>

    <div id="calendar"
        style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
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
        <div class="table-heading" style="text-align: center;">
            <?php echo '<img src="../assets/img/purkynka_logo.png" width="200px" height="80">'; ?>
        </div>
        <table>
            <tr style="border-top: 1px solid black;">
                <td>Číslo dokumentu: <?php echo "$document_number / " . date('dmY', strtotime($row['date'])); ?>
                </td>
                <td style="text-align: center;"></td>
                <td style="text-align: right;">Počet příloh: 0</td>
            </tr>
            <tr>
                <td>Dokument</td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <h3 style="font-size: 25px;padding: unset;margin: unset;">
            Záznam z jednání dne <?php echo "$date"; ?>
        </h3>
        <div class="button-container" id="buttonContainer" style=" font-family: Calibri, sans-serif;">
            <pre style="white-space: break-spaces;  font-family: Calibri, sans-serif;"><?php echo $notes; ?></pre>
        </div>

        <h> V Brně dne <?php echo "$date"; ?> <br>
            Zástupci školního Parlamentu<br>
            Zapsal: <?php echo "$username"; ?><br>
            Ověřila: Mgr. Denisa Gottwaldová <br><br></h>
        <table style="border: none;">
            <tr>
                <td><?php echo "$document_number Záznam z jednání dne $date"; ?></td>
                <td style="text-align: right;"></td>
            </tr>
        </table>
        <br>
        <div style="display: flex; justify-content: space-between;">
            <div class="table-heading button-container">
                <?php
                echo '<button onclick="window.open(\'./notes_pdf.php?idnotes_parlament=' . $idnotes_parlament . '\', \'_blank\')">';
                echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> Stáhnout PDF';
                echo '</button>';
                echo '<button onclick="window.open(\'./notes_docx.php?idnotes_parlament=' . $idnotes_parlament . '\', \'_blank\')">';
                echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> Stáhnout DOCX';
                echo '</button>';
                // Pokud není přístup povolen (parlament_access_admin != 1)
                if (isset($edit_notes) && $edit_notes == '1') {
                    echo '<button onclick="window.location.href=\'./edit_notes.php?idnotes_parlament=' . $idnotes_parlament . '\'">';
                    echo '<i class="fa fa-pencil" aria-hidden="true"></i> Upravit zápis';
                    echo '</button>';
                }
                if (isset($delete_notes) && $delete_notes == '1') {
                    echo '<button onclick="deletenotes(' . $idnotes_parlament . ')">';
                    echo '<i class="fa fa-trash" aria-hidden="true"></i> ' . ' Odstranit zápis';
                    echo '</button>';
                }

                ?>
            </div>
        </div>

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
    <script src="../assets/js/script.js">    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
    <script>
        function deletenotes(idnotes_parlament) {
            if (confirm("Opravdu chcete smazat tento zápis?")) {
                // Vytvoření instance XMLHttpRequest objektu
                var xhttp = new XMLHttpRequest();
                // Nastavení metody a URL pro požadavek
                xhttp.open("POST", "delete_notes.php", true);
                // Nastavení hlavičky požadavku
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                // Nastavení callback funkce pro zpracování odpovědi
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        // Zpracování odpovědi
                        if (this.responseText === "success") {
                            // Pokud je odpověď "success", přesměrujeme uživatele na hlavní stránku
                            window.location.replace("./?message=Zápis+byl+úspěšně+smazán.&message_type=success-message");
                        } else {
                            // Pokud je odpověď "error", zobrazíme chybovou zprávu
                            alert("Nastala chyba při mazání zápisu.");
                        }
                    }
                };
                // Odeslání požadavku s id záznamu
                xhttp.send("idnotes_parlament=" + idnotes_parlament);
            }
        }




    </script>

</body>

</html>
<?php
ob_end_flush();
?>