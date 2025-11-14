<?php
require_once '../../modules/config.php';


$barang_query = "SELECT id, kode_barang, nama_barang FROM barang ORDER BY nama_barang";
$barang_result = $conn->query($barang_query);


$supplier_query = "SELECT id, nama_supplier FROM suppliers ORDER BY nama_supplier";
$supplier_result = $conn->query($supplier_query);


$date = date('Ymd');
$check_kode = "SELECT kode_po FROM pre_order_barang WHERE kode_po LIKE 'PO-$date-%' ORDER BY kode_po DESC LIMIT 1";
$result_kode = $conn->query($check_kode);
if ($result_kode->num_rows > 0) {
    $last_kode = $result_kode->fetch_assoc()['kode_po'];
    $last_number = intval(substr($last_kode, -3));
    $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
} else {
    $new_number = '001';
}
$kode_po = "PO-$date-$new_number";
?>
<form id="form-tambah-preorder">

    <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Kode Pre-Order</label>
                <input type="text" class="form-control" name="kode_po" value="<?= $kode_po ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Pesan <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal_pesan" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Supplier <span class="text-danger">*</span></label>
            <select class="form-select" name="supplier_id" required>
                <option value="">Pilih Supplier</option>
                <?php while ($supplier = $supplier_result->fetch_assoc()): ?>
                    <option value="<?= $supplier['id'] ?>"><?= $supplier['nama_supplier'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="draft">Draft</option>
                <option value="dipesan" selected>Dipesan</option>
                <option value="diterima">Diterima</option>
                <option value="dibatalkan">Dibatalkan</option>
            </select>
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
                        <select class="form-select barang-select" name="items[0][barang_id]" required>
                            <option value="">Pilih Barang</option>
                            <?php
                            $barang_result->data_seek(0);
                            while ($barang = $barang_result->fetch_assoc()):
                                ?>
                                <option value="<?= $barang['id'] ?>"><?= $barang['kode_barang'] ?> -
                                    <?= $barang['nama_barang'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="items[0][jumlah]" min="1" value="1" required>
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
        <button type="submit" class="btn btn-primary">Simpan Pre-Order</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        let itemCount = 1;

        $('#btn-tambah-item').click(function () {
            const newItem = `
            <div class="item-row mb-3 p-3 border rounded">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                        <select class="form-select barang-select" name="items[${itemCount}][barang_id]" required>
                            <option value="">Pilih Barang</option>
                            <?php
                            $barang_result->data_seek(0);
                            while ($barang = $barang_result->fetch_assoc()):
                                ?>
                                <option value="<?= $barang['id'] ?>"><?= $barang['kode_barang'] ?> - <?= $barang['nama_barang'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="items[${itemCount}][jumlah]" min="1" value="1" required>
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

        function updateRemoveButtons() {
            const itemRows = $('.item-row').length;
            if (itemRows === 1) {
                $('.btn-remove-item').prop('disabled', true);
            } else {
                $('.btn-remove-item').prop('disabled', false);
            }
        }

        $('#form-tambah-preorder').submit(function (e) {
            e.preventDefault();

            if ($('.item-row').length === 0) {
                alert('Tambahkan minimal satu item barang');
                return;
            }

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
                alert('Tidak boleh ada barang yang sama dalam satu pre-order');
                return;
            }

            $.ajax({
                url: 'modules/proses-preorder.php?aksi=tambah-preorder',
                type: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response == 'ok') {
                        loadTable();
                        $('.modal').modal('hide');
                        alertify.success('Supplier Berhasil Ditambah');
                    } else {
                        alertify.error('Gagal menambahkan pre-order: ' + response);
                    }
                },
                error: function (xhr) {
                    alertify.error('Gagal menambahkan pre-order: ' + xhr.responseText);
                }
            });
        });
    });
</script>