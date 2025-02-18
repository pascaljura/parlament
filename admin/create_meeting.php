<?php
include '../assets/php/config.php';
session_start();
// Kontrola přihlášení
if (!isset($_SESSION['idusers'])) {
    header("Location: ./index.php");
    exit();
} else {

    // Vygenerování unikátního tokenu schůze
    $token = bin2hex(random_bytes(32));
    $datetime = date('Y-m-d H:i:s');

    // Uložení schůze do databáze
    $sql = "INSERT INTO meetings_alba_rosa_parlament (datetime, token) VALUES ('$datetime', '$token')";
    if ($conn->query($sql) === TRUE) {
        $meeting_id = $conn->insert_id;
        $meeting_url = "./attendance.php?token=" . $token;

        echo "Schůze byla vytvořena! Sdílejte tento odkaz:<br>";
        echo "<a href='$meeting_url'>$meeting_url</a>";
    } else {
        echo "Chyba: " . $conn->error;
    }
}
?>