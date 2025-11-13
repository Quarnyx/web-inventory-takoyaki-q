<?php

if (isset($_GET['page'])) {
    if ($_GET['page'] === 'dashboard') {
        include 'pages/dashboard.php';
    } else {
        include 'pages/' . $_GET['page'] . '/index.php';
    }
}