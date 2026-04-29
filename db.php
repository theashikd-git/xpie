<?php
$host = "localhost";  // ✅ Plain string
$user = "root";
$pass = "";
$db   = "namias_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
