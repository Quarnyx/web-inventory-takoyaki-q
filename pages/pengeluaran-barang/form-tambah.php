<?php
require_once '../../modules/config.php';

session_start();

$current_user_id = $_SESSION['user_id'];

$barang_query = "SELECT id, kode_barang, nama_barang, satuan FROM barang ORDER BY nama_barang";
$barang_result = $conn->query($barang_query);

$date = date('Ymd');
$check_kode = "SELECT kode_pengeluaran FROM pengeluaran_barang WHERE kode_pengeluaran LIKE 'OUT-$date-%' ORDER BY kode_pengeluaran DESC LIMIT 1";
$result_kode = $conn->query($check_kode);
if ($result_kode->num_rows > 0) {
    $last_kode = $result_kode->fetch_assoc()['kode_pengeluaran'];
    $last_number = intval(substr($last_kode, -3));
    $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
} else {
    $new_number = '001';
}
$kode_pengeluaran = "OUT-$date-$new_number";
?>
<form id="form-tambah-pengeluaran">
    <input type="hidden" name="user_id" value="<?= $current_user_id ?>">

    <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Kode Pengeluaran</label>
                <input type="text" class="form-control" name="kode_pengeluaran" value="<?= $kode_pengeluaran ?>"
                    readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal_keluar" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Tujuan Barang <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="tujuan_barang"
                placeholder="Contoh: Produksi, Cabang Jakarta, Pengiriman ke Pelanggan, dll" required>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0"><strong>Daftar Barang</strong></label>
            <button type="button" class="btn btn-sm btn-success" id="btn-tambah-item">
                <i class="bi bi-plus"></i> Tambah Item
            </button>
        </div>

        <div id="items-container">
            <div class="item-row mb-3 p-3 border rounded">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select class="form-select barang-select" name="items[0][barang_id]" data-index="0" required>
                            <option value="">Pilih Barang</option>
                            <?php
                            while ($barang = $barang_result->fetch_assoc()):
                                ?>
                                <option value="<?= $barang['id'] ?>" data-satuan="<?= $barang['satuan'] ?>">
                                    <?= $barang['kode_barang'] ?> - <?= $barang['nama_barang'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control jumlah-input" name="items[0][jumlah]" min="1"
                                value="1" required>
                            <span class="input-group-text satuan-text" id="satuan-0">-</span>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-item" disabled>
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-box-arrow-right"></i> Simpan Pengeluaran
        </button>
    </div>
</form>

<script>
    $(document).ready(function () {
        let itemCount = 1;

        // Update satuan when barang is selected
        $(document).on('change', '.barang-select', function () {
            const index = $(this).data('index');
            const satuan = $(this).find(':selected').data('satuan') || '-';
            $('#satuan-' + index).text(satuan);
        });

        // Add new item row
        $('#btn-tambah-item').click(function () {
            const newItem = `
            <div class="item-row mb-3 p-3 border rounded">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select class="form-select barang-select" name="items[${itemCount}][barang_id]" data-index="${itemCount}" required>
                            <option value="">Pilih Barang</option>
                            <?php
                            $barang_result->data_seek(0);
                            while ($barang = $barang_result->fetch_assoc()):
                                ?>
                                <option value="<?= $barang['id'] ?>" data-satuan="<?= $barang['satuan'] ?>">
                                    <?= $barang['kode_barang'] ?> - <?= $barang['nama_barang'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control jumlah-input" name="items[${itemCount}][jumlah]" min="1" value="1" required>
                            <span class="input-group-text satuan-text" id="satuan-${itemCount}">-</span>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

            $('#items-container').append(newItem);
            itemCount++;
            updateRemoveButtons();
        });

        // Remove item row
        $(document).on('click', '.btn-remove-item', function () {
            $(this).closest('.item-row').remove();
            updateRemoveButtons();
        });

        // Update remove buttons state
        function updateRemoveButtons() {
            const itemRows = $('.item-row').length;
            if (itemRows === 1) {
                $('.btn-remove-item').prop('disabled', true);
            } else {
                $('.btn-remove-item').prop('disabled', false);
            }
        }

        // Form submission
        $('#form-tambah-pengeluaran').submit(function (e) {
            e.preventDefault();

            // Validate at least one item
            if ($('.item-row').length === 0) {
                alertify.error('Tambahkan minimal satu item barang');
                return;
            }

            // Check for duplicate items
            const selectedItems = [];
            let hasDuplicate = false;

            $('.barang-select').each(function () {
                const value = $(this).val();
                if (value) {
                    if (selectedItems.includes(value)) {
                        hasDuplicate = true;
                        return false;
                    }
                    selectedItems.push(value);
                }
            });

            if (hasDuplicate) {
                alertify.error('Tidak boleh ada barang yang sama dalam satu pengeluaran');
                return;
            }

            // Validate all items are selected
            let hasEmptyItem = false;
            $('.barang-select').each(function () {
                if (!$(this).val()) {
                    hasEmptyItem = true;
                    return false;
                }
            });

            if (hasEmptyItem) {
                alertify.error('Semua barang harus dipilih');
                return;
            }

            // Confirm submission
            if (!confirm('Apakah Anda yakin ingin mencatat pengeluaran barang ini?')) {
                return;
            }

            $.ajax({
                url: 'modules/proses-pengeluaran.php?aksi=tambah-pengeluaran',
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function () {
                    $('button[type="submit"]').prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm"></span> Memproses...');
                },
                success: function (response) {
                    if (response == 'ok') {
                        alertify.success('Pengeluaran barang berhasil dicatat');
                        loadTable();
                        $('.modal').modal('hide');
                    } else {
                        alertify.error('Gagal mencatat pengeluaran: ' + response);
                        $('button[type="submit"]').prop('disabled', false)
                            .html('<i class="bi bi-box-arrow-right"></i> Simpan Pengeluaran');
                    }
                },
                error: function (xhr) {
                    alertify.error('Error: ' + xhr.responseText);
                    $('button[type="submit"]').prop('disabled', false)
                        .html('<i class="bi bi-box-arrow-right"></i> Simpan Pengeluaran');
                }
            });
        });
    });
</script>