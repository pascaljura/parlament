<?php
require '../assets/php/config.php';

$idattendances_list_parlament = isset($_GET['idattendances_list_parlament']) ? (int) $_GET['idattendances_list_parlament'] : 0;

$students = [];
$present_students = [];

// Jeden optimalizovaný dotaz pro studenty i přítomné
$sql = "
    SELECT u.*,
           a.time IS NOT NULL AS is_present, 
           COALESCE(a.time, 'nepřítomen') AS time
    FROM users_alba_rosa_parlament u
    LEFT JOIN attendances_alba_rosa_parlament a 
        ON u.idusers_parlament = a.idusers_parlament 
        AND a.idattendances_list_parlament = ?
    ORDER BY u.last_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idattendances_list_parlament);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $students[] = [
        'id' => (int) $row['idusers_parlament'],
        'name' => $row['name'],
        'last_name' => $row['last_name'],
        'email' => $row['email'],
        'time' => $row['time']
    ];
    if ($row['is_present']) {
        $present_students[] = (int) $row['idusers_parlament'];
    }
}

$stmt->close();

header('Content-Type: application/json');
echo json_encode([
    'students' => $students,
    'present' => $present_students
]);
?>