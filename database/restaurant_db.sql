-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 28, 2026 at 08:17 PM
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
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('New','Read','Replied') DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Ahmed Khan', 'ahmed@gmail.com', '03568912893', 'Feedback', 'I had a wonderful experience at Bite & Bliss. The food was delicious, the service was excellent, and the staff was very friendly. The atmosphere was also comfortable and welcoming. I would definitely recommend Bite & Bliss to anyone looking for great food and a pleasant dining experience.', 'New', '2026-09-21 18:32:14'),
(2, 'Muhammad Bilal', 'bilal@gmail.com', '03332467890', 'Reservation', 'Hello, I would like to make a reservation at Bite & Bliss. Please let me know about the available date and time slots, and whether my preferred reservation time is available. Thank you!', 'New', '2026-09-22 19:49:06'),
(3, 'Qamar Idrees', 'qamar@gmail.com', '03456789012', 'Feedback', 'I really enjoyed my experience at Bite & Bliss. The food was delicious, the staff was friendly, and the overall atmosphere was pleasant. The reservation process was also smooth and easy. Keep up the great work!', 'New', '2026-09-22 19:51:44'),
(4, 'Muhammad Ali', 'ali@gmail.com', '03457820367', 'Feedback', 'I had a wonderful experience at Bite & Bliss. The food was fresh, flavorful, and beautifully prepared. The service was friendly and the overall atmosphere was comfortable and welcoming. I really enjoyed my meal and would definitely recommend Bite & Bliss to others!', 'New', '2026-09-23 10:20:33');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` enum('starters','main','desserts','drinks') NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `category`, `description`, `price`, `image`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Crispy Chicken Nuggets', 'starters', 'Bite-sized pieces of tender chicken coated in a crispy golden crust', 450.00, 'uploads/menu/8aa8144f92d377d53e2aadd5.webp', 1, 0, '2026-09-21 10:59:20', '2026-09-21 10:59:20'),
(2, 'Crispy French Fries', 'starters', 'Golden and crispy seasoned fries served hot with a side of classic ketchup. A perfect snack or side dish.', 350.00, 'uploads/menu/0d481d0307524bf103496ea1.jpg', 1, 0, '2026-09-21 11:00:20', '2026-09-21 11:00:20'),
(3, 'BBQ Chicken Wings', 'starters', 'Juicy chicken wings coated in a smoky BBQ glaze, grilled to perfection for a deliciously bold and satisfying bite.', 650.00, 'uploads/menu/2a0546a87f15e5bbf33ade99.jpg', 1, 0, '2026-09-21 11:00:58', '2026-09-21 11:00:58'),
(4, 'Chicken Handi', 'main', 'Tender chicken cooked in a rich, creamy tomato-based gravy with aromatic spices and a delicious smoky flavor.', 850.00, 'uploads/menu/15711d0cc0e2cdb76a351ce3.jpg', 1, 0, '2026-09-21 11:03:02', '2026-09-21 11:03:35'),
(5, 'Chicken Biryani', 'main', 'Fragrant basmati rice layered with tender chicken, aromatic spices, and herbs for a flavorful and satisfying classic.', 550.00, 'uploads/menu/3051e5155f454e2b6d82470e.jpg', 1, 0, '2026-09-21 11:03:30', '2026-09-21 11:03:30'),
(6, 'Mutton Nihari', 'main', 'Slow-cooked tender meat simmered in a rich, spicy and aromatic gravy, creating a hearty traditional Pakistani favorite.', 750.00, 'uploads/menu/6b1e89d0e45ac211a5d6cb09.png', 1, 0, '2026-09-21 11:04:04', '2026-09-21 11:04:10'),
(7, 'Lamb Karahi', 'main', 'Succulent lamb cooked in a traditional karahi with tomatoes, green chilies, ginger, and bold aromatic spices.', 950.00, 'uploads/menu/ea4145a78fd9580b9916d209.jpg', 1, 0, '2026-09-21 11:04:40', '2026-09-21 11:04:40'),
(8, 'Chicken Karahi', 'main', 'Tender chicken cooked with fresh tomatoes, green chilies, ginger, and traditional spices for a rich and flavorful karahi experience.', 850.00, 'uploads/menu/7a36dc0f57bbd7fccdab6e3b.jpg', 1, 0, '2026-09-21 11:05:07', '2026-09-21 11:05:07'),
(9, 'Palak Paneer', 'main', 'Soft tofu simmered in a creamy spinach gravy with fragrant herbs and spices. A delicious vegetarian choice.', 650.00, 'uploads/menu/de8caffa585faeb44f020256.webp', 1, 0, '2026-09-21 11:06:07', '2026-09-21 11:06:22'),
(10, 'Gulab Jamun', 'desserts', 'Soft, golden milk dumplings soaked in sweet aromatic sugar syrup. A classic dessert to finish your meal.', 300.00, 'uploads/menu/14f25bb36a29978e1c804cb7.jpg', 1, 0, '2026-09-21 11:07:16', '2026-09-21 11:07:16'),
(11, 'Kheer', 'desserts', 'A creamy traditional rice pudding made with milk, rice, sugar, and aromatic flavors, topped with nuts for a delightful finish.', 250.00, 'uploads/menu/9156c8e1eed61ba040b781b4.jpg', 1, 0, '2026-09-21 11:08:47', '2026-09-21 11:08:47'),
(12, 'Carrot Halwa', 'desserts', 'Sweet and creamy carrot halwa made with perfection and garnished with crunchy nuts for a delicious traditional dessert.', 350.00, 'uploads/menu/d585848acfb1de35c7c5480d.jpg', 1, 0, '2026-09-21 11:10:48', '2026-09-21 11:10:48'),
(13, 'Chocolate Cake', 'desserts', 'Rich and moist chocolate cake layered with smooth chocolate frosting for a deliciously indulgent dessert.', 450.00, 'uploads/menu/31fbad9b184f0f9bb3e19cdf.jpg', 1, 0, '2026-09-21 11:12:44', '2026-09-21 11:12:44'),
(14, 'Fresh Lime Drink', 'drinks', 'A refreshing blend of fresh lime and chilled water with a balanced sweet and tangy flavor.', 200.00, 'uploads/menu/b1574c5d913435f41d429af4.jpg', 1, 0, '2026-09-21 11:13:08', '2026-09-21 11:13:08'),
(15, 'Mango Lassi', 'drinks', 'A creamy and refreshing yogurt-based drink blended with sweet ripe mangoes for a smooth tropical flavor.', 300.00, 'uploads/menu/6fd4cb784ff5695377d661c0.jpg', 1, 0, '2026-09-21 11:14:17', '2026-09-21 11:14:17'),
(16, 'Sweet Lassi', 'drinks', 'A cool and creamy traditional yogurt drink lightly sweetened and served chilled.', 250.00, 'uploads/menu/1f8d53107b1d343dbfebefc7.jpg', 1, 0, '2026-09-21 11:14:37', '2026-09-21 11:14:37');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `order_type` enum('Dine-in','Takeaway','Delivery') NOT NULL DEFAULT 'Takeaway',
  `table_number` varchar(20) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `special_request` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `full_name`, `email`, `phone`, `order_type`, `table_number`, `delivery_address`, `special_request`, `total_amount`, `status`, `created_at`) VALUES
(1, 6, 'Muhammad Bilal', 'bilal@gmail.com', '03332467890', 'Dine-in', '5', NULL, NULL, 1400.00, 'Pending', '2026-09-22 19:49:47'),
(2, 7, 'Qamar Idrees', 'qamar@gmail.com', '03456789012', 'Delivery', NULL, 'DHA Phase 2 Karachi', NULL, 1350.00, 'Completed', '2026-09-22 19:52:45'),
(3, 3, 'Muhammad Ali', 'ali@gmail.com', '03456789023', 'Takeaway', NULL, NULL, NULL, 2700.00, 'Pending', '2026-09-28 17:57:47');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `item_name`, `item_price`, `quantity`, `subtotal`) VALUES
(1, 1, 5, 'Chicken Biryani', 550.00, 2, 1100.00),
(2, 1, 10, 'Gulab Jamun', 300.00, 1, 300.00),
(3, 2, 6, 'Mutton Nihari', 750.00, 1, 750.00),
(4, 2, 12, 'Carrot Halwa', 350.00, 1, 350.00),
(5, 2, 16, 'Sweet Lassi', 250.00, 1, 250.00),
(6, 3, 1, 'Crispy Chicken Nuggets', 450.00, 1, 450.00),
(7, 3, 3, 'BBQ Chicken Wings', 650.00, 1, 650.00),
(8, 3, 5, 'Chicken Biryani', 550.00, 2, 1100.00),
(9, 3, 16, 'Sweet Lassi', 250.00, 2, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `guests` int(11) NOT NULL,
  `special_request` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `full_name`, `email`, `phone`, `reservation_date`, `reservation_time`, `guests`, `special_request`, `status`, `created_at`) VALUES
(1, 'Muhammad Ali', 'ali@gmail.com', '03457820367', '2026-09-29', '18:00:00', 5, '', 'Pending', '2026-09-21 10:40:10'),
(2, 'Muhammad Bilal', 'bilal@gmail.com', '03332467890', '2026-10-08', '18:00:00', 6, '', 'Pending', '2026-09-22 19:48:06'),
(3, 'Qamar Idrees', 'qamar@gmail.com', '03456789012', '2026-10-05', '20:00:00', 6, '', 'Pending', '2026-09-22 19:51:05');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `restaurant_name` varchar(100) NOT NULL DEFAULT 'Bite & Bliss',
  `address` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(30) NOT NULL DEFAULT '',
  `phone_secondary` varchar(30) DEFAULT NULL,
  `email` varchar(150) NOT NULL DEFAULT '',
  `email_secondary` varchar(150) DEFAULT NULL,
  `hours_weekday` varchar(100) NOT NULL DEFAULT '',
  `hours_weekend` varchar(100) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `restaurant_name`, `address`, `phone`, `phone_secondary`, `email`, `email_secondary`, `hours_weekday`, `hours_weekend`, `facebook_url`, `instagram_url`, `twitter_url`, `updated_at`) VALUES
(1, 'Bite & Bliss', '123 Clifton Road, Block 5, Karachi, Pakistan', '+92 300 1234567', '+92 21 3234 5678', 'info@biteandbliss.com', 'reservations@biteandbliss.com', 'Mon - Fri: 11:00 AM - 11:00 PM', 'Sat - Sun: 11:00 AM - 12:00 AM', 'https://facebook.com', 'https://instagram.com', 'https://twitter.com', '2026-09-22 19:09:28');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('Active','On Leave','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `position`, `phone`, `email`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bilal', 'Manager', '034687618689', 'qamaridrees@gmail.com', 'Dha Phase 3 karchi', 'On Leave', '2026-09-21 18:57:56', '2026-09-22 19:40:22'),
(2, 'Ali', 'Waiter', '03456782135', 'ali@gmail.com', NULL, 'Active', '2026-09-22 18:39:05', '2026-09-22 18:39:05'),
(3, 'Irfan', 'Cashier', '0342429901', 'abc@gmail.com', 'Gulshan, Karachi', 'Active', '2026-09-22 19:41:23', '2026-09-22 19:41:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'Restaurant Admin', 'admin', 'admin@gmail.com', '$2y$10$2n5My/x9ZWAR1cR8iyZXO.6A7d2V3OSUb3omFjN7y2z32pSAijW/a', 'admin', 1, '2026-09-21 10:41:23'),
(2, 'Ahmed Khan', 'ahmed', 'ahmed@gmail.com', '$2y$10$UFCDeuQ2YXvnD7n/LOaGguIzz1qr994GwLN5.odbxgeMtVVJQZjty', 'user', 1, '2026-09-21 18:30:54'),
(3, 'Muhammad Ali', 'ali', 'ali@gmail.com', '$2y$10$Mx/LrXiQjLliyMHdQjMVY.6d3nxdT0K95R.ZH2b0OCrV4THa/2x9.', 'user', 1, '2026-09-21 10:39:21'),
(6, 'Muhammad Bilal', 'bilal', 'bilal@gmail.com', '$2y$10$HbO.wLxhSO38zptXJKsjh.rqOb0IHJKIHTou2Qts9/cUUSwhOolvS', 'user', 1, '2026-09-22 19:47:23'),
(7, 'Qamar Idrees', 'qamar', 'qamar@gmail.com', '$2y$10$AiT1Wob2CCGdGatSXYT8jeQ9J59v7pJG90FvmNA8ZZUZIuVZIVIGG', 'user', 1, '2026-09-22 19:50:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_menu_category` (`category`),
  ADD KEY `idx_menu_status` (`status`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_item_fk` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
