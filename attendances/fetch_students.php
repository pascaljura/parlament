<?php
require '../assets/php/config.php';

$idattendances_list_parlament = isset($_GET['idattendances_list_parlament']) ? (int) $_GET['idattendances_list_parlament'] : 0;

$students = [];
$present_students = [];

// Načtení všech studentů s časem přítomnosti (pokud existuje)
$sql_all = "
    SELECT u.idusers_parlament, 
           CONCAT(u.username, ' - ', COALESCE(a.time, 'nepřítomen')) AS username
    FROM users_alba_rosa_parlament u
    LEFT JOIN attendances_alba_rosa_parlament a 
        ON u.idusers_parlament = a.idusers_parlament 
        AND a.idattendances_list_parlament = $idattendances_list_parlament
    ORDER BY u.username
";
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
?>