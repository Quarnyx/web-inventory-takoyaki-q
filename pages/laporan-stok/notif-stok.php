<?php
// Notification for low stock items
require_once 'modules/config.php';
$result = $conn->query("SELECT * FROM v_current_stock WHERE stok_tersedia <= 15");
while ($row = $result->fetch_assoc()): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Perhatian!</strong> Stok barang <strong><?= $row['nama_barang'] ?></strong> (Kode:
        <?= $row['kode_barang'] ?>) tersisa <strong><?= $row['stok_tersedia'] ?></strong>. Segera lakukan pengadaan.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endwhile; ?>