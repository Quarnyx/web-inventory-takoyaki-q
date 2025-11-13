<?php
include '../../modules/config.php';
$sql = "SELECT * FROM users WHERE id = '$_POST[id]'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}
?>
<form id="form-edit" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <div class="d-grid gap-3">
        <div class="row">
            <div class="col-md-4">
                <div>
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control " name="nama" id="nama" placeholder="Nama"
                        value="<?= $row['nama'] ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control " name="username" id="username" placeholder="Username"
                        value="<?= $row['username'] ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label for="level" class="form-label">Level</label>
                <select class="form-select" name="level" id="level">
                    <?php
                    $query = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'level'");
                    $enum = explode("','", substr(mysqli_fetch_array($query)['Type'], 6, -2));
                    foreach ($enum as $key => $value) {
                        echo "<option value='$value' " . ($row['level'] == $value ? 'selected' : '') . ">$value</option>";
                    }
                    ?>
                </select>
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
            url: 'modules/proses-pengguna.php?aksi=edit-pengguna',
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