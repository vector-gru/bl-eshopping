<?php
// Start session first, before any other logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../database/db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = '';

// Check for success message from signup
if (isset($_GET['success']) && $_GET['success'] === 'account_created') {
    $success = "Account created successfully! Please login with your credentials.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Check user credentials
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Debug: Log the session data
                error_log("Login successful for user: " . $user['username'] . " (ID: " . $user['id'] . ")");
                error_log("Session data: " . json_encode($_SESSION));
                
                // Redirect to home page
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Shop</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border: 1px solid #e0e0e0;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-header h2 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .auth-header p {
            color: #666;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        .error {
            color: #dc3545;
            margin-bottom: 20px;
            padding: 12px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            font-size: 14px;
        }
        .success {
            color: #155724;
            margin-bottom: 20px;
            padding: 12px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            font-size: 14px;
        }
        .auth-links {
            margin-top: 25px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        .auth-links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .form-icon {
            position: relative;
        }
        .form-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        .form-icon input {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="auth-container">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p>Sign in to your account to continue</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="form-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" required placeholder="Enter your username">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="form-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="auth-links">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>

    <?php include '../footer.php'; ?> 
</body>
</html> 