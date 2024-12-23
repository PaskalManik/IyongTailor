<?php
session_start();
require 'inc/db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_id = $_POST['log_id'];
    $upload_dir = '../img/'; // Folder penyimpanan gambar

    // Proses upload gambar
    if (isset($_FILES['bukti_selesai']) && $_FILES['bukti_selesai']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['bukti_selesai']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['bukti_selesai']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            // Update status menjadi waiting_admin
            $query = "UPDATE request_log SET bukti_selesai = ?, status = 'waiting_admin' WHERE log_id = ? AND status = 'in_progress'";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $file_name, $log_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $_SESSION['success'] = "Bukti berhasil diupload. Menunggu persetujuan admin.";

                    // Periksa apakah jumlah yang diambil sudah sesuai
                    $check_query = "
                        SELECT rl.jumlah_diambil, r.jumlah 
                        FROM request_log rl
                        INNER JOIN request r ON rl.request_id = r.request_id
                        WHERE rl.log_id = ?
                    ";
                    $check_stmt = $conn->prepare($check_query);
                    $check_stmt->bind_param("i", $log_id);
                    $check_stmt->execute();
                    $result = $check_stmt->get_result();
                    $data = $result->fetch_assoc();

                    if ($data['jumlah_diambil'] == $data['jumlah']) {
                        // Jika sudah selesai, update status menjadi completed
                        $update_query = "UPDATE request_log SET status = 'completed' WHERE log_id = ?";
                        $update_stmt = $conn->prepare($update_query);
                        $update_stmt->bind_param("i", $log_id);
                        $update_stmt->execute();
                    }
                } else {
                    $_SESSION['error'] = "Gagal mengubah status, pastikan status saat ini adalah 'in_progress'.";
                }
            } else {
                $_SESSION['error'] = "Gagal menyimpan data: " . $stmt->error;
            }
        } else {
            $_SESSION['error'] = "Gagal mengupload gambar.";
        }
    } else {
        $_SESSION['error'] = "Harap pilih file gambar yang valid.";
    }
}

header('Location: employee-work.php');
exit();
