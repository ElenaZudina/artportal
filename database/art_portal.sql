-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 12 2026 г., 21:55
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `art_portal`
--

-- --------------------------------------------------------

--
-- Структура таблицы `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `artists`
--

INSERT INTO `artists` (`id`, `name`, `location`, `birth_date`, `bio`, `picture`, `status`, `created_at`, `user_id`, `updated_at`) VALUES
(1, 'Marina Chen', 'Estonia, Tartu', '1976-01-14', 'Contemporary abstract painter exploring the intersection of color theory and emotional resonance through bold gestural works.', 'marina-chen.jpg', 'approved', '2026-01-28 11:06:17', 4, '2026-04-09 10:25:14'),
(2, 'Alexandre Dubois', 'Estonia, Tallinn', '1976-01-01', 'Digital-first artist creating immersive installations that blur the boundaries between physical and virtual art spaces.', 'alexandre-dubois.jpg', 'approved', '2026-01-28 11:09:28', 5, '2026-04-09 10:25:14'),
(3, 'Yuki Tanaka', 'Estonia, Tallinn', '1987-05-20', 'Minimalist painter focused on the power of negative space and subtle tonal variations in monochromatic compositions.', 'yuki-tanaka.jpg', 'approved', '2026-01-29 08:49:46', 6, '2026-04-09 10:25:14'),
(4, 'Sofia Rodriguez', 'Estonia, Pärnu', '1978-04-18', 'Mixed media artist combining traditional painting techniques with modern materials to create textured, layered narratives.', 'sofia-rodriguez.jpg', 'approved', '2026-01-29 09:06:17', 7, '2026-04-09 10:25:14'),
(11, 'David Park', 'Estonia, Tallinn', '2000-04-06', 'Contemporary portrait artist known for expressive brushwork and vibrant color palettes that capture emotional depth.', 'david-park.jpg', 'approved', '2026-04-23 09:14:39', 8, '2026-05-02 18:40:12'),
(12, 'Emma Williams', 'Estonia, Tartu', '1996-05-21', 'Geometric abstraction specialist creating harmonious compositions inspired by architecture and urban landscapes.', 'emma-williams.jpg', 'approved', '2026-04-23 09:14:39', 9, '2026-05-02 18:49:04'),
(15, 'Ivav Ivanov', 'Estonia, Viljandi', '1996-06-22', 'I\'m the best artist of the world', 'artist_69f9c29d9e5ef5.15303290.jpg', 'approved', '2026-05-05 13:12:45', 18, '2026-05-11 20:03:48'),
(16, 'Maria White', 'Estonia, Valga', '1960-02-23', 'Art is my passion and life', 'artist_69f9c50e4979f5.57596857.jpg', 'pending', '2026-05-05 13:23:10', 19, '2026-05-05 13:23:10');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_estonian_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Abstract'),
(2, 'Animals'),
(3, 'Landsacape'),
(4, 'Still life'),
(5, 'Contemporary'),
(6, 'Minimalism'),
(7, 'Digital'),
(8, 'Portraiture'),
(9, 'Geometric'),
(10, 'Mixed Media'),
(11, 'test'),
(13, 'test'),
(14, 'test'),
(15, 'Портрет большой');

-- --------------------------------------------------------

--
-- Структура таблицы `collections`
--

CREATE TABLE `collections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('keyword','category','latest','random','popular','ai') NOT NULL,
  `param` varchar(100) DEFAULT NULL COMMENT 'filter parameter'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `collections`
--

INSERT INTO `collections` (`id`, `title`, `type`, `param`) VALUES
(1, 'Artworks having energy in title', 'keyword', 'energy'),
(2, 'Latest artworks', 'latest', NULL),
(4, 'The most new test', 'popular', ''),
(5, 'Collection', 'popular', ''),
(6, 'Collection made throe Exhibition', 'keyword', 'cat'),
(7, 'Winter City Life', 'ai', 'city landscape winter blue'),
(8, 'Orange', 'ai', 'orange mood');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `painting_id` int(11) NOT NULL,
  `text` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_estonian_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `painting_id`, `text`, `date`) VALUES
