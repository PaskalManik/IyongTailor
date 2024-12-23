<?php
ob_start();

include './inc/header.php';
include './inc/db_connect.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$sql_menu = "SELECT * FROM menu";
$result_menu = mysqli_query($conn, $sql_menu);

$uid = $_SESSION['user_id']; // Ambil user_id dari session

$success = false; // Variabel untuk menentukan apakah berhasil atau tidak

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis_barang_id = $_POST['jenis_barang'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $ukuran_baju = $_POST['ukuran_baju'];
    $ukuran_lengan = $_POST['ukuran_lengan'];
    $jumlah = $_POST['jumlah'];
    $saran = $_POST['saran'];
    $gambar = "";
    $bukti_pembayaran = "";

    // Proses unggah gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $gambar_name = $_FILES['gambar']['name'];
        $gambar_tmp_name = $_FILES['gambar']['tmp_name'];
        $gambar_size = $_FILES['gambar']['size'];
        $gambar_ext = pathinfo($gambar_name, PATHINFO_EXTENSION);

        $target_dir = "img/";
        $target_file = $target_dir . basename($gambar_name);

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($gambar_ext), $allowed_extensions) && $gambar_size <= 5000000) {
            if (move_uploaded_file($gambar_tmp_name, $target_file)) {
                $gambar = $gambar_name;
            } else {
                echo "<script>alert('Gambar gagal diunggah.');</script>";
            }
        } else {
            echo "<script>alert('File gambar tidak valid.');</script>";
        }
    }

    // Proses unggah bukti pembayaran
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $bukti_name = $_FILES['bukti_pembayaran']['name'];
        $bukti_tmp_name = $_FILES['bukti_pembayaran']['tmp_name'];
        $bukti_size = $_FILES['bukti_pembayaran']['size'];
        $bukti_ext = pathinfo($bukti_name, PATHINFO_EXTENSION);

        $target_dir = "img/bukti_pembayaran/";
        $target_file = $target_dir . basename($bukti_name);

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($bukti_ext), $allowed_extensions) && $bukti_size <= 5000000) {
            if (move_uploaded_file($bukti_tmp_name, $target_file)) {
                $bukti_pembayaran = $bukti_name;
            } else {
                echo "<script>alert('Bukti pembayaran gagal diunggah.');</script>";
            }
        } else {
            echo "<script>alert('File bukti pembayaran tidak valid.');</script>";
        }
    }

    $sql_barang = "SELECT nama_barang FROM menu WHERE menu_id = ?";
    $stmt_barang = $conn->prepare($sql_barang);
    $stmt_barang->bind_param("i", $jenis_barang_id);
    $stmt_barang->execute();
    $result_barang = $stmt_barang->get_result();
    $row_barang = $result_barang->fetch_assoc();
    $nama_barang = $row_barang['nama_barang'];

    $sql = "CALL insert_request(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssisssss",
        $nama_barang,
        $jenis_kelamin,
        $ukuran_baju,
        $ukuran_lengan,
        $jumlah,
        $saran,
        $gambar,
        $bukti_pembayaran,
        $uid,
        $jenis_barang_id
    );

    if ($stmt->execute()) {
        $success = true; // Set variabel menjadi true jika berhasil
    } else {
        echo "<script>alert('Terjadi kesalahan. Silakan coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Product - Konveksi</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Formulir Pemesanan</h2>

        <form action="request-product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="jenis_barang">Jenis Barang</label>
                <select class="form-control" id="jenis_barang" name="jenis_barang" required>
                    <option value="">Pilih Jenis Barang</option>
                    <?php
                    while ($row_menu = mysqli_fetch_assoc($result_menu)) {
                        echo "<option value='" . $row_menu['menu_id'] . "' data-harga='" . $row_menu['harga_barang'] . "'>" . $row_menu['nama_barang'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="ukuran_baju">Ukuran Baju</label>
                <select class="form-control" id="ukuran_baju" name="ukuran_baju" required>
                    <option value="">Pilih Ukuran Baju</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                </select>
            </div>

            <div class="form-group">
                <label for="ukuran_lengan">Ukuran Lengan</label>
                <select class="form-control" id="ukuran_lengan" name="ukuran_lengan" required>
                    <option value="">Pilih Ukuran Lengan</option>
                    <option value="Pendek">Pendek</option>
                    <option value="Panjang">Panjang</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
            </div>

            <div class="form-group">
                <label for="saran">Saran atau Permintaan Khusus (Opsional)</label>
                <textarea class="form-control" id="saran" name="saran" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="gambar">Upload Gambar (Opsional)</label>
                <input type="file" class="form-control-file" id="gambar" name="gambar">
            </div>

            <div class="form-group">
                <label for="bukti_pembayaran">Upload Bukti Pembayaran</label>
                <input type="file" class="form-control-file" id="bukti_pembayaran" name="bukti_pembayaran" required>
            </div>

            <!-- Total Harga -->
            <div class="form-group">
                <label>Total Harga:</label>
                <h4 id="total_harga">Rp0</h4>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Kirim Pesanan</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jenisBarang = document.getElementById('jenis_barang');
            const jumlahInput = document.getElementById('jumlah');
            const totalHargaEl = document.getElementById('total_harga');

            function updateTotalHarga() {
                const harga = parseInt(jenisBarang.selectedOptions[0].getAttribute('data-harga')) || 0;
                const jumlah = parseInt(jumlahInput.value) || 0;
                const total = harga * jumlah;
                totalHargaEl.textContent = `Rp${total.toLocaleString()}`;
            }

            jenisBarang.addEventListener('change', updateTotalHarga);
            jumlahInput.addEventListener('input', updateTotalHarga);
        });
    </script>
</body>

</html>
