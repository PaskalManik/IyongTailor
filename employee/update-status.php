<?php
session_start();
require 'inc/db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

if (!isset($_POST['log_id'])) {
    echo "Error: Data log_id tidak lengkap!";
    exit();
}

$log_id = (int) $_POST['log_id'];

// Perbarui status log menjadi completed
$query = "UPDATE request_log SET status = 'completed' WHERE log_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $log_id);

if ($stmt->execute()) {
    header('Location: employee-work.php');
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
