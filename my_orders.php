<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$buyer_id = intval($_SESSION['user_id']);
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE buyer_id = $buyer_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Belanja Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🛍️ MiniMarket</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Katalog</a>
                <a class="nav-link" href="cart.php">Keranjang</a>
                <a class="nav-link active" href="my_orders.php">Riwayat Belanja</a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <h2 class="mb-4">Riwayat Transaksi Pesanan Anda</h2>
        <?php if (mysqli_num_rows($orders) === 0) : ?>
            <div class="alert alert-info">Anda belum memiliki riwayat transaksi pesanan. <a href="index.php">Mulai Belanja</a></div>
        <?php else : ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Tanggal</th>
                                <th>Detail Produk yang Dibeli</th>
                                <th>Total Pembayaran</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($o = mysqli_fetch_assoc($orders)) : 
                                $order_id = $o['order_id'];
                                $items_query = mysqli_query($conn, "SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = $order_id");
                            ?>
                            <tr>
                                <td>#ORD-<?= $o['order_id']; ?></td>
                                <td><?= $o['created_at']; ?></td>
                                <td>
                                    <ul class="mb-0 ps-3">
                                        <?php while ($item = mysqli_fetch_assoc($items_query)) : ?>
                                            <li>
                                                <strong><?= htmlspecialchars($item['product_name']); ?></strong> 
                                                (<?= $item['quantity']; ?>x) - Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </td>
                                <td class="fw-bold text-danger">Rp <?= number_format($o['total_amount'], 0, ',', '.'); ?></td>
                                <td><span class="badge bg-warning text-dark"><?= $o['order_status']; ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>