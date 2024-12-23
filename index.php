<?php 
   
    include './inc/header.php';
    include './inc/db_connect.php';
    
    $_SESSION["ref"] = "index";


?>


    <!-- Start Carousel  -->
    <section class="carousel-wrapper">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                <div class="carousel-item">
                    <img src="img/bannerfk3.jpg" class="d-block w-100" alt="...">
                    <div class="dark-overlay"></div>
                    <div class="carousel-text">
                        <div class="text-white text-center">
                            <h1 class="display-4">New Products</h1>
                            <h1 class="display-4">Collection</h1>
                            <a data-scroll href="#latest_deals" class="btn custom-btn btn-lg ml-2 mt-2">Shop Now</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item active">
                    <img src="img/bannerfk1.jpg" class="d-block w-100" alt="...">
                    <div class="dark-overlay"></div>
                    <div class="carousel-text">
                        <div class="text-center">
                            <h1 class="display-4 text-orange">Sale</h1>
                            <h3 class="text-white">Upto 50% Discount</h3>
                            <a data-scroll href="#todays_deal" class="btn custom-btn bg-dark btn-lg ml-2 mt-2">Shop Now</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="img/bannerfk2.jpg" class="d-block w-100" alt="...">
                    <div class="dark-overlay"></div>
                     <div class="carousel-text">
                        <div class="text-center">
                            <h1 class="display-4 text-dark">Hot Deals</h1>
                            <h3 class="text-dark">upto 50% Off</h3>
                            <a data-scroll href="#todays_deal" class="btn custom-btn btn-lg ml-2 mt-2">Shop Now</a>
                        </div>
                    </div>
                </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
                </a>
            </div>
    </section>

    <!--  End Carousel  -->

    <!-- Todays Deals Slider -->
    <section id="latest_deals">
            <div class="container">
                <div class="text-left">
                        <div class="" style="border-bottom: 1px solid #000;">
                            <h3 class="badge badge-primary custom-badge-light" style="margin-bottom: 0px;">Product</h3>
                        </div>
            <section class="deals-slider slider">
                
                <?php  

                $res = mysqli_query($conn, "SELECT * FROM products WHERE product_slider='Product' ");
                while ($row=mysqli_fetch_array($res)) {
                
                ?>

            <div class="product position-relative">
                <div class="custom-card p-0">
                    <img class="product-img img-fluid" src="./admin/<?php echo $row["product_img"] ?>">
                    <div class="p-2">
                        <div class="row">
                            <div class="col-7">
                                <h5 class="py-2">Rp<?php echo $row["product_price"] ?></h5>
                            </div>
                            <div class="col-5">
                                <div class="rating py-2 m-0">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <h6 class="py-1"> <a href="product_details.php?id=<?php echo $row["id"] ?>"><?php echo $row["product_name"] ?></a> </h6>
                        <div class="row py-3">
                            <div class="col-12 text-center">
                                <a href="product_details.php?id=<?php echo $row["id"] ?>" class="btn custom-btn btn-block font-sm"><i class="fa fa-eye text-white" aria-hidden="true"></i> SEE DETAILS </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <?php

                }
                
                ?>

                </section>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- End Today's Deals section-->


    

    <?php 
        include './inc/footer.php';
    ?>