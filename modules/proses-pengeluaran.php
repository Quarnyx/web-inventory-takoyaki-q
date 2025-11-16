<?php
require_once('../modules/config.php');
switch ($_GET['aksi'] ?? '') {
    case 'tambah-pengeluaran':
        $kode_pengeluaran = $_POST['kode_pengeluaran'];
        $tanggal_keluar = $_POST['tanggal_keluar'];
        $tujuan_barang = $_POST['tujuan_barang'];
        $user_id = $_POST['user_id'];
        $items = $_POST['items'];

        // Begin transaction
        $conn->begin_transaction();

        try {
            $success = true;

            // Insert each item
            foreach ($items as $item) {
                $barang_id = $item['barang_id'];
                $jumlah = $item['jumlah'];

                $sql = "INSERT INTO pengeluaran_barang 
                        (kode_pengeluaran, barang_id, jumlah, tanggal_keluar, tujuan_barang, user_id, status) 
                        VALUES 
                        ('$kode_pengeluaran', '$barang_id', '$jumlah', '$tanggal_keluar', '$tujuan_barang', '$user_id', 'dikirim')";

                if (!$conn->query($sql)) {
                    $success = false;
                    throw new Exception($conn->error);
                }
            }

            if ($success) {
                $conn->commit();
                echo 'ok';
                http_response_code(200);
            }

        } catch (Exception $e) {
            $conn->rollback();
            echo 'Error: ' . $e->getMessage();
            http_response_code(400);
        }
        break;
    case 'hapus-pengeluaran':
        $id = $_POST['id'];
        $sql = "DELETE FROM pengeluaran_barang WHERE kode_pengeluaran = '$id'";
        $result = $conn->query($sql);
        if ($result) {
            echo 'ok';
            http_response_code(200);
        } else {
            echo 'error';
            echo $conn->error;
            http_response_code(400);
        }
        break;
}