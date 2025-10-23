<?php
// Script para criar a tabela recipe_stats automaticamente
require_once 'includes/config.php';

try {
    echo "<h2>Criando tabela recipe_stats...</h2>";
    
    // Criar tabela recipe_stats
    $sql = "CREATE TABLE IF NOT EXISTS `recipe_stats` (
      `recipe_id` int(11) NOT NULL PRIMARY KEY,
      `total_views` int(11) DEFAULT 0,
      `total_ratings` int(11) DEFAULT 0,
      `average_rating` decimal(3,2) DEFAULT 0.00,
      `total_favorites` int(11) DEFAULT 0,
      `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✓ Tabela recipe_stats criada com sucesso!</p>";
    
    // Inserir estatísticas para receitas existentes
    $sql = "INSERT INTO recipe_stats (recipe_id, total_views, total_ratings, average_rating, total_favorites)
    SELECT 
        r.id,
        0 as total_views,
        0 as total_ratings,
        0.00 as average_rating,
        0 as total_favorites
    FROM recipes r
    WHERE NOT EXISTS (
        SELECT 1 FROM recipe_stats WHERE recipe_id = r.id
    )";
    
    $result = $pdo->exec($sql);
    echo "<p style='color: green;'>✓ Estatísticas inseridas para {$result} receitas!</p>";
    
    echo "<h3 style='color: green;'>Tudo pronto! ✓</h3>";
    echo "<p><a href='minhas_receitas.php'>Voltar para Minhas Receitas</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
?>
