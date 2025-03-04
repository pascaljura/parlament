<?php
require '../assets/php/config.php';

$idattendances_list_parlament = isset($_GET['idattendances_list_parlament']) ? (int) $_GET['idattendances_list_parlament'] : 0;
$students = [];

if ($idattendances_list_parlament > 0) {
    $sql = "
        SELECT u.username, DATE_FORMAT(a.time, '%d.%m.%Y %H:%i:%s') AS time
        FROM attendances_alba_rosa_parlament a
        JOIN users_alba_rosa_parlament u ON a.idusers_parlament = u.idusers_parlament
        WHERE a.idattendances_list_parlament = $idattendances_list_parlament
        ORDER BY a.time ASC
    ";

    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($students);
