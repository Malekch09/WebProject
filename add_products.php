<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

  
    $target_dir = "uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $image_url = $target_file;

   
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
     
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $name, $description, $price, $stock, $category, $image_url);
        
        if ($stmt->execute()) {
            $message = "Product added successfully!";
        } else {
            $message = "Error adding product: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Error uploading image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Cosmetic Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>
        <div class="admin-nav">
            <a href="admin_dashboard.php">Back to Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" required>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" required>
            </div>

            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>

            <button type="submit" class="btn">Add Product</button>
        </form>
    </div>
</body>
</html> 