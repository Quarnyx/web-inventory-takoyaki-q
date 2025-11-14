<?php
include '../../modules/config.php';

if (isset($_POST['kode_po'])) {
    $kode_po = $_POST['kode_po'];

    // Get pre-order items with details
    $query = "SELECT 
                po.id,
                po.kode_po,
                po.tanggal_pemesanan,
                po.status,
                b.kode_barang,
                b.nama_barang,
                b.satuan,
                b.harga_beli,
                po.jumlah,
                (po.jumlah * b.harga_beli) as subtotal,
                s.nama_supplier,
                s.kontak,
                s.alamat
              FROM pre_order_barang po
              INNER JOIN barang b ON po.barang_id = b.id
              INNER JOIN suppliers s ON po.supplier_id = s.id
              WHERE po.kode_po = '$kode_po'
              ORDER BY po.id";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $first_row = $result->fetch_assoc();
        $total = 0;
        ?>
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="140"><strong>Kode Pre-Order</strong></td>
                        <td>: <?= $first_row['kode_po'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Pesan</strong></td>
                        <td>: <?= date('d/m/Y', strtotime($first_row['tanggal_pemesanan'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>:
                            <?php
                            $status_badge = '';
                            switch ($first_row['status']) {
                                case 'draft':
                                    $status_badge = '<span class="badge bg-secondary">Draft</span>';
                                    break;
                                case 'dipesan':
                                    $status_badge = '<span class="badge bg-warning text-dark">Dipesan</span>';
                                    break;
                                case 'diterima':
                                    $status_badge = '<span class="badge bg-success">Diterima</span>';
                                    break;
                                case 'dibatalkan':
                                    $status_badge = '<span class="badge bg-danger">Dibatalkan</span>';
                                    break;
                            }
                            echo $status_badge;
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="100"><strong>Supplier</strong></td>
                        <td>: <?= $first_row['nama_supplier'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Kontak</strong></td>
                        <td>: <?= $first_row['kontak'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td>: <?= $first_row['alamat'] ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <!-- Items Table -->
        <h6 class="mb-3"><strong>Daftar Barang</strong></h6>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode Barang</th>
                        <th width="30%">Nama Barang</th>
                        <th width="10%">Satuan</th>
                        <th width="12%" class="text-end">Harga</th>
                        <th width="10%" class="text-center">Jumlah</th>
                        <th width="18%" class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $result->data_seek(0); // Reset pointer
                    while ($row = $result->fetch_assoc()):
                        $subtotal = $row['jumlah'] * $row['harga_beli'];
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $row['kode_barang'] ?></td>
                            <td><?= $row['nama_barang'] ?></td>
                            <td><?= $row['satuan'] ?></td>
                            <td class="text-end">Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= $row['jumlah'] ?></td>
                            <td class="text-end">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6" class="text-end">Total:</th>
                        <th class="text-end">Rp <?= number_format($total, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-warning">Data pre-order tidak ditemukan.</div>';
    }
}
?>