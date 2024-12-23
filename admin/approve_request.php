<?php
session_start();
require 'inc/db_connect.php';

// Periksa apakah user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');  // Redirect ke login jika bukan admin
    exit();
}

if (isset($_POST['approve'])) {
    $request_id = $_POST['request_id'];

    // Update status request menjadi approved
    $query = "UPDATE request SET status = 'approved' WHERE request_id = '$request_id'";
    if (mysqli_query($conn, $query)) {
        header('Location: admin_approve_request.php');  // Redirect kembali ke halaman admin setelah persetujuan
    } else {
        echo "<script>alert('Terjadi kesalahan saat mengupdate status.');</script>";
    }
}
?>
