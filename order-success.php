<?php 
	ob_start();
	include './inc/header.php';
	$ref = $_GET['ref'];
	if ($ref != 'checkout') {
		header('Location: cart.php');
	}
 ?>
 <div class="container">
		
	<div class="my-5 text-center">
		<img class="img-fluid my-5" src="img/default.jpg" alt="Hapiness Img">
		<h2>Hurrah! Your Order was placed successfully.</h2>
		<div class="alert alert-info" role="alert">
			  	<i class="fa fa-info-circle"></i> You can review your order at <a style="text-decoration: underline;" href="orders.php">My Orders</a> section.
		</div>
		<a class="btn custom-btn btn-lg my-4" href="index.php">Shop more <i class="fa fa-angle-right text-white"></i> </a>
	</div>
 </div>
 <?php 
 include './inc/footer.php';
 	ob_end_flush();
  ?>