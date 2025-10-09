-- ChefGuedes - Banco de Dados Melhorado
-- Sistema completo com estatísticas, favoritos, pesquisas e analytics

SET FOREIGN_KEY_CHECKS = 0;

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
  `color` VARCHAR(7) DEFAULT '#6c757d',
  `usage_count` INT DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tags_slug` (`slug`),
  INDEX `idx_tags_usage` (`usage_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Receitas expandidas
CREATE TABLE `recipes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `summary` TEXT DEFAULT NULL,
  `ingredients` TEXT NOT NULL,
  `instructions` TEXT NOT NULL,
  `prep_time` INT DEFAULT NULL COMMENT 'Tempo de preparo em minutos',
  `cook_time` INT DEFAULT NULL COMMENT 'Tempo de cozimento em minutos',
  `total_time` INT DEFAULT NULL COMMENT 'Tempo total em minutos',
  `servings` INT DEFAULT NULL COMMENT 'Número de porções',
  `difficulty` ENUM('facil','medio','dificil') DEFAULT 'medio',
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

-- ===== TABELAS DE RELACIONAMENTO =====

-- Receitas e Tags (muitos para muitos)
CREATE TABLE `recipe_tags` (
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recipe_id`, `tag_id`),
  KEY `idx_recipe_tags_tag` (`tag_id`),
  CONSTRAINT `fk_recipe_tags_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== TABELAS DE INTERAÇÃO E ESTATÍSTICAS =====

-- Avaliações de receitas
CREATE TABLE `recipe_ratings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipe_ratings_user_recipe` (`user_id`, `recipe_id`),
  KEY `idx_recipe_ratings_recipe` (`recipe_id`),
  KEY `idx_recipe_ratings_rating` (`rating`),
  CONSTRAINT `fk_recipe_ratings_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Favoritos
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

-- Histórico de receitas feitas pelos usuários
CREATE TABLE `recipe_made` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `difficulty_rating` TINYINT UNSIGNED DEFAULT NULL CHECK (`difficulty_rating` BETWEEN 1 AND 5),
  `would_make_again` TINYINT(1) DEFAULT NULL,
  `made_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipe_made_recipe` (`recipe_id`),
  KEY `idx_recipe_made_user` (`user_id`),
  KEY `idx_recipe_made_date` (`made_at`),
  CONSTRAINT `fk_recipe_made_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_made_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== TABELAS DE ANALYTICS E PESQUISA =====

-- Histórico de pesquisas dos usuários
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
  KEY `idx_search_history_clicked` (`clicked_recipe_id`),
  CONSTRAINT `fk_search_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_search_history_recipe` FOREIGN KEY (`clicked_recipe_id`) REFERENCES `recipes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Visualizações de receitas
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
  CONSTRAINT `fk_recipe_views_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recipe_views_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== TABELAS DE ESTATÍSTICAS PRÉ-CALCULADAS =====

-- Estatísticas de receitas (atualizada por triggers/cron)
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
  KEY `idx_recipe_stats_popularity` (`popularity_score`),
  CONSTRAINT `fk_recipe_stats_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Estatísticas gerais do sistema
CREATE TABLE `system_stats` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stat_key` VARCHAR(100) NOT NULL,
  `stat_value` TEXT NOT NULL,
  `stat_type` ENUM('counter','json','text','decimal') DEFAULT 'counter',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_system_stats_key` (`stat_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== INSERÇÃO DE DADOS INICIAIS =====

-- Categorias básicas
INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `color`, `order_position`) VALUES
('Pratos Principais', 'pratos-principais', 'Pratos principais da culinária portuguesa', 'P', '#c96b3e', 1),
('Sopas e Caldos', 'sopas', 'Sopas tradicionais e caldos nutritivos', 'S', '#396972', 2),
('Peixe e Marisco', 'peixe-marisco', 'Receitas com peixe fresco e marisco', 'M', '#1976d2', 3),
('Doces e Sobremesas', 'doces', 'Doces conventuais e sobremesas tradicionais', 'D', '#7b1fa2', 4),
('Petiscos e Aperitivos', 'petiscos', 'Petiscos para acompanhar ou partilhar', 'A', '#f57c00', 5),
('Bebidas', 'bebidas', 'Bebidas tradicionais e cocktails', 'B', '#4caf50', 6);

-- Tags básicas
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

-- Usuário admin inicial
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES
('admin', 'admin@chefguedes.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

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