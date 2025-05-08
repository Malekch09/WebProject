<?php
session_start();
require_once 'config.php';

error_log("Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
error_log("Session is_admin: " . (isset($_SESSION['is_admin']) ? ($_SESSION['is_admin'] ? 'true' : 'false') : 'not set'));

$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosmetic Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
   body {
    background-color: #f9f9f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    margin: 0;
    padding: 0;
}

.container {
    width: 90%;
    margin: 0 auto;
    padding: 20px;
}


header {
    background-color: #f7d3e0;
    padding: 15px 0;
    text-align: center;
    border-radius: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

header h1 {
    margin: 0;
    color: #c94c7c;
}

nav a {
    color: #c94c7c;
    text-decoration: none;
    margin: 0 15px;
    font-weight: bold;
    transition: color 0.3s;
}

nav a:hover {
    color: #a63663;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.product-card {
    background-color: #fff;
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease;
    text-align: center;
}

.product-card:hover {
    transform: scale(1.05);
}

.product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
}

.product-card h3 {
    color: #c94c7c;
    margin: 15px 0 10px;
}

.product-card .description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.product-card .price {
    color: #333;
    font-weight: bold;
    font-size: 1.2rem;
    margin-bottom: 10px;
}

.product-card .stock {
    color: #999;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.form-inline {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: center;
}

.form-inline input[type="number"] {
    padding: 8px;
    border-radius: 10px;
    border: 1px solid #ccc;
    width: 60px;
    text-align: center;
}

.form-inline .btn {
    background-color: #c94c7c;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.form-inline .btn:hover {
    background-color: #a63663;
}
</style>

<body>
    <div class="container">
        <header>
            <h1>Welcome to Our Cosmetic Shop</h1>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="products.php">Products</a> <!-- Link for Products -->
                    <a href="orders.php">My Orders</a> <!-- Link for My Orders -->
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <a href="admin_dashboard.php" class="admin-link">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="cart.php">Cart</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </header>

        <div class="products-grid">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p class="stock">In Stock: <?php echo $product['stock']; ?></p>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="add_to_cart.php" class="form-inline">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button type="submit" class="btn">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>