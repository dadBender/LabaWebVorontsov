-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июн 11 2025 г., 19:20
-- Версия сервера: 9.2.0
-- Версия PHP: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vanyalab4`
--

-- --------------------------------------------------------

--
-- Структура таблицы `captcha_images`
--

CREATE TABLE `captcha_images` (
  `id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `answer` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `captcha_images`
--

INSERT INTO `captcha_images` (`id`, `image_path`, `answer`) VALUES
(1, 'images/captcha/1.png', '28ivw'),
(2, 'images/captcha/2.png', 'FH2DE'),
(3, 'images/captcha/3.png', 'gwprp'),
(4, 'images/captcha/4.png', '4D7YS'),
(5, 'images/captcha/5.png', 'xmqki'),
(6, 'images/captcha/6.png', 'e5hb'),
(7, 'images/captcha/7.png', 'q98p'),
(8, 'images/captcha/8.png', 'XDHYN');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `year` int DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `title`, `genre`, `year`, `category`, `image`) VALUES
(1, 'Побег из Шоушенка', 'Драма', 1994, 'movies', 'shawshank.jpg'),
(2, 'Крестный отец', 'Драма', 1972, 'movies', 'godfather.jpg'),
(3, 'Темный рыцарь', 'Экшн', 2008, 'movies', 'dark_knight.jpg'),
(4, 'Форрест Гамп', 'Драма', 1994, 'movies', 'forrest_gump.jpg'),
(5, 'Начало', 'Фантастика', 2010, 'movies', 'inception.jpg'),
(6, 'Бойцовский клуб', 'Триллер', 1999, 'movies', 'fight_club.jpg'),
(7, 'Матрица', 'Фантастика', 1999, 'movies', 'matrix.jpg'),
(8, 'Властелин колец: Братство кольца', 'Фэнтези', 2001, 'movies', 'lotr1.jpg'),
(9, 'Интерстеллар', 'Фантастика', 2014, 'movies', 'interstellar.jpg'),
(10, 'Поймай меня, если сможешь', 'Биография', 2002, 'movies', 'catch_me.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `features` text,
  `recommended` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `name`, `price`, `features`, `recommended`, `active`) VALUES
(1, 'Free', 20.00, '• Ограниченный доступ к контенту• Только SD качество• Реклама• 1 устройство', 0, 1),
(2, 'Базовый', 170.00, '• Доступ к базовым фильмам и сериалам• 1 трансляция одновременно• Качество до 720p• Редкая реклама', 0, 1),
(3, 'Стандарт', 220.00, '• Полный доступ к библиотеке• 2 трансляции одновременно• Качество до 1080p• Без рекламы', 1, 1),
(4, 'Премиум', 470.00, '• Всё включено• 4 трансляции одновременно• Качество до 4K UHD• Поддержка Dolby Atmos• Ранний доступ к премьерам', 0, 1),
(5, 'Ультра', 620.00, '• Все возможности Премиум• 6 устройств одновременно• Оффлайн-доступ• Приоритетная поддержка• Уникальный контент', 0, 1),
(6, 'Ультра Ультра', 850.00, '• Все возможности Премиум• 6 устройств одновременно• Оффлайн-доступ• Приоритетная поддержка• Уникальный контент• Особый секретный список фильмов', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `registration_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `subscription_id` int DEFAULT NULL,
  `subscription_expires` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`, `phone`, `email`, `registration_date`, `subscription_id`, `subscription_expires`) VALUES
(1, 'a', '$2y$12$zawMo3X2aP5pEVtpvTM3zuyWkNmz.qKas21IrEpriBxmey50WKGVS', 'a', 'a', 'a@a', '2025-05-05 00:00:00', 3, '2025-05-23'),
(2, 'antoneaa26', '$2y$12$C4oUpdbsQxULVGBm.HLpHuAw3vccShfScnYmYFpBWmmBn5lUagRv.', 'Anto', '+7 (495) 123-45-67', 'ntnefimov@gmail.com', '2025-05-12 00:00:00', 2, '2025-07-11'),
(4, 'aaaaaaaaaaa', '$2y$12$BBctoakf.KOD3c8TGtzXqekfD9ZxodUShwEfoYfOR06wk6x9svPsW', 'aaaaaaaaaaa', '+7 (495) 123-45-67', 'ntnefimov@gmail.com', '2025-05-19 00:00:00', 4, '2025-05-24'),
(5, 'fevveking123', '$2y$12$sXTrleWghWNPoxyfEkUgPOrvjqLCpCQLfMlNV9bgW1BudcKF4EtcC', 'Ivan', '+7 (495) 123-45-67', 'v@gmail.com', '2025-05-06 00:00:00', 3, '2025-06-06');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `captcha_images`
--
ALTER TABLE `captcha_images`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `captcha_images`
--
ALTER TABLE `captcha_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
