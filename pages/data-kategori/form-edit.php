<?php
include '../../modules/config.php';
$sql = "SELECT * FROM kategori_barang WHERE id = '$_POST[id]'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}
?>
<form id="form-edit" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <div class="d-grid gap-3">
        <div class="row">
            <div class="col-md-12">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" name="nama_kategori" id="nama" placeholder="Nama"
                    value="<?= $row['nama_kategori'] ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label for="password" class="form-label">Keterangan</label>
                <textarea class="form-control" name="keterangan" id="password"
                    placeholder="Keterangan"><?= $row['keterangan'] ?></textarea>
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
            url: 'modules/proses-kategori.php?aksi=edit-kategori',
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