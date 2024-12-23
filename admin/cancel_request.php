<?php
session_start();
require 'inc/db_connect.php';

// Pastikan user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Periksa apakah ada request_id yang dikirim
if ($_POST['action'] === 'cancel') {
    $request_id = $_POST['request_id'];
    $cancel_date = date('Y-m-d H:i:s'); // Format datetime sekarang
    
    $query = "UPDATE request SET status = 'cancelled', cancel_date = ? WHERE request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $cancel_date, $request_id);

    if ($stmt->execute()) {
        header('Location: admin-request.php?message=Request berhasil dibatalkan');
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Request tidak valid!";
}
?>
