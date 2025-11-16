<div>
    <table id="data-table" class="table table-searchable">
        <thead>
            <tr>
                <th>Kode Retur</th>
                <th>Tanggal Retur</th>
                <th>Supplier</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../../modules/config.php';
            $query = "SELECT * FROM v_retur_details GROUP BY kode_retur ORDER BY tanggal_retur DESC";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc(($result))) {
                    ?>
                    <tr>
                        <td><?php echo $row['kode_retur']; ?></td>
                        <td><?php echo $row['tanggal_retur']; ?></td>
                        <td><?php echo $row['nama_supplier']; ?></td>
                        <td>
                            <button id="edit" class="btn btn-sm btn-warning" data-nama="<?= $row['kode_retur'] ?>"
                                data-id="<?= $row['kode_retur'] ?>">Detail</button>
                            <button id="delete" class="btn btn-sm btn-danger" data-nama="<?= $row['kode_retur'] ?>"
                                data-id="<?= $row['kode_retur'] ?>">Hapus</button>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='7'>No data found</td></tr>";
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
                url: 'pages/retur-barang/detail.php',
                data: 'kode_retur=' + id,
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
                    url: 'modules/proses-retur.php?aksi=hapus-retur',
                    data: 'id=' + id,
                    success: function (data) {
                        if (data == "ok") {
                            loadTable();
                            $('.modal').modal('hide');
                            alertify.success('Preorder Berhasil Dihapus');

                        } else {
                            alertify.error('Preorder Gagal Dihapus');

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