(1, NULL, 3, 'Very nice', '2026-03-25 13:41:56'),
(2, 1, 4, 'I like it', '2026-03-25 13:41:56'),
(3, 3, 3, 'So cute', '2026-03-25 13:42:49'),
(4, NULL, 10, 'Great work!', '2026-03-25 13:42:49');

-- --------------------------------------------------------

--
-- Структура таблицы `exhibitions`
--

CREATE TABLE `exhibitions` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `collection_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `exhibitions`
--

INSERT INTO `exhibitions` (`id`, `title`, `description`, `collection_id`, `start_date`, `end_date`) VALUES
(2, 'Energy World', 'Artworks connected to Energy', 1, '2026-04-23 13:13:20', '2026-05-11 13:13:20'),
(3, 'New test', 'test', 2, '2026-05-07 21:11:00', '2026-05-10 21:12:00'),
(5, 'Winter City Life', 'Artwork connected winter and city', 7, '2026-05-12 22:12:00', '2026-05-31 22:12:00');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `painting_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `painting_id`, `created_at`) VALUES
(61, 10, 11, '2026-05-08 08:57:09'),
(62, 10, 10, '2026-05-09 20:54:13');

-- --------------------------------------------------------

--
-- Структура таблицы `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `painting_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `painting_id`, `image_path`, `created_at`) VALUES
(1, 3, 'mages/test.jpg', '2026-03-25 13:40:40'),
(2, 3, 'mages/test.jpg', '2026-03-25 13:40:40');

-- --------------------------------------------------------

--
-- Структура таблицы `paintings`
--

CREATE TABLE `paintings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `year_created` year(4) NOT NULL,
  `category_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `medium` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dimensions` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_estonian_ci;

--
-- Дамп данных таблицы `paintings`
--

INSERT INTO `paintings` (`id`, `title`, `description`, `image`, `year_created`, `category_id`, `artist_id`, `medium`, `dimensions`, `price`, `created_at`, `updated_at`) VALUES
(3, 'Ethereal Horizons', 'A sweeping abstract composition that captures the liminal space between consciousness and dreams through layered washes of color.', 'ethereal-horizons.jpg', '2026', 1, 1, 'Acrylic on Canvas', '72\" × 48\"', 450.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(4, 'Digital Synthesis', 'An exploration of the intersection between traditional painting and digital aesthetics, creating a unique visual language.', 'digital-synthesis.jpg', '2026', 10, 2, 'Mixed Media', '60\" × 40\"', 875.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(9, 'Void Studies III', 'A meditation on absence and presence through carefully calibrated tones and the strategic use of empty space.', 'void-studies.jpg', '2026', 6, 3, 'Oil on Linen', '48\" × 48\"', 350.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(10, 'Urban Light Installation', 'The Horizon’s Fiery Dance is a vibrant acrylic seascape filled with movement, energy, and bold color. Racing sailboats cut through the waves as they head toward a glowing, swirling sun on the horizon. Dynamic brushstrokes of red, orange, and deep blue create a dramatic sky, evoking wind, speed, and freedom.\r\n\r\nExpressive and powerful, this one-of-a-kind artwork captures the raw emotion of the sea and the thrill of motion. Painted in acrylic on stretched canvas, it makes a striking focal point for a modern interior and comes with a certificate of authenticity.', 'urban-light-installation.jpg', '2026', 7, 2, 'Digital Art', 'Variable', 660.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(11, 'Portrait of Tomorrow', 'A contemporary portrait that captures the essence of modern identity through expressive brushstrokes and bold color choices.', 'portrait-of-tomorrow.jpg', '2025', 8, 11, 'Oil on Canvas\'', '36\" × 28\"', 777.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(12, 'Chromatic Energy', 'A vibrant explosion of color that celebrates the raw energy and emotion of abstract expressionism.', 'chromatic-energy.jpg', '2025', 1, 1, 'Acrylic on Canvas', '', 999.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(13, 'Geometric Meditation', 'Precise geometric forms create a sense of balance and harmony, inviting contemplation and visual exploration.', 'geometric-meditation.jpg', '2025', 9, 12, 'Acrylic on Panel', '40\" × 40\"', 1100.00, '2026-04-23 09:39:00', '2026-04-23 09:39:00'),
(14, 'Layered Narratives', 'Complex layers of paint, paper, and found materials come together to tell a multifaceted story of texture and depth.', 'layered-narratives.jpg', '2025', 10, 4, 'Mixed Media', '48\" × 36\"', 555.00, '2026-04-23 09:43:58', '2026-04-23 09:43:58'),
(23, 'Bridge', 'Bridge', 'painting_6a0383ddbb1bf4.09642702.jpg', '2025', 3, 15, 'test', 'te', 233.99, '2026-05-12 22:47:41', '2026-05-12 22:47:41');

-- --------------------------------------------------------

--
-- Структура таблицы `painting_tags`
--

CREATE TABLE `painting_tags` (
  `id` int(11) NOT NULL,
  `painting_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `painting_tags`
