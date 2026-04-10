-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 10 2026 г., 07:12
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
  `birth_date` date NOT NULL,
  `bio` text DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `artists`
--

INSERT INTO `artists` (`id`, `name`, `location`, `birth_date`, `bio`, `picture`, `created_at`, `user_id`, `updated_at`) VALUES
(1, 'Lada Kalasho', 'Estonia', '1976-01-14', 'Borned in Ukraine', 'test.jpg', '2026-01-28 11:06:17', 4, '2026-04-09 10:25:14'),
(2, 'Viktoriia Slavinska', 'Ukraine', '1976-01-01', 'Borned in Ukraine. ', NULL, '2026-01-28 11:09:28', 5, '2026-04-09 10:25:14'),
(3, 'Elena Sizonenko', 'Ukraine', '1987-05-20', 'Test', NULL, '2026-01-29 08:49:46', 6, '2026-04-09 10:25:14'),
(4, 'Marina Beikmane', 'Latvia', '1978-04-18', 'From Latvia', NULL, '2026-01-29 09:06:17', 7, '2026-04-09 10:25:14');

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
(4, 'Still life');

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
(3, 'Cute Ginger Cat', 'Cute Ginger Cat is a charming original painting depicting a sweet ginger cat with a warm, playful character. Painted in acrylic on cardboard, this one-of-a-kind artwork brings coziness and gentle emotion to any space. Perfect for cat lovers and collectors of unique art.', 'test.jpg', '2021', 2, 1, '', '', 0.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(4, 'Evening Istanbul', 'A quiet evening unfolds over Istanbul as warm city lights meet the deep blue of the Bosphorus. A solitary cat watches the scene, adding a poetic touch to this impressionistic oil painting. Rich textures and atmospheric colors capture the charm of the city at dusk. Painted on high-quality 290 gsm artist paper, the artwork is signed by the artist on both the front and back and comes with a certificate of authenticity.', 'test.jpg', '2001', 3, 2, '', '', 0.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(9, 'Sailing Boats on the Sea', 'Sailing Boats on the Sea is an original impressionist oil painting depicting graceful sailboats drifting across calm waters. Soft blue and beige tones create a peaceful, airy mood, while expressive palette knife textures add depth and gentle movement. A serene coastal artwork that brings a sense of freedom and tranquility to any interior.', 'test.jpg', '2025', 3, 2, '', '', 0.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(10, 'The Horizon’s Fiery Dance', 'The Horizon’s Fiery Dance is a vibrant acrylic seascape filled with movement, energy, and bold color. Racing sailboats cut through the waves as they head toward a glowing, swirling sun on the horizon. Dynamic brushstrokes of red, orange, and deep blue create a dramatic sky, evoking wind, speed, and freedom.\r\n\r\nExpressive and powerful, this one-of-a-kind artwork captures the raw emotion of the sea and the thrill of motion. Painted in acrylic on stretched canvas, it makes a striking focal point for a modern interior and comes with a certificate of authenticity.', 'test.jpg', '1987', 3, 1, '', '', 0.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(11, 'Lemons on the Table', '“Lemons on the Table” is an original 3D oil painting featuring vibrant citrus fruits. Rich texture created with oil paint and marble-chip putty adds depth and tactile appeal. A small yet expressive still life that brings freshness and light into any interior.', 'test.jpg', '2024', 4, 3, '', '', 0.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14'),
(12, 'After the Rain on a Quiet Irish Street', 'After the Rain on a Quiet Irish Street is an original acrylic painting that captures a fleeting moment of calm after rainfall. Wet pavement reflects soft light and passing figures, while a small café and flowering windows add warmth and life to the scene. Painted in an impressionist style, expressive brushstrokes and layered color create depth and mood without over-detail.\r\n\r\nThis one-of-a-kind artwork celebrates everyday street life, gentle movement, and the romantic atmosphere of a rainy day in a European town. The painted, staple-free canvas edges make it ready to hang without framing—perfect for a living room, hallway, or as a thoughtful gift for lovers of travel and atmospheric cityscapes.', 'test.jpg', '2026', 3, 4, '', '', 0.00, '2026-04-09 10:22:14', '2026-04-09 10:22:14');

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
(4, 'lada', 'lada@artportal.ee', '$2y$10$8MJVIEt3Q3LKq04gSRgMjOrqkf1MATsQVZvHPtUFVrhN96S0QvSby', 'artist', '2026-03-25 11:17:55'),
(5, 'viktoria', 'viktoria@artportal.ee', '$2y$10$X5Rsi.xtMaYpWrVpzLZ2o.3NSYmG35tEPsshp4RgFbMKQw2GAkTEu', 'artist', '2026-03-25 13:07:23'),
(6, 'elena', 'elena@artportal.ee', '$2y$10$8XvUTjGe.ONCraihaVsQW.yUAa7lNVxlqcpZWwvoRSNBa0o/aU2Q2', 'artist', '2026-03-25 13:08:46'),
(7, 'marina', 'marina@artportal', '$2y$10$WLfGLuEgxd0BwcB9ZVjrU.cSFM/CyDTbfmY2xb.vZq/YoRihfONiO', 'user', '2026-03-25 13:12:53');

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
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `painting_id` (`painting_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `paintings`
--
ALTER TABLE `paintings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
