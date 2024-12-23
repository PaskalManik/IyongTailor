<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Tangkap data dari form POST
$name = $_POST['name'] ?? 'N/A';
$gaji_kotor = $_POST['gaji'] ?? 0;
$start_date = $_POST['start_date'] ?? '-';
$end_date = $_POST['end_date'] ?? '-';

// Hitung potongan dan gaji bersih
$gaji_bersih = $gaji_kotor ;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran Gaji</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .receipt {
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #ccc;
            max-width: 600px;
            background-color: #f9f9f9;
        }
        .btn-print {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h4 class="text-center">Bukti Pembayaran Gaji</h4>
        <hr>
        <p><strong>Nama Karyawan:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Periode:</strong> <?php echo htmlspecialchars($start_date) . " s/d " . htmlspecialchars($end_date); ?></p>
        <p><strong>Gaji Bersih:</strong> Rp <?php echo number_format($gaji_bersih, 0, ',', '.'); ?></p>
        <hr>
        <p class="text-center">Terima kasih atas kerja keras Anda!</p>
        <div class="text-center btn-print">
            <button class="btn btn-primary" onclick="window.print()">Cetak Bukti</button>
        </div>
    </div>
</body>
</html>
