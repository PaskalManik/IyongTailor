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
?>

<!-- Main content -->
<div id="content-wrapper">
  <div class="container-fluid">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb arr-right bg-light">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">All Orders</li>
      </ol>
    </nav>

    <!-- Page Content -->
    <section>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-1"></div>

          <div class="col-md-10 my-3">
            <h3 class="text-orange text-center py-1">All Orders</h3>

            <!-- All Orders -->
            <?php 
            $res = mysqli_query($conn, 
              "SELECT 
                  o.order_id, 
                  SUM(oi.product_price * oi.quantity) AS total_price,
                  SUM(oi.quantity) AS total_qty,
                  GROUP_CONCAT(p.product_name SEPARATOR ', ') AS product_names
               FROM orders o
               JOIN order_items oi ON o.order_id = oi.order_id
               JOIN products p ON oi.product_id = p.id
               GROUP BY o.order_id
               ORDER BY o.order_id DESC"
            );

            while ($row = mysqli_fetch_assoc($res)) {
            ?>
              <div class="order px-3 my-4 custom-card">
                <div class="row my-2">
                  <div class="col-md-9">
                    <p><strong>Order ID:</strong> <?php echo $row['order_id']; ?></p>
                    <p><strong>Total Quantity:</strong> <?php echo $row['total_qty']; ?></p>
                    <p><strong>Total Price:</strong> Rp <?php echo number_format($row['total_price']); ?></p>
                  </div>
                  <div class="col-md-3 text-right">
                    <a href="order-details.php?id=<?php echo $row['order_id']; ?>" class="btn custom-btn my-2">
                      <i class="fa fa-eye"></i> View
                    </a>
                  </div>
                </div>
              </div>
            <?php } ?>
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
