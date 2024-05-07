<?php
include '../assets/php/config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./index.php");
    exit();
} else {

    // Ošetření parametru ID
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "Chybějící nebo neplatné ID záznamu.";
        exit();
    }

    $id = $_POST['id'];

    // Příprava dotazu s parametrem
    $stmt = $conn->prepare("DELETE FROM zapis WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Spuštění dotazu
    if ($stmt->execute()) {
        // Pokud dotaz proběhl úspěšně, vrátíme "success" jako odpověď
        echo "Success";
        exit();
    } else {
        // Pokud došlo k chybě při provádění dotazu, zobrazíme chybovou zprávu
        echo "Error";
        exit();
    }
}
?>