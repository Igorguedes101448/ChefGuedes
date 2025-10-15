-- ChefGuedes - Instalação Limpa (SEM DUPLICATAS)
-- Sistema completo com estatísticas, favoritos, pesquisas e analytics

SET FOREIGN_KEY_CHECKS = 0;

-- DROPAR E RECRIAR A BASE DE DADOS COMPLETAMENTE
DROP DATABASE IF EXISTS `chefguedes`;
CREATE DATABASE `chefguedes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `chefguedes`;

-- ===== TABELAS PRINCIPAIS =====

-- Usuários
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

-- Categorias
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

-- Tags para receitas
CREATE TABLE `tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `color` VARCHAR(7) DEFAULT NULL,
  `usage_count` INT UNSIGNED DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tags_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Receitas
CREATE TABLE `recipes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `ingredients` TEXT NOT NULL,
  `instructions` TEXT NOT NULL,
  `prep_time` INT UNSIGNED DEFAULT NULL COMMENT 'tempo em minutos',
  `cook_time` INT UNSIGNED DEFAULT NULL COMMENT 'tempo em minutos',
  `servings` INT UNSIGNED DEFAULT NULL,
  `difficulty` ENUM('easy','medium','hard') DEFAULT 'medium',
  `image` VARCHAR(255) DEFAULT NULL,
  `video_url` VARCHAR(500) DEFAULT NULL,
  `nutrition_info` JSON DEFAULT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipes_slug` (`slug`),
  KEY `idx_recipes_user` (`user_id`),
  KEY `idx_recipes_category` (`category_id`),
  KEY `idx_recipes_featured` (`is_featured`),
  KEY `idx_recipes_active` (`is_active`),
  CONSTRAINT `fk_recipes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipes_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Relação receitas-tags (many-to-many)
CREATE TABLE `recipe_tags` (
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recipe_id`, `tag_id`),
  KEY `idx_recipe_tags_tag` (`tag_id`),
  CONSTRAINT `fk_recipe_tags_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== SISTEMA DE AVALIAÇÕES =====

-- Avaliações das receitas
CREATE TABLE `recipe_ratings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `review` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_ratings_user` (`recipe_id`, `user_id`),
  KEY `idx_recipe_ratings_recipe` (`recipe_id`),
  KEY `idx_recipe_ratings_user` (`user_id`),
  KEY `idx_recipe_ratings_rating` (`rating`),
  CONSTRAINT `fk_recipe_ratings_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== SISTEMA DE FAVORITOS =====

-- Receitas favoritas dos usuários
CREATE TABLE `user_favorites` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_favorites` (`user_id`, `recipe_id`),
  KEY `idx_user_favorites_recipe` (`recipe_id`),
  CONSTRAINT `fk_user_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_favorites_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== ANALYTICS E ESTATÍSTICAS =====

-- Estatísticas das receitas
CREATE TABLE `recipe_stats` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `total_views` BIGINT UNSIGNED DEFAULT 0,
  `total_favorites` INT UNSIGNED DEFAULT 0,
  `total_ratings` INT UNSIGNED DEFAULT 0,
  `average_rating` DECIMAL(3,2) DEFAULT 0.00,
  `popularity_score` DECIMAL(8,2) DEFAULT 0.00,
  `last_viewed` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_stats_recipe` (`recipe_id`),
  KEY `idx_recipe_stats_views` (`total_views`),
  KEY `idx_recipe_stats_rating` (`average_rating`),
  KEY `idx_recipe_stats_popularity` (`popularity_score`),
  CONSTRAINT `fk_recipe_stats_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Visualizações das receitas (para analytics detalhados)
