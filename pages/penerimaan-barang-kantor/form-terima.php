<?php
include '../../modules/config.php';

session_start();

$current_user_id = $_SESSION['user_id'];
$current_user_level = $_SESSION['level'];

if (isset($_POST['kode_pengeluaran'])) {
    $kode_pengeluaran = $_POST['kode_pengeluaran'];

    $query = "SELECT 
                po.id,
                po.kode_pengeluaran,
                po.tanggal_keluar,
                po.barang_id,
                b.kode_barang,
                b.nama_barang,
                b.satuan,
                po.jumlah as jumlah_pesan,
                po.user_id,
                s.nama,
                s.id as pengirim_user_id
              FROM pengeluaran_barang po
              INNER JOIN barang b ON po.barang_id = b.id
              INNER JOIN users s ON po.user_id = s.id
              WHERE po.kode_pengeluaran = '$kode_pengeluaran' AND po.status = 'dikirim'
              ORDER BY po.id";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $first_row = $result->fetch_assoc();

        // Generate kode penerimaan (format: TR-YYYYMMDD-XXX)
        $date = date('Ymd');
        $check_kode = "SELECT kode_penerimaan FROM penerimaan_barang WHERE kode_penerimaan LIKE 'TR-$date-%' ORDER BY kode_penerimaan DESC LIMIT 1";
        $result_kode = $conn->query($check_kode);
        if ($result_kode->num_rows > 0) {
            $last_kode = $result_kode->fetch_assoc()['kode_penerimaan'];
            $last_number = intval(substr($last_kode, -3));
            $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $new_number = '001';
        }
        $kode_penerimaan = "TR-$date-$new_number";
        ?>

        <form id="form-terima-preorder">
            <input type="hidden" name="kode_pengeluaran" value="<?= $kode_pengeluaran ?>">
            <input type="hidden" name="kode_penerimaan" value="<?= $kode_penerimaan ?>">
            <input type="hidden" name="pengirim_user_id" value="<?= $first_row['pengirim_user_id'] ?>">
            <input type="hidden" name="penerima_user_id" value="<?= $current_user_id ?>">

            <div class="modal-body">
                <!-- Header Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Penerimaan</label>
                        <input type="text" class="form-control" value="<?= $kode_penerimaan ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_penerimaan" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Pre-Order</label>
                        <input type="text" class="form-control" value="<?= $kode_pengeluaran ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pengirim</label>
                        <input type="text" class="form-control" value="<?= $first_row['nama'] ?>" readonly>
                    </div>
                </div>

                <hr>

                <!-- Items Table -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> <strong>Petunjuk:</strong> Masukkan jumlah barang yang
                    diterima. Kosongkan atau isi 0 jika barang tidak diterima.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode Barang</th>
                                <th width="30%">Nama Barang</th>
                                <th width="10%">Satuan</th>
                                <th width="12%" class="text-center">Jumlah Pesan</th>
                                <th width="15%" class="text-center">Jumlah Terima <span class="text-danger">*</span>
                                </th>
                                <th width="13%">Status</th>
                            </tr>
                        </thead>
                        <tbody id="items-tbody">
                            <?php
                            $no = 1;
                            $result->data_seek(0);
                            while ($row = $result->fetch_assoc()):
                                ?>
                                <tr class="item-row">
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $row['kode_barang'] ?></td>
                                    <td><?= $row['nama_barang'] ?></td>
                                    <td><?= $row['satuan'] ?></td>
                                    <td class="text-center">
                                        <strong><?= $row['jumlah_pesan'] ?></strong>
                                        <input type="hidden" name="items[<?= $row['id'] ?>][po_id]" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="items[<?= $row['id'] ?>][barang_id]"
                                            value="<?= $row['barang_id'] ?>">
                                        <input type="hidden" name="items[<?= $row['id'] ?>][jumlah_pesan]"
                                            value="<?= $row['jumlah_pesan'] ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center jumlah-terima"
                                            name="items[<?= $row['id'] ?>][jumlah_terima]" min="0" max="<?= $row['jumlah_pesan'] ?>"
                                            value="<?= $row['jumlah_pesan'] ?>" data-pesan="<?= $row['jumlah_pesan'] ?>"
                                            data-row="<?= $row['id'] ?>" required>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-success" id="status-<?= $row['id'] ?>">Sesuai</span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Konfirmasi Penerimaan
                </button>
            </div>
        </form>

        <script>
            $(document).ready(function () {
                // Check jumlah terima vs jumlah pesan
                $('.jumlah-terima').on('change', function () {
                    const rowId = $(this).data('row');
                    const jumlahPesan = parseInt($(this).data('pesan'));
                    const jumlahTerima = parseInt($(this).val()) || 0;
                    const statusBadge = $('#status-' + rowId);

                    if (jumlahTerima === 0) {
                        statusBadge.removeClass('text-bg-success text-bg-warning text-bg-danger')
                            .addClass('text-bg-danger')
                            .text('Tidak Terima');
                    } else if (jumlahTerima < jumlahPesan) {
                        statusBadge.removeClass('text-bg-success text-bg-warning text-bg-danger')
                            .addClass('text-bg-warning')
                            .text('Kurang');
                        console.log(statusBadge);
                    } else if (jumlahTerima === jumlahPesan) {
                        statusBadge.removeClass('text-bg-success text-bg-warning text-bg-danger')
                            .addClass('text-bg-success')
                            .text('Sesuai');
                    } else {
                        statusBadge.removeClass('text-bg-success text-bg-warning text-bg-danger')
                            .addClass('text-bg-info')
                            .text('Lebih');
                    }
                });

                // Form submission
                $('#form-terima-preorder').submit(function (e) {
                    e.preventDefault();

                    // Check if at least one item is received
                    let hasReceivedItem = false;
                    $('.jumlah-terima').each(function () {
                        if (parseInt($(this).val()) > 0) {
                            hasReceivedItem = true;
                            return false;
                        }
                    });

                    if (!hasReceivedItem) {
                        alert('Minimal harus ada satu barang yang diterima!');
                        return;
                    }

                    // Confirm submission
                    if (!confirm('Apakah Anda yakin ingin mengkonfirmasi penerimaan barang ini?')) {
                        return;
                    }

                    $.ajax({
                        url: 'modules/proses-penerimaan.php?aksi=tambah-penerimaan-kantor',
                        type: 'POST',
                        data: $(this).serialize(),
                        beforeSend: function () {
                            $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Memproses...');
                        },
                        success: function (response) {
                            if (response == 'ok') {
                                alertify.success('Penerimaan berhasil dikonfirmasi');
                                $('#modal-terima-preorder').modal('hide');
                                location.reload();
                            } else {
                                alertify.error('Gagal mengkonfirmasi penerimaan: ' + response);
                                $('button[type="submit"]').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Konfirmasi Penerimaan');
                            }
                        },
                        error: function (xhr) {
                            alertify.error('Error: ' + xhr.responseText);
                            $('button[type="submit"]').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Konfirmasi Penerimaan');
                        }
                    });
                });
            });
        </script>

        <style>
            .status-badge {
                font-size: 0.85rem;
                padding: 0.35rem 0.65rem;
            }
        </style>

        <?php
    } else {
        echo '<div class="alert alert-warning">Permintaan tidak ditemukan atau sudah diterima.</div>';
    }
}
?>