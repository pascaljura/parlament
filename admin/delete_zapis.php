<?php
include '../assets/php/config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./index.php");
    exit();
} else {

    // Ošetření parametru ID
    if (!isset($_POST['id_zapis']) || !is_numeric($_POST['id'])) {
        echo "Chybějící nebo neplatné id_zapis záznamu.";
        exit();
    }

    $id_zapis = $_POST['id_zapis'];

    // Příprava dotazu s parametrem
    $stmt = $conn->prepare("DELETE FROM zapis WHERE id_zapis = ?");
    $stmt->bind_param("i", $id_zapis);

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