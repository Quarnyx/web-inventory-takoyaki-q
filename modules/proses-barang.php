<?php
require_once('../modules/config.php');
switch ($_GET['aksi'] ?? '') {
    case 'tambah-barang':
        $nama_barang = $_POST['nama_barang'];
        $kode_barang = $_POST['kode_barang'];
        $satuan = $_POST['satuan'];
        $kategori = $_POST['kategori'];
        $stok_minimal = $_POST['stok_minimal'];
        $harga_beli = $_POST['harga_beli'];
        $sql = "INSERT INTO barang (nama_barang, kode_barang, satuan, kategori_id, stok_minimal, harga_beli) VALUES ('$nama_barang', '$kode_barang', '$satuan', '$kategori', '$stok_minimal', '$harga_beli')";
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
    case 'hapus-barang':
        $id = $_POST['id'];
        $sql = "DELETE FROM barang WHERE id = '$id'";
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
    case 'edit-barang':
        $id = $_POST['id'];
        $nama_barang = $_POST['nama_barang'];
        $kode_barang = $_POST['kode_barang'];
        $satuan = $_POST['satuan'];
        $kategori = $_POST['kategori'];
        $stok_minimal = $_POST['stok_minimal'];
        $harga_beli = $_POST['harga_beli'];
        $sql = "UPDATE barang SET nama_barang = '$nama_barang', kode_barang = '$kode_barang', satuan = '$satuan', kategori_id = '$kategori', stok_minimal = '$stok_minimal', harga_beli = '$harga_beli' WHERE id = '$id'";

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