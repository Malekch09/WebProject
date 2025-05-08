<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

//jointure//
$query = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image_url, p.stock 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Cosmetic Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
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


header {
    background-color: var(--pink);
    color: var(--white);
    padding: 1rem;
    text-align: center;
}

header h1 {
    font-size: 2rem;
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

/* Container */
.container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 1rem;
}

/* Cart Items */
.cart-items {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.cart-item {
    display: flex;
    background-color: var(--white);
    border: 1px solid var(--medium-grey);
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    padding: 1rem;
    align-items: center;
    gap: 1.5rem;
}

.cart-item img {
    width: 120px;
    height: 120px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid var(--light-grey);
}

.item-details {
    flex: 1;
}

.item-details h3 {
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.price, .subtotal {
    color: var(--dark-pink);
    font-weight: bold;
    margin-top: 0.5rem;
}

/* Forms */
form {
    margin-top: 0.5rem;
}

.quantity-form input[type="number"] {
    width: 60px;
    padding: 5px;
    margin-right: 8px;
    border-radius: 6px;
    border: 1px solid var(--medium-grey);
}

.quantity-form button,
.remove-form button,
.cart-summary button {
    background-color: var(--pink);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s ease;
}

.quantity-form button:hover,
.remove-form button:hover,
.cart-summary button:hover {
    background-color: var(--dark-pink);
}

.cart-summary {
    text-align: right;
    margin-top: 2rem;
    background-color: var(--light-grey);
    padding: 1rem;
    border-radius: 10px;
}

.cart-summary h3 {
    color: var(--text-dark);
    margin-bottom: 1rem;
}

footer {
    background-color: var(--light-grey);
    color: var(--text-dark);
    text-align: center;
    padding: 1rem;
    margin-top: 3rem;
    font-size: 0.9rem;
}
</style>
<body>
    <header>
        <h1>Cosmetic Shop</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">My Orders</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h2>Shopping Cart</h2>
        <?php if ($result->num_rows > 0): ?>
            <div class="cart-items">
                <?php while ($item = $result->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                            <form action="update_cart.php" method="POST" class="quantity-form">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="<?php echo $item['stock']; ?>">
                                <button type="submit">Update</button>
                            </form>
                            <form action="remove_from_cart.php" method="POST" class="remove-form">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit">Remove</button>
                            </form>
                            <p class="subtotal">Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="cart-summary">
                <h3>Total: $<?php echo number_format($total, 2); ?></h3>
                <form action="checkout.php" method="POST">
                    <button type="submit">Proceed to Checkout</button>
                </form>
            </div>
        <?php else: ?>
            <p>Your cart is empty. <a href="products.php">Continue shopping</a></p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Cosmetic Shop. All rights reserved.</p>
    </footer>
</body>
</html> 