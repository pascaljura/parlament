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
        $idusers_parlament_parlament = $userData['idusers_parlament'];
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
    <style>
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
            z-index: 5;
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
            padding-left: 5px;
            border-radius: 8px;
            overflow-y: auto;
            width: 250px;
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
                <a href="../notes">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                    <a href="../attendances" class="active">Prezenční listiny</a>
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
                <a href="../notes">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                    <a href="../attendances" class="active">Prezenční listiny</a>
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

        if ((isset($show_attendances) && $show_attendances == '1') && ((isset($parlament_access_admin) && $parlament_access_admin == '1') || (isset($parlament_access_user) && $parlament_access_user == '1'))) {
        if ((isset($parlament_access_admin) && $parlament_access_admin == '1')) {

            ?>
            <div class="table-heading">
                <h2><i class="fa fa-heart blue"></i>・Správa prezenčních listin</h2>
            </div>
            <div class="button-container" id="buttonContainer">
                <form action="create_attendances_list.php" method="post">
                    <button type="submit" style="margin: 10px 0 10px 0;">Zahájit schůzi</button>
                </form>
            </div>
            <?php if (count($attendances) > 0): ?>
                <div class="button-container" id="buttonContainer">
                    <div class="layout-container">
                        <form action="save_attendances_links.php" method="post">
                            <div class="table-wrapper">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID<br>Prezenční<br>listiny</th>
                                            <th style="white-space: nowrap;">Datum a čas</th>
                                            <?php if (isset($select_idnotes_parlament) && $select_idnotes_parlament == '1') {
                                                ?>
                                                <th style="white-space: nowrap;">Přiřazený zápis</th>
                                            <?php } ?>
                                            <th style="white-space: nowrap;">Stav</th>
                                            <?php if ((isset($delete_attendances) && $delete_attendances == '1') || (isset($end_attendances) && $end_attendances == '1') || (isset($qr_attendances) && $qr_attendances == '1')) {
                                                ?>
                                                <th style="white-space: nowrap;">Akce</th>
                                            <?php } ?>
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
                                                <?php if (isset($select_idnotes_parlament) && $select_idnotes_parlament == '1') {
                                                    ?>
                                                    <td style="white-space: nowrap;">
                                                        <select name="notes[<?= $attendance['idattendances_list_parlament'] ?>]"
                                                            onclick="event.stopPropagation();">
                                                            <option value="" selected disabled>-- Vyberte zápis --</option>
                                                            <?php foreach ($notes as $note): ?>
                                                                <option value="<?= $note['idnotes_parlament'] ?>"
                                                                    <?= ($note['idnotes_parlament'] == $attendance['idnotes_parlament']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($note['date']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                <?php } ?>
                                                <td style="white-space: nowrap;">
                                                    <?= $attendance['active'] == '1' ?
                                                        '<span style="color: black; background-color: #70B95E; border-radius: 5px; padding: 5px 10px;">Probíhá</span>' :
                                                        '<span style="background-color: #ff4848; color: white; border-radius: 5px; padding: 5px 10px;">Ukončeno</span>' ?>
                                                </td>
                                                <?php if ((isset($delete_attendances) && $delete_attendances == '1') || (isset($end_attendances) && $end_attendances == '1') || (isset($qr_attendances) && $qr_attendances == '1')) {
                                                    ?>
                                                    <td style="white-space: nowrap;">
                                                        <div style="display: flex; gap: 10px; justify-content: center;">
                                                        <?php } ?>
                                                        <?php if (isset($delete_attendances) && $delete_attendances == '1') { ?>
                                                            <a href="attendances_list_actions.php?action=delete&idattendances_list_parlament=<?= $attendance['idattendances_list_parlament'] ?>"
                                                                onclick="event.stopPropagation();">
                                                                <button type="button" class="delete">Smazat</button>
                                                            </a>
                                                        <?php }
                                                        if (isset($end_attendances) && $end_attendances == '1') {
                                                            ?>
                                                            <a href="attendances_list_actions.php?action=end&idattendances_list_parlament=<?= $attendance['idattendances_list_parlament'] ?>"
                                                                onclick="event.stopPropagation();">
                                                                <button type="button" class="end">Ukončit</button>
                                                            </a>
                                                        <?php }
                                                        if (isset($qr_attendances) && $qr_attendances == '1') {
                                                            ?>
                                                            <a href="attendances_list_actions.php?action=qr&idattendances_list_parlament=<?= $attendance['idattendances_list_parlament'] ?>"
                                                                target="_blank" onclick="event.stopPropagation();">
                                                                <button type="button" class="qr">QR Kód</button>
                                                            </a>
                                                        <?php }
                                                        ?>
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
    style="display: none; max-width: 40%; overflow-x: auto;">
    <!-- Tady už žádné <ol> nepotřebuješ, JavaScript si tam sám nasype divy se studenty -->
</div>

                    </div>
                </div>
            <?php else: ?>
                <div class="info-message">
                    <i class="fa fa-info-circle" style="margin-right: 5px;"></i> Žádná schůze zatím nebyla zahájena.
                </div>
            <?php endif; ?>







            <?php
        } if((isset($show_attendances) && $show_attendances == '1') && ((isset($parlament_access_admin) && $parlament_access_admin == '1') || (isset($parlament_access_user) && $parlament_access_user == '1'))) {
       // SQL dotaz
$sqluser = "SELECT 
al.idattendances_list_parlament AS id_listiny,
allp.datetime AS datum_cas
FROM 
attendances_alba_rosa_parlament al
JOIN 
attendances_list_alba_rosa_parlament allp
ON 
al.idattendances_list_parlament = allp.idattendances_list_parlament
WHERE 
al.idusers_parlament = ?
GROUP BY 
al.idattendances_list_parlament
ORDER BY 
allp.datetime DESC";

// Příprava a provedení dotazu
if ($stmt = $conn->prepare($sqluser)) {
$stmt->bind_param("i", $idusers_parlament);
$stmt->execute();
$resultuser = $stmt->get_result();
} else {
die("Chyba při přípravě dotazu: " . $conn->error);
}
?>
  <div class="table-heading">
                <h2><i class="fa fa-heart blue"></i>・Záznamy mé účasti</h2>
            </div></thead>
<table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>ID Prezenční listiny</th>
                <th>Datum a čas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['id_listiny']) . "</td>
                            <td>" . htmlspecialchars($row['datum_cas']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Žádné záznamy</td></tr>";
            }
            ?>
        </tbody>
    </table>
       <?php
        }
    } else {
        echo ' <div class="error-message">
         <i class="fa fa-times" style="margin-right: 5px;"></i> Chybí oprávnění.
     </div>' ;

    }

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
function loadStudents(idattendances_list_parlament) {
    const container = document.getElementById('studentListContainer');

    if (!container) {
        console.error('studentListContainer nenalezen');
        return;
    }

    container.innerHTML = '';  // vyprázdnění
    container.style.display = 'block';  // zobrazíme kontejner

    fetch('fetch_students.php?idattendances_list_parlament=' + idattendances_list_parlament)
        .then(response => response.json())
        .then(data => {

            if (Array.isArray(data.all_students) && data.all_students.length > 0) {
                data.all_students.forEach((student, index) => {
                    const row = document.createElement('div');
                    row.style.display = 'flex';
                    row.style.alignItems = 'center';
                    row.style.cursor = 'pointer';
                    row.style.padding = '5px';
                    row.style.borderBottom = '1px solid #ddd';

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.value = student.idusers_parlament;
                    checkbox.checked = Array.isArray(data.present_students) &&
                        data.present_students.includes(parseInt(student.idusers_parlament));
                    checkbox.id = 'student_' + student.idusers_parlament;
                    checkbox.style.cursor = 'pointer';

                    const studentNumber = document.createElement('span');
                    studentNumber.textContent = (index + 1) + '.';
                    studentNumber.style.minWidth = '30px';

                    const studentName = document.createElement('span');
                    studentName.textContent = student.username;

                    row.onclick = () => {
                        checkbox.checked = !checkbox.checked;
                    };

                    checkbox.onclick = (e) => e.stopPropagation();

                    row.appendChild(checkbox);
                    row.appendChild(studentNumber);
                    row.appendChild(studentName);

                    container.appendChild(row);
                });

                const saveButton = document.createElement('button');
saveButton.textContent = 'Uložit prezenční listinu';
saveButton.style.width = '100%';
saveButton.style.marginTop = '2px';  // přidání marginu
saveButton.onclick = () => saveAttendanceList(idattendances_list_parlament);
                container.appendChild(saveButton);

            } else {
                const noStudentsMessage = document.createElement('div');
                noStudentsMessage.textContent = 'Žádní studenti nenalezeni.';
                container.appendChild(noStudentsMessage);
            }
        })
        .catch(err => {
            console.error('Chyba při načítání studentů:', err);
            alert('Nepodařilo se načíst studenty.');
        });
}


function saveAttendanceList(idattendances_list_parlament) {
    const checkboxes = document.querySelectorAll('#studentListContainer input[type="checkbox"]');
    const attendanceData = [];

    checkboxes.forEach(checkbox => {
        attendanceData.push({
            idusers_parlament: checkbox.value,
            present: checkbox.checked
        });
    });

    fetch('save_attendance_list.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            idattendances_list_parlament,
            attendanceData
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            window.location.href = './?message=Prezenční listina byla uložena&message_type=success-message';
        } else {
            window.location.href = './?message=Nepodařilo se uložit prezenční listinu.&message_type=error-message';
        }
    })
    .catch(err => {
        console.error('Chyba při ukládání:', err);
        alert('Nepodařilo se uložit prezenční listinu.');
    });
}

</script>

</html>
<?php
ob_end_flush();
?>