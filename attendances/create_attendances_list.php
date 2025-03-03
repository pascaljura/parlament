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
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>
    <div id="calendar">
        <div class="overlay" id="overlay" onclick="closeAllMenus()"></div>
        <nav>

            <!-- User Icon (vlevo na mobilu, vpravo na desktopu) -->
            <div class="user-icon" onclick="toggleUserMenu(event)">
                <i class="fa fa-user"></i>
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
                    <a href="../logout.php">Logout</a>
                <?php } else { ?>
                    <a href="../login.php">Login</a>
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
        if (isset($start_attendances) && isset($parlament_access_admin) && $parlament_access_admin == '1' && $start_attendances == '1') {
            // Vygenerování unikátního tokenu schůze
            $token = bin2hex(random_bytes(32));
            $datetime = date('Y-m-d H:i:s');

            // Uložení schůze do databáze
            $sql = "INSERT INTO attendances_list_alba_rosa_parlament (datetime, token) VALUES ('$datetime', '$token')";
            if ($conn->query($sql) === TRUE) {
                $meeting_id = $conn->insert_id;
                $meeting_url = "https://alba-rosa.cz/parlament/attendance.php?token=" . $token;
            } else {
                echo "Chyba: " . $conn->error;
                exit();
            }


            ?>

            <h2>Prezenční listina byla vytvořena! Sdílejte tento odkaz:</h2>
            <a href="<?= $meeting_url ?>"><?= $meeting_url ?></a>

            <h3>Nebo naskenujte QR kód:</h3>
            <div id="qrcode"></div>
        <?php } else {
            echo '<div class="error-message">';
            echo '<i class="fa fa-times" style="margin-right: 5px;"></i> Chybí oprávnění';
            echo '</div>';
        } ?>
    </div>
    <script>
        var qrCode = new QRCode(document.getElementById("qrcode"), {
            text: "<?= $meeting_url ?>",
            width: 256,  // Výchozí velikost QR kódu
            height: 256,
            colorDark: "#000000",
            colorLight: "rgba(255, 255, 255, 0)"
        });

        // Přizpůsobení velikosti pomocí CSS
        document.getElementById("qrcode").style.width = "100%";
        document.getElementById("qrcode").style.maxWidth = "100%"; // Maximální velikost
        document.getElementById("qrcode").style.height = "auto";

    </script>
</body>

</html>
<?php
ob_end_flush();
?>