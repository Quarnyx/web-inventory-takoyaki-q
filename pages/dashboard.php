<?php
require_once 'modules/config.php';
// Check if user is logged in (assuming basic auth)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Dashboard queries using views
$current_stock_query = "SELECT 
    COUNT(*) as total_items,
    SUM(stok_tersedia) as total_qty,
    SUM(nilai_stok) as total_value,
    SUM(CASE WHEN status_stok IN ('KRITIS', 'HABIS') THEN 1 ELSE 0 END) as low_stock
FROM v_current_stock";
$stock_result = $conn->query($current_stock_query);
$stock_summary = $stock_result->fetch_assoc();

// Recent movements (last 10 from view)
$recent_query = "SELECT * FROM v_stock_movements ORDER BY tanggal_transaksi DESC LIMIT 10";
$recent_result = $conn->query($recent_query);

// Pre-order pending count
$pending_preorder_query = "SELECT COUNT(*) as pending FROM pre_order_barang WHERE status = 'dipesan'";
$pending_result = $conn->query($pending_preorder_query);
$pending_preorder = $pending_result->fetch_assoc()['pending'];

// Low stock alert items
$low_stock_query = "SELECT kode_barang, nama_barang, stok_tersedia, status_stok 
                    FROM v_stock_alert LIMIT 5";
$low_stock_result = $conn->query($low_stock_query);
?>



<!-- Summary Cards -->
<div class="row mb-4 mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5><i class="bi bi-boxes"></i> Total Barang</h5>
                <h2><?= $stock_summary['total_items'] ?></h2>
                <small>Jenis item tersedia</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5><i class="bi bi-collection"></i> Total Stok</h5>
                <h2><?= number_format($stock_summary['total_qty'], 0, ',', '.') ?></h2>
                <small>Unit keseluruhan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5><i class="bi bi-currency-dollar"></i> Nilai Stok</h5>
                <h2>Rp <?= number_format($stock_summary['total_value'], 0, ',', '.') ?></h2>
                <small>Estimasi nilai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5><i class="bi bi-exclamation-triangle"></i> Stok Rendah</h5>
                <h2><?= $stock_summary['low_stock'] ?></h2>
                <small>Item kritis</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5><i class="bi bi-clock"></i> Pre-Order Tertunda</h5>
                <h2><?= $pending_preorder ?></h2>
                <small>Pesanan menunggu</small>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <?php if ($low_stock_result->num_rows > 0): ?>
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h6><i class="bi bi-alert"></i> Peringatan Stok Rendah</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Barang</th>
                                    <th>Stok Tersedia</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($low = $low_stock_result->fetch_assoc()): ?>
                                    <tr class="table-danger">
                                        <td><?= $low['kode_barang'] ?></td>
                                        <td><?= $low['nama_barang'] ?></td>
                                        <td class="text-end"><?= number_format($low['stok_tersedia'], 0, ',', '.') ?></td>
                                        <td><span class="badge bg-danger"><?= $low['status_stok'] ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Transaksi Terkini</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_result && $recent_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="recentTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Sumber</th>
                                    <th>Kode Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $recent_result->fetch_assoc()): ?>
                                    <tr class="<?= $row['tipe_transaksi'] == 'IN' ? 'table-success' : 'table-warning' ?>">
                                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])) ?></td>
                                        <td>
                                            <span
                                                class="badge <?= $row['tipe_transaksi'] == 'IN' ? 'bg-success' : 'bg-warning' ?>">
                                                <?= $row['tipe_transaksi'] ?> (<?= $row['jenis_transaksi'] ?>)
                                            </span>
                                        </td>
                                        <td><?= $row['nama_barang'] ?></td>
                                        <td class="text-end"><?= number_format($row['jumlah'], 0, ',', '.') ?>
                                            <?= $row['satuan'] ?>
                                        </td>
                                        <td><?= $row['sumber'] ?></td>
                                        <td><?= $row['kode_transaksi'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada transaksi.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>