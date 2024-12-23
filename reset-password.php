<?php
session_start();

// Pastikan Anda telah mengonfigurasi Composer atau autoload
require 'vendor/autoload.php'; // Jika menggunakan Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Koneksi ke database (ganti dengan koneksi Anda sendiri)
include './inc/db_connect.php';

if (isset($_POST['reset-pwd'])) {
    if (isset($_SESSION['otp'])) {
        $temp_otp = $_POST['otp'];
        $temp_email = $_POST['email'];
        $pwd = password_hash($_POST['pass2'], PASSWORD_DEFAULT);
        if ($temp_otp == $_SESSION['otp'] && $temp_email == $_SESSION['otp_email']) {
            mysqli_query($conn, "UPDATE users SET password = '$pwd' WHERE email = '$_SESSION[otp_email]'");

            session_destroy(); 
            echo "<script>alert('Password Anda telah berhasil direset. Silakan login untuk melanjutkan.');</script>";
            echo "<script>window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('OTP tidak valid');</script>";
        }
    }
} else if (isset($_POST['send-otp'])) {
    $uemail = $_POST['email'];

    // Mengecek apakah email terdaftar di database
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$uemail'");
    if ($row = mysqli_fetch_array($res)) {
        $_SESSION['otp_email'] = $uemail;
        $new_otp = mt_rand(100000, 999999); // Generate OTP
        $_SESSION['otp'] = $new_otp;

        // Kirim OTP menggunakan PHPMailer
        $mail = new PHPMailer(true);

        try {
			$mail->isSMTP();
			$mail->Host = 'sandbox.smtp.mailtrap.io';
			$mail->SMTPAuth = true;
			$mail->Username = 'c31033338268e6';
			$mail->Password = '15652e8119508d';
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port = 587;

            $mail->setFrom('admin@IyongTailor', 'IyongTailor');
            $mail->addAddress($uemail); 

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset - IyongTailor';
            $mail->Body = "
                <html>
                    <head>
                        <title>Password Reset - IyongTailor</title>
                    </head>
                    <body style='text-align: center;'>
                        <div>
                            <h1 class='text-center'>
                                <span style='color: #F8694A'>Iyong</span><span>Tailor</span>
                            </h1>
                        </div>
                        <br>
                        <div>
                            <h1>Your OTP for password reset is : <br> <br> <span style='color: #F8694A'> $new_otp </span></h1>
                            <br>
                            * In case you have not requested for password reset, ignore this mail.
                            <br>
                            <br>
                            <strong>Note: Please don't share OTP with others.</strong>
                        </div>
                    </body>
                </html>
            ";

            $mail->send();
            echo "<script>alert('OTP telah dikirim ke email Anda.');</script>";
            echo "<script>window.location.href = 'reset-password.php';</script>";
        } catch (Exception $e) {
            echo "Email tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "<script>alert('Email tidak terdaftar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - IyongTailor</title>

    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <section class="container">
        <div class="row py-4 my-3">
            <div class="col-md-4"></div>
            <div class="col-md-4 py-4">
                <div class="card p-3">
                    <div class="logo">
                        <h3 class="text-center py-1"><span class="text-orange">Iyong</span><span class="text-custom-1">Tailor</span></h3>
                    </div>
                    <p class="text-center pt-4 text-custom-1">Reset Password</p>
                    <form action="" method="POST" oninput='pass2.setCustomValidity(pass2.value != pass1.value ? "Passwords do not match." : "")'>
                        <div class="py-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"> <i class="fa fa-envelope-o" aria-hidden="true"></i> </span>
                                </div>
                                <input type="email" class="form-control" id="password" placeholder="Email" name="email"
                                    <?php if (isset($_SESSION['otp_email'])) { echo 'readonly'; } ?>
                                    required value="<?php if (isset($_SESSION['otp_email'])) { echo $_SESSION['otp_email']; } ?>">
                            </div>
                        </div>

                        <?php if (isset($_SESSION['otp'])) { ?>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-eye" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="text" class="form-control" id="otp" placeholder="Enter OTP" name="otp" required>
                                </div>
                            </div>

                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-key" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="password" class="form-control" id="password" placeholder="New Password" name="pass1" required>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-key" aria-hidden="true"></i> </span>
                                    </div>
                                    <input type="password" class="form-control" id="password" placeholder="Confirm Password" name="pass2" required>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if (!isset($_SESSION['otp'])) { ?>
                            <button type="submit" name="send-otp" class="btn btn-block custom-btn mt-4 mb-5">Send OTP</button>
                        <?php } else { ?>
                            <button type="submit" name="reset-pwd" class="btn btn-block custom-btn mt-4 mb-5">Reset Password</button>
                        <?php } ?>

                        <small class="text-primary text-center">** Enter email we'll send you an OTP.</small>
                    </form>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </section>

    <footer class="text-center pt-3">
        <p class="text-muted"> <small> Copyright &copy; <script>document.write(new Date().getFullYear());</script> | All rights reserved </small> </p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="./js/script.js"></script>
</body>
</html>
