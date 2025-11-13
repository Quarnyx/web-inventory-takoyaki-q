<div>
    <table id="data-table" class="table table-searchable">
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../../modules/config.php';
            $query = "SELECT * FROM kategori_barang";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc(($result))) {
                    ?>
                    <tr>
                        <td><?php echo $row['nama_kategori']; ?></td>
                        <td><?php echo $row['keterangan']; ?></td>
                        <td>
                            <button id="edit" class="btn btn-sm btn-warning" data-nama="<?= $row['nama_kategori'] ?>"
                                data-id="<?= $row['id'] ?>">Edit</button>
                            <button id="delete" class="btn btn-sm btn-danger" data-nama="<?= $row['nama_kategori'] ?>"
                                data-id="<?= $row['id'] ?>">Hapus</button>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='3'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function () {
        new DataTable('#data-table',
            {
                responsive: true
            }
        );
        $('#data-table').on('click', '#edit', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            $.ajax({
                type: 'POST',
                url: 'pages/data-kategori/form-edit.php',
                data: 'id=' + id + '&nama=' + nama,
                success: function (data) {
                    $('.modal').modal('show');
                    $('.modal-title').html('Edit Data ' + nama);
                    $('.modal .modal-body').html(data);
                }
            })
        });
        $('#data-table').on('click', '#delete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            alertify.confirm('Hapus', 'Apakah anda yakin ingin menghapus data ' + nama + '?', function () {
                $.ajax({
                    type: 'POST',
                    url: 'modules/proses-kategori.php?aksi=hapus-kategori',
                    data: 'id=' + id,
                    success: function (data) {
                        if (data == "ok") {
                            loadTable();
                            $('.modal').modal('hide');
                            alertify.success('Barang Berhasil Dihapus');

                        } else {
                            alertify.error('Barang Gagal Dihapus');

                        }
                    },
                    error: function (data) {
                        alertify.error(data);
                    }
                })
            }, function () {
                alertify.error('Hapus dibatalkan');
            })
        });
    });
</script>