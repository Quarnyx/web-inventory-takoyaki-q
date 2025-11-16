<?php
require_once '../../modules/config.php';
session_start();

// Assuming you have session with user info
$current_user_id = $_SESSION['user_id'];

// Get barang list
$barang_query = "SELECT id, kode_barang, nama_barang, satuan FROM barang ORDER BY nama_barang";
$barang_result = $conn->query($barang_query);

// Get suppliers list
$supplier_query = "SELECT id, nama_supplier FROM suppliers ORDER BY nama_supplier";
$supplier_result = $conn->query($supplier_query);

// Generate kode retur (format: RET-YYYYMMDD-XXX)
$date = date('Ymd');
$check_kode = "SELECT kode_retur FROM retur_barang WHERE kode_retur LIKE 'RET-$date-%' ORDER BY kode_retur DESC LIMIT 1";
$result_kode = $conn->query($check_kode);
if ($result_kode->num_rows > 0) {
    $last_kode = $result_kode->fetch_assoc()['kode_retur'];
    $last_number = intval(substr($last_kode, -3));
    $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
} else {
    $new_number = '001';
}
$kode_retur = "RET-$date-$new_number";
?>
<form id="form-tambah-retur">
    <input type="hidden" name="user_id" value="<?= $current_user_id ?>">

    <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Kode Retur</label>
                <input type="text" class="form-control" name="kode_retur" value="<?= $kode_retur ?>" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal Retur <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal_retur" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                <select class="form-select" name="supplier_id" required>
                    <option value="">Pilih Supplier</option>
                    <?php while ($supplier = $supplier_result->fetch_assoc()): ?>
                        <option value="<?= $supplier['id'] ?>"><?= $supplier['nama_supplier'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <hr>

        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> <strong>Perhatian:</strong> Retur barang akan mengurangi stok.
            Pastikan data yang diinput sudah benar.
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0"><strong>Daftar Barang yang Diretur</strong></label>
            <button type="button" class="btn btn-sm btn-success" id="btn-tambah-item">
                <i class="bi bi-plus"></i> Tambah Item
            </button>
        </div>

        <div id="items-container">
            <div class="item-row mb-3 p-3 border rounded bg-light">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select class="form-select barang-select" name="items[0][barang_id]" data-index="0" required>
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
                    <div class="col-md-2">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control jumlah-input" name="items[0][jumlah]" min="1"
                                value="1" required>
                            <span class="input-group-text satuan-text" id="satuan-0">-</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Penyebab Retur <span class="text-danger">*</span></label>
                        <input type="text" class="form-control penyebab-input" name="items[0][penyebab_retur]"
                            placeholder="Contoh: Barang rusak, kadaluarsa, tidak sesuai" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-item w-100" disabled>
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-arrow-return-left"></i> Simpan Retur
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
            <div class="item-row mb-3 p-3 border rounded bg-light">
                <div class="row">
                    <div class="col-md-5">
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
                    <div class="col-md-2">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control jumlah-input" name="items[${itemCount}][jumlah]" min="1" value="1" required>
                            <span class="input-group-text satuan-text" id="satuan-${itemCount}">-</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Penyebab Retur <span class="text-danger">*</span></label>
                        <input type="text" class="form-control penyebab-input" name="items[${itemCount}][penyebab_retur]" 
                               placeholder="Contoh: Barang rusak, kadaluarsa, tidak sesuai" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-item w-100">
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
        $('#form-tambah-retur').submit(function (e) {
            e.preventDefault();

            // Validate at least one item
            if ($('.item-row').length === 0) {
                alertify.error('Tambahkan minimal satu item barang untuk diretur');
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
                alertify.error('Tidak boleh ada barang yang sama dalam satu retur');
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

            // Validate all penyebab are filled
            let hasEmptyPenyebab = false;
            $('.penyebab-input').each(function () {
                if (!$(this).val().trim()) {
                    hasEmptyPenyebab = true;
                    return false;
                }
            });

            if (hasEmptyPenyebab) {
                alertify.error('Semua penyebab retur harus diisi');
                return;
            }

            // Check supplier is selected
            if (!$('select[name="supplier_id"]').val()) {
                alertify.error('Supplier harus dipilih');
                return;
            }

            // Confirm submission
            if (!confirm('Apakah Anda yakin ingin mencatat retur barang ini? Stok akan berkurang.')) {
                return;
            }

            $.ajax({
                url: 'modules/proses-retur.php?aksi=tambah-retur',
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function () {
                    $('button[type="submit"]').prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm"></span> Memproses...');
                },
                success: function (response) {
                    if (response == 'ok') {
                        alertify.success('Retur barang berhasil dicatat');
                        $('#modal-tambah-retur').modal('hide');
                        location.reload();
                    } else {
                        alertify.error('Gagal mencatat retur: ' + response);
                        $('button[type="submit"]').prop('disabled', false)
                            .html('<i class="bi bi-arrow-return-left"></i> Simpan Retur');
                    }
                },
                error: function (xhr) {
                    alertify.error('Error: ' + xhr.responseText);
                    $('button[type="submit"]').prop('disabled', false)
                        .html('<i class="bi bi-arrow-return-left"></i> Simpan Retur');
                }
            });
        });
    });
</script>