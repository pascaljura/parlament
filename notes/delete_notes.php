<?php
include '../assets/php/config.php';
session_start();
ob_start();

// Kontrola přihlášení
if (!isset($_SESSION['idusers_parlament'])) {
    echo "error";
    exit();
} else {
    // Získání id uživatele ze session
    $idusers_parlament = $_SESSION['idusers_parlament'];

    // Kontrola přístupu na základě sloupce parlament_access_admin
    $stmtAccess = $conn->prepare("SELECT parlament_access_admin FROM users_alba_rosa_parlament WHERE idusers_parlament = ?");
    $stmtAccess->bind_param("i", $idusers_parlament);
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
            echo "error";
            exit();
        }

        $idnotes_parlament = $_POST['idnotes_parlament'];

        // Příprava dotazu s parametrem
        $stmt = $conn->prepare("DELETE FROM notes_alba_rosa_parlament WHERE idnotes_parlament = ?");
        $stmt->bind_param("i", $idnotes_parlament);

        // Spuštění dotazu
        if ($stmt->execute()) {
            echo "success";  // Odeslání odpovědi zpět do AJAXu
        } else {
            echo "error";    // Odeslání odpovědi zpět do AJAXu, pokud došlo k chybě
        }

        // Uzavření dotazu
        $stmt->close();
    }
}
?>