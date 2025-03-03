<?php
include './assets/php/config.php';
session_start();
ob_start();

// Zrušení všech session proměnných
$_SESSION = array();

// Zničení session
session_destroy();

// Přesměrování na login nebo jinou stránku
header("Location: ./");
exit();

ob_end_flush();

