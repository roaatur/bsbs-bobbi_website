<?php
require 'include/db_connect.php';

// Start session
session_start();

// If already logged in, redirect to adminprod.php
if (isset($_SESSION['admin_name'])) {
    header("Location: adminprod.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Debug output
    error_log("Login attempt - Username: " . $username);

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Debug output
        error_log("User found: " . print_r($user, true));
        
        // Verify password
        if ($user['password'] === $password) {
            // Set session variables
            $_SESSION['admin_name'] = $user['username'];
            
            // Debug output
            error_log("Session set: " . print_r($_SESSION, true));
            
            // Redirect to admin dashboard
            header("Location: adminprod.php");
            exit();
        } else {
            $error = "Invalid password";
            error_log("Password mismatch");
        }
    } else {
        $error = "Invalid username";
        error_log("Username not found");
    }
}

// Destroy session and remove cookie when file is closed
function cleanupSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
        setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
    }
}

// Register shutdown function to clean up session
register_shutdown_function('cleanupSession');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-section {
            text-align: center;
        }
        .login-form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .submit-btn {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .error-message {
            color: #ff0000;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<?php require 'include/header.php'; ?>

    <main>
        <div class="login-container">
            <div class="login-section">
                <h1>Admin Login</h1>
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="login-form">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit" class="submit-btn">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html> 