<?php
require_once('../modules/config.php');
switch ($_GET['aksi'] ?? '') {
    case 'tambah-supplier':
        $nama_supplier = $_POST['nama_supplier'];
        $kontak = $_POST['kontak'];
        $alamat = $_POST['alamat'];
        $sql = "INSERT INTO suppliers (nama_supplier, kontak, alamat) VALUES ('$nama_supplier', '$kontak', '$alamat')";
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
    case 'hapus-supplier':
        $id = $_POST['id'];
        $sql = "DELETE FROM suppliers WHERE id = '$id'";
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
    case 'edit-supplier':
        $id = $_POST['id'];
        $nama_supplier = $_POST['nama_supplier'];
        $kontak = $_POST['kontak'];
        $alamat = $_POST['alamat'];
        $sql = "UPDATE suppliers SET nama_supplier = '$nama_supplier', kontak = '$kontak', alamat = '$alamat' WHERE id = '$id'";

        $result = $conn->query($sql);
        if ($result) {
            echo 'ok';
            http_response_code(200);
        } else {
            echo $conn->error;
            http_response_code(400);
        }
        break;
}