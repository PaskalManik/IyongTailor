<?php
ob_start();
include './inc/header.php';
include './inc/db_connect.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['user_id'];
?>

<div class="container">
    <div class="my-4 py-3">
        <h3 class="text-left text-orange">My Orders</h3>
        <hr class="py-2">

        <?php
        $res = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$uid' ORDER BY order_id DESC");

        // Perulangan untuk setiap order
        while ($order = mysqli_fetch_assoc($res)) {
            $order_id = $order['order_id'];
            $items = mysqli_query($conn, "
                SELECT oi.*, p.product_name, p.product_img 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = '$order_id'
            ");

            // Inisialisasi total harga untuk pesanan ini
            $total_order_price = 0;
        ?>
        <div class="order custom-card px-3 my-4">
            <h4>Order ID: <?php echo $order["order_id"]; ?></h4>
            <p>Date: <?php echo $order["order_date"]; ?></p>
            <p>Status: <?php echo $order["order_status"]; ?></p>
            <hr>

            <!-- Perulangan untuk setiap item dalam pesanan -->
            <?php while ($item = mysqli_fetch_assoc($items)) { ?>
                <div class="row my-3">
                    <div class="col-3">
                        <img class="img-fluid product-img" src="./admin/<?php echo $item["product_img"]; ?>" alt="Product Image">
                    </div>
                    <div class="col-9">
                        <h5><?php echo $item["product_name"]; ?></h5>
                        <p>Quantity: <?php echo $item["quantity"]; ?></p>
                        <p>Price: Rp<?php echo number_format($item["product_price"], 0, ',', '.'); ?></p>
                    </div>
                </div>
                <?php
                // Perhitungan total harga untuk pesanan ini
                $total_order_price += $item["product_price"] * $item["quantity"];
                ?>
            <?php } ?>

            <!-- Tampilkan total harga untuk pesanan -->
            <div class="row my-3">
                <div class="col-12 text-right">
                    <h5>Total Price: Rp<?php echo number_format($total_order_price, 0, ',', '.'); ?></h5>
                </div>
            </div>
            <div class="row my-3">
                <div class="col-12 text-right">
                    <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="btn custom-btn">Check Details</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php
include './inc/footer.php';
ob_end_flush();
?>
