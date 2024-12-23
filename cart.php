<?php
ob_start();
include './inc/header.php';
include './inc/db_connect.php';

$_SESSION["ref"] = "cart";
$user_id = $_SESSION['user_id'];
$oke = mysqli_query($conn, "SELECT * FROM cart_view WHERE user_id = '$user_id'");

?>

<div class="container">
    <div class="row my-4">
        <div class="col-md-12">
            <div class="order-summary clearfix custom-card">
                <?php
                if (mysqli_num_rows($oke) == 0) {
                    echo "<div class='py-5 my-5 text-center'>
                            <h4>Opps! No items in cart</h4>
                            <a href='index.php' class='btn custom-btn'>Start Shopping</a>
                          </div>";
                } else {
                    $g_total = 0;
                ?>
                    <table class="shopping-cart-table table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Nama</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($value = mysqli_fetch_assoc($oke)) {
                                $g_total += $value['TotalHarga'];
                            ?>
                                <tr>
                                    <td><img src="./admin/<?php echo $value['product_img']; ?>" width="50"></td>
                                    <td><?php echo $value['product_name']; ?></td>
                                    <td>Rp<?php echo $value['product_price']; ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $value['id']; ?>">
                                            <button type="submit" name="decreaseBtn" class="btn btn-sm btn-outline-danger">-</button>
                                        </form>
                                        <span><?php echo $value['quantity']; ?></span>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $value['id']; ?>">
                                            <button type="submit" name="increaseBtn" class="btn btn-sm btn-outline-success">+</button>
                                        </form>
                                    </td>
                                    <td>Rp<?php echo $value['TotalHarga']; ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="product_id" value="<?php echo $value['id']; ?>">
                                            <button name="deleteBtn" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">TOTAL</th>
                                <th>Rp<?php echo $g_total; ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                    <a href="checkout.php" class="btn custom-btn">Checkout</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php
// Handle Remove Item
if (isset($_POST['deleteBtn'])) {
    mysqli_query($conn, "DELETE FROM cart WHERE id = '$_POST[product_id]'");
    echo "<script>window.location.href = 'cart.php';</script>";
}

// Handle Increase Quantity
if (isset($_POST['increaseBtn'])) {
    $product_id = $_POST['product_id'];
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE id = '$product_id'");
    echo "<script>window.location.href = 'cart.php';</script>";
}

// Handle Decrease Quantity
if (isset($_POST['decreaseBtn'])) {
    $product_id = $_POST['product_id'];
    // Check if quantity > 1 before decreasing
    $result = mysqli_query($conn, "SELECT quantity FROM cart WHERE id = '$product_id'");
    $row = mysqli_fetch_assoc($result);
    if ($row['quantity'] > 1) {
        mysqli_query($conn, "UPDATE cart SET quantity = quantity - 1 WHERE id = '$product_id'");
    } else {
        echo "<script>alert('Minimum quantity is 1');</script>";
    }
    echo "<script>window.location.href = 'cart.php';</script>";
}

include './inc/footer.php';
ob_end_flush();
?>