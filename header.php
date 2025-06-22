<?php
    // Check if session is already started before starting it - MUST be first
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Function to get the correct base path
    function getBasePath() {
        $current_dir = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($current_dir, '/auth') !== false) {
            return '../';
        }
        return './';
    }
    
    $base_path = getBasePath();

    // Debug information
    if (isset($_SESSION['user_id'])) {
        error_log("User ID: " . $_SESSION['user_id']);
        try {
            require_once __DIR__ . '/database/db_connect.php';
            $conn = getDBConnection();
            $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Admin status: " . ($result ? json_encode($result) : 'no result'));
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
        }
    } else {
        error_log("No user session");
    }
?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B&L e-Shopping</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Owl-carousel CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha256-UhQQ4fxEeABh4JrcmAJ1+16id/1dnlOEVCFOxDef9Lw=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" integrity="sha256-kksNxjDRxd/5+jGurZUJd1sdR2v+ClrCl3svESBaJqw=" crossorigin="anonymous" />

    <!-- font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" integrity="sha256-h20CPZ0QyXlBuAw7A+KluUYx/3pK+c7lYEpqLTlxjYQ=" crossorigin="anonymous" />

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <!-- Custom CSS file -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>style.css">

    <!-- Custom JS file -->
    <script src="<?php echo $base_path; ?>assets/js/cart.js"></script>
    <script src="<?php echo $base_path; ?>assets/js/banner.js"></script>

    <?php
        // require functions.php file
        require ($base_path . 'functions.php');
    ?>

</head>
<body>

<!-- start #header -->
<header id="header">
    <div class="strip d-flex justify-content-between px-4 py-1 bg-light">
        <p class="font-rale font-size-12 text-black-50 m-0">Bafoussam TPO, Foumbot Road (+237) 678 50 95 20 / 650 15 41 83 / 683 70 41 82</p>
        <div class="font-rale font-size-14">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="px-3 border-right text-dark">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <?php if (isAdmin()): ?>
                    <a href="<?php echo $base_path; ?>admin/" class="px-3 border-right text-dark"><i class="fas fa-cog"></i> Admin Panel</a>
                <?php endif; ?>
                <a href="<?php echo $base_path; ?>auth/logout.php" class="px-3 border-right text-dark">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>auth/login.php" class="px-3 border-right text-dark">Login</a>
                <a href="<?php echo $base_path; ?>auth/signup.php" class="px-3 border-right text-dark">Sign Up</a>
            <?php endif; ?>
            <a href="<?php echo $base_path; ?>cart.php" class="px-3 border-right text-dark">Wishlist (<span class="wishlist-count"><?php echo count($product->getData('wishlist'))?></span>)</a>
        </div>
    </div>

    <!-- Primary Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark color-second-bg">
        <a href="<?php echo $base_path; ?>index.php">
            <img src="<?php echo $base_path; ?>assets/blLogo3.png" style="width: 50px;" alt="logo">
        </a>
        <a class="navbar-brand" href="<?php echo $base_path; ?>index.php">B&L e-Shopping</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav m-auto font-rubik">
                <li class="nav-item active">
                    <a class="nav-link" href="#">On Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Products <i class="fas fa-chevron-down"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Category <i class="fas fa-chevron-down"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Coming Soon</a>
                </li>
            </ul>
            <form action="#" class="font-size-14 font-rale">
                <a href="<?php echo $base_path; ?>cart.php" class="py-2 rounded-pill color-primary-bg">
                    <span class="font-size-16 px-2 text-white"><i class="fas fa-shopping-cart"></i></span>
                    <span class="px-3 py-2 rounded-pill text-dark bg-light cart-count"><?php echo count($product->getData('cart'))?></span>
                </a>
            </form>
        </div>
    </nav>
    <!-- !Primary Navigation -->

</header>
<!-- !start #header -->

<!-- start #main-site -->
<main id="main-site">