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
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <link rel="manifest" href="./assets/json/manifest.json">
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
        $sqlattendances = "SELECT idattendances_list_parlament FROM attendances_list_alba_rosa_parlament WHERE token = ? AND active = '1'";

        $stmt = $conn->prepare($sqlattendances);
        if (!$stmt) {
            die("Chyba přípravy dotazu: " . $conn->error);
        }

        $stmt->bind_param("s", $token); // 's' = string
        $stmt->execute();

        $resultattendances = $stmt->get_result();

        if ($resultattendances->num_rows === 0) {
            die("<h2>Prezenční listina je neplatná, nebo byla již ukončena.</h2>");
        }

        // Pokud potřebuješ to ID dál
        $row = $resultattendances->fetch_assoc();
        $id = $row['idattendances_list_parlament'];

        $stmt->close();


        $meeting = $resultattendances->fetch_assoc();
        $idattendances_list_parlament = $meeting['idattendances_list_parlament'];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $conn->real_escape_string($_POST['email']);

            // Ověření existence uživatele a jeho přístup
            $sqlusers = "SELECT idusers_parlament FROM users_alba_rosa_parlament WHERE email = '$email' AND parlament_access_user = '1'";
            $resultusers = $conn->query($sqlusers);

            if ($resultusers->num_rows === 0) {
                die("<h2>Neplatný e-mail nebo nemáte přístup.</h2>");
            }

            $user = $resultusers->fetch_assoc();
            $idusers_parlament = $user['idusers_parlament'];

            // Kontrola, jestli už není zapsaný v docházce pro danou schůzi
            $sqlverify = "SELECT COUNT(*) AS count FROM attendances_alba_rosa_parlament 
                    WHERE idusers_parlament = '$idusers_parlament' AND idattendances_list_parlament = '$idattendances_list_parlament'";

            $checkResult = $conn->query($sqlverify);
            $check = $checkResult->fetch_assoc();

            if ($check['count'] > 0) {
                die("<h2>Již jste potvrdili účast na této schůzi. Není možné se registrovat vícekrát.</h2>");
            }

            // Pokud ještě není zapsaný, pokračuje se vygenerováním nového tokenu
            $newToken = bin2hex(random_bytes(32));
            $expiryTime = date('Y-m-d H:i:s', strtotime('+24 hours')); // Platnost 24 hodin
        
            $sql = "INSERT INTO tokens_alba_rosa_parlament (idusers_parlament, idattendances_list_parlament, token, expires) 
                    VALUES ('$idusers_parlament', '$idattendances_list_parlament', '$newToken', '$expiryTime')";

            if ($conn->query($sql) === TRUE) {
                // Odkaz pro ověření účasti
                $verifyLink = "https://www.alba-rosa.cz/parlament/verify_attendances.php?token=$newToken";

                // Příprava e-mailu
                $subject = "Potvrzení účasti na schůzi";
                $message = '<!DOCTYPE html>
                        <html lang="cs">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Oznámení ze systému AlbaRosa</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    margin: 0;
                                    padding: 0;
                                }
                                .container {
                                    max-width: 600px;
                                    margin: auto;
                                    border-collapse: collapse;
                                }
                                .header {
                                    background-color: #f4f4f4;
                                    padding: 20px;
                                    text-align: center;
                                }
                                .header h1 {
                                    font-size: 32px;
                                    color: #007acc;
                                }
                                .header h2 {
                                    color: #000;
                                    font-size: 16px;
                                }
                                .content {
                                    background-color: #ffffff;
                                    padding: 20px;
                                    border: 1px solid #ddd;
                                }
                                .content ul {
                                    list-style-type: none;
                                    padding: 0;
                                }
                                .content ul li {
                                    margin-bottom: 10px;
                                }
                                .content h2 {
                                    color: #007acc;
                                    font-size: 20px;
                                }
                                .footer {
                                    font-size: 14px;
                                    color: #555;
                                    text-align: center;
                                    padding: 20px;
                                    background-color: #f4f4f4;
                                }
                                .footer strong {
                                    font-weight: bold;
                                }
                            </style>
                        </head>
                        <body>
                            <table class="container">
                                <tbody>
                                    <tr>
                                        <td class="header">
                                            <h1>Oznámení ze systému AlbaRosa</h1>
                                            <h2>Automatizace, která myslí za vás!</h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content">

                                        
        <html>
        <head>
            <title>Potvrzení účasti</title>
        </head>
        <body>
            <p>Dobrý den,</p>
            <p>Potvrďte svou účast kliknutím na následující tlačítko:<br>
            <a href="' . $verifyLink . '"><button style="color: #fff; background-color: #007acc; border: none; padding: 5px; border-radius: 8px; cursor: pointer;">Potvrdit účast!</button></a></p>
            <p>Tento odkaz je platný 24 hodin.</p>
        </body>
        </html>
                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="footer">
                                            <p>Pokud máte jakékoliv otázky či připomínky, neváhejte nás kontaktovat.</p>
                                            <p>S pozdravem,</p>
                                            <p><strong>Podpora systému AlbaRosa</strong></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </body>
                        </html>
        ';

                // Hlavičky pro HTML e-mail
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: noreply@alba-rosa.cz" . "\r\n";

                // Odeslání e-mailu
                if (mail($email, $subject, $message, $headers)) {
                    echo "<h2>E-mail s potvrzením byl odeslán.</h2>";
                } else {
                    echo "<h2>Chyba při odesílání e-mailu.</h2>";
                }
            } else {
                echo "<h2>Chyba při ukládání nového tokenu.</h2>";
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