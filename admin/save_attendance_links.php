<?php
require '../assets/php/config.php';

if (!empty($_POST['notes'])) {
    foreach ($_POST['notes'] as $attendanceId => $noteId) {
        if ($noteId === "") {
            // Pokud nevybrali žádný zápis, nastavíme NULL (odpojení zápisu)
            $sql = "UPDATE attendances_list_alba_rosa_parlament 
                    SET idnotes_parlament = NULL 
                    WHERE idattendances_list_parlament = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $attendanceId);
        } else {
            // Jinak normální update na vybraný zápis
            $sql = "UPDATE attendances_list_alba_rosa_parlament 
                    SET idnotes_parlament = ? 
                    WHERE idattendances_list_parlament = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $noteId, $attendanceId);
        }
        $stmt->execute();
    }
}
