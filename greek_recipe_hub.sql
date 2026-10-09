-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 09:22 AM
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
-- Database: `greek_recipe_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Meze (Appetizers)'),
(2, 'Salads'),
(3, 'Seafood'),
(4, 'Sweets');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `recipe_id`, `user_id`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Made this for the barangay fiesta and it disappeared in minutes!', '2026-10-08 19:14:18', NULL),
(2, 1, 4, 'Added a little nutmeg to the filling. Highly recommend.', '2026-10-08 20:14:18', '2026-10-09 01:14:18'),
(3, 2, 2, 'Simple and fresh. Use the ripest tomatoes you can find.', '2026-10-08 21:14:18', NULL),
(4, 3, 5, 'The lemon at the end makes it. Great recipe.', '2026-10-08 22:14:18', NULL),
(5, 4, 4, 'Too sweet for me at first, but better the next day.', '2026-10-08 23:14:18', '2026-10-09 01:14:18'),
(6, 5, 5, 'Perfect with warm pita bread.', '2026-10-09 00:14:18', NULL),
(7, 7, 1, '1 pray SIKEE', '2026-10-09 07:15:58', '2026-10-09 07:16:11');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`user_id`, `recipe_id`) VALUES
(1, 7),
(1, 8),
(2, 3),
(3, 4),
(4, 1),
(5, 1),
(5, 2),
(5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `recipe_id`, `name`, `amount`, `unit`) VALUES
(1, 1, 'Spinach', 500.00, 'g'),
(2, 1, 'Feta cheese', 250.00, 'g'),
(3, 1, 'Phyllo sheets', 12.00, 'pcs'),
(4, 1, 'Dill', 2.00, 'tbsp'),
(5, 1, 'Olive oil', 3.00, 'tbsp'),
(6, 2, 'Tomatoes', 4.00, 'pcs'),
(7, 2, 'Cucumber', 1.00, 'pcs'),
(8, 2, 'Kalamata olives', 100.00, 'g'),
(9, 2, 'Feta block', 200.00, 'g'),
(10, 2, 'Dried oregano', 1.00, 'tsp'),
(11, 3, 'Octopus', 1.50, 'kg'),
(12, 3, 'Lemon', 2.00, 'pcs'),
(13, 3, 'Olive oil', 4.00, 'tbsp'),
(14, 3, 'Oregano', 1.00, 'tsp'),
(15, 4, 'Phyllo sheets', 20.00, 'pcs'),
(16, 4, 'Walnuts', 400.00, 'g'),
(17, 4, 'Butter', 250.00, 'g'),
(18, 4, 'Honey', 300.00, 'g'),
(19, 4, 'Cinnamon', 1.50, 'tsp'),
(20, 5, 'Greek yogurt', 400.00, 'g'),
(21, 5, 'Cucumber', 1.00, 'pcs'),
(22, 5, 'Garlic cloves', 3.00, 'pcs'),
(23, 5, 'Olive oil', 2.00, 'tbsp'),
(24, 6, 'Shrimp', 500.00, 'g'),
(25, 6, 'Crushed tomatoes', 400.00, 'g'),
(26, 6, 'Feta cheese', 150.00, 'g'),
(27, 6, 'Ouzo', 3.00, 'tbsp'),
(30, 7, 'Milk', 1.00, 'L'),
(31, 8, 'Greens', 2.00, 'pieces'),
(32, 8, 'Cheese', 5.00, 'grams'),
(33, 8, 'Tomatoes', 3.00, 'cups');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `steps` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `category_id`, `title`, `description`, `steps`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Spanakopita Triangles', 'Crispy phyllo triangles filled with spinach, feta and fresh dill, perfect for gatherings.', 'Saute onion and spinach until wilted.\nMix with feta, egg and dill.\nFill buttered phyllo strips and fold into triangles.\nBake at 180C for 25 minutes until golden.', '2026-10-03 01:14:18', NULL),
(2, 3, 2, 'Classic Horiatiki Salad', 'The village salad: ripe tomatoes, cucumber, olives and a thick slab of feta with oregano.', 'Chop tomatoes, cucumber and onion into chunks.\nAdd olives and green pepper.\nTop with a slab of feta and dust with oregano.\nDrizzle generously with olive oil.', '2026-10-04 01:14:18', '2026-10-09 01:14:18'),
(3, 4, 3, 'Grilled Octopus with Lemon', 'Tender octopus simmered then charred on the grill, finished with lemon and olive oil.', 'Simmer octopus gently for 45 minutes until tender.\nCool and cut into pieces.\nGrill over high heat until charred.\nDress with lemon juice, oil and oregano.', '2026-10-05 01:14:18', NULL),
(4, 5, 4, 'Honey Walnut Baklava', 'Layers of buttery phyllo with spiced walnuts, soaked in fragrant honey syrup.', 'Layer buttered phyllo in a pan.\nSpread chopped walnuts and cinnamon between layers.\nCut into diamonds and bake at 170C for 50 minutes.\nPour cooled honey syrup over the hot baklava.', '2026-10-06 01:14:18', NULL),
(5, 2, 1, 'Tzatziki Dip', 'Cool, garlicky yogurt and cucumber dip that goes with almost everything on the table.', 'Grate cucumber and squeeze out the water.\nMix with strained yogurt, garlic and dill.\nSeason with salt, oil and a splash of vinegar.\nChill for an hour before serving.', '2026-10-07 01:14:18', NULL),
(6, 3, 3, 'Shrimp Saganaki', 'Plump shrimp baked in a tomato and feta sauce with a hint of ouzo.', 'Cook onion and garlic in olive oil.\nAdd tomatoes and simmer into a sauce.\nAdd shrimp and ouzo, cook 4 minutes.\nCrumble feta on top and bake until bubbling.', '2026-10-08 01:14:18', NULL),
(7, 1, 1, 'Feta Cheese', 'This is a cheese. Lactose intolerant people are not able to eat this', 'Step 1. Add 1 Milk to the milk', '2026-10-09 07:15:29', '2026-10-09 07:16:04'),
(8, 1, 2, 'Greek Salad', 'This is a salad. You need be healthy.', '1. Slide the tomatoes', '2026-10-09 07:18:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'gpancog', 'aingreenx@gmail.com', '$2y$10$VBxZTHILx6UCYgRLAvz1/eXHEJPo5Zp/e7bFrsxmE8iyTwIGB8XNy', '2026-10-09 07:13:31'),
(2, 'maria_k', 'maria@example.com', '$2y$10$jubx8Z2dBlIIFwN7DCoLJeoldXj/q4BnlZSkUDvc3Pp.VFaiGmdEK', '2026-10-09 07:14:18'),
(3, 'nikos_g', 'nikos@example.com', '$2y$10$jubx8Z2dBlIIFwN7DCoLJeoldXj/q4BnlZSkUDvc3Pp.VFaiGmdEK', '2026-10-09 07:14:18'),
(4, 'eleni_p', 'eleni@example.com', '$2y$10$jubx8Z2dBlIIFwN7DCoLJeoldXj/q4BnlZSkUDvc3Pp.VFaiGmdEK', '2026-10-09 07:14:18'),
(5, 'demo_user', 'demo@example.com', '$2y$10$jubx8Z2dBlIIFwN7DCoLJeoldXj/q4BnlZSkUDvc3Pp.VFaiGmdEK', '2026-10-09 07:14:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_name` (`name`);

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
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
