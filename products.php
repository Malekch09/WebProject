<?php
session_start();
require_once 'config.php';

$query = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Cosmetic Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        :root {
            --pink: #f8a5c2;
            --dark-pink: #e06c9f;
            --light-grey: #f5f5f5;
            --medium-grey: #ccc;
            --white: #fff;
            --text-dark: #333;
        }

        /* Header */
        header {
            background-color: var(--pink);
            color: var(--white);
            padding: 1rem;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        nav a {
            margin: 0 10px;
            color: var(--white);
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Main container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        /* Product card */
        .product-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .product-card h3 {
            color: var(--text-dark);
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }

        .product-card .price {
            color: var(--dark-pink);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .product-card .description {
            color: var(--medium-grey);
            margin-bottom: 1rem;
        }

        .product-card .stock {
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .product-card form {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .product-card input[type="number"] {
            width: 60px;
            padding: 5px;
            border-radius: 6px;
            border: 1px solid var(--medium-grey);
        }

        .product-card button {
            background-color: var(--pink);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .product-card button:hover {
            background-color: var(--dark-pink);
        }

        .product-card p a {
            color: var(--dark-pink);
            text-decoration: none;
        }

        .product-card p a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            background-color: var(--light-grey);
            color: var(--text-dark);
            text-align: center;
            padding: 1rem;
            margin-top: 3rem;
            font-size: 0.9rem;
        }
    </style>
    <header>
        <h1>Cosmetic Shop</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php">Cart</a>
                <a href="orders.php">My Orders</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
        <div class="products-grid">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="stock">In Stock: <?php echo $product['stock']; ?></p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button type="submit">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <p><a href="login.php">Login to purchase</a></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Cosmetic Shop. All rights reserved.</p>
    </footer>
</body>
</html> 