<?php
session_start();
require 'inc/db_connect.php';

// Pastikan user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');  // Redirect ke login jika bukan admin
    exit();
}

if (isset($_GET['log_id'])) {
    $log_id = $_GET['log_id'];

    // Update status request_log menjadi waiting_admin atau in_progress
    $query = "UPDATE request_log SET status = 'waiting_admin' WHERE log_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $log_id);

    if ($stmt->execute()) {
        // Update status di table request (status kembali ke 'pending' atau 'in_progress')
        $update_query = "UPDATE request SET status = 'in_progress' WHERE request_id = (SELECT request_id FROM request_log WHERE log_id = ?)";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $log_id);

        if ($update_stmt->execute()) {
            $_SESSION['success'] = "Bukti penyelesaian ditolak, status dikembalikan menjadi 'in_progress'.";
        } else {
            $_SESSION['error'] = "Gagal mengubah status di request.";
        }
    } else {
        $_SESSION['error'] = "Gagal mengubah status request log.";
    }
} else {
    $_SESSION['error'] = "Log ID tidak ditemukan.";
}

header('Location: admin-request.php');
exit();
?>
