<?php
ob_start();
include './inc/db_connect.php';
include './inc/header.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses upload file bukti pembayaran
    // Upload Payment Proof
if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "./img/payment_proofs/"; // Store in 'img/payment_proofs' directory (relative to public folder)
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_tmp = $_FILES['payment_proof']['tmp_name'];
    $file_name = basename($_FILES['payment_proof']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

    // Validate file extension
    if (in_array($file_ext, $allowed_ext)) {
        $file_new_name = uniqid("proof_", true) . "." . $file_ext;

        // Move file to the target directory
        if (move_uploaded_file($file_tmp, $target_dir . $file_new_name)) {
            $payment_proof_path = "img/payment_proofs/" . $file_new_name; // Store relative path to file
        } else {
            die("Failed to upload payment proof.");
        }
    } else {
        die("Invalid file type. Only JPG, PNG, and PDF are allowed.");
    }
} else {
    die("Payment proof is required or upload failed. Error: " . $_FILES['payment_proof']['error']);
}


    // Simpan data pesanan ke tabel `orders`
    $order_date = date("Y-m-d H:i:s");
    $order_status = "Pending";
    $payment_mode = $_POST['payment_mode'];

    $insert_order = "INSERT INTO orders 
                    (user_id, address, firstname, lastname, email, contact_no, city, pincode, payment_mode, payment_proof, order_date, order_status) 
                    VALUES ('$uid', '{$_POST['address']}', '{$_POST['firstname']}', '{$_POST['lastname']}', '{$_POST['email']}', 
                            '{$_POST['contact_no']}', '{$_POST['city']}', '{$_POST['pincode']}', 
                            '$payment_mode', '$payment_proof_path', '$order_date', '$order_status')";
    mysqli_query($conn, $insert_order);

    // Ambil ID order yang baru saja dibuat
    $order_id = mysqli_insert_id($conn);

    // Ambil data dari tabel `cart` untuk pengguna ini
    $cart_items = mysqli_query($conn, "SELECT * FROM cart_view WHERE user_id = '$uid'");

    // Proses setiap item di keranjang untuk dimasukkan ke tabel `order_items`
    $total_price = 0;
    while ($item = mysqli_fetch_assoc($cart_items)) {
        $product_id = $item['id']; // ID produk
        $price = $item['product_price']; // Harga produk
        $quantity = $item['quantity']; // Kuantitas produk

        $total_price += $price * $quantity;

        // Masukkan data ke tabel `order_items`
        mysqli_query($conn, "INSERT INTO order_items 
            (order_id, product_id, product_price, quantity) 
            VALUES ('$order_id', '$product_id', '$price', '$quantity')");
    }

    // Hapus data keranjang setelah checkout selesai
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$uid'");

    // Redirect ke halaman orders
    header("Location: orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>

<body>
    <div class="container">
        <h3 class="text-left text-orange">Checkout</h3>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="contact_no">Contact Number</label>
                <input type="text" id="contact_no" name="contact_no" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="pincode">Pincode</label>
                <input type="text" id="pincode" name="pincode" class="form-control" required>
            </div>

            <!-- Upload Bukti Pembayaran -->
            <div class="form-group">
                <label for="payment_proof">Upload Payment Proof</label><br>
                <input type="file" id="payment_proof" name="payment_proof" accept="image/*,application/pdf" required>
            </div>

            <!-- Pilihan Metode Pembayaran -->
            <div class="form-group">
                <label for="payment_mode">Payment Method</label><br>
                <input type="radio" id="bank_transfer" name="payment_mode" value="BRI" required>
                <label for="bank_transfer">BRI (0320-01-013785-50-6 A.N IyongTailor)</label><br>
                <input type="radio" id="bank_transfer" name="payment_mode" value="BNI" required>
                <label for="bank_transfer">BNI (010 642 703 5 A.N IyongTailor)</label><br>
                <input type="radio" id="bank_transfer" name="payment_mode" value="BCA" required>
                <label for="bank_transfer">BCA (032 900 977 9 A.N IyongTailor)</label><br>
            </div>

            <button type="submit" class="btn custom-btn">Place Order</button>
        </form>
    </div>
</body>

</html>

<?php
include './inc/footer.php';
ob_end_flush();
?>
