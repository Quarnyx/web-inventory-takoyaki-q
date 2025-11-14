<?php
require_once('../modules/config.php');
switch ($_GET['aksi'] ?? '') {
    case 'tambah-preorder':
        $kode_po = $_POST['kode_po'];
        $supplier_id = $_POST['supplier_id'];
        $tanggal_pesan = $_POST['tanggal_pesan'];
        $status = $_POST['status'];
        $items = $_POST['items'];

        // Begin transaction
        $conn->begin_transaction();

        try {
            $success = true;

            // Insert each item
            foreach ($items as $item) {
                $barang_id = $item['barang_id'];
                $jumlah = $item['jumlah'];

                $sql = "INSERT INTO pre_order_barang (kode_po, barang_id, supplier_id, jumlah, tanggal_pemesanan, status) 
                        VALUES ('$kode_po', '$barang_id', '$supplier_id', '$jumlah', '$tanggal_pesan', '$status')";

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
    case 'hapus-po':
        $id = $_POST['id'];
        $sql = "DELETE FROM pre_order_barang WHERE kode_po = '$id'";
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