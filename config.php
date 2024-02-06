<?php
// Přístupové údaje k databázi (Localhost)
//$servername = "localhost";
//$username = 'root';
//$password = "";
//$dbname = "parla";

// Přístupové údaje k databázi (WEDOS)
$servername = "md75.wedos.net";
$username = 'a237642_parla';
$password = ",l1wQK59hP";
$dbname = "d237642_parla";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    die("Chyba při připojování k databázi: " . $conn->connect_error);
}
?>