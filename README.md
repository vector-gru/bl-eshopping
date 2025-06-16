# BL E-Shopping

A modern e-commerce web application built with PHP, MySQL, and JavaScript. This project provides a complete shopping experience with features including user authentication, product browsing, shopping cart, wishlist, and order management.

## Features

- User Authentication (Register/Login)
- Product Catalog
- Shopping Cart Management
- Wishlist Functionality
- Order Processing
- Responsive Design
- User Profile Management

## Prerequisites

Before you begin, ensure you have the following installed:
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP (recommended for easy setup)

## Installation Steps

1. **Clone the Repository**
   ```bash
   git clone git@github.com:vector-gru/bl-eshopping.git
   cd bl-eshopping
   ```

2. **Database Setup**
   - Start your MySQL server (if using XAMPP, start Apache and MySQL from XAMPP Control Panel)
   - Navigate to `http://localhost/phpmyadmin`
   - Create a new database named `eshop` (or use the provided init_db.php script)
   - The database will be automatically initialized with the required tables when you run the application

3. **Configuration**
   - The project uses default database credentials:
     - Host: localhost
     - Username: root
     - Password: (empty)
     - Database: eshop
   - If you need to modify these settings, update them in:
     - `database/db_connect.php`
     - `database/init_db.php`

4. **Project Setup**
   - Place the project in your web server's document root:
     - For XAMPP: `/Applications/XAMPP/xamppfiles/htdocs/projects/bl-eshopping`
     - For other servers: configure according to your setup
   - Ensure the web server has read/write permissions for the project directory

5. **Initialize Database**
   - Visit `http://localhost/projects/bl-eshopping/database/init_db.php` in your browser
   - This will create all necessary database tables
   - You should see success messages for each table creation

6. **Access the Application**
   - Open your web browser and navigate to:
     `http://localhost/projects/bl-eshopping`
   - The application should now be running

## Project Structure

```
bl-eshopping/
├── assets/              # Static assets (CSS, JS, images)
├── auth/               # Authentication related files
├── database/           # Database connection and models
│   ├── DBController.php
│   ├── init_db.php     # Database initialization
│   ├── Cart.php
│   ├── Product.php
│   └── Wishlist.php
├── Template/           # Template files and AJAX handlers
├── index.php          # Main entry point
├── header.php         # Common header
├── footer.php         # Common footer
└── functions.php      # Common functions
```

## Development

- The project uses a modular structure with separate files for different functionalities
- Database operations are handled through PDO for secure database interactions
- AJAX is used for dynamic cart and wishlist operations
- Session-based authentication is implemented for user management

## Troubleshooting

If you encounter any issues:

1. **Database Connection Issues**
   - Verify MySQL is running
   - Check database credentials in `database/db_connect.php`
   - Ensure the `eshop` database exists

2. **Permission Issues**
   - Ensure proper file permissions (typically 755 for directories, 644 for files)
   - Check web server user has access to the project directory

3. **Session Issues**
   - Verify PHP session is working
   - Check session storage directory permissions

## Security Notes

- The project uses PDO with prepared statements for database operations
- Passwords are hashed before storage
- Input validation is implemented for user data
- Session management includes security measures

## Contributing

Feel free to submit issues and enhancement requests!

## License

This project is licensed under the MIT License - see the LICENSE file for details. 