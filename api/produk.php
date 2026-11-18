<?php
header("Content-Type: application/json; charset=UTF-8");
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $query = "SELECT p.product_id, p.product_name, p.description, p.price, p.stock, p.image, p.created_at,
                     c.category_id, c.category_name, 
                     u.user_id AS seller_id, u.username AS seller_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id 
              JOIN users u ON p.seller_id = u.user_id 
              WHERE p.product_id = $id";
              
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode([
            "status" => "success",
            "code" => 200,
            "message" => "Detail produk ditemukan",
            "data" => $row
        ], JSON_PRETTY_PRINT);
    } else {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "code" => 404,
            "message" => "Produk dengan ID $id tidak ditemukan"
        ], JSON_PRETTY_PRINT);
    }
} 
else {
    $query = "SELECT p.product_id, p.product_name, p.description, p.price, p.stock, p.image, p.created_at,
                     c.category_id, c.category_name, 
                     u.user_id AS seller_id, u.username AS seller_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id 
              JOIN users u ON p.seller_id = u.user_id";
              
    $result = mysqli_query($conn, $query);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "code" => 200,
        "message" => "Daftar seluruh produk berhasil diambil",
        "total" => count($products),
        "data" => $products
    ], JSON_PRETTY_PRINT);
}
?>