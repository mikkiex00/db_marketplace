<?php
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);
ini_set('display_errors', 0);

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "code" => 405,
        "message" => "Method tidak diizinkan. Gunakan POST."
    ]);
    exit();
}

$json_input = file_get_contents("php://input");
$data = json_decode($json_input, true);

if (!isset($data['buyer_id']) || !isset($data['items']) || !is_array($data['items'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "code" => 400,
        "message" => "Format request tidak valid. 'buyer_id' dan array 'items' wajib diisi."
    ]);
    exit();
}

$buyer_id = intval($data['buyer_id']);
$items = $data['items'];

$buyer_check = mysqli_query($conn, "SELECT user_id FROM users WHERE user_id = $buyer_id AND role = 'buyer'");
if (!$buyer_check || mysqli_num_rows($buyer_check) === 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "code" => 400,
        "message" => "Buyer ID tidak ditemukan atau bukan akun pembeli."
    ]);
    exit();
}

mysqli_begin_transaction($conn);

try {
    $total_amount = 0;
    $processed_items = [];

    foreach ($items as $item) {
        $product_id = intval($item['product_id']);
        $quantity = intval($item['quantity']);

        if ($quantity <= 0) {
            throw new Exception("Jumlah quantity untuk produk ID $product_id tidak valid.");
        }

        $prod_query = mysqli_query($conn, "SELECT price, stock, product_name FROM products WHERE product_id = $product_id");
        if (!$prod_query || mysqli_num_rows($prod_query) === 0) {
            throw new Exception("Produk dengan ID $product_id tidak ditemukan.");
        }

        $prod_data = mysqli_fetch_assoc($prod_query);
        
        if (intval($prod_data['stock']) < $quantity) {
            throw new Exception("Stok tidak mencukupi untuk produk: '{$prod_data['product_name']}' (Sisa stok: {$prod_data['stock']}).");
        }

        $subtotal = floatval($prod_data['price']) * $quantity;
        $total_amount += $subtotal;

        $processed_items[] = [
            "product_id" => $product_id,
            "quantity" => $quantity,
            "subtotal" => $subtotal
        ];
    }

    $insert_order = "INSERT INTO orders (buyer_id, total_amount, order_status) VALUES ($buyer_id, $total_amount, 'Pending')";
    if (!mysqli_query($conn, $insert_order)) {
        throw new Exception("Gagal menyimpan data pesanan ke database: " . mysqli_error($conn));
    }
    
    $order_id = mysqli_insert_id($conn);

    foreach ($processed_items as $pi) {
        $pid = $pi['product_id'];
        $qty = $pi['quantity'];
        $sub = $pi['subtotal'];

        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES ($order_id, $pid, $qty, $sub)");
        mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE product_id = $pid");
    }

    mysqli_commit($conn);

    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "code" => 201,
        "message" => "Pesanan berhasil dibuat dan stok produk telah diperbarui",
        "data" => [
            "order_id" => $order_id,
            "buyer_id" => $buyer_id,
            "total_amount" => $total_amount,
            "order_status" => "Pending"
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "code" => 400,
        "message" => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>