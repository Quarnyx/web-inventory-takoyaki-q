<?php
require_once('../modules/config.php');
switch ($_GET['aksi'] ?? '') {
    case 'tambah-penerimaan':
        $kode_po = $_POST['kode_po'];
        $kode_penerimaan = $_POST['kode_penerimaan'];
        $supplier_id = $_POST['supplier_id'];
        $tanggal_penerimaan = $_POST['tanggal_penerimaan'];
        $penerima_user_id = $_POST['penerima_user_id'];
        $items = $_POST['items'];

        // Begin transaction
        $conn->begin_transaction();

        try {
            $received_items = 0;
            $fully_received_items = 0;
            $total_items = count($items);

            // Process each item
            foreach ($items as $item) {
                $po_id = $item['po_id'];
                $barang_id = $item['barang_id'];
                $jumlah_pesan = $item['jumlah_pesan'];
                $jumlah_terima = $item['jumlah_terima'];

                // Only insert if jumlah_terima > 0
                if ($jumlah_terima > 0) {
                    // Insert into penerimaan_barang
                    $sql_insert = "INSERT INTO penerimaan_barang 
                                  (kode_penerimaan, barang_id, jumlah, tanggal_penerimaan, 
                                   tipe_penerimaan, supplier_id, dari_user_id, penerima_user_id) 
                                  VALUES 
                                  ('$kode_penerimaan', '$barang_id', '$jumlah_terima', '$tanggal_penerimaan', 
                                   'supplier', '$supplier_id', NULL, '$penerima_user_id')";

                    if (!$conn->query($sql_insert)) {
                        throw new Exception($conn->error);
                    }

                    $received_items++;

                    if ($jumlah_terima == $jumlah_pesan) {
                        $fully_received_items++;
                    }
                }
            }

            // Update pre_order_barang status
            if ($fully_received_items == $total_items) {
                // All items fully received
                $new_status = 'diterima';
            } else if ($received_items > 0) {
                // Partially received - keep as 'dipesan' or create new status 'sebagian'
                $new_status = 'diterima'; // You can change this to 'sebagian' if needed
            } else {
                // Nothing received
                throw new Exception('Tidak ada barang yang diterima');
            }

            $sql_update_po = "UPDATE pre_order_barang 
                            SET status = '$new_status' 
                            WHERE kode_po = '$kode_po'";

            if (!$conn->query($sql_update_po)) {
                throw new Exception($conn->error);
            }

            $conn->commit();
            echo 'ok';
            http_response_code(200);

        } catch (Exception $e) {
            $conn->rollback();
            echo 'Error: ' . $e->getMessage();
            http_response_code(400);
        }
        break;
    case 'tambah-penerimaan-kantor':
        $kode_pengeluaran = $_POST['kode_pengeluaran'];
        $kode_penerimaan = $_POST['kode_penerimaan'];
        $tanggal_penerimaan = $_POST['tanggal_penerimaan'];
        $pengirim_user_id = $_POST['pengirim_user_id'];
        $penerima_user_id = $_POST['penerima_user_id'];
        $items = $_POST['items'];

        // Begin transaction
        $conn->begin_transaction();

        try {
            $received_items = 0;
            $fully_received_items = 0;
            $total_items = count($items);

            // Process each item
            foreach ($items as $item) {
                $po_id = $item['po_id'];
                $barang_id = $item['barang_id'];
                $jumlah_pesan = $item['jumlah_pesan'];
                $jumlah_terima = $item['jumlah_terima'];

                // Only insert if jumlah_terima > 0
                if ($jumlah_terima > 0) {
                    // Insert into penerimaan_barang
                    $sql_insert = "INSERT INTO penerimaan_barang 
                                  (kode_penerimaan, barang_id, jumlah, tanggal_penerimaan, 
                                   tipe_penerimaan, supplier_id, dari_user_id, penerima_user_id) 
                                  VALUES 
                                  ('$kode_penerimaan', '$barang_id', '$jumlah_terima', '$tanggal_penerimaan', 
                                   'admin', NULL, '$pengirim_user_id', '$penerima_user_id')";

                    if (!$conn->query($sql_insert)) {
                        throw new Exception($conn->error);
                    }

                    $received_items++;

                    if ($jumlah_terima == $jumlah_pesan) {
                        $fully_received_items++;
                    }
                }
            }

            // Update pre_order_barang status
            if ($fully_received_items == $total_items) {
                // All items fully received
                $new_status = 'diterima';
            } else if ($received_items > 0) {
                // Partially received - keep as 'dipesan' or create new status 'sebagian'
                $new_status = 'diterima'; // You can change this to 'sebagian' if needed
            } else {
                // Nothing received
                throw new Exception('Tidak ada barang yang diterima');
            }

            $sql_update_po = "UPDATE pengeluaran_barang 
                            SET status = '$new_status' 
                            WHERE kode_pengeluaran = '$kode_pengeluaran'";

            if (!$conn->query($sql_update_po)) {
                throw new Exception($conn->error);
            }

            $conn->commit();
            echo 'ok';
            http_response_code(200);

        } catch (Exception $e) {
            $conn->rollback();
            echo 'Error: ' . $e->getMessage();
            http_response_code(400);
        }
        break;
}