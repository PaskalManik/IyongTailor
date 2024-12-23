<?php
session_start();
require './inc/db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo "<script>alert('You are already logged in. Redirecting to homepage.');</script>";
    header('Location: index.php');
    exit();
}

if (isset($_POST['signup-btn'])) {
    $fname = htmlspecialchars($_POST['firstname']);
    $lname = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['pass2'], PASSWORD_BCRYPT);
    $contact_no = htmlspecialchars($_POST['contact_no']);
    $role = 'customer'; // Default role

    // Prepare statement untuk memanggil prosedur
    $stmt = $conn->prepare("CALL RegisterUser(?, ?, ?, ?, ?, ?, @otp, @status)");
    $stmt->bind_param("ssssss", $fname, $lname, $email, $password, $contact_no, $role);
    $stmt->execute();

    // Ambil output dari prosedur
    $result_otp = $conn->query("SELECT @otp AS otp, @status AS status");
    $data = $result_otp->fetch_assoc();

    $status = $data['status'];
    $otp = $data['otp'];

    if ($status === 'User registered successfully') {
        // Kirim email menggunakan PHPMailer
        require 'vendor/autoload.php';
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'c31033338268e6';
            $mail->Password = '15652e8119508d';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('IyongTailor@gmail.com', 'IyongTailor');
            $mail->addAddress($email, "$fname $lname");

            $mail->isHTML(true);
            $mail->Subject = 'Verify your account with OTP';
            $mail->Body = "
                <html>
                    <body>
                        <h1>Your OTP is: <span style='color: #F8694A;'>$otp</span></h1>
                        <p>Please use this OTP to verify your account. Do not share it with anyone.</p>
                    </body>
                </html>
            ";

            $mail->send();
            echo "<script>alert('Account created! Check your email for the OTP to verify your account.');</script>";
            header('Location: verify.php?email=' . urlencode($email));
        } catch (Exception $e) {
            echo "<script>alert('Error sending verification email. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('$status');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Signup - IyongTailor</title>

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
                    <!-- Sign Up form -->
                    <div id="signup" class="py-2">
                        <h6 class="text-center text-orange">Create an account</h6>
                        <form action="" method="POST" oninput='pass2.setCustomValidity(pass2.value != pass1.value ? "Passwords do not match." : "")'>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-user" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-user" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-envelope-o" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-phone" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="text" class="form-control" name="contact_no" placeholder="Contact Number" required>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-key" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="password" class="form-control" name="pass1" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-key" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="password" class="form-control" name="pass2" placeholder="Confirm Password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-block custom-btn mt-3" name="signup-btn">Sign Up</button>
                            <div class="text-center pt-4 pb-1">
                                <a href="login.php">Already have an account?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </section>
</body>

</html>