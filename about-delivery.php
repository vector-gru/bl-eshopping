<?php
    // Check if session is already started before starting it - MUST be first
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us & Delivery Information - BLe-Smart  </title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" integrity="sha256-h20CPZ0QyXlBuAw7A+KluUYx/3pK+c7lYEpqLTlxjYQ=" crossorigin="anonymous" />

    <!-- Custom CSS file -->
    <link rel="stylesheet" href="style.css">

    <style>
        .section-padding {
            padding: 80px 0;
        }
        .section-title {
            color: #003859;
            font-weight: bold;
            margin-bottom: 30px;
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background: #00A5C4;
        }
        .about-content {
            line-height: 1.8;
            color: #555;
        }
        .delivery-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        .delivery-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .delivery-icon {
            font-size: 3rem;
            color: #00A5C4;
            margin-bottom: 20px;
        }
        .nav-pills .nav-link {
            color: #003859;
            border-radius: 25px;
            margin: 0 5px;
            padding: 10px 25px;
        }
        .nav-pills .nav-link.active {
            background-color: #00A5C4;
            color: white;
        }
        .nav-pills .nav-link:hover {
            background-color: #e3f2fd;
        }
        .theme-icon {
            color: #00A5C4;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Page Navigation -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">About Us & Delivery</li>
        </ol>
    </nav>
</div>

<!-- Navigation Pills -->
<div class="container mt-4">
    <ul class="nav nav-pills nav-pills justify-content-center" id="pageTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="about-tab" data-bs-toggle="pill" data-bs-target="#about" type="button" role="tab" aria-controls="about" aria-selected="true">
                <i class="fas fa-info-circle me-2 theme-icon"></i>About Us
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="delivery-tab" data-bs-toggle="pill" data-bs-target="#delivery" type="button" role="tab" aria-controls="delivery" aria-selected="false">
                <i class="fas fa-truck me-2 theme-icon"></i>Delivery Information
            </button>
        </li>
    </ul>
</div>

<!-- Content Sections -->
<div class="tab-content" id="pageTabsContent">
    <!-- About Us Section -->
    <div class="tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="about-tab">
        <section class="section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <h1 class="section-title">About BLe-Smart  </h1>
                        <div class="about-content">
                            <p class="lead mb-4">
                                Welcome to BLe-Smart  , your premier online shopping destination in Bafoussam, Cameroon. 
                                We are passionate about bringing quality products and exceptional service to our community.
                            </p>
                            
                            <div class="row mt-5">
                                <div class="col-md-6 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-heart delivery-icon"></i>
                                        <h4>Our Mission</h4>
                                        <p>To provide our customers with a seamless online shopping experience, offering quality products at competitive prices while supporting local businesses and communities.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-eye delivery-icon"></i>
                                        <h4>Our Vision</h4>
                                        <p>To become the leading e-commerce platform in the West Region of Cameroon, known for reliability, customer satisfaction, and community development.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-shield-alt delivery-icon"></i>
                                        <h5>Quality Assurance</h5>
                                        <p>We carefully select all our products to ensure they meet the highest quality standards.</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-headset delivery-icon"></i>
                                        <h5>Customer Support</h5>
                                        <p>Our dedicated team is always ready to assist you with any questions or concerns.</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-handshake delivery-icon"></i>
                                        <h5>Local Partnership</h5>
                                        <p>We work closely with local suppliers and businesses to support our community.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <h3>Why Choose BLe-Smart  ?</h3>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check theme-icon me-2"></i>Free delivery in Bafoussam</li>
                                            <li class="mb-2"><i class="fas fa-check theme-icon me-2"></i>Secure payment options</li>
                                            <li class="mb-2"><i class="fas fa-check theme-icon me-2"></i>Quality product guarantee</li>
                                            <li class="mb-2"><i class="fas fa-check theme-icon me-2"></i>Fast and reliable service</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check theme-icon me-2"></i>24/7 customer support</li>
                                            <li class="mb-2"><i class="fas fa-check theme-icon me-2"></i>Easy returns and exchanges</li>
                                            <li class="mb-2"><i class="fas fa-check theme-icon me-2"></i>Competitive pricing</li>
                                            <li class="mb-2"><i class="fas fa-check theme-icon me-2"></i>Local business support</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <h3>Contact Information</h3>
                                <p class="mb-2"><i class="fas fa-map-marker-alt theme-icon me-2"></i><strong>Address:</strong> Bafoussam, West Region, Cameroon</p>
                                <p class="mb-2"><i class="fas fa-phone theme-icon me-2"></i><strong>Phone:</strong> (+237) 678 50 95 20 / 650 15 41 83 / 683 70 41 82</p>
                                <p class="mb-2"><i class="fas fa-envelope theme-icon me-2"></i><strong>Email:</strong> info@blesmart.com</p>
                                <p class="mb-2"><i class="fas fa-clock theme-icon me-2"></i><strong>Business Hours:</strong> Monday - Saturday: 8:00 AM - 8:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Delivery Information Section -->
    <div class="tab-pane fade" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
        <section class="section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <h1 class="section-title text-center">Delivery Information</h1>
                        
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="delivery-card text-center">
                                    <i class="fas fa-home delivery-icon"></i>
                                    <h4>Local Delivery (Bafoussam)</h4>
                                    <p class="mb-3">Your order is eligible for <strong class="text-success">FREE Delivery</strong> in Bafoussam and surrounding areas.</p>
                                    <ul class="list-unstyled text-start">
                                        <li><i class="fas fa-check theme-icon me-2"></i>Free delivery for all orders</li>
                                        <li><i class="fas fa-check theme-icon me-2"></i>Same-day delivery (orders placed before 2 PM)</li>
                                        <li><i class="fas fa-check theme-icon me-2"></i>Next-day delivery for orders placed after 2 PM</li>
                                        <li><i class="fas fa-check theme-icon me-2"></i>Delivery time: 9:00 AM - 7:00 PM</li>
                                        <li><i class="fas fa-check theme-icon me-2"></i>Real-time delivery tracking</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 mb-4">
                                <div class="delivery-card text-center">
                                    <i class="fas fa-plane delivery-icon"></i>
                                    <h4>Out-of-Town Delivery</h4>
                                    <p class="mb-3">For deliveries outside Bafoussam, we partner with reliable third-party courier services.</p>
                                    <ul class="list-unstyled text-start">
                                        <li><i class="fas fa-info-circle theme-icon me-2"></i>Customer pays shipping fees</li>
                                        <li><i class="fas fa-info-circle theme-icon me-2"></i>Fees determined by courier service</li>
                                        <li><i class="fas fa-info-circle theme-icon me-2"></i>Delivery time: 2-5 business days</li>
                                        <li><i class="fas fa-info-circle theme-icon me-2"></i>Tracking number provided</li>
                                        <li><i class="fas fa-info-circle theme-icon me-2"></i>Insurance coverage included</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="delivery-card">
                                    <h4 class="text-center mb-4"><i class="fas fa-info-circle theme-icon me-2"></i>Important Delivery Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Before Delivery</h5>
                                            <ul>
                                                <li>Ensure someone is available to receive the package</li>
                                                <li>Provide accurate delivery address and contact information</li>
                                                <li>Have payment ready if cash on delivery</li>
                                                <li>Check product availability before ordering</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>During Delivery</h5>
                                            <ul>
                                                <li>Inspect package for damage before signing</li>
                                                <li>Verify order contents match your purchase</li>
                                                <li>Keep delivery receipt for warranty purposes</li>
                                                <li>Contact us immediately if there are issues</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="delivery-card">
                                    <h4 class="text-center mb-4"><i class="fas fa-shipping-fast theme-icon me-2"></i>Delivery Partners</h4>
                                    <p class="text-center mb-4">We work with trusted courier services to ensure your packages are delivered safely and on time:</p>
                                    <div class="row text-center">
                                        <div class="col-md-4 mb-3">
                                            <i class="fas fa-truck fa-2x theme-icon mb-2"></i>
                                            <h6>Local Couriers</h6>
                                            <p class="small">Reliable local delivery partners</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <i class="fas fa-shipping-fast fa-2x theme-icon mb-2"></i>
                                            <h6>Express Services</h6>
                                            <p class="small">Fast delivery for urgent orders</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <i class="fas fa-shield-alt fa-2x theme-icon mb-2"></i>
                                            <h6>Secure Handling</h6>
                                            <p class="small">Insurance and secure packaging</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="delivery-card bg-light">
                                    <h4 class="text-center mb-4"><i class="fas fa-question-circle theme-icon me-2"></i>Frequently Asked Questions</h4>
                                    <div class="accordion" id="deliveryFAQ">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faq1">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                                    How long does delivery take in Bafoussam?
                                                </button>
                                            </h2>
                                            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#deliveryFAQ">
                                                <div class="accordion-body">
                                                    Orders placed before 2 PM are delivered the same day. Orders placed after 2 PM are delivered the next business day.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faq2">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                                    What are the delivery fees for out-of-town orders?
                                                </button>
                                            </h2>
                                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#deliveryFAQ">
                                                <div class="accordion-body">
                                                    Delivery fees vary based on distance and courier service rates. You will be informed of the exact cost before confirming your order.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="faq3">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                                    Can I track my delivery?
                                                </button>
                                            </h2>
                                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#deliveryFAQ">
                                                <div class="accordion-body">
                                                    Yes! For local deliveries, you'll receive real-time updates. For out-of-town deliveries, you'll get a tracking number from the courier service.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
// Handle URL hash navigation
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash === '#delivery') {
        const deliveryTab = document.getElementById('delivery-tab');
        const deliveryContent = document.getElementById('delivery');
        
        // Remove active class from about tab
        document.getElementById('about-tab').classList.remove('active');
        document.getElementById('about').classList.remove('show', 'active');
        
        // Add active class to delivery tab
        deliveryTab.classList.add('active');
        deliveryContent.classList.add('show', 'active');
    }
});

// Update URL when tabs are clicked
document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
    tab.addEventListener('click', function(e) {
        const target = this.getAttribute('data-bs-target');
        if (target === '#delivery') {
            window.location.hash = 'delivery';
        } else {
            window.location.hash = '';
        }
    });
});
</script>

</body>
</html> 