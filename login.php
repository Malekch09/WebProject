<?php
session_start();
require_once 'config.php';

$error = '';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
      
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin']; 
            
     
            error_log("User logged in: " . $username);
            error_log("Is admin: " . ($_SESSION['is_admin'] ? 'true' : 'false'));
            
            if ($_SESSION['is_admin']) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Invalid username";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cosmetic Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #fce4ec, #f8bbd0);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background-color: #fff;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #d81b60;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #d81b60;
            outline: none;
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.75rem;
            background-color: #d81b60;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #c2185b;
        }

        p {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        p a {
            color: #d81b60;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Login</h2>
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html> 