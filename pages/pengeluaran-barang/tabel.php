<div>
    <table id="data-table" class="table table-searchable">
        <thead>
            <tr>
                <th>Kode Pengeluaran</th>
                <th>Tanggal Keluar</th>
                <th>Tujuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../../modules/config.php';
            $query = "SELECT * FROM pengeluaran_barang GROUP BY kode_pengeluaran";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc(($result))) {
                    ?>
                    <tr>
                        <td><?php echo $row['kode_pengeluaran']; ?></td>
                        <td><?php echo $row['tanggal_keluar']; ?></td>
                        <td><?php echo $row['tujuan_barang']; ?></td>
                        <td>
                            <button id="edit" class="btn btn-sm btn-warning" data-nama="<?= $row['kode_pengeluaran'] ?>"
                                data-id="<?= $row['kode_pengeluaran'] ?>">Detail</button>
                            <button id="delete" class="btn btn-sm btn-danger" data-nama="<?= $row['kode_pengeluaran'] ?>"
                                data-id="<?= $row['kode_pengeluaran'] ?>">Hapus</button>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='4'>No data found</td></tr>";
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
                url: 'pages/pengeluaran-barang/detail.php',
                data: 'kode_pengeluaran=' + id,
                success: function (data) {
                    $('.modal').modal('show');
                    $('.modal-title').html('Detail ' + nama);
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
                    url: 'modules/proses-pengeluaran.php?aksi=hapus-pengeluaran',
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