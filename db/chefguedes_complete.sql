-- ChefGuedes - Esquema completo (unificado)
-- Inclui `recipe_favorites` e compatibilidade com `user_favorites` para evitar erros
-- Importante: execute em MySQL/MariaDB com charset utf8mb4

SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS `chefguedes`;
CREATE DATABASE `chefguedes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `chefguedes`;

-- ===== TABELAS PRINCIPAIS =====

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(80) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('user','admin','chef') NOT NULL DEFAULT 'user',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_login` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `icon` VARCHAR(50) DEFAULT NULL,
  `color` VARCHAR(7) DEFAULT NULL,
  `order_position` INT DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `color` VARCHAR(7) DEFAULT '#6c757d',
  `usage_count` INT DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tags_slug` (`slug`),
  INDEX `idx_tags_usage` (`usage_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `recipes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `summary` TEXT DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `ingredients` TEXT NOT NULL,
  `instructions` TEXT NOT NULL,
  `prep_time` INT DEFAULT NULL COMMENT 'Tempo de preparo em minutos',
  `cook_time` INT DEFAULT NULL COMMENT 'Tempo de cozimento em minutos',
  `total_time` INT DEFAULT NULL COMMENT 'Tempo total em minutos',
  `servings` INT DEFAULT NULL COMMENT 'Número de porções',
  `difficulty` ENUM('facil','medio','dificil','easy','medium','hard') DEFAULT 'medio',
  `is_vegetarian` TINYINT(1) NOT NULL DEFAULT 0,
  `is_vegan` TINYINT(1) NOT NULL DEFAULT 0,
  `is_gluten_free` TINYINT(1) NOT NULL DEFAULT 0,
  `is_premium` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `image_url` VARCHAR(500) DEFAULT NULL,
  `video_url` VARCHAR(500) DEFAULT NULL,
  `nutrition_info` JSON DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `published_at` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipes_slug` (`slug`),
  KEY `idx_recipes_user` (`user_id`),
  KEY `idx_recipes_category` (`category_id`),
  KEY `idx_recipes_featured` (`is_featured`),
  KEY `idx_recipes_premium` (`is_premium`),
  KEY `idx_recipes_published` (`published_at`),
  CONSTRAINT `fk_recipes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipes_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Relação receitas-tags
CREATE TABLE `recipe_tags` (
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recipe_id`, `tag_id`),
  KEY `idx_recipe_tags_tag` (`tag_id`),
  CONSTRAINT `fk_recipe_tags_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== Interações e estatísticas =====

CREATE TABLE `recipe_ratings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `comment` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_ratings_user_recipe` (`user_id`, `recipe_id`),
  KEY `idx_recipe_ratings_recipe` (`recipe_id`),
  CONSTRAINT `fk_recipe_ratings_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela preferida original: recipe_favorites (alguns arquivos esperam este nome).
CREATE TABLE `recipe_favorites` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_favorites_user_recipe` (`user_id`, `recipe_id`),
  KEY `idx_recipe_favorites_recipe` (`recipe_id`),
  CONSTRAINT `fk_recipe_favorites_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Criar também user_favorites como view para compatibilidade com versões que usam esse nome
CREATE VIEW `user_favorites` AS
SELECT rf.id, rf.user_id, rf.recipe_id, rf.created_at FROM recipe_favorites rf;

