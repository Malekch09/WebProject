<?php
require_once 'config.php';

$admin_username = "admin";
$admin_email = "admin@cosmeticshop.com";
$admin_password = "Admin@123";


$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);


$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $admin_username, $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, TRUE)");
    $stmt->bind_param("sss", $admin_username, $admin_email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully!<br>";
        echo "Username: " . $admin_username . "<br>";
        echo "Password: " . $admin_password . "<br>";
        echo "Please change the password after first login.";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
} else {
    echo "Admin user already exists.";
}

$stmt->close();
?> 