-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 27-Out-2025 às 10:03
-- Versão do servidor: 8.3.0
-- versão do PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `chefguedes`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `order_position` int DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `color`, `order_position`, `is_active`, `created_at`) VALUES
(1, 'Pratos Principais', 'pratos-principais', 'Pratos principais da culinária portuguesa', 'P', '#c96b3e', 1, 1, '2025-10-21 15:42:18'),
(2, 'Sopas e Caldos', 'sopas', 'Sopas tradicionais e caldos nutritivos', 'S', '#396972', 2, 1, '2025-10-21 15:42:18'),
(3, 'Peixe e Marisco', 'peixe-marisco', 'Receitas com peixe fresco e marisco', 'M', '#1976d2', 3, 1, '2025-10-21 15:42:18'),
(4, 'Doces e Sobremesas', 'doces', 'Doces conventuais e sobremesas tradicionais', 'D', '#7b1fa2', 4, 1, '2025-10-21 15:42:18'),
(5, 'Petiscos e Aperitivos', 'petiscos', 'Petiscos para acompanhar ou partilhar', 'A', '#f57c00', 5, 1, '2025-10-21 15:42:18'),
(6, 'Bebidas', 'bebidas', 'Bebidas tradicionais e cocktails', 'B', '#4caf50', 6, 1, '2025-10-21 15:42:18'),
(7, 'Sobremesas', 'sobremesas', 'Receitas de sobremesas', NULL, NULL, 0, 1, '2025-10-27 09:47:20'),
(8, 'Entradas', 'entradas', 'Receitas de entradas e aperitivos', NULL, NULL, 0, 1, '2025-10-27 09:47:20'),
(9, 'Snacks', 'snacks', 'Receitas de lanches rápidos', NULL, NULL, 0, 1, '2025-10-27 09:47:20');

-- --------------------------------------------------------

--
-- Estrutura da tabela `family_groups`
--

DROP TABLE IF EXISTS `family_groups`;
CREATE TABLE IF NOT EXISTS `family_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `owner_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `family_invites`
--

