<?php
require '../assets/php/config.php';

if (empty($_POST['notes'])) {
    // Pokud není nic v POSTu, přesměrujeme s hláškou
    header("Location: ./?message=" . urlencode("Neproběhly žádné změny, protože nebylo nic zvoleno.") . "&message_type=success-message");
    exit();
}

$success = true; // sleduje, jestli všechno proběhlo OK
$message = "Zápis/zápisy byl úspěšně přiděleny k prezenční/m listině/listinám"; // výchozí hláška pro success

foreach ($_POST['notes'] as $attendanceId => $noteId) {
    if ($noteId === "") {
        // Odpojení zápisu (nastavíme NULL)
        $sql = "UPDATE attendances_list_alba_rosa_parlament 
                SET idnotes_parlament = NULL 
                WHERE idattendances_list_parlament = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $attendanceId);
    } else {
        // Update na vybraný zápis
        $sql = "UPDATE attendances_list_alba_rosa_parlament 
                SET idnotes_parlament = ? 
                WHERE idattendances_list_parlament = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $noteId, $attendanceId);
    }

    if (!$stmt->execute()) {
        // Pokud update failne, nastavíme error a ukončíme
        $success = false;
        $message = "Nastala chyba při aktualizaci zápisu ID: $attendanceId";
        break;
    }
    $stmt->close();
}

$message_type = $success ? "success-message" : "error-message";

// Přesměrování s výslednou zprávou
header("Location: ./?message=" . urlencode($message) . "&message_type=$message_type");
exit();
