<?php
session_start();
require 'inc/db_connect.php';  // Pastikan koneksi ke database sudah benar

// Pastikan user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    $query = "UPDATE request SET status = 'cancelled' WHERE request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        header('Location: admin-request.php?message=Request cancelled successfully');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Request tidak valid!";
}
?>