--

INSERT INTO `painting_tags` (`id`, `painting_id`, `tag_id`) VALUES
(24, 23, 23),
(25, 23, 4),
(26, 23, 24),
(27, 23, 25);

-- --------------------------------------------------------

--
-- Структура таблицы `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `painting_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `purchase_requests`
--

INSERT INTO `purchase_requests` (`id`, `user_id`, `painting_id`, `created_at`) VALUES
(1, 10, 3, '2026-05-07 13:36:00'),
(3, NULL, 12, '2026-05-07 13:39:16'),
(4, 10, NULL, '2026-05-07 15:08:22'),
(5, 10, NULL, '2026-05-07 15:08:24'),
(6, 10, NULL, '2026-05-07 15:08:26'),
(7, 10, NULL, '2026-05-07 15:09:38'),
(8, 10, NULL, '2026-05-07 15:09:40'),
(9, 10, NULL, '2026-05-07 15:09:42'),
(10, 10, NULL, '2026-05-07 15:09:43'),
(11, 10, NULL, '2026-05-07 15:09:44'),
(12, 10, NULL, '2026-05-07 15:09:46'),
(13, 10, NULL, '2026-05-07 15:09:47'),
(14, 10, NULL, '2026-05-07 15:09:47'),
(15, 10, NULL, '2026-05-07 15:09:52'),
(16, 10, NULL, '2026-05-07 15:09:57'),
(17, 10, NULL, '2026-05-07 15:09:57'),
(18, 10, NULL, '2026-05-07 15:09:59'),
(19, 10, NULL, '2026-05-07 15:11:32'),
(20, 10, NULL, '2026-05-07 15:11:34'),
(21, 10, NULL, '2026-05-07 15:15:40'),
(22, 10, NULL, '2026-05-07 15:15:41'),
(23, 10, NULL, '2026-05-08 08:18:18'),
(24, 10, NULL, '2026-05-08 08:18:19'),
(25, 10, NULL, '2026-05-08 08:18:20'),
(26, 10, NULL, '2026-05-08 08:18:21'),
(27, 10, NULL, '2026-05-08 08:18:21'),
(28, 10, 14, '2026-05-08 08:31:22'),
(29, 10, 4, '2026-05-08 08:32:45'),
(30, 10, 11, '2026-05-08 08:57:11'),
(31, 10, 13, '2026-05-08 09:17:19'),
(32, 10, 9, '2026-05-08 09:26:44'),
(33, 10, 12, '2026-05-08 09:28:13'),
(34, 10, 11, '2026-05-09 20:51:48'),
(35, 10, NULL, '2026-05-09 20:53:37'),
(36, 10, 10, '2026-05-09 20:54:25');

