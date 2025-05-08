<?php
// DB CONFIG
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cosmetic.order');

// Error 
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// CONNECTION
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("A system error occurred. Please try again later.");
}

// CreatION DATABASE
$sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`";
if (!$conn->query($sql)) {
    error_log("Error creating database: " . $conn->error);
    die("A system error occurred. Please try again later.");
}

// SelECTION DATABASE
if (!$conn->select_db(DB_NAME)) {
    error_log("Error selecting database: " . $conn->error);
    die("A system error occurred. Please try again later.");
}

$conn->set_charset("utf8mb4");
?> 