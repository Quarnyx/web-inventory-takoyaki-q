<?php
require_once 'modules/config.php';
// Filter parameters
$tanggal_dari = isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : date('Y-m-01');
$tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : date('Y-m-d');
$supplier_id = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : '';

// Get supplier list
$supplier_query = "SELECT id, nama_supplier FROM suppliers ORDER BY nama_supplier";
$supplier_result = $conn->query($supplier_query);

// Build WHERE clause
$where_clause = "WHERE tanggal_penerimaan BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";
if (!empty($supplier_id)) {
    $where_clause .= " AND supplier_id = '$supplier_id'";
}

// Query for stock in
$query = "SELECT 
    pe.*, b.kode_barang, b.nama_barang, b.satuan, s.nama_supplier
FROM penerimaan_barang pe
JOIN barang b ON pe.barang_id = b.id
JOIN suppliers s ON pe.supplier_id = s.id
$where_clause ORDER BY pe.tanggal_penerimaan DESC, pe.kode_penerimaan";
$result = $conn->query($query);

// Summary
$summary_query = "SELECT 
    COUNT(DISTINCT pe.kode_penerimaan) as total_transaksi,
    SUM(pe.jumlah) as total_qty,
    COUNT(DISTINCT pe.barang_id) as unique_items
FROM penerimaan_barang pe
JOIN barang b ON pe.barang_id = b.id
$where_clause AND pe.tipe_penerimaan = 'supplier'";
$summary_result = $conn->query($summary_query);
$summary = $summary_result->fetch_assoc();
?>


<!-- Filter Section -->
<div class="card mb-4 d-print-none">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET">
            <input type="hidden" name="page" value="laporan-penerimaan">
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
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select class="form-select" name="supplier_id">
                        <option value="">Semua Supplier</option>
                        <?php while ($s = $supplier_result->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>" <?= $supplier_id == $s['id'] ? 'selected' : '' ?>>
                                <?= $s['nama_supplier'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
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
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5><i class="bi bi-receipt"></i> Total Transaksi</h5>
                <h2><?= $summary['total_transaksi'] ?></h2>
                <small>Kode penerimaan</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5><i class="bi bi-boxes"></i> Total Quantity</h5>
                <h2><?= number_format($summary['total_qty'], 0, ',', '.') ?></h2>
                <small>Unit barang</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white">
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
        <h5 class="mb-0"><i class="bi bi-table"></i> Detail Penerimaan</h5>
        <div>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>
    <div class="card-body">
        <h3 class="text-center mt-10">Laporan Penerimaan Stok</h3>
        <h5 class="text-center mb-1">Periode: <?= $tanggal_dari ?> - <?= $tanggal_sampai ?></h5>
        <hr />
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode Penerimaan</th>
                            <th width="15%">Barang</th>
                            <th width="8%">Satuan</th>
                            <th width="10%" class="text-end">Jumlah</th>
                            <th width="12%">Tipe Penerimaan</th>
                            <th width="15%">Supplier</th>
                            <th width="13%">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= $row['kode_penerimaan'] ?></td>
                                <td><?= $row['nama_barang'] ?></td>
                                <td class="text-center"><?= $row['satuan'] ?></td>
                                <td class="text-end"><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td><?= $row['tipe_penerimaan'] ?? '-' ?></td>
                                <td><?= $row['nama_supplier'] ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_penerimaan'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Tidak ada data penerimaan pada periode ini.</div>
        <?php endif; ?>
    </div>
</div>