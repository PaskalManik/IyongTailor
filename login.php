<?php
session_start();
require './inc/db_connect.php';

// If the user is already logged in, redirect to the appropriate page
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo "<script>alert('You are already logged in. Redirecting to homepage.');</script>";
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login-btn'])) {
        $email = $_POST['lemail'];
        $password = $_POST['lpassword'];

        $query = "SELECT * FROM users WHERE email = '$email' AND is_verified = 1";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } elseif ($user['role'] === 'employee') {
                header('Location: employee/index.php');
            } elseif ($user['role'] === 'customer') {
                header('Location: index.php'); 
            }
            exit();
        } else {
            echo "<script>alert('Invalid credentials or email not verified.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login/Signup - IyongTailor</title>

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <!-- Favicon -->
	<link rel="icon" href="./img/favicon_iyongtailor2.png" type="image/x-icon"/>
	<link rel="shortcut icon" href="./img/favicon_iyongtailor2.png" type="image/x-icon"/>
</head>
<body>
    <section class="container">
        <div class="row py-4 my-3">
            <div class="col-md-4"></div>
            <div class="col-md-4 py-4">
                <div class="card p-3">
                <a class="logo my-1" style="text-decoration: none;" href="index.php">
    <h2 class="text-center">
        <img src="./img/icon_iyongtailor.png" style="max-width: 300px; height: 70px;">
    </h2>
</a>
                    <!-- Login Form -->
                    <div id="login" class="py-2">
                        <h6 class="text-center text-orange">Login</h6>
                        <form action="" method="POST" class="text-center">
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-envelope-o" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="email" class="form-control" name="lemail" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-key" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="password" class="form-control" name="lpassword" placeholder="Password" required>
                                </div>
                                <p class="ml-2 mt-1 text-left"> <a href="reset-password.php"><small>Forgot Password?</small></a></p>
                            </div>
                            <button type="submit" class="btn btn-block custom-btn mt-3" name="login-btn">Login</button>
                            <div class="text-center pt-4 pb-1">
                                <a href="register.php">Create an account?</a>
                            </div>
                        </form>
                    </div>

                   
    <footer class="text-center pt-3">
        <p class="text-muted"> <small> Copyright &copy; <script>document.write(new Date().getFullYear());</script> | All rights reserved </small> </p>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
