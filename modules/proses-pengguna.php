<?php
require_once('../modules/config.php');
switch ($_GET['aksi'] ?? '') {
    case 'tambah-pengguna':
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $nama_lengkap = $_POST['nama_lengkap'];
        $role = $_POST['role'];
        $no_telepon = $_POST['no_telepon'] ?? '';
        $sql = "INSERT INTO users (username, password, role, nama_lengkap, no_telepon) VALUES ('$username', '$password', '$role', '$nama_lengkap', '$no_telepon')";
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
    case 'hapus-pengguna':
        $id = $_POST['id'];
        $sql = "DELETE FROM users WHERE id = '$id'";
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
    case 'edit-pengguna':
        $id = $_POST['id'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $no_telepon = $_POST['no_telepon'] ?? '';
        $sql = "UPDATE users SET username = '$username', nama_lengkap = '$nama_lengkap', role = '$role', no_telepon = '$no_telepon' WHERE id = '$id'";

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
    case 'ganti-password':
        $id = $_POST['id'];
        $password = md5($_POST['password']);
        $sql = "UPDATE users SET password = '$password' WHERE id = '$id'";
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