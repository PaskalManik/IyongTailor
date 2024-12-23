<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
  echo "<script>alert('Unauthorized access! Please log in as admin.');</script>";
  header('Location: ../login.php');
  exit();
}

include './inc/header.php';
include './inc/sidebar.php';
include './inc/db_connect.php';

$oid = $_GET['id'];
?>

<!-- Main Content -->
<div id="content-wrapper">
  <div class="container-fluid">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb arr-right bg-light">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="orders.php">All Orders</a></li>
        <li class="breadcrumb-item active" aria-current="page">Order Information</li>
      </ol>
    </nav>

    <!-- Page Content -->
    <section>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-10 my-3">
            <h3 class="text-orange text-center py-1">Order Information</h3>
            <div class="container">
              <div class="custom-card my-4 py-3">

                <!-- Fetch Order Data -->
                <?php
                $query = "
                  SELECT 
                    o.order_id, 
                    o.order_date, 
                    o.order_status, 
                    o.payment_mode, 
                    o.payment_proof,
                    u.firstname, 
                    u.lastname, 
                    o.address, 
                    o.city, 
                    o.pincode,
                    p.product_name,
                    p.product_img,
                    oi.quantity,
                    (oi.quantity * oi.product_price) AS sub_total
                  FROM orders o
                  JOIN order_items oi ON o.order_id = oi.order_id
                  JOIN products p ON oi.product_id = p.id
                  JOIN users u ON o.user_id = u.user_id
                  WHERE o.order_id = $oid
                ";

                $res = mysqli_query($conn, $query);
                $order_details = [];

                while ($row = mysqli_fetch_assoc($res)) {
                  $order_details[] = $row;
                }

                if (count($order_details) > 0) {
                ?>
                  <div class="row mt-4">
                    <div class="col-12 text-center">
                      <h4 class="text-orange">Product Details</h4>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <?php foreach ($order_details as $item): ?>
                      <div class="col-md-3 text-center">
                        <img
                          class="img-fluid product-img mb-2 border rounded"
                          src="./<?php echo htmlspecialchars($item['product_img']); ?>"
                          alt="Product Image"
                          style="max-height: 150px; width: auto;">
                        <p class="text-muted font-weight-bold"><?php echo htmlspecialchars($item['product_name']); ?></p>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                        <p><strong>Subtotal:</strong> Rp<?php echo number_format($item['sub_total'], 2); ?></p>
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <hr class="py-2">
                  <div class="row">
                    <div class="col-md-6">
                      <h4 class="text-orange">Shipping Address</h4>
                      <p><?php echo htmlspecialchars($order_details[0]["firstname"] . ' ' . $order_details[0]["lastname"]); ?></p>
                      <p><?php echo htmlspecialchars($order_details[0]["address"]); ?></p>
                      <p><?php echo htmlspecialchars($order_details[0]["city"]); ?></p>
                      <p><?php echo htmlspecialchars($order_details[0]["pincode"]); ?></p>
                    </div>
                    <div class="col-md-6">
                      <h4 class="text-orange">Payment Proof</h4>
                      <?php if (!empty($order_details[0]["payment_proof"])): ?>
                        <img src="../<?php echo htmlspecialchars($order_details[0]["payment_proof"]); ?>" class="img-fluid border rounded" alt="Payment Proof" style="max-width: 300px;">
                      <?php else: ?>
                        <p class="text-muted">No payment proof uploaded.</p>
                      <?php endif; ?>
                    </div>
                  </div>
                  <hr class="py-2">
                  <div class="row">
                    <div class="col-md-6">
                      <p><strong>Order Date:</strong> <?php echo $order_details[0]["order_date"]; ?></p>
                      <p><strong>Total Price:</strong> Rp<?php echo number_format(array_sum(array_column($order_details, 'sub_total')), 2); ?></p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Status:</strong> <span class="text-orange"><?php echo $order_details[0]["order_status"]; ?></span>
                        <a href="javascript:void(0);" class="btn custom-btn ml-4" data-toggle="modal" data-target="#statusChangeModal">Change</a>
                      </p>
                    </div>
                  </div>
                <?php } else { ?>
                  <div class="alert alert-danger text-center">Order not found!</div>
                <?php } ?>

                <!-- Update Status Modal -->
                <div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Change Order Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <form method="POST" action="">
                        <div class="modal-body">
                          <p class="text-danger">This status will be visible to the user. Make sure it is correct.</p>
                          <select class="form-control" name="status" required>
                            <option value="In-Process">Pending</option>
                            <option value="Complete">Complete</option>
                          </select>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button type="submit" name="submit-btn" class="btn custom-btn">Save Changes</button>
                        </div>
                      </form>
                      <?php
                      if (isset($_POST["submit-btn"])) {
                        $status = mysqli_real_escape_string($conn, $_POST['status']);
                        $update_query = "UPDATE orders SET order_status='$status' WHERE order_id=$oid";
                        if (mysqli_query($conn, $update_query)) {
                          echo "<script>alert('Order status updated successfully!'); window.location.href = window.location.href;</script>";
                        } else {
                          echo "Error updating status: " . mysqli_error($conn);
                        }
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-1"></div>
        </div>
      </div>
    </section>
  </div>
</div>

<?php
include './inc/footer.php';
?>
