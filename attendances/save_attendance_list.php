<?php
require '../assets/php/config.php';

$data = json_decode(file_get_contents('php://input'), true);
$idattendances_list_parlament = (int) $data['idattendances_list_parlament'];
$attendanceData = $data['attendanceData'] ?? [];

$presentStudentIDs = array_column(array_filter($attendanceData, fn($s) => $s['present']), 'idusers_parlament');

// Načti aktuální přítomné
$currentStudents = [];
$sql = "SELECT idusers_parlament FROM attendances_alba_rosa_parlament WHERE idattendances_list_parlament = $idattendances_list_parlament";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $currentStudents[] = $row['idusers_parlament'];
}

// Studenty k odstranění
$toRemove = array_diff($currentStudents, $presentStudentIDs);
// Studenty k přidání
$toAdd = array_diff($presentStudentIDs, $currentStudents);

// Odebrání studentů
if (!empty($toRemove)) {
    $toRemoveSql = implode(',', array_map('intval', $toRemove));
    $conn->query("DELETE FROM attendances_alba_rosa_parlament WHERE idattendances_list_parlament = $idattendances_list_parlament AND idusers_parlament IN ($toRemoveSql)");
}

// Přidání nových studentů
if (!empty($toAdd)) {
    $insertValues = [];
    foreach ($toAdd as $id) {
        $insertValues[] = "($idattendances_list_parlament, $id, NOW())";
    }
    $insertSql = "INSERT INTO attendances_alba_rosa_parlament (idattendances_list_parlament, idusers_parlament, time) VALUES " . implode(',', $insertValues);
    $conn->query($insertSql);
}

echo json_encode(['success' => true, 'message' => 'Prezenční listina uložena']);
