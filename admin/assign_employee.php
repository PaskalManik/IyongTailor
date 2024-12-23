<?php
session_start();
require 'inc/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];

    // Perbarui status permintaan menjadi 'approved'
    $query = "UPDATE request SET status = 'approved' WHERE request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Request berhasil disetujui.";
    } else {
        $_SESSION['error'] = "Gagal menyetujui request.";
    }

    header('Location: admin-request.php');
    exit();
}
