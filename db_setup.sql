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