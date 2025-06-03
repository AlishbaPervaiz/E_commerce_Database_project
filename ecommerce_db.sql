-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 10:06 PM
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
-- Database: `ecommerce_db`
--
CREATE DATABASE IF NOT EXISTS `ecommerce_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecommerce_db`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `DeleteUserAccount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUserAccount` (IN `user_username` VARCHAR(255))   BEGIN
    -- Delete reviews
    DELETE FROM `review` WHERE username = user_username;

    -- Delete shipping records for user's orders
    DELETE s
    FROM `shipping` s
    JOIN `order` o ON s.order_id = o.order_id
    WHERE o.username = user_username;

    -- Delete order details for user's orders
    DELETE od
    FROM `orderdetails` od
    JOIN `order` o ON od.order_id = o.order_id
    WHERE o.username = user_username;

    -- Delete orders
    DELETE FROM `order` WHERE username = user_username;

    -- Finally, delete the user
    DELETE FROM `user` WHERE username = user_username;
END$$

DROP PROCEDURE IF EXISTS `EditUserInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `EditUserInfo` (IN `p_user_id` INT, IN `p_name` VARCHAR(255), IN `p_email` VARCHAR(255), IN `p_contact` VARCHAR(20), IN `p_address` TEXT)   BEGIN
    UPDATE User
    SET name = p_name,
        email = p_email,
        contact = p_contact,
        address = p_address
    WHERE user_id = p_user_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `coupon`
--

DROP TABLE IF EXISTS `coupon`;
CREATE TABLE IF NOT EXISTS `coupon` (
  `coupon_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_code` varchar(255) DEFAULT NULL,
  `discount` decimal(5,2) NOT NULL,
  `starting_date` date DEFAULT NULL,
  `expiry_date` date NOT NULL,
  `minimum_amount` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`coupon_id`),
  UNIQUE KEY `code` (`coupon_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon`
--

INSERT INTO `coupon` (`coupon_id`, `coupon_code`, `discount`, `starting_date`, `expiry_date`, `minimum_amount`) VALUES
(1, 'SAVE10', 10.00, '2025-05-01', '2025-12-31', 100.00),
(2, 'FREESHIP', 50.00, '2025-06-01', '2025-11-30', 200.00),
(3, 'NEWUSER20', 20.00, '2025-01-01', '2025-08-31', 0.00),
(4, 'MEGADEAL', 100.00, '2025-07-01', '2025-09-30', 500.00),
(5, 'SHOPZONE5', 5.00, '2025-05-15', '2026-01-15', 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(256) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Shipped','Delivered') DEFAULT 'Pending',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `order_date` datetime DEFAULT current_timestamp(),
  `coupon_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `username` (`username`),
  KEY `coupon_id` (`coupon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `username`, `full_name`, `email`, `contact`, `delivery_address`, `status`, `total_amount`, `discount`, `order_date`, `coupon_id`) VALUES
(1, 'alishbapervaiz34008@gmail.com', 'Alishba', 'alishbapervaiz34008@gmail.com', '03465177659', 'House no m1423 Street 66 Amar Pura Cha Sultan Rawalpindi', 'Delivered', 199.98, 10.00, '2025-06-03 16:59:09', 1),
(2, 'alishbapervaiz34008@gmail.com', 'Andleeb', 'andleeb34008@gmail.com', '03465177659', 'House no m1423 Street 66 Amar Pura Cha Sultan Rawalpindi', 'Delivered', 279.97, 10.00, '2025-06-03 20:38:55', 1),
(3, 'alishbapervaiz34008@gmail.com', 'Naseem', 'Naseem@gmail.com', '03465177659', 'House no m1423 Street 66 Amar Pura Cha Sultan Rawalpindi', 'Delivered', 109.99, 0.00, '2025-06-03 20:42:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

DROP TABLE IF EXISTS `orderdetails`;
CREATE TABLE IF NOT EXISTS `orderdetails` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`detail_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 2, 79.99),
(2, 2, 2, 3, 79.99),
(3, 3, 6, 1, 59.99);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `product_category` varchar(100) DEFAULT NULL,
  `product_image_url` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `price`, `product_category`, `product_image_url`, `description`, `is_available`) VALUES
(1, 'Wireless Earbuds', 49.99, 'Electronics', 'images/Wireless_Earbuds.jpg', 'High-quality wireless earbuds with noise cancellation.', 1),
(2, 'Bluetooth Speaker', 79.99, 'Electronics', 'images/Bluetooth_Speaker.jpg', 'Portable speaker with rich bass and 10-hour battery life.', 1),
(3, 'Smart Watch', 129.99, 'Electronics', 'images/Smart_Watch.jpg', 'Fitness-focused smart watch with heart rate monitor.', 1),
(4, 'Running Shoes', 89.99, 'Footwear', 'images/Running_Shoes.jpg', 'Comfortable running shoes for everyday exercise.', 1),
(5, 'Leather Wallet', 39.99, 'Accessories', 'images/Leather_Wallet.jpg', 'Genuine leather wallet with multiple compartments.', 1),
(6, 'Laptop Backpack', 59.99, 'Accessories', 'images/Laptop_Backpack.jpg', 'Waterproof laptop backpack with USB charging port.', 1),
(16, 'Toys', 15.03, 'Educational Toys', 'data:image/jpeg;base64,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', 'Help children to learn while playing.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`review_id`),
  KEY `order_id` (`order_id`),
  KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `username`, `order_id`, `rating`, `comment`) VALUES
(1, 'alishbapervaiz34008@gmail.com', 1, 5, ''),
(2, 'alishbapervaiz34008@gmail.com', 2, 4, 'Excellent'),
(3, 'alishbapervaiz34008@gmail.com', 3, 2, 'Good');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

DROP TABLE IF EXISTS `shipping`;
CREATE TABLE IF NOT EXISTS `shipping` (
  `shipping_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `courier_company` varchar(100) DEFAULT NULL,
  `shipping_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  PRIMARY KEY (`shipping_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `order_id`, `tracking_number`, `shipping_address`, `courier_company`, `shipping_date`, `delivery_date`) VALUES
(1, 1, 'PK032506001', 'House no m1423 Street 66 Amar Pura Cha Sultan Rawalpindi', 'TCS Express', '2025-06-03', '2025-06-03'),
(2, 2, 'PK032506002', 'House no m1423 Street 66 Amar Pura Cha Sultan Rawalpindi', 'TCS Express', '2025-06-03', '2025-06-03'),
(3, 3, 'PK032506003', 'House no m1423 Street 66 Amar Pura Cha Sultan Rawalpindi', 'TCS Express', '2025-06-03', '2025-06-03');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `name`, `email`, `contact`, `address`, `password`) VALUES
('alishbapervaiz34008@gmail.com', 'Alishba', 'alishbapervaiz34008@gmail.com', '03465177659', 'House no m1423 Street 66 Amar Pura Cha Sultan Rawalpindi', '$2y$10$glyjmlrAJNLPTmKWEUomEuaubgz9p8GlOfkwnG04vbhJnb9ENJXDC');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`),
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`coupon_id`);

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`username`) REFERENCES `user` (`username`);

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
