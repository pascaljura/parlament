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
        $admin = $userData['admin'];

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
    <style>
        .user-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }

        .note-list {
            margin-top: 10px;
            background: #f9f9f9;
            padding: 10px;
        }

        .note-item {
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .note-content {
            flex: 1;
        }

        form.inline {
            display: inline;
            margin: 0;
        }

        button.delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
        }
    </style>
    <script>
        function confirmDelete(form) {
            if (confirm("Opravdu chcete tento záznam odstranit?")) {
                form.submit();
            }
        }
    </script>
</head>
<?php

// Zpracování přidání záznamu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id = (int) $_POST['idusers_parlament'];
    $section = $_POST['section'];
    $notes = $_POST['notes'];

    if (!empty($section)) {
        $stmt = $conn->prepare("INSERT INTO actions_alba_rosa_parlament (idusers_parlament, section, notes) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id, $section, $notes);
        $stmt->execute();
    }
}

// Zpracování odstranění záznamu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $delete_id = (int) $_POST['idactions_parlament'];
    $conn->query("DELETE FROM actions_alba_rosa_parlament WHERE idactions_parlament = $delete_id");
}

// Načtení uživatelů
$users = $conn->query("SELECT * FROM users_alba_rosa_parlament ORDER BY last_name, name");
?>

<body>
    <div id="loading-overlay">
        <div class="loader"></div>
    </div>
    <div id="calendar">
        <div class="overlay" id="overlay" onclick="closeAllMenus()"></div>

        <nav>
            <!-- User Icon (vlevo na mobilu, vpravo na desktopu) -->
            <div class="user-icon" onclick="toggleUserMenu(event)">
                <?php if (!empty($username_parlament)) { ?>
                    <i class="fa fa-user" style="color: #5481aa;"></i>
                <?php } else { ?>
                    <i class="fa fa-user" style="color: #3C3C3B;"></i>
                <?php } ?>
            </div>

            <!-- Navigation Links (vlevo na PC) -->
            <div class="nav-links">
                <a href="../">Domů</a>
                <a href="../notes">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                    <a href="../attendances">Prezenční listiny</a>
                <?php } ?>
                <?php if (isset($admin) && $admin == '1') { ?>
                    <a href="../admin" class="active">Admin</a>
                <?php } ?>
            </div>

            <!-- Hamburger Menu Icon (vpravo na mobilu) -->
            <div class="hamburger" onclick="toggleMobileMenu(event)">
                <i class="fa fa-bars"></i>
            </div>

            <!-- User Dropdown Menu -->
            <div class="user-dropdown" id="userDropdown">
                <?php if (!empty($username_parlament)) { ?>
                    <p>Přihlášen/a jako: <b><?php echo $username_parlament; ?></b></p>
                    <a href="../logout.php">Odhlásit se</a>
                <?php } else { ?>
                    <a class="popup-trigger" data-link="../login.php">Přihlásit se</a>
                <?php } ?>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu" id="mobileMenu">
                <a href="../">Domů</a>
                <a href="../notes">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                    <a href="../attendances">Prezenční listiny</a>
                <?php } ?>
                <?php if (isset($admin) && $admin == '1') { ?>
                    <a href="../admin" class="active">Admin</a>
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

            // Převod HTML zpět, aby seznamy fungovaly správně
            $decoded_message = htmlspecialchars_decode($message);

            // Výstup zprávy s ikonou a třídou
            echo '<div onclick="removeQueryString()" class="' . $message_class . '" style="cursor: pointer;">';
            echo '<i class="fa ' . $message_icon . '" style="margin-right: 5px;"></i> ' . $decoded_message;
            echo '</div>';
        } ?>

        <div class="table-heading">
            <h2><i class="fa fa-heart blue"></i>・Seznam uživatelů parlamentu</h2>
        </div>
        <?php while ($user = $users->fetch_assoc()): ?>
            <div class="user-box">
                <strong><?= htmlspecialchars($user['name'] . ' ' . $user['last_name']) ?></strong><br>
                <em><?= htmlspecialchars($user['email']) ?></em>

                <!-- Formulář pro přidání záznamu -->
                <form method="POST" style="margin-top:10px;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="idusers_parlament" value="<?= $user['idusers_parlament'] ?>">
                    <label>Sekce:</label>
                    <select name="section" required>
                        <option value="">-- Vyber --</option>
                        <option value="Účast na akci">Účast na akci</option>
                        <option value="Organizátor akce">Organizátor akce</option>
                        <option value="Focení akce">Focení akce</option>
                        <option value="Výbor">Výbor</option>
                    </select><br><br>

                    <label>Poznámka:</label><br>
                    <textarea name="notes" rows="2" cols="50" placeholder="Poznámka..."
                        style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; white-space: nowrap;"></textarea><br><br>
                    <div class="button-container" id="buttonContainer">
                        <button type="submit">Přidat záznam</button>
                    </div>
                </form>

                <!-- Seznam akcí -->
                <div class="note-list">
                    <strong>Záznamy:</strong>
                    <?php
                    $id = (int) $user['idusers_parlament'];
                    $result = $conn->query("SELECT idactions_parlament, section, notes FROM actions_alba_rosa_parlament WHERE idusers_parlament = $id ORDER BY idactions_parlament DESC");
                    if ($result->num_rows === 0): ?>
                        <p><em>Žádné záznamy</em></p>
                    <?php else:
                        while ($row = $result->fetch_assoc()): ?>
                            <div class="note-item">
                                <div class="note-content">
                                    <strong><?= htmlspecialchars($row['section']) ?>:</strong>
                                    <?= nl2br(htmlspecialchars($row['notes'])) ?>
                                </div>
                                <form method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this);">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="idactions_parlament" value="<?= $row['idactions_parlament'] ?>">
                                    <button class="delete-btn" type="submit">Odstranit</button>
                                </form>
                            </div>
                        <?php endwhile;
                    endif; ?>
                </div>
            </div>
        <?php endwhile;
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
        } ?>
    </div>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>