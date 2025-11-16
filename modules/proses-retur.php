<?php
require_once('../modules/config.php');
switch ($_GET['aksi'] ?? '') {
    case 'tambah-retur':
        $kode_retur = $_POST['kode_retur'];
        $tanggal_retur = $_POST['tanggal_retur'];
        $supplier_id = $_POST['supplier_id'];
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
                $penyebab_retur = $conn->real_escape_string($item['penyebab_retur']);

                $sql = "INSERT INTO retur_barang 
                        (kode_retur, barang_id, jumlah, penyebab_retur, tanggal_retur, supplier_id, user_id) 
                        VALUES 
                        ('$kode_retur', '$barang_id', '$jumlah', '$penyebab_retur', '$tanggal_retur', '$supplier_id', '$user_id')";

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
    case 'hapus-retur':
        $id = $_POST['id'];
        $sql = "DELETE FROM retur_barang WHERE kode_retur = '$id'";
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