-- --------------------------------------------------------

--
-- Структура таблицы `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(10, 'acrylic paint'),
(17, 'aegean cat'),
(18, 'arabian mau'),
(3, 'art'),
(4, 'art paint'),
(23, 'blue'),
(14, 'carnivores'),
(11, 'cat'),
(12, 'felidae'),
(13, 'felinae'),
(8, 'modern art'),
(21, 'ojos azules'),
(2, 'orange'),
(7, 'paint'),
(5, 'painting'),
(20, 'ragamuffin'),
(1, 'red'),
(24, 'spire'),
(22, 'tabby cat'),
(25, 'tower'),
(19, 'turkish van'),
(15, 'vertebrate'),
(6, 'visual arts'),
(9, 'watercolor painting'),
(16, 'whiskers');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','artist','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_estonian_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@newsportal.ee', '$2y$12$pxB2ofiiNZkxObmbBvBOyegwCjHCVFYhapjiSsdYXUaJ9Z1IH6pQW', 'admin', '2019-11-05 00:00:00'),
(2, 'anonim', 'user@newsportal.ee', '$2y$10$dYK1sCogKL/zZBef.V/gBeynL5mdt0QxZlwvEUBkS0jkdXYRMPHRa', 'user', '2019-11-05 00:00:00'),
(3, 'vasya', 'vasya@mail.ru', '$2y$10$EIn5N6yvbp807VbgEHgr0OUAt1T14c4fXwE0eo02E0x4KbFrCSXbK', 'user', '2020-09-19 00:00:00'),
(4, 'marina', 'marina@artportal.ee', '$2y$10$3BaKKk1rcaMpEx2pBQfBnuTp8AkPG33P6EyvY6OIkctda3rSICbs6', 'artist', '2026-03-25 11:17:55'),
(5, 'alexandre', 'alexandre@artportal.ee', '$2y$10$T4XRXaxjEK3ifIxgQzHnZe3f9cAFVTt57NBnsA8PgHguWkuj6BLGe', 'artist', '2026-03-25 13:07:23'),
(6, 'yuki', 'yuki@artportal.ee', '$2y$10$BrSY3RMOOQYnAHAHd29CbeTui.PWY6.YVb4m8.rryrNde0hzMk4PW', 'artist', '2026-03-25 13:08:46'),
(7, 'sofia', 'sofia@artportal', '$2y$10$HOX69EjaxPtXhIqwGbEC/OQtl0brd4WiJ8NWINc0/gnHau77Ka3.a', 'artist', '2026-03-25 13:12:53'),
(8, 'david', 'david@artportal.ee', '$2y$10$isZBs0DDwi1nNtQQ/qwpXO49auxCLTqlhkQM12g0wBNznyeNUuIeC', 'artist', '2026-04-23 09:11:04'),
(9, 'emma', 'emma@artportal.ee', '$2y$10$ZE9d5dGo7gmiTDJ/dh4OyuYmvuFr75YBa0cbowZLv9mR/yA0VEUka', 'artist', '2026-04-23 09:11:04'),
(10, 'test', 'test@test.ee', '$2y$10$5bOaxg0.ue40sLfgp.CONOT8hcincHJrt.DlQUS.L5JrvhtVebfoa', 'user', '2026-04-25 00:00:00'),
(14, 'test2', 'test2@test2.ee', '$2y$10$Hy43cA5HApKjCXJYitwsBuH5z8MbVNzTb0Y/fh1/Fbsqf5mTHFlIa', 'user', '2026-04-25 00:00:00'),
(15, 'new', 'new@test.ee', '$2y$10$Az1Vc5SggqlAtvEin3gAu.58u7zAVro7wJBVhyqfzHw9tBOY1FeBC', 'user', '2026-04-25 00:00:00'),
(17, 'Newnew', 'myemail@mail.ee', '$2y$10$zwYuLFQMSHaaXe01dAhhH.BllpgW.LhM0FVlDM9iYjm9f7hpb629e', 'user', '2026-05-02 00:00:00'),
(18, 'ivan', 'ivan@artportal.ee', '$2y$10$D/.GOeM/AAo9zgabK3UCJeyPm2ZR6a3Ozq7AD05vCwN5egKmAbvwa', 'artist', '2026-05-05 00:00:00'),
(19, 'maria', 'maria@artportal.ee', '$2y$10$qLDaDXNvrGBH5KgWDak9s.m/W8arYP7o6TFuxJEYyULZr99Qszxr6', 'artist', '2026-05-05 00:00:00'),
(20, 'nnn', 'nnn@artportal.ee', '$2y$10$KOXc9VF1ln5Fr9Q6B.JnSeNsLdXIM/V1Rw0VoQ0RkPLKt9JW6Vm4i', 'user', '2026-05-08 00:00:00'),
(21, 'rrr', 'rrr@artportal.ee', '$2y$10$njW70kStMAr.rj7EVfYm5eUmgadXjm6IsRklWkO117EoMhUl.zMOm', 'user', '2026-05-08 00:00:00'),
(22, 'qqq', 'qqq@artportal.ee', '$2y$10$oP1nodEp/ChLKJI4nNEDR.XDHpOKUm7A0Qaz7yrWUCcyGv7d2ycu2', 'user', '2026-05-08 00:00:00'),
(23, 'vvv', 'vvv@artportal.ee', '$2y$10$jcXnkTE3eP2IuDCAwN2u3eTZhEev2bAwvzpZYVVTVn74YcnD6wAEu', 'user', '2026-05-08 00:00:00'),
(24, 'aaa', 'aaa@artportal.ee', '$2y$10$o3yGCdXmDJoUMyfA20bO3OHwNiThr364UOkeEqEcxl1ISGFbvFmvO', 'user', '2026-05-08 00:00:00');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `painting_id` (`painting_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `exhibitions`
--
ALTER TABLE `exhibitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collection_id` (`collection_id`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_painting_unique` (`user_id`,`painting_id`),
  ADD KEY `painting_id` (`painting_id`);

--
-- Индексы таблицы `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `painting_id` (`painting_id`);

--
-- Индексы таблицы `paintings`
--
ALTER TABLE `paintings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `painting_tags`
--
ALTER TABLE `painting_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `painting_tags_ibfk_1` (`painting_id`),
  ADD KEY `painting_tags_ibfk_2` (`tag_id`);

--
-- Индексы таблицы `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_requests_ibfk_2` (`painting_id`),
  ADD KEY `purchase_requests_ibfk_1` (`user_id`);

--
-- Индексы таблицы `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `exhibitions`
--
ALTER TABLE `exhibitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT для таблицы `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `paintings`
--
ALTER TABLE `paintings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `painting_tags`
--
ALTER TABLE `painting_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT для таблицы `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT для таблицы `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `artists`
--
ALTER TABLE `artists`
  ADD CONSTRAINT `artists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`painting_id`) REFERENCES `paintings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `exhibitions`
--
ALTER TABLE `exhibitions`
  ADD CONSTRAINT `exhibitions_ibfk_1` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`);

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`painting_id`) REFERENCES `paintings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `gallery_images_ibfk_1` FOREIGN KEY (`painting_id`) REFERENCES `paintings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `paintings`
--
ALTER TABLE `paintings`
  ADD CONSTRAINT `paintings_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `paintings_ibfk_3` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `painting_tags`
--
ALTER TABLE `painting_tags`
  ADD CONSTRAINT `painting_tags_ibfk_1` FOREIGN KEY (`painting_id`) REFERENCES `paintings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `painting_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD CONSTRAINT `purchase_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_requests_ibfk_2` FOREIGN KEY (`painting_id`) REFERENCES `paintings` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
