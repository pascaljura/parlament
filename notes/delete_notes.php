<?php
include '../assets/php/config.php';
session_start();
ob_start();

// Kontrola přihlášení
if (!isset($_SESSION['idusers'])) {
    header("Location: ./index.php");
    exit();
} else {

    // Získání id uživatele ze session
    $idusers = $_SESSION['idusers'];

    // Kontrola přístupu na základě sloupce parlament_access_admin
    $stmtAccess = $conn->prepare("SELECT parlament_access_admin FROM users_alba_rosa_parlament WHERE idusers = ?");
    $stmtAccess->bind_param("i", $idusers);
    $stmtAccess->execute();
    $stmtAccess->bind_result($parlament_access_admin);
    $stmtAccess->fetch();
    $stmtAccess->close();

    // Pokud není přístup povolen (parlament_access_admin != 1)
    if ($parlament_access_admin != '1') {
        echo "Chybí přístup";
        exit();
    } else {

        // Ošetření parametru ID
        if (!isset($_POST['idnotes_parlament']) || !is_numeric($_POST['idnotes_parlament'])) {
            // Pokud není záznam, přesměrování s parametrem message a message_type=error
            header("Location: ./?message=Chybějící+ID+zápisu&message_type=error");
            exit();
        }

        $idnotes_parlament = $_POST['idnotes_parlament'];

        // Příprava dotazu s parametrem
        $stmt = $conn->prepare("DELETE FROM notes_alba_rosa_parlament WHERE idnotes_parlament = ?");
        $stmt->bind_param("i", $idnotes_parlament);

        // Spuštění dotazu
        if ($stmt->execute()) {
            // Pokud není záznam, přesměrování s parametrem message a message_type=error
            header("Location: ./?message=Zápis+s+tímto+ID+byl+úspěšně+smazán&message_type=success");
            exit();
        } else {
            // Pokud není záznam, přesměrování s parametrem message a message_type=error
            header("Location: ./?message=Zápis+s+tímto+ID+nebyl+smazán&message_type=success");
            exit();
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
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