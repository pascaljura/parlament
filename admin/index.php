<?php
include '../config.php';
session_start();

// Kontrola, zda je uživatel již přihlášen
if (isset($_SESSION['user_id'])) {
    header("Location: ./main.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredUsername = $_POST["username"];
    $enteredPassword = $_POST["password"];
    $stmt = $conn->prepare("SELECT id FROM users WHERE name = ? AND password = ?");
    $stmt->bind_param("ss", $enteredUsername, $enteredPassword);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['user_id'] = 1;
        header("Location: ./main.php");
        exit();
    } else {
        $loginError = "Nesprávné přihlašovací údaje.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="https://www.alba-rosa.cz/parlament/logo.png" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <?php
    $headerText = '&#x1F499;・Přihlášení';
    ?>
</head>
<div id="loading-overlay">
    <div id="loading-icon"></div>
</div>

<body>
    <div id="calendar">
        <div class="table-heading">
            <?php echo $headerText; ?>
        </div>
        <?php
        if (isset($loginError)) {
            echo '<div style="color: #FF0000; margin-bottom: 5px;">' . $loginError . '</div>';
        }
        ?>
        <div class="button-container" id="buttonContainer">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="loginForm"
                style="width: 80%; max-width: 400px; margin-bottom: 20px;">
                <label for="username" style="font-size: 16px; margin-bottom: 8px;">Uživatelské jméno:</label>
                <input type="text" name="username" required
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                <label for="password" style="font-size: 16px; margin-bottom: 8px;">Heslo:</label>
                <input type="password" name="password" required
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                <button type="submit">
                    Přihlásit
                </button>
            </form>
        </div>
        <hr color="#3e6181" style="height: 2px; border: none;" />
        <?php
        include '../config.php'; // Připojení k databázi
        
        // Získání dat z tabulky
        $query = "SELECT text FROM other WHERE id = 1";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $text = $row['text'];

            // Výpis HTML s dynamickým obsahem
            echo "$text";
        } else {
            echo 'Chyba při získávání dat z databáze: ' . mysqli_error($conn);
        }

        // Uzavření připojení k databázi
        mysqli_close($conn);
        ?>
    </div>
    <script src="../script.js">
    </script>
</body>

</html>