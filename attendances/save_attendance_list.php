<?php
header('Content-Type: application/json');

require '../assets/php/config.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Neplatný JSON vstup']);
    exit;
}

$idattendances_list_parlament = (int) $data['idattendances_list_parlament'];
$attendanceData = $data['attendanceData'] ?? [];

$presentStudentIDs = array_column(array_filter($attendanceData, fn($s) => $s['present']), 'idusers_parlament');

if (empty($presentStudentIDs)) {
    echo json_encode(['success' => false, 'message' => 'Žádní studenti nejsou přítomní']);
    exit;
}

// Načtení aktuálních přítomných studentů
$currentStudents = [];
$sql = "SELECT idusers_parlament FROM attendances_alba_rosa_parlament WHERE idattendances_list_parlament = $idattendances_list_parlament";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Chyba při dotazu na databázi: ' . $conn->error]);
    exit;
}

while ($row = $result->fetch_assoc()) {
    $currentStudents[] = $row['idusers_parlament'];
}

// Studenty k odstranění (měli přítomnost, ale teď už ji nemají)
$toRemove = array_diff($currentStudents, $presentStudentIDs);
// Studenty k přidání (nově přítomní studenti)
$toAdd = array_diff($presentStudentIDs, $currentStudents);

// Odebrání studentů
if (!empty($toRemove)) {
    $toRemoveSql = implode(',', array_map('intval', $toRemove));
    $query = "DELETE FROM attendances_alba_rosa_parlament WHERE idattendances_list_parlament = $idattendances_list_parlament AND idusers_parlament IN ($toRemoveSql)";
    if (!$conn->query($query)) {
        echo json_encode(['success' => false, 'message' => 'Chyba při mazání: ' . $conn->error]);
        exit;
    }
}

// Přidání nových studentů
if (!empty($toAdd)) {
    $insertValues = [];
    foreach ($toAdd as $id) {
        $insertValues[] = "($idattendances_list_parlament, $id, NOW())";
    }
    $insertSql = "INSERT INTO attendances_alba_rosa_parlament (idattendances_list_parlament, idusers_parlament, time) VALUES " . implode(',', $insertValues);
    if (!$conn->query($insertSql)) {
        echo json_encode(['success' => false, 'message' => 'Chyba při vkládání: ' . $conn->error]);
        exit;
    }
}

// Načtení jmen přidaných/odebraných studentů
$changedStudents = [];
if (!empty($toAdd) || !empty($toRemove)) {
    $idsSql = implode(',', array_map('intval', array_merge($toAdd, $toRemove)));
    $nameQuery = "SELECT idusers_parlament, username FROM users_alba_rosa_parlament WHERE idusers_parlament IN ($idsSql)";
    $nameResult = $conn->query($nameQuery);

    if (!$nameResult) {
        echo json_encode(['success' => false, 'message' => 'Chyba při načítání jmen: ' . $conn->error]);
        exit;
    }

    while ($nameRow = $nameResult->fetch_assoc()) {
        $id = $nameRow['idusers_parlament'];
        $changedStudents[$id] = $nameRow['username'];
    }
}

// Vytvoření zprávy s formátováním seznamu
$message = "Prezenční listina byla uložena.<br>";

if (!empty($toAdd) || !empty($toRemove)) {
    if (!empty($toAdd)) {
        $message .= "<p><strong>Přidáni:</strong><ol>";
        foreach ($toAdd as $id) {
            $message .= "<li>" . htmlspecialchars($changedStudents[$id]) . "</li>";
        }
        $message .= "</ol></p>";
    }

    if (!empty($toRemove)) {
        $message .= "<p><strong>Odebráni:</strong><ol>";
        foreach ($toRemove as $id) {
            $message .= "<li>" . htmlspecialchars($changedStudents[$id]) . "</li>";
        }
        $message .= "</ol></p>";
    }

}

echo json_encode([
    'success' => true,
    'message' => $message
]);
?>