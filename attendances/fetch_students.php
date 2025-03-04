<?php
require '../assets/php/config.php';

$idattendances_list_parlament = isset($_GET['idattendances_list_parlament']) ? (int) $_GET['idattendances_list_parlament'] : 0;

$students = [];
$present_students = [];

// Načtení všech studentů
$sql_all = "SELECT idusers_parlament, username FROM users_alba_rosa_parlament ORDER BY username";
$result_all = $conn->query($sql_all);
while ($row = $result_all->fetch_assoc()) {
    $students[] = $row;
}

// Načtení přítomných studentů
if ($idattendances_list_parlament > 0) {
    $sql_present = "
        SELECT idusers_parlament
        FROM attendances_alba_rosa_parlament
        WHERE idattendances_list_parlament = $idattendances_list_parlament
    ";
    $result_present = $conn->query($sql_present);
    while ($row = $result_present->fetch_assoc()) {
        $present_students[] = (int) $row['idusers_parlament'];
    }
}

header('Content-Type: application/json');
echo json_encode([
    'all_students' => $students,
    'present_students' => $present_students
]);
