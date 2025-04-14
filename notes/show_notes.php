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


    } else {
        // Uživatel nenalezen (může být smazán), odhlásíme ho
        header("Location: ../logout.php");
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

        // Kontrola, zda je zápis již přiřazen jiné prezenční listině
        $check_attendance = "SELECT COUNT(*) as count FROM attendances_list_alba_rosa_parlament WHERE idnotes_parlament = ?";
        $stmt = $conn->prepare($check_attendance);
        $stmt->bind_param("i", $idnotes_parlament);
        $stmt->execute();
        $result_check = $stmt->get_result();
        $row_attendance = $result_check->fetch_assoc();

        // Získání záznamu ze schůze
        $sql = "SELECT z.*, u.username 
            FROM notes_alba_rosa_parlament z
            LEFT JOIN users_alba_rosa_parlament u ON z.idusers_parlament = u.idusers_parlament
            WHERE z.idnotes_parlament = ?";

        $stmt2 = $conn->prepare($sql); // Použití nového statementu pro nový dotaz
        $stmt2->bind_param("i", $idnotes_parlament);
        $stmt2->execute();
        $result = $stmt2->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Debugging
            if (!$row) {
                echo "⚠️ Chyba: Žádná data nebyla nalezena pro idnotes_parlament = $idnotes_parlament";
                exit();
            }

            // Kontrola, zda pole existují
            $date = isset($row['date']) ? date('d.m.Y', strtotime($row['date'])) : 'Neznámé datum';
            $directoryName = isset($row['date']) ? date('d_m_Y', strtotime($row['date'])) : 'unknown';
            $idusers_parlament = isset($row['idusers_parlament']) ? $row['idusers_parlament'] : 0;
            $notes = isset($row['notes']) ? $row['notes'] : 'Žádné poznámky';
            $username = isset($row['username']) ? $row['username'] : 'Neznámý uživatel';
            $document_number = isset($row['document_number']) ? $row['document_number'] : 'Neznámý dokument';

            // Formátování textu
            $notes = str_replace("=", "<br>", $notes);
            $notes = preg_replace('/(?<=^|<br>)(?![\w])--/', '&#160;&#160;&#9702;', $notes);
            $notes = preg_replace('/(?<=^|<br>)(?![\w])-(?!-)/', '&#8226;', $notes);

            function ziskatTextVLomitkach($notes)
            {
                if (preg_match('/\/\/([^\/]+)\/\//', $notes, $matches)) {
                    return $matches[1];
                }
                return "";
            }
            $textInLomitkach = ziskatTextVLomitkach($notes);
            $notes = preg_replace('/\/\/([^\/]+)\/\//', '<div style="color: #3e6181; font-weight: bold; font-size: 20px">$1</div>', $notes);
            $notes = preg_replace('/\*\*\*([^*]+)\*\*\*/', '<b><i>$1</i></b>', $notes);
            $notes = preg_replace('/\*\*([^*]+)\*\*/', '<b>$1</b>', $notes);
            $notes = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $notes);
            $notes = preg_replace('/~~([^~]+)~~/', '<strike>$1</strike>', $notes);
            $notes = preg_replace('/__([^_]+)__/', '<u>$1</u>', $notes);

            // Kontrola existence ID uživatele před SQL dotazem
            if ($idusers_parlament > 0) {
                $stmt3 = $conn->prepare("SELECT username FROM users_alba_rosa_parlament WHERE idusers_parlament = ?");
                $stmt3->bind_param("i", $idusers_parlament);
                $stmt3->execute();
                $resultUser = $stmt3->get_result();

                if ($resultUser->num_rows > 0) {
                    $rowUser = $resultUser->fetch_assoc();
                    $userName = $rowUser['username'];
                } else {
                    $userName = 'Neznámý uživatel';
                }
                $stmt3->close();
            } else {
                $userName = 'Neznámý uživatel';
            }

        } else {
            echo "⚠️ Chyba: Záznam s idnotes_parlament $idnotes_parlament nebyl nalezen.";
            exit();
        }

        // Zavření statementů
        $stmt->close();
        $stmt2->close();
    } else {
        echo "Chybějící nebo neplatné idnotes_parlament v URL.";
        exit();
    }
    ?>

    <div id="calendar"
        style="width: 80%; background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin: 10px; height: 20%;">
        <div class="overlay" id="overlay" onclick="closeAllMenus()"></div>

        <div class="table-heading" style="text-align: center; margin: 1rem 0px -20px;">
            <?php echo '<img src="../assets/img/purkynka_logo.png" width="300px" height="auto" >'; ?>
        </div>
        <table>
            <tr style="border-top: 1px solid black;">
                <td>Číslo dokumentu: <?php echo "$document_number / " . date('dmY', strtotime($row['date'])); ?>
                </td>
                <td style="text-align: center;"></td>
                <td style="text-align: right;">Počet příloh:
                    <?php
                    if ($row_attendance['count'] > 0) {
                        echo $row_attendance['count'];
                    } else {
                        echo "0";
                    } ?>
                </td>
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
        <h3 style="font-size: 25px;padding: unset;margin: unset;">
            Přílohy
        </h3>
        <?php

        // Ověření, zda je nastaven GET parametr
        if (!isset($_GET['idnotes_parlament']) || empty($_GET['idnotes_parlament'])) {
            echo "<p>Chyba: Nebyl zadán platný idnotes_parlament.</p>";
            exit;
        }

        $idnotes_parlament = intval($_GET['idnotes_parlament']); // Ochrana proti SQL injection
        

        if (isset($parlament_access_admin) && $parlament_access_admin == '1') {
            echo "<h4>Prezenční listina</h4>";

            $query = "
            SELECT u.username, u.email, a.time
            FROM users_alba_rosa_parlament u
            JOIN attendances_alba_rosa_parlament a ON u.idusers_parlament = a.idusers_parlament
            JOIN attendances_list_alba_rosa_parlament al ON a.idattendances_list_parlament = al.idattendances_list_parlament
            WHERE al.idnotes_parlament = ?
            ORDER BY a.time DESC
            ";

            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("i", $idnotes_parlament);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<div style='overflow-x: auto; max-width: 100%;'><table border='1' style='width:100%; border-collapse: collapse;text-align: center; white-space: nowrap;'>
                    <tr>
                        <th>Jméno</th>
                        <th>Email</th>
                        <th>Čas docházky</th>
                    </tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>";

                        $datetime = strtotime($row['time']);
                        if ($datetime !== false) {
                            echo "<td>" . date('d.m.Y H:i:s', $datetime) . "</td>";
                        } else {
                            echo "<td>Chyba při parsování času</td>";
                        }


                        echo "</tr>";
                    }
                    echo "</table></div>";
                } else {
                    echo "<p>Pro tento zápis nebyly nalezeny žádné docházky.</p>";
                }
                $stmt->close();
            } else {
                echo "<p>Chyba při dotazu do databáze.</p>";
            }
        } else {
            echo "<p>Nemáte oprávnění zobrazit tuto sekci.</p>";
        }

        ?>

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

        <div id="footer-text"></div>
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