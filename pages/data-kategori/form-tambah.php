<form id="tambah-kategori" enctype="multipart/form-data">
    <div class="d-grid gap-3">
        <div class="row">
            <div class="col-md-12">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" name="nama_kategori" id="nama" placeholder="Nama" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label for="password" class="form-label">Keterangan</label>
                <textarea class="form-control" name="keterangan" id="password" placeholder="Keterangan"
                    required></textarea>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Simpan</button>
</form>
<script>
    $("#tambah-kategori").submit(function (e) {
        var formData = new FormData(this);

        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "modules/proses-kategori.php?aksi=tambah-kategori",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data == "ok") {
                    loadTable();
                    $('.modal').modal('hide');
                    alertify.success('Kategori Berhasil Ditambah');

                } else {
                    alertify.error('Kategori Gagal Ditambah');

                }
            }
        });
    });
</script>