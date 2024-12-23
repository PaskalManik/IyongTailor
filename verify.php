<?php
session_start();
require './inc/db_connect.php';

if (isset($_POST['verify-btn'])) {
    $email = htmlspecialchars($_POST['email']);
    $otp = htmlspecialchars($_POST['otp']);

    $query = "SELECT * FROM users WHERE email='$email' AND verification_code='$otp'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $update_query = "UPDATE users SET is_verified=1, verification_code=NULL WHERE email='$email'";
        mysqli_query($conn, $update_query);
        echo "<script>alert('Account verified successfully! Please log in.');</script>";
        header('Location: login.php');
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <section class="container">
        <div class="row py-4 my-3">
            <div class="col-md-4"></div>
            <div class="col-md-4 py-4">
                <div class="card p-3">
                    <h3 class="text-center py-1">Verify OTP</h3>
                    <form action="" method="POST">
                        <div class="py-3">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="py-3">
                            <input type="text" class="form-control" name="otp" placeholder="Enter OTP" required>
                        </div>
                        <button type="submit" class="btn btn-block btn-primary" name="verify-btn">Verify</button>
                    </form>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </section>
</body>
</html>
