<?php
require_once('../modules/config.php');
switch ($_GET['aksi'] ?? '') {
    case 'tambah-kategori':
        $nama_kategori = $_POST['nama_kategori'];
        $keterangan = $_POST['keterangan'];
        $sql = "INSERT INTO kategori_barang (nama_kategori, keterangan) VALUES ('$nama_kategori', '$keterangan')";
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
    case 'hapus-kategori':
        $id = $_POST['id'];
        $sql = "DELETE FROM kategori_barang WHERE id = '$id'";
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
    case 'edit-kategori':
        $id = $_POST['id'];
        $nama_kategori = $_POST['nama_kategori'];
        $keterangan = $_POST['keterangan'];
        $sql = "UPDATE kategori_barang SET nama_kategori = '$nama_kategori', keterangan = '$keterangan' WHERE id = '$id'";

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