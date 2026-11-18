<?php
session_start();
include 'koneksi.php';
$query = mysqli_query($conn, "SELECT * FROM products ORDER BY product_id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Marketplace Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">🛍️ MiniMarket</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Katalog</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Keranjang</a></li>
                    <?php if (isset($_SESSION['user_id'])) : ?>
                        <?php if ($_SESSION['role'] == 'buyer') : ?>
                            <li class="nav-item"><a class="nav-link" href="my_orders.php">Riwayat Belanja</a></li>
                        <?php else : ?>
                            <li class="nav-item"><a class="nav-link" href="admin_products.php">Kelola Produk (Admin)</a></li>
                        <?php endif; ?>
                        <li class="nav-item ms-lg-3">
                            <span class="text-light small me-2">Halo, <strong><?= $_SESSION['username']; ?></strong></span>
                            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item ms-lg-3">
                            <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
                            <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4 text-center">Katalog Produk Terbaru</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php while ($row = mysqli_fetch_assoc($query)) : ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="image/<?= !empty($row['image']) ? $row['image'] : 'default.jpg'; ?>" class="card-img-top" alt="Produk" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($row['product_name']); ?></h5>
                        <p class="text-danger fw-bold fs-5">Rp <?= number_format($row['price'], 0, ',', '.'); ?></p>
                        <p class="card-text text-muted small"><?= substr(htmlspecialchars($row['description']), 0, 80); ?>...</p>
                        <div class="mt-auto">
                            <a href="detail.php?id=<?= $row['product_id']; ?>" class="btn btn-outline-primary w-100 mb-2">Detail Produk</a>
                            <a href="cart.php?add=<?= $row['product_id']; ?>" class="btn btn-primary w-100">Tambah ke Keranjang</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>