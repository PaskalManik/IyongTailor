<?php
session_start();
require 'inc/db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

// Validasi input
if (!isset($_POST['request_id']) || !isset($_POST['ambil_jumlah'])) {
    echo "Error: Data tidak lengkap!";
    exit();
}

$request_id = (int) $_POST['request_id'];
$ambil_jumlah = (int) $_POST['ambil_jumlah'];
$user_id = $_SESSION['user_id'];
$waktu_diambil = date('Y-m-d H:i:s'); 

if ($ambil_jumlah <= 0) {
    echo "Error: Jumlah yang diambil harus lebih besar dari 0!";
    exit();
}

$query = "SELECT jumlah, status FROM request WHERE request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $jumlah_tersedia = (int) $row['jumlah'];

    if ($ambil_jumlah > $jumlah_tersedia) {
        echo "Error: Jumlah yang diambil melebihi jumlah tersedia!";
        exit();
    }

    // Hitung jumlah sisa
    $jumlah_sisa = $jumlah_tersedia - $ambil_jumlah;

    // Jangan ubah status langsung menjadi 'completed' jika jumlah diambil sudah sesuai
    $status = 'approved'; // Tetap 'approved', menunggu persetujuan admin

    // Update jumlah di tabel request (status tetap 'approved' untuk menunggu persetujuan admin)
    $update_query = "UPDATE request SET jumlah = ?, status = ? WHERE request_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("isi", $jumlah_sisa, $status, $request_id);

    if ($update_stmt->execute()) {
        // Catat ke tabel request_log dengan status 'in_progress'
        $log_query = "INSERT INTO request_log (request_id, user_id, jumlah_diambil, waktu_diambil, status) 
                      VALUES (?, ?, ?, ?, 'in_progress')";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("iiis", $request_id, $user_id, $ambil_jumlah, $waktu_diambil);
        $log_stmt->execute();

        // Redirect ke halaman employee-work
        header('Location: employee-work.php');
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Request tidak ditemukan!";
}
?>
