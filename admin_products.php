<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'buyer') {
    echo "<script>alert('Akses ditolak! Halaman ini khusus admin.'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['add_product'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $desc  = mysqli_real_escape_string($conn, $_POST['desc']);
    
    $img = '';
    if (!empty($_FILES['image']['name'])) {
        $img = time() . '_' . basename($_FILES['image']['name']);
        $target_dir = 'image/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $img);
    }

    mysqli_query($conn, "INSERT INTO products (category_id, seller_id, product_name, description, price, stock, image) VALUES (1, 1, '$name', '$desc', $price, $stock, '$img')");
    header("Location: admin_products.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = mysqli_query($conn, "SELECT image FROM products WHERE product_id = $id");
    $p = mysqli_fetch_assoc($res);
    if (!empty($p['image']) && file_exists('image/' . $p['image'])) {
        unlink('image/' . $p['image']);
    }
    mysqli_query($conn, "DELETE FROM products WHERE product_id = $id");
    header("Location: admin_products.php");
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY product_id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🛍️ MiniMarket - Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Lihat Katalog Toko</a>
                <a class="nav-link text-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <h2 class="mb-4">Kelola Data Produk</h2>
        
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <h4 class="card-title mb-3">Tambah Produk Baru</h4>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="desc" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Produk</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary">Simpan Produk</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Daftar Produk</h4>
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($p = mysqli_fetch_assoc($products)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <img src="image/<?= !empty($p['image']) ? $p['image'] : 'default.jpg'; ?>" width="50" height="50" style="object-fit: cover;" class="rounded">
                            </td>
                            <td><?= htmlspecialchars($p['product_name']); ?></td>
                            <td>Rp <?= number_format($p['price'], 0, ',', '.'); ?></td>
                            <td><?= $p['stock']; ?></td>
                            <td>
                                <a href="admin_products.php?delete=<?= $p['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>