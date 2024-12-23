<?php
$db_username = "root";
$db_password = "";
$db_name = "msbd";

$conn = mysqli_connect("localhost", $db_username, $db_password);
mysqli_select_db($conn, $db_name);
