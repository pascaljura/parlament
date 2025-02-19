<?php
include '../assets/php/config.php';
session_start();


    if (!isset($_GET['token'])) {
        die("Neplatný přístup.");
    }

    $token = $_GET['token'];

    // Získání schůze podle tokenu
    $sql = "SELECT idmeetings_parlament FROM meetings_alba_rosa_parlament WHERE token = '$token'";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        die("Neplatný nebo vypršelý odkaz.");
    }

    $meeting = $result->fetch_assoc();
    $idmeetings_parlament = $meeting['idmeetings_parlament'];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = $conn->real_escape_string($_POST['email']);

        // Ověření existence uživatele
        $sql = "SELECT idusers FROM users_alba_rosa WHERE email = '$email'";
        $result = $conn->query($sql);
        if ($result->num_rows === 0) {
            die("Neplatný e-mail. Nemáte přístup.");
        }

        $user = $result->fetch_assoc();
        $idusers = $user['idusers'];

        // Uložení docházky
        $sql = "INSERT INTO attendances_alba_rosa_parlament (idusers, idmeetings_parlament, time) VALUES ('$idusers', '$idmeetings_parlament', NOW())";
        if ($conn->query($sql) === TRUE) {
            echo "Úspěšně jste potvrdili svou účast!";
        } else {
            echo "Chyba při ukládání účasti: " . $conn->error;
        }
        exit;
    }

?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Potvrzení účasti</title>
</head>

<body>
    <h2>Potvrzení účasti na schůzi</h2>
    <form method="post">
        <label for="email">Zadejte svůj školní e-mail:</label>
        <input type="email" name="email" required>
        <button type="submit">Potvrdit účast</button>
    </form>
</body>

</html>