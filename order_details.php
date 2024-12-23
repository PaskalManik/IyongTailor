<?php
ob_start();
include "./inc/header.php";
include "./inc/db_connect.php";

// Cek jika pengguna sudah login dan memiliki role 'customer'
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit();
}

// Cek jika ID pesanan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid or missing Order ID. Redirecting to orders page...</p>";
    header("Refresh: 2; URL=orders.php");
    exit();
}

$oid = intval($_GET['id']);
?>

<div class="container">
    <div class="custom-card my-4 py-3">
        <h3 class="text-left text-orange">Order Information</h3>
        <hr class="py-2">
        <div class="order px-3 my-4">

            <?php
            // Fetch order details with order items
            $stmt = $conn->prepare("SELECT o.*, oi.*, p.product_name, p.product_img 
                                    FROM orders o 
                                    JOIN order_items oi ON o.order_id = oi.order_id 
                                    JOIN products p ON oi.product_id = p.id 
                                    WHERE o.order_id = ?");
            $stmt->bind_param("i", $oid);
            $stmt->execute();
            $res = $stmt->get_result();

            $total_price = 0; 

            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $uid = $row['user_id'];
                    $total_price += $row['product_price'] * $row['quantity'];
            ?>
                    <!-- Product details -->
                    <div class="row mt-4">
                        <div class="col-4">
                            <span class="d-inline-block align-middle">
                                <img class="img-fluid product-img p-2 d-inline-block" src="./admin/<?php echo htmlspecialchars($row["product_img"]); ?>" alt="Product Image">
                            </span>
                        </div>
                        <div class="col-8">
                            <h4 class="text-orange"><?php echo htmlspecialchars($row["product_name"]); ?></h4>
                            <p>Price: Rp<?php echo htmlspecialchars(number_format($row["product_price"], 0, ',', '.')); ?></p>
                            <p>Status: <?php echo htmlspecialchars($row["order_status"]); ?></p>
                            <p>Date: <?php echo htmlspecialchars($row["order_date"]); ?></p>
                            <p>Qty: <?php echo htmlspecialchars($row["quantity"]); ?></p>
                        </div>
                    </div>
                    <hr class="my-3">
            <?php
                }
            } else {
                echo "<p>Order not found.</p>";
            }
            ?>

            <!-- Total Price -->
            <div class="row my-4">
                <div class="col-12 text-right">
                    <h5 class="text-orange">Total Price: Rp<?php echo number_format($total_price, 0, ',', '.'); ?></h5>
                </div>
            </div>

            <!-- Payment Information and Addresses from 'orders' table -->
            <?php
            // Query ulang untuk mendapatkan informasi pembayaran dan alamat
            $stmt2 = $conn->prepare("SELECT o.payment_mode, o.firstname, o.lastname, o.address, o.city, o.pincode 
                                    FROM orders o 
                                    WHERE o.order_id = ?");
            $stmt2->bind_param("i", $oid);
            $stmt2->execute();
            $res2 = $stmt2->get_result();

            if ($res2->num_rows > 0) {
                $row2 = $res2->fetch_assoc();
            ?>
                <div class="row mb-3 pl-5">
                    <div class="col-12">
                        <hr class="py-2">
                        <h4 class="text-orange">Payment Information</h4>
                        <h6 class="my-3"><?php echo htmlspecialchars($row2["payment_mode"]); ?></h6>
                        <hr class="py-2">
                    </div>
                    

                    

                    <!-- Shipping Address (Same as Billing Address) -->
                    <div class="col-md-6 py-3">
                        <h4 class="text-orange">Shipping Address</h4>
                        <p><?php echo htmlspecialchars($row2["firstname"] . ' ' . $row2['lastname']); ?></p>
                        <p><?php echo htmlspecialchars($row2["address"]); ?></p>
                        <p><?php echo htmlspecialchars($row2["city"]); ?></p>
                        <p><?php echo htmlspecialchars($row2["pincode"]); ?></p>
                    </div>
                </div>
            <?php
            } else {
                echo "<p>No payment and address information available.</p>";
            }
            ?>

            <!-- Help Section -->
            <div class="text-center my-5">
                <h6>Need help with your order? <a href="javascript:void(0);" class="text-orange btn custom-btn"  data-toggle="modal" data-target="#exampleModal">Need Help</a></h6>
            </div>

        </div>
    </div>
</div>

<!-- Modal for Help -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Need Help: Submit your issue</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Subject</label>
                        <select class="form-control" name="subject" required>
                            <option>Select your issue</option>
                            <option value="Cancel Order">Cancel Order</option>
                            <option value="Size Issue">Size Issue</option>
                            <option value="Item not Received">Item not Received</option>
                            <option value="Defect Item Received">Defect Item Received</option>
                            <option value="Others">Others(Describe it below)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea class="form-control" rows="3" name="message" placeholder="Enter Message.." required></textarea>
                    </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn custom-btn" name="submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php  
// Submitting the user issue to database
if (isset($_POST["submit-btn"])) {
    $msg = htmlentities($_POST['message']);
    $date = date("d-m-y");

    if (!mysqli_query($conn, "INSERT INTO reports (order_id, user_id, subject, message, status, report_date) VALUES($oid, $uid, '$_POST[subject]', '$msg', 'In Process', '$date')")) {
        echo("Error description: " . mysqli_error($conn));
        ?>
        <script type="text/javascript">
            alert("Opps! Some error occurred.");
        </script>
        <?php
    } else {
        ?>
        <script type="text/javascript">
            alert("Issue successfully submitted. We'll contact you soon.");
        </script>
        <?php
    }
}
?>

<?php
include "./inc/footer.php";
ob_end_flush();
?>
