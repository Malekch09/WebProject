<?php
session_start();
require_once 'config.php';

// Debug information
error_log("Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
error_log("Session is_admin: " . (isset($_SESSION['is_admin']) ? ($_SESSION['is_admin'] ? 'true' : 'false') : 'not set'));

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    error_log("Access denied - redirecting to login");
    header("Location: login.php");
    exit();
}

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all products
$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cosmetic Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fce4ec;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 2rem;
        }

        header {
            background-color: #d81b60;
            padding: 1rem 2rem;
            border-radius: 8px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        header nav a {
            color: white;
            margin-left: 1rem;
            text-decoration: none;
            font-weight: bold;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        h2 {
            margin-top: 2rem;
            color: #c2185b;
        }

        .admin-actions {
            margin: 1rem 0;
        }

        .btn {
            background-color: #ec407a;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s;
            display: inline-block;
        }

        .btn:hover {
            background-color: #d81b60;
        }

        .btn.delete {
            background-color: #e57373;
        }

        .btn.delete:hover {
            background-color: #d32f2f;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .product-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
        }

        .product-card h3 {
            margin: 0.5rem 0;
            color: #d81b60;
        }

        .product-card p {
            margin: 0.3rem 0;
        }

        .product-card .admin-actions {
            margin-top: 0.5rem;
        }

        form {
            display: inline;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            header nav {
                margin-top: 1rem;
            }
        }
    </style>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <a href="index.php">Back to Shop</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <div class="admin-actions">
            <a href="add_products.php" class="btn">Add New Product</a>
        </div>

        <h2>Manage Products</h2>
        <div class="products-grid">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                    <p>Stock: <?php echo $product['stock']; ?></p>
                    <div class="admin-actions">
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn">Edit</a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="delete_product" class="btn delete">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html> 