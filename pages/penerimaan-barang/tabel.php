<div>
    <table id="data-table" class="table table-searchable">
        <thead>
            <tr>
                <th>Kode PO</th>
                <th>Tanggal Pesan</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../../modules/config.php';
            $query = "SELECT pre_order_barang.*, barang.nama_barang, suppliers.nama_supplier FROM pre_order_barang INNER JOIN barang ON pre_order_barang.barang_id = barang.id INNER JOIN suppliers ON pre_order_barang.supplier_id = suppliers.id GROUP BY pre_order_barang.kode_po";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc(($result))) {
                    ?>
                    <tr>
                        <td><?php echo $row['kode_po']; ?></td>
                        <td><?php echo $row['tanggal_pemesanan']; ?></td>
                        <td><?php echo $row['nama_supplier']; ?></td>
                        <td>
                            <?php
                            if ($row['status'] == 'dipesan') {
                                echo "<span class='badge rounded-pill text-bg-info'>Dipesan</span>";
                            } elseif ($row['status'] == 'diterima') {
                                echo "<span class='badge rounded-pill text-bg-success'>Diterima</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <button id="edit" class="btn btn-sm btn-warning" data-nama="<?= $row['kode_po'] ?>"
                                data-id="<?= $row['kode_po'] ?>">Detail</button>
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
                url: 'pages/penerimaan-barang/form-terima.php',
                data: 'kode_po=' + id,
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