<?php
session_start();
include 'koneksi.php';

if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header("Location: cart.php");
    exit;
}

if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

if (isset($_POST['checkout'])) {
    // Validasi Login: Wajib login sebelum checkout
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Silakan login terlebih dahulu untuk melakukan checkout!'); window.location='login.php';</script>";
        exit;
    }

    if (!empty($_SESSION['cart'])) {
        $buyer_id = intval($_SESSION['user_id']); // Mengambil ID dari akun yang sedang login
        $total_amount = 0;
        
        // 1. Hitung total belanja
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $pid = intval($pid);
            $qty = intval($qty);
            $res = mysqli_query($conn, "SELECT price FROM products WHERE product_id = $pid");
            if ($res && $p = mysqli_fetch_assoc($res)) {
                $total_amount += $p['price'] * $qty;
            }
        }

        $query_order = "INSERT INTO orders (buyer_id, total_amount, order_status) VALUES ($buyer_id, $total_amount, 'Pending')";
        if (mysqli_query($conn, $query_order)) {
            $order_id = mysqli_insert_id($conn);

            foreach ($_SESSION['cart'] as $pid => $qty) {
                $pid = intval($pid);
                $qty = intval($qty);
                $res = mysqli_query($conn, "SELECT price FROM products WHERE product_id = $pid");
                if ($res && $p = mysqli_fetch_assoc($res)) {
                    $subtotal = $p['price'] * $qty;
                    
                    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES ($order_id, $pid, $qty, $subtotal)");
                    mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE product_id = $pid");
                }
            }

            unset($_SESSION['cart']);
            echo "<script>alert('Pesanan berhasil dibuat dan tersimpan ke riwayat Anda!'); window.location='my_orders.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal memproses pesanan: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🛍️ MiniMarket</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Katalog</a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <h2 class="mb-4">Keranjang Belanja Anda</h2>
        <?php if (empty($_SESSION['cart'])) : ?>
            <div class="alert alert-info">Keranjang belanja Anda masih kosong. <a href="index.php">Mulai Belanja</a></div>
        <?php else : ?>
            <table class="table table-bordered bg-white shadow-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_total = 0;
                    foreach ($_SESSION['cart'] as $id => $qty) :
                        $q = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $id");
                        $item = mysqli_fetch_assoc($q);
                        if ($item) {
                            $subtotal = $item['price'] * $qty;
                            $grand_total += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']); ?></td>
                        <td>Rp <?= number_format($item['price'], 0, ',', '.'); ?></td>
                        <td><?= $qty; ?></td>
                        <td>Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                        <td><a href="cart.php?del=<?= $id; ?>" class="btn btn-danger btn-sm">Hapus</a></td>
                    </tr>
                    <?php 
                        }
                    endforeach; 
                    ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm">
                <h4>Total: Rp <?= number_format($grand_total, 0, ',', '.'); ?></h4>
                <form method="POST">
                    <button type="submit" name="checkout" class="btn btn-success btn-lg">Buat Pesanan (Checkout)</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>