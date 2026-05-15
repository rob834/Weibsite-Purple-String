-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 05:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `purplestring_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `slug`, `created_at`) VALUES
(1, 'Crochet', 'crochet', '2026-04-27 10:43:04'),
(2, 'Prints', 'prints', '2026-05-02 10:58:32');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping` decimal(10,2) NOT NULL DEFAULT 50.00,
  `tax` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','delivering','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `notif_deleted` tinyint(1) DEFAULT 0,
  `mark_paid_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `subtotal`, `shipping`, `tax`, `total`, `status`, `created_at`, `is_read`, `notif_deleted`) VALUES
(7, 0, 450.00, 50.00, 36.00, 536.00, 'pending', '2026-05-12 08:57:33', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(18, 7, 9, 1, 450.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `sku` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock`, `sku`, `category_id`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Scarecrow', '4 inches', 150.00, 4, NULL, 1, 1, 2147483647, '2026-04-27 10:43:04', '2026-04-27 10:43:28'),
(2, 'Deadpool Baby Costume', 'Deadpool Baby Costume 1-3 years old', 450.00, 10, NULL, 1, 1, 2147483647, '2026-05-02 10:50:34', NULL),
(3, 'Calendar 2025', 'Calendar 2025', 175.00, 3, NULL, 2, 1, 2147483647, '2026-05-02 10:58:32', NULL),
(4, 'Anime Keychain Head', 'Keychain brochure stuff', 150.00, 20, NULL, 1, 1, 2147483647, '2026-05-02 11:00:31', NULL),
(5, 'Unicorn Girl Plushie', 'Unicorn Girl Plushie', 450.00, 1, NULL, 1, 1, 2147483647, '2026-05-02 11:02:44', NULL),
(6, 'Flower Girl Plushie', 'Flower Girl Plushie', 400.00, 1, NULL, 1, 1, 2147483647, '2026-05-02 11:04:05', NULL),
(7, 'Snow White Costume', 'for 1 - 3 years old', 450.00, 4, NULL, 1, 1, 2147483647, '2026-05-02 11:05:35', NULL),
(8, 'Santa Baby Costume', 'for 1-3 years old', 450.00, 67, NULL, 1, 1, 2147483647, '2026-05-02 11:06:44', NULL),
(9, 'Tinker Bell Baby Costume', 'for 1-3 years old', 450.00, 69, NULL, 1, 1, 2147483647, '2026-05-02 11:07:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `file_name`, `is_primary`, `created_at`) VALUES
(1, 1, '1777286584_9c426a0f6afc.jpg', 1, '2026-04-27 10:43:04'),
(2, 2, '1777719034_4e8d6b63204c.jpg', 1, '2026-05-02 10:50:34'),
(3, 3, '1777719512_6ce1fc063a35.jpg', 1, '2026-05-02 10:58:32'),
(4, 4, '1777719631_4b297902737c.jpg', 1, '2026-05-02 11:00:31'),
(5, 5, '1777719764_c27513e5ab66.jpg', 1, '2026-05-02 11:02:44'),
(6, 6, '1777719845_789fbbee1ff5.jpg', 1, '2026-05-02 11:04:05'),
(7, 7, '1777719935_8653aa6b41c6.jpg', 1, '2026-05-02 11:05:35'),
(8, 8, '1777720004_6395387e6c38.jpg', 1, '2026-05-02 11:06:44'),
(9, 9, '1777720059_5554d72343d5.jpg', 1, '2026-05-02 11:07:39');

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `rating_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ratings`
--

INSERT INTO `product_ratings` (`rating_id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 2147483647, 5, NULL, '2026-04-27 12:41:09', NULL),
(2, 9, NULL, 5, NULL, '2026-05-12 00:25:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` varchar(20) DEFAULT 'client',
  `display_name` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `user_name`, `password`, `date`, `role`, `display_name`, `bio`, `phone`, `address`, `avatar`, `email`, `email_verified`, `verification_token`) VALUES
(1, 26791416141148, 'Administrator', '67yourmama', '2026-05-11 23:00:06', 'admin', NULL, NULL, NULL, NULL, NULL, 'admin@purpledb', 1, NULL),
(2, 619223082120, 'winesap', '1234', '2026-04-27 13:21:27', 'client', 'winsesap', 'i hate codinh', '666', 'lalalalala', '1777296055_d75c1fa6a381.jpg', NULL, 0, NULL),
(4, 36226273801573942, 'lois', '1234', '2026-04-16 19:33:56', 'client', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(5, 7432672885077343, 'robbie', 'ella', '2026-04-17 10:03:23', 'client', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(15, 0, 'Louise Manio', 'louise1229', '2026-05-12 00:19:10', 'client', NULL, NULL, NULL, NULL, NULL, 'louisemanio048@gmail.com', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `date` (`date`),
  ADD KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD CONSTRAINT `product_ratings_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- Migration: add mark_paid_token column if upgrading an existing DB
-- Safe to run even if column already exists (uses IF NOT EXISTS via stored proc workaround)
-- Simply run the ALTER below once on your existing database:
--
-- ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `mark_paid_token` varchar(64) DEFAULT NULL;
--