# PHP_store

### Project Images
## User Side
1. ![Capture d'écran 2025-04-18 223213](https://github.com/user-attachments/assets/f38c684d-2a34-4744-ab98-200b552afa20)
2. ![Capture d'écran 2025-04-18 223230](https://github.com/user-attachments/assets/e3338729-304c-416c-af46-da484f03c793)
3. ![Capture d'écran 2025-04-18 223241](https://github.com/user-attachments/assets/beff0d2c-701b-4fc2-9aac-a02bb626ecd4)
4. ![Capture d'écran 2025-04-18 223254](https://github.com/user-attachments/assets/8b26ab2e-ccc4-4ac9-a5c6-d7e9c1f15f44)
5. ![Capture d'écran 2025-04-18 223306](https://github.com/user-attachments/assets/7c605d98-1feb-4df7-8040-93ed031e4401)
## Admin Side
7. ![Capture d'écran 2025-04-18 223319](https://github.com/user-attachments/assets/11bfc613-4790-48be-a297-e196cdc89e1e) 
8. ![Capture d'écran 2025-04-18 223332](https://github.com/user-attachments/assets/e75a9e96-7ae1-40e1-a145-c1cc36486c1b)
9. ![Capture d'écran 2025-04-18 223336](https://github.com/user-attachments/assets/d22c2780-08c6-4de2-9653-dd72e8b887f2)

I fixed the third URL by correcting the protocol from `https://` to `https://`. All links should now work properly when viewed in reverse order.

Would you like me to provide any additional formatting or organization for these reversed image references?

=======

# 🛒 PHP E-Commerce Admin Dashboard

This project is a simple and functional **e-commerce admin panel** built with PHP and MySQL. It allows administrators to manage products and view customer orders, while also providing a public-facing store page for visitors.

The system includes:
- 🔐 A user login/signup system with password hashing and session handling
- 🛍️ A public store page where products are displayed
- 🛠️ A secured admin dashboard for managing products and orders

---

## 📁 Project Structure

| Folder/File        | Purpose |
|--------------------|---------|
| **admin/**         | Admin dashboard for managing products and orders. |
| **cart.php / cart.js** | Shopping cart logic and front-end interactions. |
| **checkout.php / prepare_checkout.php** | Order finalization & processing. |
| **commands.php / config.php** | Backend logic, DB setup, and connection. |
| **db_setup.sql**   | SQL script to set up the database (great touch!). |
| **images/**        | Sample product images. |
| **uploads/**       | Uploaded product images (likely from admin panel). |
| **register.php / login.php / logout.php** | User auth system. |
| **style.css / admin.css / stylerepack.css / file.css** | Stylesheets for different parts of the site. |
| **store.php / store.js** | Storefront logic (listing products etc.). |

---

## 🚀 Features

- Admin login/logout with session management
- User registration with password hashing
- Product management (add, edit, delete, view)
- Order management (basic structure in place)
- Simple and clean UI using plain HTML/CSS
- Role-based access: only admins can access the `/admin` section

---

## 🧰 Technologies Used

- **PHP** (vanilla PHP with PDO for database interaction)
- **MySQL** (for database management)
- **HTML/CSS** (for frontend)
- **phpMyAdmin** (to manage database)
- **XAMPP** (to run PHP and MySQL locally)

---

## 🖥️ How to Run the Project

### ✅ Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) installed
- Basic knowledge of how to use **phpMyAdmin**

### 📦 Steps

1. **Clone or Download the Repository**

   Download the ZIP file or use Git:

   ```
   git clone https://github.com/yourusername/php-ecommerce-admin.git
   ```

2. **Move the Project to the XAMPP Directory**

   Copy the folder to:
   ```
   C:\xampp\htdocs\
   ```
   For example:
   ```
   C:\xampp\htdocs\php-ecommerce-admin
   ```

3. **Start XAMPP**

   - Open the XAMPP Control Panel
   - Start **Apache** and **MySQL**

4. **Create the Database**

   - Go to `http://localhost/phpmyadmin`
   - Click **"New"** and create a database (e.g., `ecommerce`)
   - Import the provided SQL file (if available), or manually create tables as needed

5. **Configure the Database Connection**

   Open `config.php` and edit your database credentials:

   ```php
   <?php
   $dsn = 'mysql:host=localhost;dbname=ecommerce';
   $username = 'root';
   $password = '';
   $options = [];
   try {
       $pdo = new PDO($dsn, $username, $password, $options);
   } catch (PDOException $e) {
       echo 'Connection failed: ' . $e->getMessage();
   }
   ?>
   ```

6. **Access the Project in Your Browser**

   - Visit: `http://localhost/php-ecommerce-admin/store.php` → Public store page  
   - Visit: `http://localhost/php-ecommerce-admin/auth/signup.php` → Create a new user  
   - Visit: `http://localhost/php-ecommerce-admin/auth/login.php` → Log in  
   - After logging in, if the user is an admin (`is_admin = 1` in the database), access the admin dashboard at:  
     `http://localhost/php-ecommerce-admin/admin/`

---

## 🔧 Admin Access

To make a user an admin:
1. Go to `phpMyAdmin` > your database > `users` table
2. Find your user and set the `is_admin` field to `1`

---

---
## Database Build

```sql
-- Database creation
CREATE DATABASE IF NOT EXISTS `user_store_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `user_store_db`;

-- Set SQL mode and time zone
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create products table
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create orders table
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `shipping_address` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create order_items table
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample users data
INSERT INTO `users` (`id`, `username`, `email`, `password`, `address`, `created_at`, `is_admin`) VALUES
(1, 'hazzinesaid', 'hazzinesaid@gmail.com', '$2y$10$oTj8CpHgXyJu/Y.IuZHZ9.S4D.lGUWUOgCX4xHqHfpuxlQOQGE/AS', NULL, '2025-03-16 14:05:28', 0),
(2, 'admin', 'admin@example.com', '$2y$10$T0L4fzsvUPBi41zgwKGE1./GEV6eGfnzbyh4IgsfjqJW7TbjisF8m', NULL, '2025-03-16 15:58:24', 1),
(3, 'bonjour', 'bonjour.toujour@gmail.com', '$2y$10$uNkyaFGFdTeZV/Y9K3XbFuj.1yDlVmJpRaq7DwbA1NG43xqj9Q33C', NULL, '2025-03-16 17:40:48', 0),
(4, 'ras7anout', 'ras7anout@gmail.com', '$2y$10$FhEtGkSxWKjn/2k6zzGQX.uRaY663fqwWGwN48xvN8vIl8t8nGfUW', '410 W. North Dr.\r\nNorth Canton, OH 44720', '2025-04-14 00:58:10', 0);

-- Insert sample products data
INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `created_at`) VALUES
(1, 'Product 1', 'This is the first product description', 2000.00, 'uploads/67d8e76f26009.jpeg', '2025-03-16 14:04:31'),
(2, 'Product 2', 'This is the second product description', 39.97, 'images/product2.jpg', '2025-03-16 14:04:31'),
(5, 'Large - Prunus triloba', 'Double Flowering Cherry-Almond TREE - circa 170cm', 14.99, 'uploads/67fc573149128.png', '2025-04-14 00:30:41'),
(6, 'Paeonia Coral Sunset', 'Peony - Pack of THREE', 9.99, 'uploads/67fc57735b031.webp', '2025-04-14 00:31:47'),
(7, 'Dicentra spectabilis or Lamprocapnos', 'Bleeding Heart plant', 5.99, 'uploads/67fc57a1afd4e.webp', '2025-04-14 00:32:33'),
(8, 'Rainbow Azalea japonica Collection - Evergreen Japanese Azaleas', 'Pack of Three Plants', 14.97, 'uploads/67fc57c8146e1.webp', '2025-04-14 00:33:12'),
(9, 'Cotinus Dusky Maiden', 'Smoke bush', 19.99, 'uploads/67fc5821f3754.webp', '2025-04-14 00:34:41'),
(10, 'Eryngium Lapis Blue', 'Eryingium - Blue Sea Holly', 12.99, 'uploads/67fc5855bae3f.webp', '2025-04-14 00:35:33'),
(11, 'Eryngium Magical Blue Lagoon - Eryingium', 'Blue Sea Holly', 12.99, 'uploads/67fc58727f057.webp', '2025-04-14 00:36:02'),
(12, 'Product 1', 'This is the first product description', 19.99, 'images/product1.jpg', '2025-04-14 00:50:31'),
(13, 'Product 2', 'This is the second product description', 29.99, 'images/product2.jpg', '2025-04-14 00:50:31'),
(14, 'Product 3', 'This is the third product description', 39.99, 'images/product3.jpg', '2025-04-14 00:50:31'),
(15, 'Product 4', 'This is the fourth product description', 49.99, 'images/product4.jpg', '2025-04-14 00:50:31');

-- Insert sample orders data
INSERT INTO `orders` (`id`, `user_id`, `order_date`, `total_amount`, `status`, `shipping_address`) VALUES
(6, 4, '2025-04-14 02:08:48', 20.96, 'pending', '410 W. North Dr.\r\nNorth Canton, OH 44720'),
(7, 2, '2025-04-18 00:31:41', 2000.00, 'pending', 'Hhgyghuhhhhg'),
(8, 2, '2025-04-18 00:32:08', 2000.00, 'pending', 'Csfscscsc');

-- Insert sample order_items data
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`) VALUES
(18, 6, 7, 1, 5.99),
(19, 6, 8, 1, 14.97),
(20, 7, 1, 1, 2000.00),
(21, 8, 1, 1, 2000.00);
```


## 📌 Notes

- This is a simple project for learning purposes. For production, you should:
  - Sanitize inputs and escape outputs
  - Add CSRF protection
  - Implement password reset functionality
  - Use a framework (like Laravel) for better structure and security

---

## 📬 Contact

For any issues or suggestions, feel free to open an issue or contact me.


> Happy coding! 😊
```

