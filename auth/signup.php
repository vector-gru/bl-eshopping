<?php
require_once '../database/db_connect.php';
session_start();

$error = '';
$success = '';

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
                $success = "Account created successfully! You can now login.";
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
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .success {
            color: #28a745;
            margin-bottom: 15px;
        }
        .auth-links {
            margin-top: 15px;
            text-align: center;
        }
        .required:after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="auth-container">
        <h2>Create an Account</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username" class="required">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email" class="required">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone_number" class="required">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" placeholder="e.g., +237 678 50 95 20" required>
            </div>

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name">
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name">
            </div>

            <div class="form-group">
                <label for="password" class="required">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="required">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Sign Up</button>
        </form>

        <div class="auth-links">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html> 