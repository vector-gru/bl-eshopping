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
    <title>Privacy Policy & Terms - BLe-Smart  </title>

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
        .policy-content {
            line-height: 1.8;
            color: #555;
        }
        .policy-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        .policy-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .policy-icon {
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
        .last-updated {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #00A5C4;
            margin-bottom: 30px;
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
            <li class="breadcrumb-item active" aria-current="page">Privacy Policy & Terms</li>
        </ol>
    </nav>
</div>

<!-- Navigation Pills -->
<div class="container mt-4">
    <ul class="nav nav-pills nav-pills justify-content-center" id="pageTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="privacy-tab" data-bs-toggle="pill" data-bs-target="#privacy" type="button" role="tab" aria-controls="privacy" aria-selected="true">
                <i class="fas fa-shield-alt me-2 theme-icon"></i>Privacy Policy
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="terms-tab" data-bs-toggle="pill" data-bs-target="#terms" type="button" role="tab" aria-controls="terms" aria-selected="false">
                <i class="fas fa-file-contract me-2 theme-icon"></i>Terms & Conditions
            </button>
        </li>
    </ul>
</div>

<!-- Content Sections -->
<div class="tab-content" id="pageTabsContent">
    <!-- Privacy Policy Section -->
    <div class="tab-pane fade show active" id="privacy" role="tabpanel" aria-labelledby="privacy-tab">
        <section class="section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <h1 class="section-title text-center">Privacy Policy</h1>
                        
                        <div class="last-updated">
                            <i class="fas fa-calendar-alt theme-icon me-2"></i>
                            <strong>Last Updated:</strong> January 2025
                        </div>

                        <div class="policy-content">
                            <div class="policy-card">
                                <h4><i class="fas fa-info-circle theme-icon me-2"></i>1. Information We Collect</h4>
                                <p>At BLe-Smart  , we collect information you provide directly to us, such as when you create an account, place an order, or contact us for support. This may include:</p>
                                <ul>
                                    <li><strong>Personal Information:</strong> Name, email address, phone number, and shipping address</li>
                                    <li><strong>Account Information:</strong> Username, password, and account preferences</li>
                                    <li><strong>Order Information:</strong> Products purchased, payment details, and delivery information</li>
                                    <li><strong>Communication:</strong> Messages, feedback, and support requests</li>
                                </ul>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-cog theme-icon me-2"></i>2. How We Use Your Information</h4>
                                <p>We use the information we collect to:</p>
                                <ul>
                                    <li>Process and fulfill your orders</li>
                                    <li>Provide customer support and respond to inquiries</li>
                                    <li>Send order confirmations and delivery updates</li>
                                    <li>Improve our services and user experience</li>
                                    <li>Send promotional offers (with your consent)</li>
                                    <li>Comply with legal obligations</li>
                                </ul>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-share-alt theme-icon me-2"></i>3. Information Sharing</h4>
                                <p>We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:</p>
                                <ul>
                                    <li><strong>Service Providers:</strong> With trusted third-party services that help us operate our business (payment processors, delivery partners)</li>
                                    <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and safety</li>
                                    <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
                                </ul>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-lock theme-icon me-2"></i>4. Data Security</h4>
                                <p>We implement appropriate security measures to protect your personal information:</p>
                                <ul>
                                    <li>Encryption of sensitive data during transmission</li>
                                    <li>Secure storage of personal information</li>
                                    <li>Regular security assessments and updates</li>
                                    <li>Limited access to personal information on a need-to-know basis</li>
                                </ul>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-cookie-bite theme-icon me-2"></i>5. Cookies and Tracking</h4>
                                <p>We use cookies and similar technologies to:</p>
                                <ul>
                                    <li>Remember your preferences and settings</li>
                                    <li>Analyze website traffic and usage patterns</li>
                                    <li>Provide personalized content and recommendations</li>
                                    <li>Improve website functionality and performance</li>
                                </ul>
                                <p>You can control cookie settings through your browser preferences.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-user-edit theme-icon me-2"></i>6. Your Rights</h4>
                                <p>You have the right to:</p>
                                <ul>
                                    <li>Access and review your personal information</li>
                                    <li>Update or correct inaccurate information</li>
                                    <li>Request deletion of your personal information</li>
                                    <li>Opt-out of marketing communications</li>
                                    <li>Withdraw consent for data processing</li>
                                </ul>
                                <p>To exercise these rights, please contact us using the information provided below.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-globe theme-icon me-2"></i>7. International Data Transfers</h4>
                                <p>Your information may be transferred to and processed in countries other than your own. We ensure that such transfers comply with applicable data protection laws and implement appropriate safeguards.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-child theme-icon me-2"></i>8. Children's Privacy</h4>
                                <p>Our services are not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If you believe we have collected such information, please contact us immediately.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-bell theme-icon me-2"></i>9. Changes to This Policy</h4>
                                <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on our website and updating the "Last Updated" date. Your continued use of our services after such changes constitutes acceptance of the updated policy.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-envelope theme-icon me-2"></i>10. Contact Us</h4>
                                <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
                                <ul>
                                    <li><strong>Email:</strong> privacy@bleshopping.com</li>
                                    <li><strong>Phone:</strong> (+237) 678 50 95 20</li>
                                    <li><strong>Address:</strong> Bafoussam, West Region, Cameroon</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Terms & Conditions Section -->
    <div class="tab-pane fade" id="terms" role="tabpanel" aria-labelledby="terms-tab">
        <section class="section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <h1 class="section-title text-center">Terms & Conditions</h1>
                        
                        <div class="last-updated">
                            <i class="fas fa-calendar-alt theme-icon me-2"></i>
                            <strong>Last Updated:</strong> January 2025
                        </div>

                        <div class="policy-content">
                            <div class="policy-card">
                                <h4><i class="fas fa-handshake theme-icon me-2"></i>1. Acceptance of Terms</h4>
                                <p>By accessing and using BLe-Smart  's website and services, you accept and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our services.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-user-plus theme-icon me-2"></i>2. Account Registration</h4>
                                <p>To use certain features of our services, you must create an account. You agree to:</p>
                                <ul>
                                    <li>Provide accurate, current, and complete information</li>
                                    <li>Maintain and update your account information</li>
                                    <li>Keep your password secure and confidential</li>
                                    <li>Accept responsibility for all activities under your account</li>
                                    <li>Notify us immediately of any unauthorized use</li>
                                </ul>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-shopping-cart theme-icon me-2"></i>3. Product Information and Orders</h4>
                                <p>We strive to provide accurate product information, but we do not guarantee that all information is complete, accurate, or current. Product availability and prices are subject to change without notice.</p>
                                <p><strong>Order Acceptance:</strong> All orders are subject to acceptance and availability. We reserve the right to refuse or cancel any order for any reason.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-credit-card theme-icon me-2"></i>4. Payment Terms</h4>
                                <p>Payment is due at the time of order placement. We accept various payment methods as indicated on our website. You agree to provide valid payment information and authorize us to charge the specified amount.</p>
                                <p><strong>Pricing:</strong> All prices are in Central African CFA francs (XAF) unless otherwise stated. Prices are subject to change without notice.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-truck theme-icon me-2"></i>5. Delivery and Shipping</h4>
                                <p><strong>Local Delivery:</strong> Free delivery is available in Bafoussam and surrounding areas. Delivery times are estimates and may vary.</p>
                                <p><strong>Out-of-Town Delivery:</strong> Shipping fees apply and are determined by third-party courier services. Customers are responsible for all shipping costs.</p>
                                <p><strong>Risk of Loss:</strong> Risk of loss and title for items purchased pass to you upon delivery to the carrier.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-undo theme-icon me-2"></i>6. Returns and Refunds</h4>
                                <p><strong>Return Policy:</strong> You may return items within 14 days of delivery for a full refund, subject to the following conditions:</p>
                                <ul>
                                    <li>Items must be unused and in original packaging</li>
                                    <li>Items must not be damaged or altered</li>
                                    <li>Certain items may not be eligible for return</li>
                                    <li>Return shipping costs are the customer's responsibility</li>
                                </ul>
                                <p><strong>Refunds:</strong> Refunds will be processed within 5-7 business days after we receive the returned item.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-exclamation-triangle theme-icon me-2"></i>7. Prohibited Uses</h4>
                                <p>You agree not to use our services to:</p>
                                <ul>
                                    <li>Violate any applicable laws or regulations</li>
                                    <li>Infringe on intellectual property rights</li>
                                    <li>Transmit harmful, offensive, or inappropriate content</li>
                                    <li>Attempt to gain unauthorized access to our systems</li>
                                    <li>Interfere with the proper functioning of our services</li>
                                </ul>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-gavel theme-icon me-2"></i>8. Intellectual Property</h4>
                                <p>All content on our website, including text, graphics, logos, and software, is the property of BLe-Smart   or its licensors and is protected by copyright and other intellectual property laws.</p>
                                <p>You may not reproduce, distribute, or create derivative works without our express written consent.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-shield-alt theme-icon me-2"></i>9. Disclaimers and Limitations</h4>
                                <p><strong>Service Availability:</strong> We strive to maintain service availability but do not guarantee uninterrupted access to our website.</p>
                                <p><strong>Product Warranties:</strong> Products are sold "as is" without warranties, except as required by law or provided by manufacturers.</p>
                                <p><strong>Limitation of Liability:</strong> Our liability is limited to the amount paid for the specific product or service giving rise to the claim.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-balance-scale theme-icon me-2"></i>10. Governing Law</h4>
                                <p>These Terms and Conditions are governed by the laws of Cameroon. Any disputes shall be resolved in the courts of Bafoussam, West Region, Cameroon.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-edit theme-icon me-2"></i>11. Changes to Terms</h4>
                                <p>We reserve the right to modify these Terms and Conditions at any time. Changes will be effective immediately upon posting on our website. Your continued use of our services constitutes acceptance of the modified terms.</p>
                            </div>

                            <div class="policy-card">
                                <h4><i class="fas fa-envelope theme-icon me-2"></i>12. Contact Information</h4>
                                <p>For questions about these Terms and Conditions, please contact us:</p>
                                <ul>
                                    <li><strong>Email:</strong> legal@bleshopping.com</li>
                                    <li><strong>Phone:</strong> (+237) 678 50 95 20</li>
                                    <li><strong>Address:</strong> Bafoussam, West Region, Cameroon</li>
                                </ul>
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
    if (hash === '#terms') {
        const termsTab = document.getElementById('terms-tab');
        const termsContent = document.getElementById('terms');
        
        // Remove active class from privacy tab
        document.getElementById('privacy-tab').classList.remove('active');
        document.getElementById('privacy').classList.remove('show', 'active');
        
        // Add active class to terms tab
        termsTab.classList.add('active');
        termsContent.classList.add('show', 'active');
    }
});

// Update URL when tabs are clicked
document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
    tab.addEventListener('click', function(e) {
        const target = this.getAttribute('data-bs-target');
        if (target === '#terms') {
            window.location.hash = 'terms';
        } else {
            window.location.hash = '';
        }
    });
});
</script>

</body>
</html> 