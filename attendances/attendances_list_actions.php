<?php
session_start();
ob_start();
include '../assets/php/config.php';

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
</head>

<body>
    <div id="calendar">
        <?php
        // Kontrola přihlášení
        if (!isset($_SESSION['idusers_parlament'])) {
            header("Location: ./index.php");
            exit();
        } else {
            // Funkce pro generování QR kódu
            function generateQRCode($url)
            {
                echo ' <h2>Sdílení prezenční listiny vytvořeno! Sdílejte tento odkaz:</h2>';
                echo '<a href="' . $url . '">' . $url . '</a>';
                echo '<h3>Nebo naskenujte QR kód:</h3>';
                echo '<div id="qrcode"></div>';
                echo '<script>
                    var qrCode = new QRCode(document.getElementById("qrcode"), {
                        text: "' . $url . '",
                        width: 256,
                        height: 256,
                        colorDark: "#000000",
                        colorLight: "rgba(255, 255, 255, 0)"
                    });
                    document.getElementById("qrcode").style.width = "30%";
                    document.getElementById("qrcode").style.maxWidth = "30%";
                    document.getElementById("qrcode").style.height = "auto";
                  </script>';
            }

            // Získání ID z URL parametru
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $idattendances_list_parlament = isset($_GET['idattendances_list_parlament']) ? $_GET['idattendances_list_parlament'] : null;

            // Proveďte akci podle typu akce (delete, end nebo qr)
            // Proveďte akci podle typu akce (create, delete, end nebo qr)
            if (($idattendances_list_parlament && $idattendances_list_parlament != null) && ($action && $action != null)) {
                if ($action == 'delete' && $delete_attendances == '1') {
                    // SQL pro smazání záznamu
                    $stmt = $conn->prepare("DELETE FROM `attendances_list_alba_rosa_parlament` WHERE `idattendances_list_parlament` = ?");
                    $stmt->bind_param("i", $idattendances_list_parlament);
                    $stmt->execute();
                    $stmt->close();

                    header("Location: ./?message=Záznam+byl+úspěšně+smazán&message_type=success-message");
                    exit();

                } elseif ($action == 'end' && $end_attendances == '1') {
                    // SQL pro změnu stavu na 0 (Ukončeno)
                    $stmt = $conn->prepare("UPDATE `attendances_list_alba_rosa_parlament` SET `active` = 0 WHERE `idattendances_list_parlament` = ?");
                    $stmt->bind_param("i", $idattendances_list_parlament);
                    $stmt->execute();
                    $stmt->close();

                    header("Location: ./?message=Stav+byl+úspěšně+změněn+na+Ukončeno&message_type=success-message");
                    exit();

                } elseif ($action == 'qr' && $qr_attendances == '1') {
                    // Získání tokenu pro QR kód
                    $stmt = $conn->prepare("SELECT `token` FROM `attendances_list_alba_rosa_parlament` WHERE `idattendances_list_parlament` = ?");
                    $stmt->bind_param("i", $idattendances_list_parlament);
                    $stmt->execute();
                    $stmt->bind_result($token);
                    $stmt->fetch();

                    if ($token) {
                        $meeting_url = "https://alba-rosa.cz/parlament/attendance.php?token=" . $token;
                        generateQRCode($meeting_url);
                    } else {
                        header("Location: ./?message=Záznam+s+tímto+ID+nebyl+nalezen&message_type=error-message");
                        exit();
                    }
                    $stmt->close();

                } else {
                    header("Location: ./?message=Nemáte+povolení+k+této+akci&message_type=error-message");
                    exit();
                }
            } elseif ($action == 'create' && $start_attendances == '1') {
                // Vytvoření nové schůze
                $token = bin2hex(random_bytes(32));
                $datetime = date('Y-m-d H:i:s');

                $sql = "INSERT INTO attendances_list_alba_rosa_parlament (datetime, token) VALUES ('$datetime', '$token')";
                if ($conn->query($sql) === TRUE) {
                    $meeting_id = $conn->insert_id;
                    $meeting_url = "https://alba-rosa.cz/parlament/attendance.php?token=" . $token;
                    generateQRCode($meeting_url);
                } else {
                    echo "Chyba: " . $conn->error;
                    exit();
                }
            }


            // Uzavření připojení
            $conn->close();
        }
        ?>
    </div>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>
<?php
ob_end_flush();
?>