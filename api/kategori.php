<?php
header("Content-Type: application/json; charset=UTF-8");
include 'koneksi.php';

$query = "SELECT category_id, category_name FROM categories";
$result = mysqli_query($conn, $query);

$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

echo json_encode([
    "status" => "success",
    "code" => 200,
    "message" => "Daftar kategori berhasil diambil",
    "total" => count($categories),
    "data" => $categories
], JSON_PRETTY_PRINT);
?>