CREATE TABLE `recipe_views` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `referrer` VARCHAR(500) DEFAULT NULL,
  `viewed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipe_views_recipe` (`recipe_id`),
  KEY `idx_recipe_views_user` (`user_id`),
  KEY `idx_recipe_views_date` (`viewed_at`),
  CONSTRAINT `fk_recipe_views_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_views_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Histórico de pesquisas
CREATE TABLE `search_history` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `search_query` VARCHAR(255) NOT NULL,
  `results_count` INT UNSIGNED DEFAULT 0,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_search_history_user` (`user_id`),
  KEY `idx_search_history_query` (`search_query`),
  KEY `idx_search_history_date` (`created_at`),
  CONSTRAINT `fk_search_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estatísticas do sistema
CREATE TABLE `system_stats` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stat_key` VARCHAR(100) NOT NULL,
  `stat_value` VARCHAR(500) NOT NULL,
  `stat_type` ENUM('counter','decimal','json','text') DEFAULT 'counter',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_system_stats_key` (`stat_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== SISTEMA DE COMENTÁRIOS =====

-- Comentários nas receitas
CREATE TABLE `recipe_comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `parent_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Para respostas a comentários',
  `comment` TEXT NOT NULL,
  `is_approved` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipe_comments_recipe` (`recipe_id`),
  KEY `idx_recipe_comments_user` (`user_id`),
  KEY `idx_recipe_comments_parent` (`parent_id`),
  KEY `idx_recipe_comments_approved` (`is_approved`),
  CONSTRAINT `fk_recipe_comments_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `recipe_comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== SISTEMA DE NOTIFICAÇÕES =====

-- Notificações para usuários
CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('recipe_liked','new_comment','new_follower','recipe_featured','system') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `data` JSON DEFAULT NULL COMMENT 'dados extras como IDs relacionados',
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_read` (`is_read`),
  KEY `idx_notifications_type` (`type`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== DADOS INICIAIS =====

-- Usuário admin inicial (senha: admin123)
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES
('admin', 'admin@chefguedes.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Categorias iniciais
INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `color`) VALUES
('Pratos Principais', 'pratos-principais', 'Receitas para refeições completas', 'P', '#c96b3e'),
('Sopas', 'sopas', 'Sopas tradicionais e modernas', 'S', '#2c5530'),
('Sobremesas', 'sobremesas', 'Doces e sobremesas deliciosas', 'D', '#8b4513'),
('Entradas', 'entradas', 'Aperitivos e entradas', 'E', '#556b2f'),
('Bebidas', 'bebidas', 'Bebidas e sumos naturais', 'B', '#4a90e2');

-- Tags iniciais
INSERT INTO `tags` (`name`, `slug`, `color`) VALUES
('Tradicional', 'tradicional', '#c96b3e'),
('Vegetariano', 'vegetariano', '#2c5530'),
('Rápido', 'rapido', '#e74c3c'),
('Saudável', 'saudavel', '#27ae60'),
('Festa', 'festa', '#f39c12'),
('Comfort Food', 'comfort-food', '#8b4513');

-- Estatísticas iniciais do sistema
INSERT INTO `system_stats` (`stat_key`, `stat_value`, `stat_type`) VALUES
('total_recipes', '0', 'counter'),
('total_users', '1', 'counter'),
('total_views', '0', 'counter'),
('total_searches', '0', 'counter'),
('avg_rating', '0.00', 'decimal');

SET FOREIGN_KEY_CHECKS = 1;

-- ===== VIEWS ÚTEIS =====

-- View para receitas populares
CREATE VIEW `popular_recipes` AS
SELECT 
    r.*,
    rs.total_views,
    rs.total_favorites,
    rs.average_rating,
    rs.popularity_score,
    u.username as chef_name,
    c.name as category_name
FROM `recipes` r
LEFT JOIN `recipe_stats` rs ON r.id = rs.recipe_id
LEFT JOIN `users` u ON r.user_id = u.id
LEFT JOIN `categories` c ON r.category_id = c.id
WHERE r.is_active = 1
ORDER BY rs.popularity_score DESC;

-- View para receitas mais pesquisadas
CREATE VIEW `trending_searches` AS
SELECT 
    search_query,
    COUNT(*) as search_count,
    COUNT(DISTINCT user_id) as unique_users,
    MAX(created_at) as last_searched
FROM `search_history`
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY search_query
ORDER BY search_count DESC
LIMIT 50;