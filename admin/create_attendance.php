<?php
include '../assets/php/config.php';
session_start();

// Kontrola přihlášení
if (!isset($_SESSION['idusers'])) {
    header("Location: ./index.php");
    exit();
} else {
    // Vygenerování unikátního tokenu schůze
    $token = bin2hex(random_bytes(32));
    $datetime = date('Y-m-d H:i:s');

    // Uložení schůze do databáze
    $sql = "INSERT INTO meetings_alba_rosa_parlament (datetime, token) VALUES ('$datetime', '$token')";
    if ($conn->query($sql) === TRUE) {
        $meeting_id = $conn->insert_id;
        $meeting_url = "https://alba-rosa.cz/parlament/attendance.php?token=" . $token;
    } else {
        echo "Chyba: " . $conn->error;
        exit();
    }
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
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>
    <div id="calendar">
        <h2>Prezence byla vytvořena! Sdílejte tento odkaz:</h2>
        <a href="<?= $meeting_url ?>"><?= $meeting_url ?></a>

        <h3>Nebo naskenujte QR kód:</h3>
        <div id="qrcode"></div>
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