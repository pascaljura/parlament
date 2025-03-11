<?php
include '../assets/php/config.php';
session_start();
ob_start();

if (empty($_POST['notes'])) {
    header("Location: ./?message=" . urlencode("Neproběhly žádné změny, protože nebylo nic zvoleno.") . "&message_type=info-message");
    exit();
}

$success = true;
$message = "Zápis/zápisy byl úspěšně přiděleny k prezenční/m listině/listinám";

foreach ($_POST['notes'] as $attendanceId => $noteId) {
    if ($noteId === "") {
        $sql = "UPDATE attendances_list_alba_rosa_parlament 
                SET idnotes_parlament = NULL 
                WHERE idattendances_list_parlament = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $attendanceId);
    } else {
        // Ověření, zda už zápis není přiřazen jiné prezenční listině
        $checkSql = "SELECT idattendances_list_parlament FROM attendances_list_alba_rosa_parlament 
                     WHERE idnotes_parlament = ? AND idattendances_list_parlament != ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $noteId, $attendanceId);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $success = false;
            $message = "Zápis pro prezenční listinu $attendanceId nebyl uložen, jelikož je již přiřazen k jiné prezenční listině!";
            $checkStmt->close();
            break;
        }
        $checkStmt->close();

        // Pokud zápis není přiřazen, provedeme update
        $sql = "UPDATE attendances_list_alba_rosa_parlament 
                SET idnotes_parlament = ? 
                WHERE idattendances_list_parlament = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $noteId, $attendanceId);
    }

    if (!$stmt->execute()) {
        $success = false;
        $message = "Nastala chyba při aktualizaci zápisu ID: $attendanceId";
        break;
    }
    $stmt->close();
}

$message_type = $success ? "success-message" : "error-message";
header("Location: ./?message=" . urlencode($message) . "&message_type=$message_type");
exit();
ob_end_flush();
