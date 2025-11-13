<?php
include '../../modules/config.php';
$sql = "SELECT * FROM barang WHERE id = '$_POST[id]'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}
?>
<form id="form-edit" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <div class="d-grid gap-3">
        <div class="row">
            <div class="col-md-6">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" name="nama_barang" id="nama" placeholder="Nama"
                    value="<?= $row['nama_barang'] ?>">
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">Koda Barang</label>
                <input type="text" class="form-control" name="kode_barang" id="kode_barang" placeholder="Username"
                    value="<?= $row['kode_barang'] ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="satuan" class="form-label">Satuan</label>
                <input type="satuan" class="form-control" name="satuan" id="satuan" placeholder="Password"
                    value="<?= $row['satuan'] ?>">
            </div>
            <div class="col-md-6">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" name="kategori" id="kategori">
                    <?php
                    require_once '../../modules/config.php';
                    $sql = "SELECT * FROM kategori_barang";
                    $resultb = $conn->query($sql);
                    while ($rowb = $resultb->fetch_assoc()) {
                        echo '<option value="' . $rowb['id'] . '" ' . ($row['kategori_id'] == $rowb['id'] ? 'selected' : '') . '">' . $rowb['nama_kategori'] . '</option>';
                    }
                    ?>

                </select>
            </div>

        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="stok_minimal" class="form-label">Stok Minimal</label>
                <input type="text" class="form-control" name="stok_minimal" id="stok_minimal" placeholder="Stok Minimal"
                    value="<?= $row['stok_minimal'] ?>">
            </div>
            <div class="col-md-6">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="text" class="form-control" name="harga_beli" id="harga_beli" placeholder="Harga Beli"
                    value="<?= $row['harga_beli'] ?>">
            </div>

        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Simpan</button>
</form>
<script>
    $("#form-edit").submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: 'modules/proses-barang.php?aksi=edit-barang',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data == "ok") {
                    loadTable();
                    $('.modal').modal('hide');
                    alertify.success('Edit Berhasil');

                } else {
                    alertify.error('Edit Gagal');

                }
            },
            error: function (data) {
                alertify.error(data);
            }
        });
    });
</script>