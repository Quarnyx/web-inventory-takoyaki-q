<?php

require_once('../modules/config.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);


    // Fetch user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if ($user['password'] == $password) {
            session_start();
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = $user['level'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];

            header('Location: ../app.php?page=dashboard');
        } else {
            header("location:../login.php?error=Password salah");
        }
    } else {
        header("location:../auth-login.php?error=Username tidak ditemukan");
    }

    $stmt->close();
}
?>