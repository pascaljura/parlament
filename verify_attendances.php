<?php
include 'assets/php/config.php';
session_start();
ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <title>Parlament na Purkyňce</title>
    <link rel="manifest" href="./assets/json/manifest.json">
    <link rel="stylesheet" href="./assets/css/style.css">

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


        if (!isset($_GET['token'])) {
            die("<h2>Neplatný nebo chybějící token.</h2>");
        }

        $token = $conn->real_escape_string($_GET['token']);

        // Ověření platnosti tokenu
        $sql = "SELECT idtokens_parlament, idusers_parlament, idattendances_list_parlament, expires FROM tokens_alba_rosa_parlament WHERE token = '$token'";
        $result = $conn->query($sql);

        if ($result->num_rows === 0) {
            die("<h2>Neplatný nebo použitý token.</h2>");
        }

        $tokenData = $result->fetch_assoc();

        // Kontrola expirace tokenu
        if (strtotime($tokenData['expires']) < time()) {
            die("<h2>Token vypršel.");
        }

        $idusers_parlament = $tokenData['idusers_parlament'];
        $idattendances_list_parlament = $tokenData['idattendances_list_parlament'];

        // Zapsání účasti
        $sql = "INSERT INTO attendances_alba_rosa_parlament (idusers_parlament, idattendances_list_parlament, time) VALUES ('$idusers_parlament', '$idattendances_list_parlament', NOW())";
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
<?php
ob_end_flush();
?>