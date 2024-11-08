<?php
include '../assets/php/config.php';
session_start();
if (!isset($_SESSION['idusers'])) {
    header("Location: ./index.php");
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
        exit();
    } else {
        // Pokud došlo k chybě při provádění dotazu, zobrazíme chybovou zprávu
        echo "error";
        exit();
    }
}
?>