<?php
session_start();
require 'inc/db_connect.php';

// Pastikan user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Ambil log_id dari URL
if (isset($_GET['log_id'])) {
    $log_id = (int)$_GET['log_id'];

    // Ambil data log yang relevan
    $log_query = "
        SELECT rl.log_id, rl.request_id, r.jumlah, r.status as request_status
        FROM request_log rl
        INNER JOIN request r ON rl.request_id = r.request_id
        WHERE rl.log_id = ? AND rl.status = 'waiting_admin'
    ";
    $log_stmt = $conn->prepare($log_query);
    $log_stmt->bind_param("i", $log_id);
    $log_stmt->execute();
    $log_result = $log_stmt->get_result();

    if ($log_result->num_rows === 1) {
        $log = $log_result->fetch_assoc();
        $request_id = $log['request_id'];
        $current_request_status = $log['request_status'];
        $jumlah_barang = $log['jumlah'];

        // Update status request_log menjadi 'completed'
        $update_log_query = "
            UPDATE request_log
            SET status = 'completed'
            WHERE log_id = ?
        ";
        $update_log_stmt = $conn->prepare($update_log_query);
        $update_log_stmt->bind_param("i", $log_id);
        $update_log_stmt->execute();

        // Jika jumlah barangnya 0, update status request menjadi 'completed', jika tidak 'approved'
        $new_request_status = ($jumlah_barang == 0) ? 'completed' : 'approved';

        // Update status pada request table
        $update_request_query = "
            UPDATE request
            SET status = ?
            WHERE request_id = ?
        ";
        $update_request_stmt = $conn->prepare($update_request_query);
        $update_request_stmt->bind_param("si", $new_request_status, $request_id);
        $update_request_stmt->execute();

        $_SESSION['success'] = "Bukti disetujui dan status berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Data tidak ditemukan atau status tidak sesuai.";
    }
} else {
    $_SESSION['error'] = "ID log tidak ditemukan.";
}

header('Location: admin-employee.php');  
exit();
?>
