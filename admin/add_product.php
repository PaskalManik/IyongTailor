<?php
  session_start();

  // Check if user is logged in and is an admin
  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access! Please log in as admin.');</script>";
    header('Location: ../login.php'); // Redirect to login page
    exit();
  }

  include './inc/header.php';
  include './inc/sidebar.php';
  include './inc/db_connect.php';

  if (isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id']; // Get user_id from session
      mysqli_query($conn, "SET @user_id = $user_id"); // Set the user_id for MySQL session
  } else {
      echo "User is not logged in.";
      exit();
  }

  $showModal = false;

  if (isset($_POST["add_product"])) {
    // Generate unique names for the files
    $v1 = rand(1111, 9999);
    $v2 = rand(1111, 9999);
    $v3 = $v1 . $v2;
    $v3 = md5($v3);

    // Upload the featured image
    $fnm = $_FILES["pimage"]["name"];
    $destn = "./product_img/" . $v3 . $fnm;
    $destn1 = "product_img/" . $v3 . $fnm;
    if (!move_uploaded_file($_FILES["pimage"]["tmp_name"], $destn)) {
        echo "Error uploading featured image.";
        exit;
    }

    // Upload preview images
    $img_arr = [];
    for ($i = 1; $i <= 4; $i++) {
        $pre_fnm = $_FILES["pimg$i"]["name"];
        if ($pre_fnm) {
            $pre_destn = "./product_img/preview_img/" . $v3 . $pre_fnm;
            $pre_destn1 = "product_img/preview_img/" . $v3 . $pre_fnm;
            if (move_uploaded_file($_FILES["pimg$i"]["tmp_name"], $pre_destn)) {
                $img_arr[] = $pre_destn1;
            } else {
                echo "Error uploading preview image $i.";
                exit;
            }
        }
    }

    // Serialize preview images
    $img_arr_serialized = serialize($img_arr);

    // Collect form data
    $product_name = mysqli_real_escape_string($conn, $_POST['pname']);
    $product_slider = mysqli_real_escape_string($conn, $_POST['pslider']);
    $product_price = mysqli_real_escape_string($conn, $_POST['pprice']);
    $product_stock = mysqli_real_escape_string($conn, $_POST['pqty']);
    $product_description = mysqli_real_escape_string($conn, $_POST['pdesc']);

    // Call the stored procedure
    $stmt = $conn->prepare("CALL AddProduct(?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssdiiss", 
        $product_name, 
        $product_slider, 
        $product_price, 
        $product_stock, 
        $destn1, 
        $img_arr_serialized, 
        $product_description
    );

    if ($stmt->execute()) {
        $showModal = true; // Set flag to show modal
    } else {
        echo "Error: " . mysqli_error($conn);
    }
  }
?>

<!-- HTML Starts Here -->
<div id="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumbs-->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb arr-right bg-light">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Products</li>
      </ol>
    </nav>

    <!-- Page Content -->
    <section>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-10 custom-card my-4 p-5">
            <h3 class="text-orange text-center py-1">Add Product</h3>
            <form name="form1" action="" method="POST" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-6 form-group py-2">
                  <label>Product Name:</label>
                  <input type="text" class="form-control" placeholder="Product Name" name="pname" required>
                </div>
                <div class="col-md-6 form-group py-2">
                  <label>Product Featured Image:</label>
                  <input type="file" class="form-control-file" name="pimage" required>
                </div>
                <div class="col-md-6 form-group py-2">
                  <label>Product Preview Images (Max 4):</label>
                  <input type="file" class="form-control-file" name="pimg1">
                  <input type="file" class="form-control-file" name="pimg2">
                  <input type="file" class="form-control-file" name="pimg3">
                  <input type="file" class="form-control-file" name="pimg4">
                </div>
                <div class="col-md-6 form-group py-2">
                  <label>Product Price:</label>
                  <input type="text" class="form-control" placeholder="Product Price" name="pprice" required>
                </div>
                <div class="col-md-6 form-group py-2">
                  <label>Product Quantity:</label>
                  <input type="text" class="form-control" placeholder="Product Quantity" name="pqty" required>
                </div>
                <div class="col-md-6 form-group py-2">
                  <label>Slider(Appears on):</label>
                  <select class="form-control" name="pslider" required>
                    <option value="Product">Product</option>
                  </select>
                </div>
                <div class="col-md-6 form-group py-2">
                  <label>Product Description:</label>
                  <textarea class="form-control" name="pdesc" rows="3" required></textarea>
                </div>
                <button type="submit" name="add_product" class="btn custom-btn btn-block my-2" value="upload">Add Product</button>
              </div>
            </form>
          </div>
          <div class="col-md-1"></div>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Produk Ditambahkan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p>Produk berhasil ditambahkan!</p>
      </div>
      <div class="modal-footer d-flex justify-content-center">
        <a href="index.php" class="btn btn-success">Kembali ke Dashboard</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($showModal): ?>
  <script>
    var myModal = new bootstrap.Modal(document.getElementById('successModal'));
    myModal.show();
  </script>
<?php endif; ?>

<?php include './inc/footer.php'; ?>
