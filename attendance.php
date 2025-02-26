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
            die("<h2>Neplatný přístup.</h2>");
        }

        $token = $_GET['token'];

        // Získání ID schůze podle tokenu
        $sql = "SELECT idattendances_list_parlament FROM attendances_list_alba_rosa_parlament WHERE token = '$token'";
        $result = $conn->query($sql);
        if ($result->num_rows === 0) {
            die("<h2>Neplatný nebo vypršelý odkaz.</h2>");
        }

        $meeting = $result->fetch_assoc();
        $idattendances_list_parlament = $meeting['idattendances_list_parlament'];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $conn->real_escape_string($_POST['email']);

            // Ověření existence uživatele
            $sql = "SELECT idusers FROM users_alba_rosa WHERE email = '$email'";
            $result = $conn->query($sql);
            if ($result->num_rows === 0) {
                die("<h2>Neplatný e-mail. Nemáte přístup.</h2>");
            }

            $user = $result->fetch_assoc();
            $idusers = $user['idusers'];

            // Generování unikátního tokenu
            $newToken = bin2hex(random_bytes(32));
            $expiryTime = date('Y-m-d H:i:s', strtotime('+24 hours')); // Platnost 24 hodin
        
            // Uložení tokenu do tabulky
            $sql = "INSERT INTO tokens_alba_rosa_parlament (idusers, idmeetings_parlament, token, expires) 
            VALUES ('$idusers', '$idattendances_list_parlament', '$newToken', '$expiryTime')";

            if ($conn->query($sql) === TRUE) {
                // Odkaz pro ověření účasti
                $verifyLink = "https://www.alba-rosa.cz/parlament/verify_attendances.php?token=$newToken";

                // Příprava e-mailu
                $subject = "Potvrzení účasti na schůzi";
                $message = "
        <html>
        <head>
            <title>Potvrzení účasti</title>
        </head>
        <body>
            <p>Dobrý den,</p>
            <p>Potvrďte svou účast kliknutím na následující odkaz:</p>
            <p><a href='$verifyLink'>$verifyLink</a></p>
            <p>Tento odkaz je platný 24 hodin.</p>
        </body>
        </html>
        ";

                // Hlavičky pro HTML e-mail
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: noreply@alba-rosa.cz" . "\r\n";

                // Odeslání e-mailu
                if (mail($email, $subject, $message, $headers)) {
                    echo "E-mail s potvrzením byl odeslán.";
                } else {
                    echo "Chyba při odesílání e-mailu.";
                }
            } else {
                echo "Chyba při ukládání nového tokenu";
            }
            exit;
        }
        ?>
        <h2>Potvrzení účasti na schůzi</h2>
        <div class="button-container" id="buttonContainer">
            <form method="post">
                <label for="email" style="font-size: 16px; margin-bottom: 8px;">Zadejte svůj školní e-mail:</label>
                <input type="email" name="email"
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;"
                    required>
                <button type="submit">Potvrdit účast</button>
            </form>
        </div>
    </div>
</body>

</html>