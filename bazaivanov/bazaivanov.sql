-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 14 2025 г., 13:04
-- Версия сервера: 10.11.6-MariaDB
-- Версия PHP: 8.1.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `bazaivanov`
--

-- --------------------------------------------------------

--
-- Структура таблицы `book_cards`
--

CREATE TABLE `book_cards` (
  `card_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `author` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `type_id` int(11) NOT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `binding_type` varchar(50) DEFAULT NULL,
  `condition_description` text DEFAULT NULL,
  `status_id` int(11) DEFAULT 1,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `book_cards`
--

INSERT INTO `book_cards` (`card_id`, `user_id`, `author`, `title`, `type_id`, `publisher`, `publication_year`, `binding_type`, `condition_description`, `status_id`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 2, 'Достоевский Ф.М.', 'Преступление и наказание', 1, 'Эксмо', 2015, 'Твердый', 'Отличное состояние', 2, NULL, '2025-05-14 12:46:07', '2025-05-14 12:46:07'),
(2, 2, 'Толстой Л.Н.', 'Война и мир', 2, 'АСТ', 2010, 'Мягкий', NULL, 1, NULL, '2025-05-14 12:46:07', '2025-05-14 12:46:07');

-- --------------------------------------------------------

--
-- Структура таблицы `card_statuses`
--

CREATE TABLE `card_statuses` (
  `status_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `card_statuses`
--

INSERT INTO `card_statuses` (`status_id`, `name`, `description`) VALUES
(1, 'pending', 'Ожидает модерации'),
(2, 'approved', 'Одобрена администратором'),
(3, 'rejected', 'Отклонена администратором'),
(4, 'archived', 'В архиве');

-- --------------------------------------------------------

--
-- Структура таблицы `card_types`
--

CREATE TABLE `card_types` (
  `type_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `card_types`
--

INSERT INTO `card_types` (`type_id`, `name`, `description`) VALUES
(1, 'share', 'Готов поделиться книгой'),
(2, 'wish', 'Хочу получить книгу');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `phone`, `email`, `login`, `password_hash`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 'Администратор системы', '+7(999)-999-99-99', 'admin@bookworm.ru', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-05-14 12:46:07', '2025-05-14 12:46:07'),
(2, 'Иванов Иван Иванович', '+7(123)-456-78-90', 'user1@example.com', 'user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-05-14 12:46:07', '2025-05-14 12:46:07');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `book_cards`
--
ALTER TABLE `book_cards`
  ADD PRIMARY KEY (`card_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `idx_author` (`author`),
  ADD KEY `idx_title` (`title`),
  ADD KEY `idx_status` (`status_id`);

--
-- Индексы таблицы `card_statuses`
--
ALTER TABLE `card_statuses`
  ADD PRIMARY KEY (`status_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `card_types`
--
ALTER TABLE `card_types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `idx_login` (`login`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `book_cards`
--
ALTER TABLE `book_cards`
  MODIFY `card_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `card_statuses`
--
ALTER TABLE `card_statuses`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `card_types`
--
ALTER TABLE `card_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `book_cards`
--
ALTER TABLE `book_cards`
  ADD CONSTRAINT `book_cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_cards_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `card_types` (`type_id`),
  ADD CONSTRAINT `book_cards_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `card_statuses` (`status_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
