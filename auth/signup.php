<?php
// Start session first, before any other logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../database/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = trim($_POST['phone_number']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($phone_number)) {
        $error = "All required fields must be filled";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (!preg_match('/^[0-9+\-\s()]{8,20}$/', $phone_number)) {
        $error = "Invalid phone number format";
    } else {
        // Check if username, email, or phone number already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR phone_number = ?");
        $stmt->execute([$username, $email, $phone_number]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Username, email, or phone number already exists";
        } else {
            // Hash password and create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, phone_number, first_name, last_name) VALUES (?, ?, ?, ?, ?, ?)");
            
            try {
                $stmt->execute([$username, $email, $hashed_password, $phone_number, $first_name, $last_name]);
                
                // Redirect to login page with success message
                header("Location: login.php?success=account_created");
                exit();
            } catch(PDOException $e) {
                $error = "Error creating account: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - E-Shop</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .auth-container {
            max-width: 500px;
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
        .btn-signup {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .btn-signup:hover {
            background: linear-gradient(135deg, #1e7e34, #155724);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40,167,69,0.3);
        }
        .btn-signup:active {
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
        .required:after {
            content: " *";
            color: #dc3545;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="auth-container">
        <div class="auth-header">
            <h2>Create Account</h2>
            <p>Join us and start shopping today</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <div class="form-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter first name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <div class="form-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter last name">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="username" class="required">Username</label>
                <div class="form-icon">
                    <i class="fas fa-at"></i>
                    <input type="text" id="username" name="username" required placeholder="Choose a username">
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="required">Email</label>
                <div class="form-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
            </div>

            <div class="form-group">
                <label for="phone_number" class="required">Phone Number</label>
                <div class="form-icon">
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="phone_number" name="phone_number" placeholder="e.g., +237 678 50 95 20" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="required">Password</label>
                <div class="form-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="Create a password">
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="required">Confirm Password</label>
                <div class="form-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                </div>
            </div>

            <button type="submit" class="btn-signup">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="auth-links">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html> 