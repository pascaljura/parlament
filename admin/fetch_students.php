<?php
require '../assets/php/config.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$students = [];

if ($id > 0) {
    $sql = "
        SELECT u.username, DATE_FORMAT(a.time, '%d.%m.%Y %H:%i:%s') AS time
        FROM attendances_alba_rosa_parlament a
        JOIN users_alba_rosa_parlament u ON a.idusers = u.idusers
        WHERE a.idmeetings_parlament = $id
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
