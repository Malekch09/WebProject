<?php
require_once 'config.php';


$check_table = $conn->query("SHOW TABLES LIKE 'users'");
if ($check_table->num_rows == 0) {
    die("Users table does not exist. Please run the database setup first.");
}


$check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
if ($check_column->num_rows == 0) {
    $alter_table = "ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT FALSE";
    if ($conn->query($alter_table)) {
        echo "Added is_admin column to users table successfully.<br>";
    } else {
        die("Error adding is_admin column: " . $conn->error);
    }
}


$admin_username = "admin";
$admin_email = "admin@cosmeticshop.com";
$admin_password = "Admin@123"; 

$check_admin = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check_admin->bind_param("ss", $admin_username, $admin_email);
$check_admin->execute();
$result = $check_admin->get_result();

if ($result->num_rows == 0) {
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
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
    $stmt->close();
} else {

    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $update_admin = $conn->prepare("UPDATE users SET password = ?, is_admin = TRUE WHERE username = ?");
    $update_admin->bind_param("ss", $hashed_password, $admin_username);
    
    if ($update_admin->execute()) {
        echo "Admin user updated successfully!<br>";
        echo "Username: " . $admin_username . "<br>";
        echo "Password: " . $admin_password . "<br>";
    } else {
        echo "Error updating admin user: " . $update_admin->error;
    }
    $update_admin->close();
}

$check_admin->close();
?> 