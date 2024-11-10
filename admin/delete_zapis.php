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
        if (!isset($_POST['idzapis']) || !is_numeric($_POST['idzapis'])) {
            echo "Chybějící nebo neplatné idzapis záznamu.";
            exit();
        }

        $idzapis = $_POST['idzapis'];

        // Příprava dotazu s parametrem
        $stmt = $conn->prepare("DELETE FROM zapis_alba_rosa_parlament WHERE idzapis = ?");
        $stmt->bind_param("i", $idzapis);

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