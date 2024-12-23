<?php
session_start();
require 'inc/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];

    $query = "UPDATE request SET status = 'completed' WHERE request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Request berhasil dikonfirmasi selesai.";
    } else {
        $_SESSION['error'] = "Gagal mengonfirmasi request.";
    }

    header('Location: admin_requests.php');
    exit();
}
?>
