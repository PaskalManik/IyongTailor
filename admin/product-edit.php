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
      include './inc/sidebar.php';

      include './inc/db_connect.php';

      $pid=$_GET['id'];

    ?>
      
  
  


  <!-- main content here -->

    <div id="content-wrapper">

      <div class="container-fluid">

        <!-- Breadcrumbs-->
        
        <nav aria-label="breadcrumb ">
         
          <ol class="breadcrumb arr-right bg-light ">
         
            <li class="breadcrumb-item "><a href="index.php">Dashboard</a></li>
         
            <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
         
          </ol>
         
        </nav>


        <!-- Page Content -->

      <section>
        <div class="container-fluid">
           <div class="row">
              <div class="col-md-1">
              </div>

              <div class="col-md-10 custom-card my-4 p-5">
                <h3 class="text-orange text-center py-1">Edit Product</h3>
                <form name="form1" action="" method="POST" enctype="multipart/form-data">
                  <div class="row">
                  <div class="col-md-6 form-group py-2">
                    <?php 
                     
                      $res = mysqli_query($conn, "SELECT * FROM products WHERE id=$pid");
                                        
                      while($row = mysqli_fetch_array($res)) {

                        $pimg_arr = $row['product_preview'];

                        $pimg_arr = unserialize($pimg_arr);
                      
                        ?>


                    <label>Product Name:</label>
                    <input type="text" class="form-control" value="<?php echo $row['product_name']; ?>" placeholder="Product Name" name="pname" required>
                  </div>
                  <div class="col-md-6 form-group py-2">
                    <label>Product Featured Image:</label>
                    <input type="file" class="form-control-file" name="pimage" >
                    <div style="height: 80px; width: 80px;">
                      <img class="img-fluid thumbnail" src="./<?php echo $row['product_img']; ?>" alt="">
                    </div>
                  </div>
                  <div class="col-md-6 form-group py-2">
                    <label>Product Preview Images (Max 4):</label>
                    <input type="file" class="form-control-file" name="pimg1" >
                    <input type="file" class="form-control-file" name="pimg2">
                    <input type="file" class="form-control-file" name="pimg3">
                    <input type="file" class="form-control-file" name="pimg4">
                    <div>
                      <?php 

                       ?>
                      <div class="row py-2">
                          <div class="col"><img class="img-fluid thumbnail" src="./<?php echo $pimg_arr[0]; ?>"/></div>
                          <div class="col"><img class="img-fluid thumbnail" src="./<?php echo $pimg_arr[1]; ?>"/></div>
                          <div class="col"><img class="img-fluid thumbnail" src="./<?php echo $pimg_arr[2]; ?>"/></div>
                          <div class="col"><img class="img-fluid thumbnail" src="./<?php echo $pimg_arr[3]; ?>"/></div>
                      
                    </div>
                    </div>
                  </div>
                  <div class="col-md-6 form-group py-5 mt-3">
                    <label>Product Price:</label>
                    <input type="text" class="form-control" placeholder="Product Price" name="pprice" required value="<?php echo $row['product_price']; ?>">
                  </div>
                  <div class="col-md-6 form-group py-2">
                    <label>Product Quantity:</label>
                    <input type="text" class="form-control" placeholder="Product Quantity" name="pqty" required value="<?php echo $row['product_stock']; ?>">
                  </div>
                  <div class="col-md-6 form-group py-2">
                    <label>Slider(Appears on):</label>
                    <select class="form-control" name="pslider" required>
                      <option value="Product">Product </option>
                    </select>
                    <span class="pl-2 text-orange"> <small>Current: <?php echo $row['product_slider']; ?> </small></span>
                  </div>
                  <div class="col-md-6 form-group py-2">
                    <label>Product Description:</label>
                    <textarea class="form-control" name="pdesc" rows="3" required><?php echo $row['product_description']; ?>
                    </textarea>
                  </div>
                  <?php
                        
                      }

                     ?>
                  <button type="submit" name="add_product" class="btn custom-btn btn-block my-2" value="upload">Edit Product</button>
                </div>
                </form>
              </div>

              <div class="col-md-1">
              </div>
           </div>
        </div>
      </section>
        
      <?php 
        
        if (isset($_POST["add_product"])) {
          
          $v1 = rand(1111,9999);
          $v2 = rand(1111,9999);
          $v3 = $v1.$v2;

          $v3 = md5($v3);

          $fnm = $_FILES["pimage"]["name"];
          $destn = "./product_img/".$v3.$fnm;
          $destn1 = "product_img/".$v3.$fnm;
          move_uploaded_file($_FILES["pimage"]["tmp_name"],$destn);

          // uploading preview images
          // pre img 1
          $pre_fnm1 = $_FILES["pimg1"]["name"];
          $pre_destn = "./product_img/preview_img/".$v3.$pre_fnm1;
          $pre_destn1 = "product_img/preview_img/".$v3.$pre_fnm1; // store it in db
          move_uploaded_file($_FILES["pimg1"]["tmp_name"],$pre_destn);

          //pre img 2
          $pre_fnm2 = $_FILES["pimg2"]["name"];
          $pre_destn = "./product_img/preview_img/".$v3.$pre_fnm2;
          $pre_destn2 = "product_img/preview_img/".$v3.$pre_fnm2; // store it in db
          move_uploaded_file($_FILES["pimg2"]["tmp_name"],$pre_destn);

          //pre img 3
          $pre_fnm3 = $_FILES["pimg3"]["name"];
          $pre_destn = "./product_img/preview_img/".$v3.$pre_fnm3;
          $pre_destn3 = "product_img/preview_img/".$v3.$pre_fnm3; // store it in db
          move_uploaded_file($_FILES["pimg3"]["tmp_name"],$pre_destn);

          //pre img 4
          $pre_fnm4 = $_FILES["pimg4"]["name"];
          $pre_destn = "./product_img/preview_img/".$v3.$pre_fnm4;
          $pre_destn4 = "product_img/preview_img/".$v3.$pre_fnm4; // store it in db
          move_uploaded_file($_FILES["pimg4"]["tmp_name"],$pre_destn);

          $img_arr = array($pre_destn1,$pre_destn2,$pre_destn3,$pre_destn4);
          $img_arr = serialize($img_arr);
        

          if (!mysqli_query($conn, "UPDATE products SET product_name='$_POST[pname]',  product_price=$_POST[pprice],  product_stock=$_POST[pqty], product_slider='$_POST[pslider]', product_img='$destn1', product_preview='$img_arr', product_description='$_POST[pdesc]' WHERE id=$pid ")) 

          {
            
          echo "Error description: " . mysqli_error($conn);

          } 

          else {

          ?>
          <script type="text/javascript">
            alert('Product Updated successfuly!');
           window.location.href = window.location.href; 
          </script>
          
          <?php
        }
      }

       
       ?> 
        
        <!-- / Page Content -->

      <!-- /.container-fluid -->

      
      <?php 
        include './inc/footer.php';
       ?>