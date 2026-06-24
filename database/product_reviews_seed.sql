-- Sample product review data for testing and display (10 products)
-- Requires: product_reviews table (run product_reviews.sql first)
-- Run: mysql -u root eatery < database/product_reviews_seed.sql

INSERT INTO `product_reviews` (`product_no`, `customer_id`, `rating`, `review_text`, `created_at`) VALUES
-- Product 1: Special Pizza
(1, 4, 5, 'The Special Pizza was fresh and perfectly baked. Great balance of cheese and toppings.', '2025-11-02 18:30:00'),
(1, 5, 4, 'Really tasty for the price. Would order again on my next visit.', '2025-11-10 13:15:00'),

-- Product 2: Italian Pizza
(2, 18, 5, 'Authentic Italian flavors. The crust was crisp and the sauce was excellent.', '2025-11-05 19:00:00'),
(2, 19, 4, 'Large portion and very filling. One of the better pizzas on the menu.', '2025-11-12 20:45:00'),

-- Product 3: Margherita Pizza
(3, 20, 5, 'Simple and delicious. Fresh basil and mozzarella made this a standout.', '2025-11-01 12:00:00'),
(3, 15, 4, 'Light and classic. Perfect for a quick lunch.', '2025-11-08 14:20:00'),

-- Product 4: Farmhouse Pizza
(4, 17, 5, 'Loaded with veggies and full of flavor. Highly recommend for vegetarians.', '2025-11-03 17:50:00'),
(4, 4, 4, 'Good amount of toppings and arrived hot. Solid farmhouse choice.', '2025-11-14 21:10:00'),

-- Product 5: Peppy Paneer Pizza
(5, 5, 5, 'Paneer was soft and well seasoned. Best paneer pizza I have had here.', '2025-11-06 19:30:00'),
(5, 18, 4, 'Spicy and cheesy in the right way. Great for paneer lovers.', '2025-11-11 18:00:00'),

-- Product 6: Red Chilli Pizza
(6, 19, 4, 'Nice kick of heat without overpowering the cheese. Enjoyed every slice.', '2025-11-04 20:00:00'),
(6, 20, 5, 'Perfect spice level. Will definitely order this again.', '2025-11-13 19:15:00'),

-- Product 7: Supreme Veggie Burger
(7, 15, 4, 'Fresh veggies and a soft bun. Tasty vegetarian burger option.', '2025-11-07 13:45:00'),
(7, 17, 5, 'Surprisingly filling and flavorful for a veggie burger.', '2025-11-09 12:30:00'),

-- Product 8: Stuffed Bean Burger
(8, 4, 4, 'Bean patty was well cooked and the stuffing added nice texture.', '2025-11-02 15:00:00'),
(8, 5, 3, 'Good taste overall. Could use a little more sauce, but still enjoyable.', '2025-11-15 16:40:00'),

-- Product 9: Butter Chicken Twin Burger
(9, 18, 5, 'Rich butter chicken flavor in burger form. Unique and delicious.', '2025-11-05 14:10:00'),
(9, 19, 4, 'Juicy and satisfying. Great fusion of Indian flavors with a burger.', '2025-11-10 17:25:00'),

-- Product 10: Classic Veg Cheeseburger
(10, 20, 4, 'Classic done right. Melted cheese and a good patty ratio.', '2025-11-06 11:50:00'),
(10, 15, 5, 'My go-to burger here. Consistent quality every time.', '2025-11-16 13:00:00');
