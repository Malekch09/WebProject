<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    if ($product_id && $quantity > 0) {

        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product && $product['stock'] >= $quantity) {
            $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $cart_item = $result->fetch_assoc();

            if ($cart_item) {
        
                $new_quantity = $cart_item['quantity'] + $quantity;
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
            } else {
        
                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            }

            if ($stmt->execute()) {
                header("Location: cart.php");
                exit();
            }
        }
    }
}

header("Location: products.php");
exit();
?> 