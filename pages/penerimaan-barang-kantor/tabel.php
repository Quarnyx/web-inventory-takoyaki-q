<div>
    <table id="data-table" class="table table-searchable">
        <thead>
            <tr>
                <th>Kode PO</th>
                <th>Tanggal Dikirim</th>
                <th>Pengirim</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../../modules/config.php';
            $query = "SELECT pengeluaran_barang.*, barang.nama_barang, users.nama FROM pengeluaran_barang INNER JOIN barang ON pengeluaran_barang.barang_id = barang.id INNER JOIN users ON pengeluaran_barang.user_id = users.id GROUP BY pengeluaran_barang.kode_pengeluaran";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc(($result))) {
                    ?>
                    <tr>
                        <td><?php echo $row['kode_pengeluaran']; ?></td>
                        <td><?php echo $row['tanggal_keluar']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td>
                            <?php
                            if ($row['status'] == 'dikirim') {
                                echo "<span class='badge rounded-pill text-bg-info'>Dikirim</span>";
                            } elseif ($row['status'] == 'diterima') {
                                echo "<span class='badge rounded-pill text-bg-success'>Diterima</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <button id="edit" class="btn btn-sm btn-warning" data-nama="<?= $row['kode_pengeluaran'] ?>"
                                data-id="<?= $row['kode_pengeluaran'] ?>">Detail</button>
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
                url: 'pages/penerimaan-barang-kantor/form-terima.php',
                data: 'kode_pengeluaran=' + id,
                success: function (data) {
                    $('.modal').modal('show');
                    $('.modal-title').html('Penerimaan Barang ' + nama);
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
                    url: 'modules/proses-preorder.php?aksi=hapus-po',
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