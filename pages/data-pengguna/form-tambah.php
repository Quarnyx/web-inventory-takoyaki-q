<form id="tambah-pengguna" enctype="multipart/form-data">
    <div class="d-grid gap-3">
        <div class="row">
            <div class="col-md-6">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama">
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>
            <div class="col-md-6">
                <label for="level" class="form-label">Role</label>
                <select class="form-select" name="level" id="level">
                    <?php
                    require_once '../../modules/config.php';

                    $query = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'level'");
                    $enum = explode("','", substr(mysqli_fetch_array($query)['Type'], 6, -2));
                    foreach ($enum as $key => $value) {
                        echo "<option value='$value'>$value</option>";
                    }

                    ?>

                </select>
            </div>

        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Simpan</button>
</form>
<script>
    $("#tambah-pengguna").submit(function (e) {
        var formData = new FormData(this);

        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "modules/proses-pengguna.php?aksi=tambah-pengguna",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data == "ok") {
                    loadTable();
                    $('.modal').modal('hide');
                    alertify.success('Pengguna Berhasil Ditambah');

                } else {
                    alertify.error('Pengguna Gagal Ditambah');

                }
            }
        });
    });
</script>