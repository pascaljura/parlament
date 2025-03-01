<?php
include 'assets/php/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/favicon.ico" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
</head>

<body>
    <div id="calendar">

        <?php


        if (!isset($_GET['token'])) {
            die("<h2>Neplatný nebo chybějící token.</h2>");
        }

        $token = $conn->real_escape_string($_GET['token']);

        // Ověření platnosti tokenu
        $sql = "SELECT idtokens_parlament, idusers, idmeetings_parlament, expires FROM tokens_alba_rosa_parlament WHERE token = '$token'";
        $result = $conn->query($sql);

        if ($result->num_rows === 0) {
            die("<h2>Neplatný nebo použitý token.</h2>");
        }

        $tokenData = $result->fetch_assoc();

        // Kontrola expirace tokenu
        if (strtotime($tokenData['expires']) < time()) {
            die("<h2>Token vypršel.");
        }

        $idusers = $tokenData['idusers'];
        $idmeetings_parlament = $tokenData['idmeetings_parlament'];

        // Zapsání účasti
        $sql = "INSERT INTO attendances_alba_rosa_parlament (idusers, idmeetings_parlament, time) VALUES ('$idusers', '$idmeetings_parlament', NOW())";
        if ($conn->query($sql) === TRUE) {
            echo "<h2>Účast byla úspěšně potvrzena!</h2>";
        } else {
            echo "<h2>Chyba při zapisování účasti.</h2>";
        }

        // Smazání tokenu po použití
        $sql = "DELETE FROM tokens_alba_rosa_parlament WHERE idtokens_parlament = '{$tokenData['idtokens_parlament']}'";
        $conn->query($sql);

        $conn->close();
        ?>
    </div>
</body>

</html>