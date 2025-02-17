<?php
include '../assets/php/config.php';
session_start();

// Kontrola přihlášení
if (!isset($_SESSION['idusers'])) {
    header("Location: ./index.php");
    exit();
} else {

    // Získání id uživatele ze session
    $idusers = $_SESSION['idusers'];

    // Kontrola přístupu na základě sloupce parlament_access
    $stmtAccess = $conn->prepare("SELECT parlament_access FROM users_alba_rosa WHERE idusers = ?");
    $stmtAccess->bind_param("i", $idusers);
    $stmtAccess->execute();
    $stmtAccess->bind_result($parlament_access);
    $stmtAccess->fetch();
    $stmtAccess->close();

    // Pokud není přístup povolen (parlament_access != 1)
    if ($parlament_access != '1') {
        echo "Chybí přístup";
        exit();
    } else {

        // Ošetření parametru ID
        if (!isset($_POST['idnotes']) || !is_numeric($_POST['idnotes'])) {
            echo "Chybějící nebo neplatné idnotes záznamu.";
            exit();
        }

        $idnotes = $_POST['idnotes'];

        // Příprava dotazu s parametrem
        $stmt = $conn->prepare("DELETE FROM notes_alba_rosa_parlament WHERE idnotes = ?");
        $stmt->bind_param("i", $idnotes);

        // Spuštění dotazu
        if ($stmt->execute()) {
            // Pokud dotaz proběhl úspěšně, vrátíme "success" jako odpověď
            echo "success";
        } else {
            // Pokud došlo k chybě při provádění dotazu, zobrazíme chybovou zprávu
            echo "error";
        }

        // Uzavření dotazu
        $stmt->close();
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
</head>
<body>
<script src="../assets/js/script.js">    </script>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
</body>
</html>