<?php
include '../../modules/config.php';

if (isset($_POST['kode_retur'])) {
    $kode_retur = $_POST['kode_retur'];

    // Use the view for detailed retur information
    $query = "SELECT * FROM v_retur_details WHERE kode_retur = '$kode_retur' ORDER BY id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $first_row = $result->fetch_assoc();
        $total_nilai = 0;
        ?>
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="140"><strong>Kode Retur</strong></td>
                        <td>: <?= $first_row['kode_retur'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Retur</strong></td>
                        <td>: <?= date('d/m/Y', strtotime($first_row['tanggal_retur'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Penanggung Jawab</strong></td>
                        <td>: <?= $first_row['nama_user'] ?? '-' ?></td>
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
                        <td>: <?= $first_row['kontak_supplier'] ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <!-- Items Table -->
        <h6 class="mb-3"><strong>Daftar Barang yang Diretur</strong></h6>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Kode</th>
                        <th width="20%">Nama Barang</th>
                        <th width="10%">Kategori</th>
                        <th width="8%">Satuan</th>
                        <th width="10%" class="text-end">Harga</th>
                        <th width="8%" class="text-center">Jumlah</th>
                        <th width="12%" class="text-end">Nilai</th>
                        <th width="15%">Penyebab</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()):
                        $total_nilai += $row['nilai_retur'];
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $row['kode_barang'] ?></td>
                            <td><?= $row['nama_barang'] ?></td>
                            <td><?= $row['nama_kategori'] ?? '-' ?></td>
                            <td class="text-center"><?= $row['satuan'] ?></td>
                            <td class="text-end">Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                            <td class="text-center"><strong><?= $row['jumlah'] ?></strong></td>
                            <td class="text-end">Rp <?= number_format($row['nilai_retur'], 0, ',', '.') ?></td>
                            <td><small><?= $row['penyebab_retur'] ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="7" class="text-end">Total Nilai Retur:</th>
                        <th class="text-end">Rp <?= number_format($total_nilai, 0, ',', '.') ?></th>
                        <th></th>
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