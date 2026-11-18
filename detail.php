<?php
include 'koneksi.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $id");
$product = mysqli_fetch_assoc($result);
if (!$product) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><= htmlspecialchars($product['product_name']); ?> - Detail Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🛍️ MiniMarket</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Kembali ke Katalog</a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <div class="row bg-white p-4 rounded shadow-sm">
            <div class="col-md-5">
                <img src="image/<?= !empty($product['image']) ? $product['image'] : 'default.jpg'; ?>" class="img-fluid rounded" alt="Produk" style="width: 100%; height: 350px; object-fit: cover;">
            </div>
            <div class="col-md-7">
                <h2><?= htmlspecialchars($product['product_name']); ?></h2>
                <h3 class="text-danger my-3">Rp <?= number_format($product['price'], 0, ',', '.'); ?></h3>
                <p class="text-muted">Stok Tersedia: <strong><?= $product['stock']; ?></strong></p>
                <hr>
                <h5>Deskripsi Produk:</h5>
                <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>
                <a href="cart.php?add=<?= $product['product_id']; ?>" class="btn btn-primary btn-lg mt-3">Tambah ke Keranjang Belanja</a>
            </div>
        </div>
    </div>
</body>
</html>