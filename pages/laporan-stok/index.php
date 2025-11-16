<?php
require_once 'modules/config.php';
// Get filter parameters
$tanggal_dari = isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : date('Y-m-01');
$tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : date('Y-m-d');
$barang_id = isset($_GET['barang_id']) ? $_GET['barang_id'] : '';

// Get barang list for filter
$barang_list_query = "SELECT barang_id, kode_barang, nama_barang FROM v_current_stock ORDER BY nama_barang";
$barang_list_result = $conn->query($barang_list_query);

// Build WHERE clause for filtering
$where_clause = "WHERE tanggal BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";
if (!empty($barang_id)) {
    $where_clause .= " AND barang_id = '$barang_id'";
}

// Query using the updated view with retur
$query = "
    SELECT 
        barang_id,
        kode_barang,
        nama_barang,
        satuan,
        nama_kategori,
        tanggal,
        stok_masuk,
        pengeluaran,
        retur,
        stok_keluar,
        selisih
    FROM v_daily_stock_summary
    $where_clause
    ORDER BY nama_barang, tanggal
";

$result = $conn->query($query);
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Laporan Stok</h4>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4 d-print-none">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" id="filterForm">
            <input type="hidden" name="page" value="laporan-stok">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Dari</label>
                    <input type="date" class="form-control" name="tanggal_dari" value="<?= $tanggal_dari ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Sampai</label>
                    <input type="date" class="form-control" name="tanggal_sampai" value="<?= $tanggal_sampai ?>"
                        required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Barang (Opsional)</label>
                    <select class="form-select" name="barang_id">
                        <option value="">Semua Barang</option>
                        <?php while ($b = $barang_list_result->fetch_assoc()): ?>
                            <option value="<?= $b['barang_id'] ?>" <?= $barang_id == $b['barang_id'] ? 'selected' : '' ?>>
                                <?= $b['kode_barang'] ?> - <?= $b['nama_barang'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards - Including Retur -->
<?php
$summary_query = "
            SELECT 
                SUM(stok_masuk) as total_masuk,
                SUM(pengeluaran) as total_pengeluaran,
                SUM(retur) as total_retur,
                SUM(stok_keluar) as total_keluar,
                COUNT(DISTINCT CASE WHEN stok_masuk > 0 THEN barang_id END) as jumlah_barang_masuk,
                COUNT(DISTINCT CASE WHEN stok_keluar > 0 THEN barang_id END) as jumlah_barang_keluar
            FROM v_daily_stock_summary
            $where_clause
        ";

$summary_result = $conn->query($summary_query);
$summary = $summary_result->fetch_assoc();
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5><i class="bi bi-box-arrow-in-down"></i> Total Stok Masuk</h5>
                <h2><?= number_format($summary['total_masuk'], 0, ',', '.') ?></h2>
                <small><?= $summary['jumlah_barang_masuk'] ?> jenis barang</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5><i class="bi bi-box-arrow-right"></i> Total Pengeluaran</h5>
                <h2><?= number_format($summary['total_pengeluaran'], 0, ',', '.') ?></h2>
                <small>Barang keluar</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5><i class="bi bi-arrow-return-left"></i> Total Retur</h5>
                <h2><?= number_format($summary['total_retur'], 0, ',', '.') ?></h2>
                <small>Retur ke supplier</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5><i class="bi bi-plus-minus"></i> Selisih Bersih</h5>
                <h2><?= number_format($summary['total_masuk'] - $summary['total_keluar'], 0, ',', '.') ?></h2>
                <small><?= $summary['total_masuk'] >= $summary['total_keluar'] ? 'Surplus' : 'Defisit' ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Report Table -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0 d-print-none"><i class="bi bi-table"></i> Detail Pergerakan Stok (Termasuk Retur)</h5>
        <div>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>
    <div class="card-body">
        <h3 class="text-center mt-10">Laporan Pergerakan Stok</h3>
        <h5 class="text-center mb-1">Periode: <?= $tanggal_dari ?> - <?= $tanggal_sampai ?></h5>
        <hr />
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="stockTable">
                    <thead class="table-light">
                        <tr>
                            <th width="4%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="10%">Kode</th>
                            <th width="20%">Nama Barang</th>
                            <th width="12%">Kategori</th>
                            <th width="6%">Satuan</th>
                            <th width="8%" class="text-center">Masuk</th>
                            <th width="8%" class="text-center">Keluar</th>
                            <th width="8%" class="text-center">Retur</th>
                            <th width="8%" class="text-center">Total Out</th>
                            <th width="6%" class="text-center">Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $stok_running = [];

                        // Calculate initial stock using view
                        $initial_stock_query = "
                                    SELECT 
                                        barang_id,
                                        SUM(stok_masuk) - SUM(stok_keluar) as stok_awal
                                    FROM v_daily_stock_summary
                                    WHERE tanggal < '$tanggal_dari'
                                    " . (!empty($barang_id) ? "AND barang_id = '$barang_id'" : '') . "
                                    GROUP BY barang_id
                                ";
                        $initial_result = $conn->query($initial_stock_query);
                        while ($init = $initial_result->fetch_assoc()) {
                            $stok_running[$init['barang_id']] = $init['stok_awal'];
                        }

                        while ($row = $result->fetch_assoc()):
                            if (!isset($stok_running[$row['barang_id']])) {
                                $stok_running[$row['barang_id']] = 0;
                            }

                            $stok_running[$row['barang_id']] += $row['selisih'];
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= $row['kode_barang'] ?></td>
                                <td><?= $row['nama_barang'] ?></td>
                                <td><?= $row['nama_kategori'] ?? '-' ?></td>
                                <td class="text-center"><?= $row['satuan'] ?></td>
                                <td class="text-center">
                                    <?php if ($row['stok_masuk'] > 0): ?>
                                        <span class="badge bg-success">+<?= number_format($row['stok_masuk'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['pengeluaran'] > 0): ?>
                                        <span
                                            class="badge bg-warning text-dark">-<?= number_format($row['pengeluaran'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['retur'] > 0): ?>
                                        <span class="badge bg-danger">-<?= number_format($row['retur'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <strong><?= number_format($row['stok_keluar'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-center">
                                    <strong class="<?= $stok_running[$row['barang_id']] <= 0 ? 'text-danger' : '' ?>">
                                        <?= number_format($stok_running[$row['barang_id']], 0, ',', '.') ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Tidak ada pergerakan stok pada periode yang dipilih.
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    @media print {

        .card-header .btn,
        .breadcrumb,
        .card:first-child {
            display: none !important;
        }
    }
</style>