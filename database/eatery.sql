-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2023 at 02:53 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eatery`
--

-- --------------------------------------------------------

--
-- Table structure for table `book_table`
--

CREATE TABLE `book_table` (
  `table_no` int(3) NOT NULL,
  `table_size` int(2) NOT NULL,
  `table_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_table`
--

INSERT INTO `book_table` (`table_no`, `table_size`, `table_status`) VALUES
(2, 3, 'non'),
(3, 2, 'non'),
(4, 4, 'non'),
(5, 2, 'non');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_id` int(3) NOT NULL,
  `cust_username` varchar(8) NOT NULL,
  `cust_name` varchar(20) DEFAULT NULL,
  `cust_mobile` bigint(10) DEFAULT NULL,
  `cust_email` varchar(50) NOT NULL,
  `cust_password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_id`, `cust_username`, `cust_name`, `cust_mobile`, `cust_email`, `cust_password`) VALUES
(4, 'tejas10', 'tejas savaliya', 2147483647, 'tejas10@gmail.com', 'tejas10'),
(5, 'rumit10', 'rumit savaliya', 1234561234, 'rumit10@gmail.com', '123456'),
(15, 'deep1234', 'deep', 9512545142, 'deepsavaliya265@gmail.com', '123456'),
(17, 'deep123', '1223', 2315324321, 'deep@123gmail.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(4) NOT NULL,
  `customer_id` int(4) NOT NULL,
  `order_desc` varchar(100) NOT NULL,
  `table_no` int(3) NOT NULL,
  `oreder_time` time(6) NOT NULL DEFAULT current_timestamp(),
  `status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(4) NOT NULL,
  `product_no` int(3) NOT NULL,
  `product_name` varchar(20) NOT NULL,
  `product_price` int(5) NOT NULL,
  `product_type` varchar(15) NOT NULL,
  `product_qty` int(5) NOT NULL,
  `product_desc` varchar(100) NOT NULL,
  `product_img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_no`, `product_name`, `product_price`, `product_type`, `product_qty`, `product_desc`, `product_img`) VALUES
(1, 1, 'Special Pizza', 299, 'Pizza', 100, 'Medium size 6 slice 2 serve', 'IMG-640cf49eacf3d4.67407097.jpg'),
(2, 2, 'Italian Pizza', 499, 'Pizza', 100, 'Large size 8 slice 3 serve', 'IMG-640cf529dbdf75.46674764.jpg'),
(3, 3, 'Margherita Pizza', 199, 'Pizza', 100, 'Small size 6 slice 2 serve', 'IMG-640cf56f407301.75286433.jpg'),
(4, 4, 'Farmhouse Pizza', 299, 'Pizza', 100, 'Medium size 6 slice 2 serve', 'IMG-640cf5c7d7f532.79852739.jpg'),
(5, 5, 'Peppy Paneer Pizza', 349, 'Pizza', 1000, 'Medium size 5 slice 2 serve', 'IMG-640cf63213ae56.28592059.jpg'),
(6, 6, 'Red Chilli Pizza', 459, 'Pizza', 1000, 'Large size 8 slice 2 serve', 'IMG-640cf736191191.51052387.jpeg'),
(7, 7, 'Supreme Veggie Burge', 79, 'Burger', 200, 'Medium Veggie Burger serve 1', 'IMG-640cfc405a0e97.70406266.jpg'),
(8, 8, 'Stuffed Bean Burger', 75, 'Burger', 200, 'Medium Stuffed Burger serve 1', 'IMG-640cfc9921c222.17060468.jpg'),
(9, 9, 'Butter Chicken Twin ', 59, 'Burger', 200, 'Medium Chicken Burger serve 1', 'IMG-640cfd0687b984.71269756.jpg'),
(10, 10, 'Classic Veg Cheesebu', 61, 'Burger', 200, 'Jumbo Classic Veg Burger serve 1', 'IMG-640cfd7cc97b97.13045102.jpg'),
(11, 11, 'Zinger burger', 69, 'Burger', 200, 'Jumbo Zinger Burger serve 1', 'IMG-640cfe15e73b64.61491332.jpg'),
(12, 12, 'Black burger', 99, 'Burger', 200, 'Jumbo Black Burger serve 1', 'IMG-640cfe5cbec783.05698734.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book_table`
--
ALTER TABLE `book_table`
  ADD PRIMARY KEY (`table_no`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`),
  ADD UNIQUE KEY `cust_mobile` (`cust_mobile`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_no` (`product_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
