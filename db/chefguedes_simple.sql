-- ChefGuedes - Arquivo SQL simples e único
-- Uso: mysql -u root -p < "c:\wamp64\www\ChefGuedes\db\chefguedes_simple.sql"

-- Banco alvo: MySQL / MariaDB (utf8mb4)

SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `chefguedes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `chefguedes`;

-- Tabela de usuários (simplificada)
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(80) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categorias simples
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Receitas (simplificada)
CREATE TABLE IF NOT EXISTS `recipes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `summary` TEXT DEFAULT NULL,
  `ingredients` TEXT NOT NULL,
  `instructions` TEXT NOT NULL,
  `is_vegetarian` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipes_slug` (`slug`),
  KEY `idx_recipes_user` (`user_id`),
  CONSTRAINT `fk_recipes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ligação receita-categoria (muitos-para-muitos simplificado)
CREATE TABLE IF NOT EXISTS `recipe_categories` (
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`recipe_id`,`category_id`),
  CONSTRAINT `fk_rc_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Imagens (apenas URL)
CREATE TABLE IF NOT EXISTS `recipe_images` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `url` VARCHAR(1000) NOT NULL,
  `position` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_recipe_images_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Comentários
CREATE TABLE IF NOT EXISTS `comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_comments_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Avaliações (1 a 5) - uma por user/recipe
CREATE TABLE IF NOT EXISTS `ratings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `score` TINYINT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ratings_user_recipe` (`recipe_id`,`user_id`),
  CONSTRAINT `fk_ratings_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contagem de "feitas" para saber as mais feitas
CREATE TABLE IF NOT EXISTS `recipe_mades` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mades_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mades_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de estatísticas simples (cache)
CREATE TABLE IF NOT EXISTS `recipe_stats` (
  `recipe_id` BIGINT UNSIGNED NOT NULL,
  `avg_rating` DOUBLE DEFAULT NULL,
  `rating_count` INT UNSIGNED DEFAULT 0,
  `made_count` INT UNSIGNED DEFAULT 0,
  PRIMARY KEY (`recipe_id`),
  CONSTRAINT `fk_stats_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- Inserts de exemplo
INSERT INTO `users` (`username`,`email`,`password_hash`,`role`) VALUES
('admin','admin@example.com','<hash>','admin'),
('alice','alice@example.com','<hash>','user'),
('bob','bob@example.com','<hash>','user');

INSERT INTO `categories` (`name`,`slug`) VALUES
('Vegetarian','vegetarian'),
('Carnivore','carnivore'),
('Desserts','desserts');

INSERT INTO `recipes` (`user_id`,`title`,`slug`,`summary`,`ingredients`,`instructions`,`is_vegetarian`) VALUES
(2,'Salada de Quinoa','salada-de-quinoa','Salada vegetariana leve','Quinoa, tomate, pepino, azeite','Cozinhe a quinoa e misture os ingredientes',1),
(3,'Frango Assado','frango-assado','Frango assado com ervas','Frango, ervas, sal, pimenta','Tempere e asse por 1 hora',0);

INSERT INTO `recipe_categories` (`recipe_id`,`category_id`) VALUES
(1,1),(2,2);

INSERT INTO `recipe_images` (`recipe_id`,`url`,`position`) VALUES
(1,'/uploads/quinoa.jpg',0),(2,'/uploads/frango.jpg',0);

INSERT INTO `comments` (`recipe_id`,`user_id`,`content`) VALUES
(1,3,'Adorei a receita!'),(2,2,'Ficou muito bom');

INSERT INTO `ratings` (`recipe_id`,`user_id`,`score`) VALUES
(1,3,5),(1,2,4),(2,2,5);

INSERT INTO `recipe_mades` (`recipe_id`,`user_id`) VALUES
(1,2),(1,3),(2,2);

-- Atualizar stats simples
REPLACE INTO `recipe_stats` (`recipe_id`,`avg_rating`,`rating_count`,`made_count`)
SELECT r.id, IFNULL(AVG(rt.score),0), COUNT(rt.id), (SELECT COUNT(*) FROM recipe_mades m WHERE m.recipe_id = r.id)
FROM recipes r
LEFT JOIN ratings rt ON rt.recipe_id = r.id
GROUP BY r.id;

-- Queries úteis (exemplos):
-- Mais feitas (all time):
-- SELECT r.id, r.title, rs.made_count FROM recipes r LEFT JOIN recipe_stats rs ON rs.recipe_id = r.id ORDER BY rs.made_count DESC LIMIT 20;

-- Melhores da semana: (depende de como você registra datas de rating)

-- Fim do arquivo