CREATE TABLE `recipe_made` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `difficulty_rating` TINYINT UNSIGNED DEFAULT NULL,
  `would_make_again` TINYINT(1) DEFAULT NULL,
  `made_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipe_made_recipe` (`recipe_id`),
  KEY `idx_recipe_made_user` (`user_id`),
  KEY `idx_recipe_made_date` (`made_at`),
  CONSTRAINT `fk_recipe_made_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_made_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `search_history` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `search_query` VARCHAR(255) NOT NULL,
  `search_type` ENUM('text','category','tag','ingredient') DEFAULT 'text',
  `results_count` INT DEFAULT 0,
  `clicked_recipe_id` BIGINT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_search_history_user` (`user_id`),
  KEY `idx_search_history_query` (`search_query`),
  KEY `idx_search_history_date` (`created_at`),
  KEY `idx_search_history_clicked` (`clicked_recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `recipe_views` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `referrer` VARCHAR(500) DEFAULT NULL,
  `view_duration` INT DEFAULT NULL COMMENT 'Duração em segundos',
  `viewed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipe_views_recipe` (`recipe_id`),
  KEY `idx_recipe_views_user` (`user_id`),
  KEY `idx_recipe_views_date` (`viewed_at`),
  KEY `idx_recipe_views_ip` (`ip_address`),
  CONSTRAINT `fk_recipe_views_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `recipe_stats` (
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `total_views` INT DEFAULT 0,
  `unique_views` INT DEFAULT 0,
  `total_favorites` INT DEFAULT 0,
  `total_made` INT DEFAULT 0,
  `total_ratings` INT DEFAULT 0,
  `average_rating` DECIMAL(3,2) DEFAULT 0.00,
  `last_viewed_at` DATETIME DEFAULT NULL,
  `last_favorited_at` DATETIME DEFAULT NULL,
  `popularity_score` DECIMAL(10,4) DEFAULT 0.0000,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`recipe_id`),
  KEY `idx_recipe_stats_views` (`total_views`),
  KEY `idx_recipe_stats_favorites` (`total_favorites`),
  KEY `idx_recipe_stats_rating` (`average_rating`),
  KEY `idx_recipe_stats_popularity` (`popularity_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `system_stats` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stat_key` VARCHAR(100) NOT NULL,
  `stat_value` TEXT NOT NULL,
  `stat_type` ENUM('counter','json','text','decimal') DEFAULT 'counter',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_system_stats_key` (`stat_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== DADOS INICIAIS =====

INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `color`, `order_position`) VALUES
('Pratos Principais', 'pratos-principais', 'Pratos principais da culinária portuguesa', 'P', '#c96b3e', 1),
('Sopas e Caldos', 'sopas', 'Sopas tradicionais e caldos nutritivos', 'S', '#396972', 2),
('Peixe e Marisco', 'peixe-marisco', 'Receitas com peixe fresco e marisco', 'M', '#1976d2', 3),
('Doces e Sobremesas', 'doces', 'Doces conventuais e sobremesas tradicionais', 'D', '#7b1fa2', 4),
('Petiscos e Aperitivos', 'petiscos', 'Petiscos para acompanhar ou partilhar', 'A', '#f57c00', 5),
('Bebidas', 'bebidas', 'Bebidas tradicionais e cocktails', 'B', '#4caf50', 6);

INSERT INTO `tags` (`name`, `slug`, `color`) VALUES
('Tradicional', 'tradicional', '#ef6c00'),
('Vegetariano', 'vegetariano', '#4caf50'),
('Proteína', 'proteina', '#e91e63'),
('Saudável', 'saudavel', '#2196f3'),
('Rápido', 'rapido', '#ff9800'),
('Fácil', 'facil', '#4caf50'),
('Premium', 'premium', '#9c27b0'),
('Verão', 'verao', '#ffeb3b'),
('Inverno', 'inverno', '#607d8b'),
('Festivo', 'festivo', '#f44336'),
('Caseiro', 'caseiro', '#795548'),
('Gourmet', 'gourmet', '#3f51b5');

-- Admin user
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES
('admin', 'admin@chefguedes.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample recipes (IDs start at 1)
INSERT INTO `recipes` (`user_id`, `category_id`, `title`, `slug`, `summary`, `description`, `ingredients`, `instructions`, `prep_time`, `cook_time`, `total_time`, `servings`, `difficulty`, `is_vegetarian`, `is_vegan`, `is_gluten_free`, `is_featured`, `published_at`) VALUES
(1, 1, 'Bacalhau à Brás', 'bacalhau-a-bras', 'Prato com bacalhau e batata palha', 'Versão tradicional do Bacalhau à Brás', '500g bacalhau...','1. Faça isto\n2. Faça aquilo', 30,25,55,4,'medio',0,0,1,1,NOW()),
(1, 1, 'Francesinha', 'francesinha', 'Sandes do Porto', 'Famosa francesinha', 'Ingredientes...','1. Faça isto',20,15,35,4,'facil',0,0,0,1,NOW()),
(1, 2, 'Caldo Verde', 'caldo-verde', 'Sopa tradicional', 'Caldo verde com couve', 'Ingredientes...','1. Faça isto',15,30,45,6,'facil',0,0,1,1,NOW());

-- Inicializar recipe_stats
INSERT INTO `recipe_stats` (`recipe_id`, `total_views`, `unique_views`, `total_favorites`, `total_made`, `total_ratings`, `average_rating`, `popularity_score`) VALUES
(1, 145, 98, 23, 45, 12, 4.30, 287.5),
(2, 198, 156, 34, 28, 18, 4.60, 412.8),
(3, 234, 187, 45, 67, 25, 4.80, 623.4);

-- Inicializar recipe_favorites (exemplo)
INSERT INTO `recipe_favorites` (`recipe_id`, `user_id`) VALUES
(1,1),(2,1),(3,1);

-- Search history and views
INSERT INTO `search_history` (`user_id`, `search_query`, `search_type`, `results_count`, `ip_address`, `created_at`) VALUES
(NULL, 'bacalhau', 'text', 3, '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 DAY));

INSERT INTO `recipe_views` (`recipe_id`, `user_id`, `ip_address`, `view_duration`, `viewed_at`) VALUES
(1,1,'127.0.0.1',120,DATE_SUB(NOW(), INTERVAL 1 HOUR));

INSERT INTO `system_stats` (`stat_key`, `stat_value`, `stat_type`) VALUES
('total_recipes','3','counter'),
('total_users','1','counter'),
('total_views','577','counter'),
('avg_rating','4.57','decimal');

SET FOREIGN_KEY_CHECKS = 1;