DROP TABLE IF EXISTS `family_invites`;
CREATE TABLE IF NOT EXISTS `family_invites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `inviter_id` int NOT NULL,
  `invitee_id` int NOT NULL,
  `status` enum('pending','accepted','declined','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `invited_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `inviter_id` (`inviter_id`),
  KEY `invitee_id` (`invitee_id`),
  KEY `idx_family_invites_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `family_members`
--

DROP TABLE IF EXISTS `family_members`;
CREATE TABLE IF NOT EXISTS `family_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('owner','admin','member','viewer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `can_add_recipes` tinyint(1) NOT NULL DEFAULT '1',
  `can_assign_tasks` tinyint(1) NOT NULL DEFAULT '1',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_user` (`group_id`,`user_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `family_recipes`
--

DROP TABLE IF EXISTS `family_recipes`;
CREATE TABLE IF NOT EXISTS `family_recipes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `recipe_id` int DEFAULT NULL,
  `recipe_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meal_date` date NOT NULL,
  `meal_type` enum('breakfast','lunch','dinner','snack') COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_to` int DEFAULT NULL,
  `added_by` int NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `recipe_id` (`recipe_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `added_by` (`added_by`),
  KEY `meal_date` (`meal_date`),
  KEY `idx_family_recipes_date_type` (`meal_date`,`meal_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `family_tasks`
--

DROP TABLE IF EXISTS `family_tasks`;
CREATE TABLE IF NOT EXISTS `family_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `family_recipe_id` int DEFAULT NULL,
  `task_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `assigned_to` int DEFAULT NULL,
  `assigned_by` int NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `family_recipe_id` (`family_recipe_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `assigned_by` (`assigned_by`),
  KEY `idx_family_tasks_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recipes`
--

DROP TABLE IF EXISTS `recipes`;
CREATE TABLE IF NOT EXISTS `recipes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text,
  `description` text,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `prep_time` int DEFAULT NULL COMMENT 'Tempo de preparo em minutos',
  `cook_time` int DEFAULT NULL COMMENT 'Tempo de cozimento em minutos',
  `total_time` int DEFAULT NULL COMMENT 'Tempo total em minutos',
  `servings` int DEFAULT NULL COMMENT 'Número de porções',
  `difficulty` enum('facil','medio','dificil','easy','medium','hard') DEFAULT 'medio',
  `is_vegetarian` tinyint(1) NOT NULL DEFAULT '0',
  `is_vegan` tinyint(1) NOT NULL DEFAULT '0',
  `is_gluten_free` tinyint(1) NOT NULL DEFAULT '0',
  `is_premium` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `image_url` varchar(500) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `nutrition_info` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `published_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipes_slug` (`slug`),
  KEY `idx_recipes_user` (`user_id`),
  KEY `idx_recipes_category` (`category_id`),
  KEY `idx_recipes_featured` (`is_featured`),
  KEY `idx_recipes_premium` (`is_premium`),
  KEY `idx_recipes_published` (`published_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `category_id`, `title`, `slug`, `summary`, `description`, `ingredients`, `instructions`, `prep_time`, `cook_time`, `total_time`, `servings`, `difficulty`, `is_vegetarian`, `is_vegan`, `is_gluten_free`, `is_premium`, `is_featured`, `image_url`, `video_url`, `nutrition_info`, `created_at`, `updated_at`, `published_at`, `is_active`) VALUES
(1, 2, NULL, 'couto', 'couto', NULL, 'couto', 'agua e limao', 'junta tudo e esta feito', NULL, NULL, NULL, NULL, 'medio', 0, 0, 0, 0, 0, NULL, NULL, NULL, '2025-10-23 11:47:01', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `recipe_favorites`
--

DROP TABLE IF EXISTS `recipe_favorites`;
CREATE TABLE IF NOT EXISTS `recipe_favorites` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_favorites_user_recipe` (`user_id`,`recipe_id`),
  KEY `idx_recipe_favorites_recipe` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recipe_made`
--

DROP TABLE IF EXISTS `recipe_made`;
CREATE TABLE IF NOT EXISTS `recipe_made` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `notes` text,
  `difficulty_rating` tinyint UNSIGNED DEFAULT NULL,
  `would_make_again` tinyint(1) DEFAULT NULL,
  `made_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipe_made_recipe` (`recipe_id`),
  KEY `idx_recipe_made_user` (`user_id`),
  KEY `idx_recipe_made_date` (`made_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recipe_ratings`
--

DROP TABLE IF EXISTS `recipe_ratings`;
CREATE TABLE IF NOT EXISTS `recipe_ratings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_ratings_user_recipe` (`user_id`,`recipe_id`),
  KEY `idx_recipe_ratings_recipe` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recipe_stats`
--

DROP TABLE IF EXISTS `recipe_stats`;
CREATE TABLE IF NOT EXISTS `recipe_stats` (
  `recipe_id` bigint UNSIGNED NOT NULL,
  `total_views` int DEFAULT '0',
  `unique_views` int DEFAULT '0',
  `total_favorites` int DEFAULT '0',
  `total_made` int DEFAULT '0',
  `total_ratings` int DEFAULT '0',
  `average_rating` decimal(3,2) DEFAULT '0.00',
  `last_viewed_at` datetime DEFAULT NULL,
  `last_favorited_at` datetime DEFAULT NULL,
  `popularity_score` decimal(10,4) DEFAULT '0.0000',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`recipe_id`),
  KEY `idx_recipe_stats_views` (`total_views`),
  KEY `idx_recipe_stats_favorites` (`total_favorites`),
  KEY `idx_recipe_stats_rating` (`average_rating`),
  KEY `idx_recipe_stats_popularity` (`popularity_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `recipe_stats`
--

INSERT INTO `recipe_stats` (`recipe_id`, `total_views`, `unique_views`, `total_favorites`, `total_made`, `total_ratings`, `average_rating`, `last_viewed_at`, `last_favorited_at`, `popularity_score`, `updated_at`) VALUES
(1, 0, 0, 0, 0, 0, 0.00, NULL, NULL, 0.0000, '2025-10-23 12:10:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `recipe_tags`
--

DROP TABLE IF EXISTS `recipe_tags`;
CREATE TABLE IF NOT EXISTS `recipe_tags` (
  `recipe_id` bigint UNSIGNED NOT NULL,
  `tag_id` int UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recipe_id`,`tag_id`),
  KEY `idx_recipe_tags_tag` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recipe_views`
--

DROP TABLE IF EXISTS `recipe_views`;
CREATE TABLE IF NOT EXISTS `recipe_views` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `referrer` varchar(500) DEFAULT NULL,
  `view_duration` int DEFAULT NULL COMMENT 'Duração em segundos',
  `viewed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipe_views_recipe` (`recipe_id`),
  KEY `idx_recipe_views_user` (`user_id`),
  KEY `idx_recipe_views_date` (`viewed_at`),
  KEY `idx_recipe_views_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `search_history`
--

DROP TABLE IF EXISTS `search_history`;
CREATE TABLE IF NOT EXISTS `search_history` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `search_query` varchar(255) NOT NULL,
  `search_type` enum('text','category','tag','ingredient') DEFAULT 'text',
  `results_count` int DEFAULT '0',
  `clicked_recipe_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_search_history_user` (`user_id`),
  KEY `idx_search_history_query` (`search_query`),
  KEY `idx_search_history_date` (`created_at`),
  KEY `idx_search_history_clicked` (`clicked_recipe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `search_history`
--

INSERT INTO `search_history` (`id`, `user_id`, `search_query`, `search_type`, `results_count`, `clicked_recipe_id`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, 'bacalhau', 'text', 3, NULL, '127.0.0.1', NULL, '2025-10-20 15:42:18');

-- --------------------------------------------------------

--
-- Estrutura da tabela `system_stats`
--

DROP TABLE IF EXISTS `system_stats`;
CREATE TABLE IF NOT EXISTS `system_stats` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `stat_key` varchar(100) NOT NULL,
  `stat_value` text NOT NULL,
  `stat_type` enum('counter','json','text','decimal') DEFAULT 'counter',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_system_stats_key` (`stat_key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `system_stats`
--

INSERT INTO `system_stats` (`id`, `stat_key`, `stat_value`, `stat_type`, `updated_at`) VALUES
(1, 'total_recipes', '3', 'counter', '2025-10-21 15:42:18'),
(2, 'total_users', '1', 'counter', '2025-10-21 15:42:18'),
(3, 'total_views', '577', 'counter', '2025-10-21 15:42:18'),
(4, 'avg_rating', '4.57', 'decimal', '2025-10-21 15:42:18');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(7) DEFAULT '#6c757d',
  `usage_count` int DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tags_slug` (`slug`),
  KEY `idx_tags_usage` (`usage_count`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `color`, `usage_count`, `created_at`) VALUES
(1, 'Tradicional', 'tradicional', '#ef6c00', 0, '2025-10-21 15:42:18'),
(2, 'Vegetariano', 'vegetariano', '#4caf50', 0, '2025-10-21 15:42:18'),
(3, 'Proteína', 'proteina', '#e91e63', 0, '2025-10-21 15:42:18'),
(4, 'Saudável', 'saudavel', '#2196f3', 0, '2025-10-21 15:42:18'),
(5, 'Rápido', 'rapido', '#ff9800', 0, '2025-10-21 15:42:18'),
(6, 'Fácil', 'facil', '#4caf50', 0, '2025-10-21 15:42:18'),
(7, 'Premium', 'premium', '#9c27b0', 0, '2025-10-21 15:42:18'),
(8, 'Verão', 'verao', '#ffeb3b', 0, '2025-10-21 15:42:18'),
(9, 'Inverno', 'inverno', '#607d8b', 0, '2025-10-21 15:42:18'),
(10, 'Festivo', 'festivo', '#f44336', 0, '2025-10-21 15:42:18'),
(11, 'Caseiro', 'caseiro', '#795548', 0, '2025-10-21 15:42:18'),
(12, 'Gourmet', 'gourmet', '#3f51b5', 0, '2025-10-21 15:42:18'),
(13, 'Económico', 'economico', '#6c757d', 0, '2025-10-27 09:47:20'),
(14, 'Vegano', 'vegano', '#6c757d', 0, '2025-10-27 09:47:20'),
(15, 'Sem Glúten', 'sem-gluten', '#6c757d', 0, '2025-10-27 09:47:20'),
(16, 'Proteico', 'proteico', '#6c757d', 0, '2025-10-27 09:47:20');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin','chef') NOT NULL DEFAULT 'user',
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text,
  `location` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `avatar`, `bio`, `location`, `created_at`, `updated_at`, `last_login`, `is_active`) VALUES
(1, 'admin', 'admin@chefguedes.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, NULL, '2025-10-21 15:42:18', NULL, NULL, 1),
(2, 'igor', 'igor123@gmail.com', '$2y$10$vzqvjL7OBfV8aUWNlgYvHuoBf9/.kkzjuUnbGzLLNC9QE/n4kFWFW', 'user', 'uploads/profiles/avatar_2_68f7a08709770.jpg', 'CHEFGUEDES MASTER', 'Portugal', '2025-10-21 15:46:06', '2025-10-23 11:21:00', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura stand-in para vista `user_favorites`
-- (Veja abaixo para a view atual)
--
DROP VIEW IF EXISTS `user_favorites`;
CREATE TABLE IF NOT EXISTS `user_favorites` (
`id` bigint unsigned
,`user_id` bigint unsigned
,`recipe_id` bigint unsigned
,`title` varchar(255)
,`slug` varchar(255)
,`description` text
,`chef_name` varchar(80)
,`created_at` datetime
);

-- --------------------------------------------------------

--
-- Estrutura para vista `user_favorites`
--
DROP TABLE IF EXISTS `user_favorites`;

DROP VIEW IF EXISTS `user_favorites`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_favorites`  AS SELECT `rf`.`id` AS `id`, `rf`.`user_id` AS `user_id`, `rf`.`recipe_id` AS `recipe_id`, `r`.`title` AS `title`, `r`.`slug` AS `slug`, `r`.`description` AS `description`, `u`.`username` AS `chef_name`, `rf`.`created_at` AS `created_at` FROM ((`recipe_favorites` `rf` join `recipes` `r` on((`rf`.`recipe_id` = `r`.`id`))) join `users` `u` on((`r`.`user_id` = `u`.`id`))) ;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `fk_recipes_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_recipes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `recipe_favorites`
--
ALTER TABLE `recipe_favorites`
  ADD CONSTRAINT `fk_recipe_favorites_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recipe_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `recipe_made`
--
ALTER TABLE `recipe_made`
  ADD CONSTRAINT `fk_recipe_made_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recipe_made_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `recipe_ratings`
--
ALTER TABLE `recipe_ratings`
  ADD CONSTRAINT `fk_recipe_ratings_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recipe_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `recipe_tags`
--
ALTER TABLE `recipe_tags`
  ADD CONSTRAINT `fk_recipe_tags_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recipe_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `recipe_views`
--
ALTER TABLE `recipe_views`
  ADD CONSTRAINT `fk_recipe_views_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
