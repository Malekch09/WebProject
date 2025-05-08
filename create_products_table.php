<?php
require_once 'config.php';

// Create products table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    stock INT NOT NULL DEFAULT 0,
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_table)) {
    echo "Products table created successfully!";
} else {
    echo "Error creating products table: " . $conn->error;
}
?> 