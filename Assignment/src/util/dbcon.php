<?php
error_reporting(E_ALL);

$host = 'localhost';
$user = 'root';
$password = '';
$database = "ecospot";

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect($host, $user, $password, $database);
    $db_connection = $conn;
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>