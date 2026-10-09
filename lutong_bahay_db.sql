-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 09:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lutong_bahay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(3, 'Gulay at Kakanin'),
(2, 'Inihaw at Prito'),
(5, 'Pampalamig at Dessert'),
(4, 'Pang-Meryenda'),
(1, 'Sarsa at Sabaw');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_edited` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `recipe_id`, `user_id`, `content`, `is_edited`, `created_at`, `updated_at`) VALUES
(2, 3, 2, 'sarap mhmhmhm kjjkjk char', 1, '2026-10-09 07:03:04', '2026-10-09 07:05:41'),
(3, 4, 4, 'the perfect pinakupsan', 0, '2026-10-09 07:12:58', '2026-10-09 07:12:58'),
(4, 4, 3, 'wow sarap naman niyaan', 0, '2026-10-09 07:15:02', '2026-10-09 07:15:02');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`user_id`, `recipe_id`, `created_at`) VALUES
(2, 4, '2026-10-09 07:20:30'),
(4, 4, '2026-10-09 07:12:34');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `recipe_id`, `item_name`) VALUES
(6, 2, '1 kg Liempo'),
(7, 2, 'Uling'),
(8, 3, '1 pack knorr sinigang mix'),
(9, 3, '1 kg Pork'),
(10, 3, 'Kangkong'),
(11, 3, 'Sitaw'),
(12, 4, '1 kg baboy'),
(13, 4, 'cooking oil');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `instructions` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `prep_time_mins` int(11) DEFAULT 30,
  `servings` int(11) DEFAULT 4,
  `estimated_cost` decimal(10,2) DEFAULT 150.00,
  `difficulty` enum('Easy','Medium','Hard') DEFAULT 'Easy',
  `is_edited` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `category_id`, `title`, `description`, `instructions`, `image`, `prep_time_mins`, `servings`, `estimated_cost`, `difficulty`, `is_edited`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 'Roasted Liempo', 'Liempo masarap', 'isugba\r\nitimpla\r\nkaon', NULL, 30, 4, 150.00, 'Medium', 0, '2026-10-09 06:16:35', '2026-10-09 06:16:35'),
(3, 2, 1, 'Special Sinigang na Pork', 'Sinigang maasim parang manamis namis', 'Boil pork, add mix, add veggies. Sarap!', '1791528410_6ac88ddae186e.png', 45, 6, 300.00, 'Easy', 1, '2026-10-09 06:43:01', '2026-10-09 06:46:50'),
(4, 4, 2, 'PINAKUPSAN', 'lami isawsaw sa pinakurat', '1. Prep and Season: Dry 2 lbs pork belly cubes and toss with 2 tsp salt.\r\n2. Initial Sear: Place pork in a hot, dry pan over high heat for 7–10 minutes until it sweats out oil.\r\n3. Render Low & Slow: Cover, lower heat, and let pork simmer in its own fat for 40–50 minutes, stirring occasionally.\r\n4. Crisp: Once shrunk and golden, remove lid and fry on medium-high heat for 3–5 minutes until skin blisters.\r\n5. Drain & Serve: Remove from fat, drain on paper towels, and serve with spicy vinegar.', '1791529850_6ac8937a74a7e.jpg', 45, 5, 250.00, 'Easy', 1, '2026-10-09 07:10:50', '2026-10-09 07:18:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'cooked04', 'imcooked@gmail.com', '$2y$10$o5j7BszTvmWYF0RMXs0h8.s2JLBbQu2oc.8oVXrh2u1vCe23HrH8G', '2026-10-09 06:08:42'),
(2, 'juan23', 'juan@example.com', '$2y$10$/1osIg.b2kxCCqBvmdVAD.mxElBYwPDdNx9Gy60AAp/z7puDRXmPu', '2026-10-09 06:09:41'),
(3, 'maria321', 'maria@example.com', '$2y$10$ThkCKwL/6N78UNuSocQSROpzDoY/c5hHmNOY7CTEcbJQZ/5M2.8zW', '2026-10-09 06:19:41'),
(4, 'testuser', 'test@gmail.com', '$2y$10$kwFbGtyYZPj1wEQZ4eD6BemKB866gsNQYKwlpA1pBUcsl.YI2WIXK', '2026-10-09 07:08:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
