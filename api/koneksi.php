<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_marketplace";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        "status" => "error",
        "code" => 500,
        "message" => "Koneksi database gagal: " . mysqli_connect_error()
    ]);
    exit();
}
?>