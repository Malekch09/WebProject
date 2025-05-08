<?php
session_start();
require_once 'config.php';


$error = "";
$success = "";

function createTables($conn) {
    $tables = [
        "users" => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            address TEXT,
            phone VARCHAR(20),
            is_admin BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "products" => "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            image_url VARCHAR(255),
            stock INT NOT NULL DEFAULT 0,
            category VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "orders" => "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            shipping_address TEXT,
            payment_method VARCHAR(50),
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )",
        
        "order_items" => "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        )",
        
        "cart" => "CREATE TABLE IF NOT EXISTS cart (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        )"
    ];

    foreach ($tables as $table => $sql) {
        if (!$conn->query($sql)) {
            error_log("Error creating table $table: " . $conn->error);
            return false;
        }
    }
    return true;
}


function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $username = validateInput($_POST['username']);
    $email = filter_var(validateInput($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = validateInput($_POST['password']);
    $confirm_password = validateInput($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
     
        if (!createTables($conn)) {
            $error = "A system error occurred. Please try again later.";
        } else {
         
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Username or email already exists.";
            } else {
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

              
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $hashed_password);

                if ($stmt->execute()) {
                    $success = "Registration successful! You can now login.";
                } else {
                    error_log("Error registering user: " . $stmt->error);
                    $error = "A system error occurred. Please try again later.";
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cosmetic Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #fff0f6;
            color: #333;
        }
        .container {
            max-width: 500px;
            margin: 60px auto;
            padding: 40px;
            background: #fff;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 16px;
        }
        h2 {
            text-align: center;
            color: #d63384;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: 500;
            display: block;
            margin-bottom: 5px;
            color: #6c757d;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            background: #f8f9fa;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 14px;
            border: none;
            background: #d63384;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #c2185b;
        }
        .error, .success {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        p {
            text-align: center;
            margin-top: 20px;
        }
        a {
            color: #d63384;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Register</h2>
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html> 