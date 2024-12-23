<?php
session_start();

include './inc/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access! Please log in as admin.');</script>";
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user_id from the session

$pid = $_GET['id']; // Get the product ID

// Set the user_id variable in MySQL session
mysqli_query($conn, "SET @user_id = $user_id");

// Perform the DELETE operation
if (isset($pid) && mysqli_query($conn, "DELETE FROM products WHERE id=$pid")) {
    header('Location: all_products.php'); 
} else {
    echo "<script>alert('Failed to delete product.');</script>";
}
?>
