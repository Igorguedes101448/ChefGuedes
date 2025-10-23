-- Criar tabela recipe_stats se não existir
CREATE TABLE IF NOT EXISTS `recipe_stats` (
  `recipe_id` int(11) NOT NULL PRIMARY KEY,
  `total_views` int(11) DEFAULT 0,
  `total_ratings` int(11) DEFAULT 0,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `total_favorites` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir estatísticas para todas as receitas existentes
INSERT INTO recipe_stats (recipe_id, total_views, total_ratings, average_rating, total_favorites)
SELECT 
    r.id,
    0 as total_views,
    0 as total_ratings,
    0.00 as average_rating,
    0 as total_favorites
FROM recipes r
WHERE NOT EXISTS (
    SELECT 1 FROM recipe_stats WHERE recipe_id = r.id
);
