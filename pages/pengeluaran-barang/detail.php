<?php
include '../../modules/config.php';

if (isset($_POST['kode_pengeluaran'])) {
    $kode_pengeluaran = $_POST['kode_pengeluaran'];

    // Get pengeluaran items with details
    $query = "SELECT 
                pe.id,
                pe.kode_pengeluaran,
                pe.tanggal_keluar,
                pe.tujuan_barang,
                b.kode_barang,
                b.nama_barang,
                b.satuan,
                pe.jumlah,
                u.nama as nama_user
              FROM pengeluaran_barang pe
              INNER JOIN barang b ON pe.barang_id = b.id
              LEFT JOIN users u ON pe.user_id = u.id
              WHERE pe.kode_pengeluaran = '$kode_pengeluaran'
              ORDER BY pe.id";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $first_row = $result->fetch_assoc();
        ?>
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="150"><strong>Kode Pengeluaran</strong></td>
                        <td>: <?= $first_row['kode_pengeluaran'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Keluar</strong></td>
                        <td>: <?= date('d/m/Y', strtotime($first_row['tanggal_keluar'])) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="120"><strong>Tujuan Barang</strong></td>
                        <td>: <?= $first_row['tujuan_barang'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Penanggung Jawab</strong></td>
                        <td>: <?= $first_row['nama_user'] ?? '-' ?></td>
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
                        <th width="10%">No</th>
                        <th width="20%">Kode Barang</th>
                        <th width="40%">Nama Barang</th>
                        <th width="15%">Satuan</th>
                        <th width="15%" class="text-center">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $row['kode_barang'] ?></td>
                            <td><?= $row['nama_barang'] ?></td>
                            <td><?= $row['satuan'] ?></td>
                            <td class="text-center"><strong><?= $row['jumlah'] ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>

        <?php
    } else {
        echo '<div class="alert alert-warning">Data pengeluaran tidak ditemukan.</div>';
    }
}
?>