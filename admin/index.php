<!-- header include -->
<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
  // Jika tidak login atau bukan admin, arahkan ke login
  echo "<script>alert('Unauthorized access! Please log in as admin.');</script>";
  header('Location: ../login.php'); // Arahkan ke halaman login utama
  exit();
}

include './inc/header.php';
include './inc/db_connect.php';
?>
<!-- sidebar include -->
<?php
include './inc/sidebar.php';
?>

<!-- Main Page content  -->

<div id="content-wrapper">

  <div class="container-fluid">

    <!-- Breadcrumbs-->
    <nav aria-label="breadcrumb ">
      <ol class="breadcrumb arr-right bg-light ">
        <li class="breadcrumb-item "><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Overview</li>
      </ol>
    </nav>
    <!-- Icon Cards-->
    <div class="row">
      <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-primary o-hidden h-100">
          <div class="card-body">
            <div class="card-body-icon">
              <i class="fa fa-fw fa-user"></i>
            </div>
            <div class="mr-5">
              <?php

              $result = mysqli_query($conn, "SELECT count(*) FROM users");
              $rows = mysqli_fetch_array($result);

              echo  $rows[0];

              ?>
              Registered Users</div>
          </div>
          <a class="card-footer text-white clearfix small z-1" href="users.php">
            <span class="float-left">View Details</span>
            <span class="float-right">
              <i class="fa fa-angle-right"></i>
            </span>
          </a>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-warning o-hidden h-100">
          <div class="card-body">
            <div class="card-body-icon">
              <i class="fa fa-fw fa-list"></i>
            </div>
            <div class="mr-5"><?php

                              $result = mysqli_query($conn, "SELECT count(*) FROM products");
                              $rows = mysqli_fetch_array($result);

                              echo  $rows[0];

                              ?> Products</div>
          </div>
          <a class="card-footer text-white clearfix small z-1" href="all_products.php">
            <span class="float-left">View Details</span>
            <span class="float-right">
              <i class="fa fa-angle-right"></i>
            </span>
          </a>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-success o-hidden h-100">
          <div class="card-body">
            <div class="card-body-icon">
              <i class="fa fa-fw fa-shopping-cart"></i>
            </div>
            <div class="mr-5"><?php

                              $result = mysqli_query($conn, "SELECT count(*) FROM orders");
                              $rows = mysqli_fetch_array($result);

                              echo  $rows[0];

                              ?> Orders!</div>
          </div>
          <a class="card-footer text-white clearfix small z-1" href="orders.php">
            <span class="float-left">View Details</span>
            <span class="float-right">
              <i class="fa fa-angle-right"></i>
            </span>
          </a>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-danger o-hidden h-100">
          <div class="card-body">
            <div class="card-body-icon">
              <i class="fa fa-fw fa-ticket"></i>
            </div>
            <div class="mr-5"><?php

                              $result = mysqli_query($conn, "SELECT count(*) FROM reports");
                              $rows = mysqli_fetch_array($result);

                              echo  $rows[0];

                              ?> Reports!</div>
          </div>
          <a class="card-footer text-white clearfix small z-1" href="tickets.php">
            <span class="float-left">View Details</span>
            <span class="float-right">
              <i class="fa fa-angle-right"></i>
            </span>
          </a>
        </div>
      </div>
    </div>

    <!-- Recent Orders-->
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-shopping-cart text-dark"></i>
        Recent Orders
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Ordered by</th>
                <th>Total</th>
                <th>View</th>
              </tr>
            </thead>
            <tbody>

              <?php

              $res = mysqli_query($conn, "SELECT o.order_id,o.order_date,u.firstname,u.lastname,oi.product_price,oi.quantity,(oi.product_price * oi.quantity) AS total_price,p.product_name
  FROM orders o
  JOIN users u ON o.user_id = u.user_id
  JOIN order_items oi ON o.order_id = oi.order_id
  JOIN products p ON oi.product_id = p.id 
  ORDER BY o.order_id DESC
  LIMIT 5
");


              while ($row = mysqli_fetch_array($res)) {
              ?>
                <tr>
                  <td><?php echo $row['order_date']; ?></td>
                  <td><?php echo $row['product_name']; ?></td>
                  <td><?php echo $row['quantity']; ?></td>
                  <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                  <td>Rp <?php echo number_format($row['total_price'], 2); ?></td>
                  <td>
                    <a href="order-details.php?id=<?php echo $row['order_id']; ?>" style="text-decoration: none;">
                      <i class="fa text-dark fa-eye"></i> View
                    </a>
                  </td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer small text-muted">Updated Today at <?php echo date("h:i:sa"); ?></div>
      </div>

      <!-- Recently Registered users-->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fa fa-user text-dark"></i>
          Recently Registered Users
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>User id</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Contact</th>

                </tr>
              </thead>
              <tbody>

                <?php

                $res = mysqli_query($conn, " SELECT * FROM users ORDER BY user_id DESC LIMIT 5 ");

                while ($row = mysqli_fetch_array($res)) {

                ?>
                  <tr>
                    <td><?php echo $row['user_id'] ?></td>
                    <td><?php echo $row['firstname'] ?></td>
                    <td> <?php echo $row['lastname'] ?> </td>
                    <td><?php echo $row['email'] ?></td>
                    <td><?php echo $row['contact_no'] ?> </td>

                  <?php
                }
                  ?>
              </tbody>
            </table>
          </div>
          <div class="card-footer small text-muted">Updated Today at <?php echo date("h:i:sa"); ?> </div>
        </div>

        <!-- Recent Reports -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fa fa-fw fa-ticket text-dark"></i>
            Recent Reports
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Ticket id</th>
                    <th>Created On</th>
                    <th>Order id</th>
                    <th>Subject</th>
                    <th>Status</th>

                  </tr>
                </thead>
                <tbody>

                  <?php

                  $res = mysqli_query($conn, " SELECT * FROM reports ORDER BY report_id DESC LIMIT 5 ");

                  while ($row = mysqli_fetch_array($res)) {

                  ?>
                    <tr>
                      <td><?php echo $row['report_id'] ?></td>
                      <td><?php echo $row['report_date'] ?></td>
                      <td>
                        <a style="text-decoration: none;" href="order-details.php?id=<?php echo $row['order_id']; ?> ">
                          <i class="fa text-dark fa-eye"></i> View
                        </a>
                      </td>
                      <td><?php echo $row['subject'] ?></td>
                      <td><?php echo $row['status'] ?> </td>

                    <?php
                  }
                    ?>
                </tbody>
              </table>
            </div>
            <div class="card-footer small text-muted">Updated Today at <?php echo date("h:i:sa"); ?> </div>
          </div>

        </div>

        <!-- Tabel Info Pemasukan -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fa fa-fw fa-money text-dark"></i>
            Total Income Details
          </div>
          <div class="card-body">

            <!-- Form Filter Tanggal -->
            <form method="GET" action="">
              <div class="form-group row">
                <label for="start_date" class="col-sm-2 col-form-label">Start Date</label>
                <div class="col-sm-4">
                  <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>" required>
                </div>
                <label for="end_date" class="col-sm-2 col-form-label">End Date</label>
                <div class="col-sm-4">
                  <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $_GET['end_date'] ?? ''; ?>" required>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Filter</button>
            </form>
            <hr>

            <!-- Tabel Data -->
            <div class="table-responsive">
              <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Sumber</th>
                    <th>Total Pemasukan</th>
                    <th>Pendapatan Pemilik </th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Ambil tanggal dari form
                  $start_date = $_GET['start_date'] ?? null;
                  $end_date = $_GET['end_date'] ?? null;

                  // Validasi input tanggal
                  if ($start_date && $end_date) {
                    // Query untuk pemasukan dari request
                    $request_query = "
              SELECT SUM(rl.jumlah_diambil * m.harga_barang) AS total_request_income
              FROM request_log rl
              JOIN users u ON rl.user_id = u.user_id
              JOIN request r ON rl.request_id = r.request_id
              JOIN menu m ON r.menu_id = m.menu_id
              WHERE u.role = 'employee'
              AND rl.status = 'completed'
              AND rl.waktu_diambil BETWEEN '$start_date' AND '$end_date'
            ";
                    $request_result = mysqli_query($conn, $request_query);
                    $request_row = mysqli_fetch_array($request_result);
                    $total_request_income = $request_row['total_request_income'] ?? 0;

                    // Query untuk pemasukan dari orders
                    $order_query = "
              SELECT SUM(oi.product_price * oi.quantity) AS total_order_income
              FROM orders o
              JOIN order_items oi ON o.order_id = oi.order_id
              WHERE o.order_status = 'complete'
              AND o.order_date BETWEEN '$start_date' AND '$end_date'
            ";
                    $order_result = mysqli_query($conn, $order_query);
                    $order_row = mysqli_fetch_array($order_result);
                    $total_order_income = $order_row['total_order_income'] ?? 0;

                    // Hitung total income
                    $total_income = $total_request_income + $total_order_income;

                    $owner_share_request = $total_request_income * 0.82;
                    $owner_share_order = $total_order_income * 0.82;
                  } else {
                    $total_request_income = $total_order_income = $total_income = $owner_share_request = $owner_share_order = 0;
                  }
                  ?>
                  <tr>
                    <td>Pemasukan dari Request</td>
                    <td><?php echo "Rp " . number_format($total_request_income, 2); ?></td>
                    <td><?php echo "Rp " . number_format($owner_share_request, 2); ?></td>
                  </tr>
                  <tr>
                    <td>Pemasukan dari Orderan</td>
                    <td><?php echo "Rp " . number_format($total_order_income, 2); ?></td>
                    <td><?php echo "Rp " . number_format($owner_share_order, 2); ?></td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th>Total Income</th>
                    <th><?php echo "Rp " . number_format($total_income, 2); ?></th>
                    <th><?php echo "Rp " . number_format($total_income * 0.82, 2); ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div class="card-footer small text-muted">Updated Today at <?php echo date("h:i:sa"); ?></div>
          </div>
        </div>


        <!-- /.container-fluid -->



        <!-- footer include -->

        <?php
        include './inc/footer.php';
        ?>