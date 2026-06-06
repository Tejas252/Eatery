-- Product reviews table for Eatery
-- Run in phpMyAdmin or: mysql -u root eatery < database/product_reviews.sql

CREATE TABLE IF NOT EXISTS `product_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_no` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review_text` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `uniq_customer_product` (`product_no`, `customer_id`),
  KEY `idx_product_no` (`product_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
