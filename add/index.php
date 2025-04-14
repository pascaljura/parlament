<?php
include '../assets/php/config.php';
session_start();
ob_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Parlament na Purkyňce</title>
    <link rel="manifest" href="./assets/json/manifest.json">
    <meta content="Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#5481aa" data-react-helmet="true" name="theme-color" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
</head>

<body>
    <div id="calendar">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Hashování hesla
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // SQL dotaz - ukládáme jen email, username a hash hesla
            $sql = "INSERT INTO users_alba_rosa_parlament (email, username, password) VALUES (?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sss", $email, $username, $passwordHash);

                if ($stmt->execute()) {
                    echo "<h2>Uživatel úspěšně uložen.</h2>";
                } else {
                    echo "<h2>Chyba při ukládání:</h2><br>" . htmlspecialchars($stmt->error);
                }

                $stmt->close();
            } else {
                echo "<h2>Chyba při přípravě dotazu:</h2><br>" . htmlspecialchars($conn->error);
            }

            $conn->close();
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="email">E-mail:</label>
            <input type="email" name="email" required><br>

            <label for="username">Jméno a příjmení</label>
            <input type="text" name="username" required><br>

            <label for="password">Heslo:</label>
            <input type="password" name="password" required><br>

            <input type="submit" value="Uložit uživatele">
        </form>
    </div>
</body>

</html>
<?php
ob_end_flush();
?>