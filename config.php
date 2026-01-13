<?php
$host = "sql206.infinityfree.com"; // CHANGE THIS (Copy from MySQL Databases page)
$user = "if0_40847310";             // CHANGE THIS (Your MySQL Username)
$pass = "Nihali123";      // CHANGE THIS (Your Account Password)
$db   = "if0_40847310_puredrop_db";    // CHANGE THIS (Your Full Database Name)

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>