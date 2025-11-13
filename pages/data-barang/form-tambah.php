<form id="tambah-barang" enctype="multipart/form-data">
    <div class="d-grid gap-3">
        <div class="row">
            <div class="col-md-6">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" name="nama_barang" id="nama" placeholder="Nama">
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">Koda Barang</label>
                <input type="text" class="form-control" name="kode_barang" id="kode_barang" placeholder="Username">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="satuan" class="form-label">Satuan</label>
                <input type="satuan" class="form-control" name="satuan" id="satuan" placeholder="Password">
            </div>
            <div class="col-md-6">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" name="kategori" id="kategori">
                    <?php
                    require_once '../../modules/config.php';
                    $sql = "SELECT * FROM kategori_barang";
                    $resultb = $conn->query($sql);
                    while ($rowb = $resultb->fetch_assoc()) {
                        echo '<option value="' . $rowb['id'] . '">' . $rowb['nama_kategori'] . '</option>';
                    }
                    ?>

                </select>
            </div>

        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="stok_minimal" class="form-label">Stok Minimal</label>
                <input type="text" class="form-control" name="stok_minimal" id="stok_minimal"
                    placeholder="Stok Minimal">
            </div>
            <div class="col-md-6">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="text" class="form-control" name="harga_beli" id="harga_beli" placeholder="Harga Beli">
            </div>

        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Simpan</button>
</form>
<script>
    $("#tambah-barang").submit(function (e) {
        var formData = new FormData(this);

        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "modules/proses-barang.php?aksi=tambah-barang",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data == "ok") {
                    loadTable();
                    $('.modal').modal('hide');
                    alertify.success('Barang Berhasil Ditambah');

                } else {
                    alertify.error('Barang Gagal Ditambah');

                }
            }
        });
    });
</script>