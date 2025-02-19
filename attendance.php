<?php
include 'assets/php/config.php';
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
    <h2>Potvrzení účasti na schůzi</h2>
    <form method="post">
        <label for="email">Zadejte svůj školní e-mail:</label>
        <input type="email" name="email" required>
        <button type="submit">Potvrdit účast</button>
    </form>
</body>

</html>