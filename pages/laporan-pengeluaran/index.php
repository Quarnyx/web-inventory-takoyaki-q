<?php
require_once 'modules/config.php';
// Filter parameters
$tanggal_dari = isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : date('Y-m-01');
$tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : date('Y-m-d');

// Build WHERE clause
$where_clause = "WHERE tanggal_keluar BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";

// Query for stock out
$query = "SELECT 
    pg.*, b.kode_barang, b.nama_barang, b.satuan, u.nama as user_nama
FROM pengeluaran_barang pg
JOIN barang b ON pg.barang_id = b.id
LEFT JOIN users u ON pg.user_id = u.id
$where_clause ORDER BY pg.tanggal_keluar DESC, pg.kode_pengeluaran";
$result = $conn->query($query);

// Summary
$summary_query = "SELECT 
    COUNT(DISTINCT pg.kode_pengeluaran) as total_transaksi,
    SUM(pg.jumlah) as total_qty,
    COUNT(DISTINCT pg.barang_id) as unique_items
FROM pengeluaran_barang pg
JOIN barang b ON pg.barang_id = b.id
$where_clause";
$summary_result = $conn->query($summary_query);
$summary = $summary_result->fetch_assoc();
?>



<!-- Filter Section -->
<div class="card mb-4 d-print-none">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET">
            <input type="hidden" name="page" value="laporan-pengeluaran">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Dari</label>
                    <input type="date" class="form-control" name="tanggal_dari" value="<?= $tanggal_dari ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Sampai</label>
                    <input type="date" class="form-control" name="tanggal_sampai" value="<?= $tanggal_sampai ?>"
                        required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4 d-print-none">
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5><i class="bi bi-receipt-cut"></i> Total Transaksi</h5>
                <h2><?= $summary['total_transaksi'] ?></h2>
                <small>Kode pengeluaran</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5><i class="bi bi-boxes"></i> Total Quantity</h5>
                <h2><?= number_format($summary['total_qty'], 0, ',', '.') ?></h2>
                <small>Unit keluar</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h5><i class="bi bi-tags"></i> Jenis Barang</h5>
                <h2><?= $summary['unique_items'] ?></h2>
                <small>Item unik</small>
            </div>
        </div>
    </div>
</div>

<!-- Report Table -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center d-print-none">
        <h5 class="mb-0"><i class="bi bi-table"></i> Detail Pengeluaran</h5>
        <div>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>
    <div class="card-body">
        <h3 class="text-center mt-10">Laporan Pengeluaran Stok</h3>
        <h5 class="text-center mb-1">Periode: <?= $tanggal_dari ?> - <?= $tanggal_sampai ?></h5>
        <hr />
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode Pengeluaran</th>
                            <th width="15%">Barang</th>
                            <th width="8%">Satuan</th>
                            <th width="10%" class="text-end">Jumlah</th>
                            <th width="15%">Tujuan</th>
                            <th width="10%">User</th>
                            <th width="15%">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= $row['kode_pengeluaran'] ?></td>
                                <td><?= $row['nama_barang'] ?></td>
                                <td class="text-center"><?= $row['satuan'] ?></td>
                                <td class="text-end"><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td><?= $row['tujuan_barang'] ?></td>
                                <td><?= $row['user_nama'] ?? '-' ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_keluar'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Tidak ada data pengeluaran pada periode ini.</div>
        <?php endif; ?>
    </div>
</div>