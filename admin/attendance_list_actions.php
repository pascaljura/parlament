<?php
session_start();
ob_start();
include '../assets/php/config.php';
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
        <?php
        // Kontrola přihlášení
        if (!isset($_SESSION['idusers'])) {
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
                    document.getElementById("qrcode").style.width = "100%";
                    document.getElementById("qrcode").style.maxWidth = "100%";
                    document.getElementById("qrcode").style.height = "auto";
                  </script>';
            }

            // Získání ID z URL parametru
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $idattendances_list_parlament = isset($_GET['idattendances_list_parlament']) ? $_GET['idattendances_list_parlament'] : null;

            // Proveďte akci podle typu akce (delete, end nebo qr)
            if (($idattendances_list_parlament && $idattendances_list_parlament != null) && ($action && $action != null)) {
                if ($action == 'delete') {
                    // SQL pro smazání záznamu
                    $stmt = $conn->prepare("DELETE FROM `attendances_list_alba_rosa_parlament` WHERE `idattendances_list_parlament` = ?");
                    $stmt->bind_param("i", $idattendances_list_parlament); // "i" znamená, že id je integer
                    $stmt->execute();
                    $stmt->close();

                    // Přesměrování s parametrem message a message_type=success
                    header("Location: ./?message=Záznam+byl+úspěšně+smazán&message_type=success");
                    exit();
                } elseif ($action == 'end') {
                    // SQL pro změnu stavu na 0 (Ukončeno)
                    $stmt = $conn->prepare("UPDATE `attendances_list_alba_rosa_parlament` SET `active` = 0 WHERE `idattendances_list_parlament` = ?");
                    $stmt->bind_param("i", $idattendances_list_parlament);
                    $stmt->execute();
                    $stmt->close();

                    // Přesměrování s parametrem message a message_type=success
                    header("Location: ./?message=Stav+byl+úspěšně+změněn+na+Ukončeno&message_type=success");
                    exit();
                } elseif ($action == 'qr') {
                    // Získání tokenu pro QR kód
                    $stmt = $conn->prepare("SELECT `token` FROM `attendances_list_alba_rosa_parlament` WHERE `idattendances_list_parlament` = ?");
                    $stmt->bind_param("i", $idattendances_list_parlament);
                    $stmt->execute();
                    $stmt->bind_result($token);
                    $stmt->fetch();

                    if ($token) {
                        // URL pro QR kód
                        $meeting_url = "https://alba-rosa.cz/parlament/attendance.php?token=" . $token;
                        generateQRCode($meeting_url);
                    } else {
                        // Pokud není záznam, přesměrování s parametrem message a message_type=error
                        header("Location: ./?message=Záznam+s+tímto+ID+nebyl+nalezen&message_type=error");
                        exit();
                    }
                    $stmt->close();
                }
            }

            // Uzavření připojení
            $conn->close();
        }
        ?>
    </div>
</body>

</html>