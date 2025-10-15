-- ChefGuedes - Dados de Exemplo para Testes
-- Execute após a criação do banco principal

USE `chefguedes`;

-- ===== INSERIR RECEITAS DE EXEMPLO =====

-- Receitas portuguesas tradicionais
INSERT INTO `recipes` (`user_id`, `category_id`, `title`, `slug`, `summary`, `ingredients`, `instructions`, `prep_time`, `cook_time`, `total_time`, `servings`, `difficulty`, `is_vegetarian`, `is_vegan`, `is_gluten_free`, `is_featured`, `published_at`) VALUES

-- Pratos Principais
(1, 1, 'Bacalhau à Brás', 'bacalhau-a-bras', 'O clássico prato português com bacalhau desfiado, batatas palha e ovos mexidos.', 
'500g bacalhau salgado demolhado\n4 batatas grandes\n6 ovos\n2 cebolas médias\n4 dentes de alho\nAzeite q.b.\nSalsa picada\nAzeitonas pretas\nSal e pimenta', 
'1. Demolhe o bacalhau 24h antes\n2. Cozinhe e desfie o bacalhau\n3. Corte as batatas em palitos finos e frite\n4. Refogue a cebola e alho\n5. Junte o bacalhau e batatas\n6. Adicione os ovos mexidos\n7. Tempere e sirva com salsa', 
30, 25, 55, 4, 'medio', 0, 0, 1, 1, NOW()),

(1, 1, 'Francesinha', 'francesinha', 'A famosa sandes do Porto com molho especial e queijo derretido.', 
'8 fatias de pão de forma\n4 bifes de vitela\n4 salsichas frescas\n4 fatias de fiambre\n4 fatias de mortadela\n200g queijo\nCerveja\nMolho de tomate\nMostarda\nPiri-piri', 
'1. Grelhe os bifes e salsichas\n2. Torre o pão levemente\n3. Monte as sandes com carnes\n4. Prepare o molho com cerveja\n5. Cubra com queijo\n6. Leve ao forno até gratinar\n7. Sirva bem quente', 
20, 15, 35, 4, 'facil', 0, 0, 0, 1, NOW()),

(1, 1, 'Arroz de Marisco', 'arroz-de-marisco', 'Arroz cremoso com uma variedade de mariscos frescos do mar português.', 
'400g arroz\n1kg marisco variado\n1 cebola\n3 tomates\n4 dentes alho\nCoentros\nAzeite\nVinho branco\nCaldo de peixe\nColorau', 
'1. Limpe bem o marisco\n2. Prepare um refogado com cebola, alho e tomate\n3. Junte o marisco e regue com vinho\n4. Adicione o arroz e o caldo\n5. Cozinhe 18 minutos\n6. Finalize com coentros', 
25, 30, 55, 6, 'medio', 0, 0, 1, 1, NOW()),

-- Sopas
(1, 2, 'Caldo Verde', 'caldo-verde', 'A tradicional sopa minhota com couve galega e chouriço.', 
'500g batatas\n200g couve galega\n150g chouriço\n1 cebola\n3 dentes alho\nAzeite\nSal', 
'1. Cozinhe as batatas com cebola e alho\n2. Passe tudo pelo passe-vite\n3. Junte a couve cortada fininha\n4. Adicione o chouriço às rodelas\n5. Tempere com sal e azeite\n6. Sirva bem quente', 
15, 30, 45, 6, 'facil', 0, 0, 1, 1, NOW()),

(1, 2, 'Açorda Alentejana', 'acorda-alentejana', 'Sopa tradicional alentejana com pão, alho, coentros e ovo escalfado.', 
'6 fatias pão alentejano\n6 ovos\n6 dentes alho\nCoentros\nAzeite\nÁgua a ferver\nSal', 
'1. Corte o pão em fatias grossas\n2. Esmague o alho com sal\n3. Misture com coentros picados\n4. Regue com azeite e água a ferver\n5. Deixe repousar 5 minutos\n6. Sirva com ovo escalfado', 
10, 15, 25, 4, 'facil', 1, 0, 0, 0, NOW()),

-- Peixe e Marisco
(1, 3, 'Cataplana de Marisco', 'cataplana-marisco', 'Deliciosa cataplana com amêijoas, camarão e lingueirão.', 
'1kg amêijoas\n500g camarão\n300g lingueirão\n2 cebolas\n4 tomates\nCoentros\nAzeite\nVinho branco\nAlho\nPimento', 
'1. Limpe bem o marisco\n2. Refogue a cebola e alho\n3. Junte o tomate e pimento\n4. Adicione o marisco\n5. Regue com vinho branco\n6. Cozinhe na cataplana 15 min\n7. Polvilhe com coentros', 
25, 20, 45, 4, 'medio', 0, 0, 1, 1, NOW()),

(1, 3, 'Bacalhau com Natas', 'bacalhau-com-natas', 'Receita cremosa de bacalhau gratinado com natas e queijo.', 
'800g bacalhau demolhado\n500ml natas\n1kg batatas\n2 cebolas\n200g queijo ralado\nAzeite\nSal e pimenta', 
'1. Cozinhe e desfie o bacalhau\n2. Corte as batatas às rodelas e frite\n3. Refogue as cebolas\n4. Numa travessa, alterne camadas\n5. Cubra com natas e queijo\n6. Gratine no forno 20 minutos', 
30, 35, 65, 6, 'medio', 0, 0, 1, 0, NOW()),

-- Doces
(1, 4, 'Pastéis de Nata', 'pasteis-de-nata', 'Os famosos pastéis de nata com massa folhada e creme.', 
'300g massa folhada\n500ml leite\n6 gemas\n200g açúcar\n2 c.sopa farinha\nCanela\nRaspa de limão', 
'1. Prepare o creme com leite, açúcar e farinha\n2. Junte as gemas uma a uma\n3. Forre as formas com massa\n4. Deite o creme nas formas\n5. Leve ao forno a 220°C\n6. Asse até dourar o topo\n7. Polvilhe com canela', 
45, 15, 60, 12, 'dificil', 1, 0, 0, 1, NOW()),

(1, 4, 'Pudim Flan', 'pudim-flan', 'Sobremesa cremosa com caramelo, perfeita para ocasiões especiais.', 
'6 ovos\n1 lata leite condensado\n200ml leite\n150g açúcar\n2 c.sopa água', 
'1. Faça caramelo com açúcar e água\n2. Forre a forma com o caramelo\n3. Bata os ovos com leite condensado\n4. Adicione o leite e misture bem\n5. Deite na forma e coza em banho-maria\n6. Refrigere antes de desenformar', 
20, 50, 70, 8, 'medio', 1, 0, 1, 0, NOW()),

-- Petiscos
(1, 5, 'Pataniscas de Bacalhau', 'pataniscas-bacalhau', 'Deliciosos bolinhos de bacalhau fritos, perfeitos para petisco.', 
'300g bacalhau salgado\n200g farinha\n3 ovos\n1 cebola\nSalsa\nAzeite para fritar\nSal e pimenta', 
'1. Demolhe e cozinhe o bacalhau\n2. Desfie muito bem\n3. Misture com farinha e ovos\n4. Junte cebola e salsa picadas\n5. Tempere a gosto\n6. Frite às colheradas em azeite quente\n7. Escorra em papel absorvente', 
20, 15, 35, 6, 'facil', 0, 0, 0, 0, NOW()),

(1, 5, 'Rissóis de Camarão', 'rissois-de-camarao', 'Rissóis crocantes recheados com camarão e bechamel.', 
'500g camarão\n500ml leite\n3 c.sopa farinha\n1 cebola\nMassa para rissóis\nOvos para empanar\nPão ralado', 
'1. Faça bechamel com farinha e leite\n2. Refogue camarão com cebola\n3. Misture com bechamel\n4. Rechei as massas\n5. Passe em ovo e pão ralado\n6. Frite em óleo bem quente', 
40, 20, 60, 8, 'dificil', 0, 0, 0, 0, NOW()),

-- Bebidas
(1, 6, 'Sangria Portuguesa', 'sangria-portuguesa', 'Refrescante sangria com vinho tinto e frutas da época.', 
'1 garrafa vinho tinto\n2 pêssegos\n1 maçã\n1 laranja\n50ml brandy\n2 c.sopa açúcar\nÁgua com gás', 
'1. Corte as frutas em cubos\n2. Misture com açúcar e brandy\n3. Deixe macerar 2 horas\n4. Adicione o vinho tinto\n5. Complete com água com gás\n6. Sirva bem gelada com gelo', 
15, 0, 15, 6, 'facil', 1, 1, 1, 0, NOW());

-- ===== INSERIR TAGS NAS RECEITAS =====

INSERT INTO `recipe_tags` (`recipe_id`, `tag_id`) VALUES
-- Bacalhau à Brás
(1, 1), (1, 3), (1, 10),
-- Francesinha  
(2, 1), (2, 3), (2, 12),
-- Arroz de Marisco
(3, 1), (3, 3), (3, 12),
-- Caldo Verde
(4, 1), (4, 4), (4, 9),
-- Açorda Alentejana
(5, 1), (5, 2), (5, 5),
-- Cataplana
(6, 1), (6, 3), (6, 12),
-- Bacalhau com Natas
(7, 1), (7, 3), (7, 9),
-- Pastéis de Nata
(8, 1), (8, 10), (8, 11),
-- Pudim Flan
(9, 1), (9, 2), (9, 10),
-- Pataniscas
(10, 1), (10, 3), (10, 5),
-- Rissóis
(11, 1), (11, 3), (11, 12),
-- Sangria
(12, 1), (12, 2), (12, 8);

-- ===== INICIALIZAR ESTATÍSTICAS =====

INSERT INTO `recipe_stats` (`recipe_id`, `total_views`, `unique_views`, `total_favorites`, `total_made`, `total_ratings`, `average_rating`, `popularity_score`) VALUES
(1, 145, 98, 23, 45, 12, 4.3, 287.5),
(2, 198, 156, 34, 28, 18, 4.6, 412.8),
(3, 189, 142, 29, 38, 15, 4.4, 356.2),
(4, 234, 187, 45, 67, 25, 4.8, 623.4),
(5, 156, 124, 31, 42, 19, 4.2, 389.6),
(6, 89, 72, 18, 15, 8, 4.2, 178.6),
(7, 167, 134, 26, 33, 14, 4.5, 334.8),
(8, 312, 245, 78, 123, 45, 4.7, 998.5),
(9, 201, 178, 52, 89, 32, 4.6, 567.3),
(10, 123, 95, 28, 35, 15, 4.1, 287.5),
(11, 134, 108, 22, 29, 11, 4.3, 298.7),
(12, 98, 76, 19, 24, 9, 4.4, 224.8);

-- ===== SIMULAR ATIVIDADE DE USUÁRIOS =====

-- Pesquisas populares
INSERT INTO `search_history` (`user_id`, `search_query`, `search_type`, `results_count`, `ip_address`, `created_at`) VALUES
(NULL, 'bacalhau', 'text', 3, '192.168.1.100', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(NULL, 'doces', 'category', 5, '192.168.1.101', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'francesinha', 'text', 1, '192.168.1.102', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(NULL, 'sopas', 'category', 4, '192.168.1.103', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(NULL, 'marisco', 'text', 2, '192.168.1.104', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(NULL, 'tradicional', 'tag', 6, '192.168.1.105', DATE_SUB(NOW(), INTERVAL 30 MINUTE)),
(1, 'petiscos', 'category', 3, '192.168.1.106', DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
(NULL, 'pastéis', 'text', 1, '192.168.1.107', NOW());

-- Visualizações recentes
INSERT INTO `recipe_views` (`recipe_id`, `user_id`, `ip_address`, `view_duration`, `viewed_at`) VALUES
(1, 1, '192.168.1.100', 120, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(2, NULL, '192.168.1.101', 85, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(3, 1, '192.168.1.102', 156, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(5, NULL, '192.168.1.103', 234, DATE_SUB(NOW(), INTERVAL 30 MINUTE)),
(4, NULL, '192.168.1.104', 98, DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
(6, 1, '192.168.1.105', 67, NOW());

-- Favoritos
INSERT INTO `recipe_favorites` (`recipe_id`, `user_id`, `created_at`) VALUES
(1, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, 1, DATE_SUB(NOW(), INTERVAL 1 HOUR));

-- Receitas feitas
INSERT INTO `recipe_made` (`recipe_id`, `user_id`, `notes`, `difficulty_rating`, `would_make_again`, `made_at`) VALUES
(1, 1, 'Ficou delicioso! Usei menos sal.', 3, 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 1, 'Perfeito para o inverno.', 2, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 1, 'Difícil mas vale a pena!', 5, 1, DATE_SUB(NOW(), INTERVAL 1 WEEK));

-- Avaliações
INSERT INTO `recipe_ratings` (`recipe_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 5, 'Receita excelente, seguindo à risca fica perfeito!', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 1, 4, 'Muito boa, mas o molho poderia ter mais explicações.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 1, 5, 'O melhor caldo verde que já fiz!', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 1, 5, 'Difícil mas o resultado é fantástico.', DATE_SUB(NOW(), INTERVAL 1 WEEK));

-- ===== ATUALIZAR ESTATÍSTICAS DO SISTEMA =====

UPDATE `system_stats` SET `stat_value` = '12' WHERE `stat_key` = 'total_recipes';
UPDATE `system_stats` SET `stat_value` = '15' WHERE `stat_key` = 'total_searches';
UPDATE `system_stats` SET `stat_value` = '2346' WHERE `stat_key` = 'total_views';
UPDATE `system_stats` SET `stat_value` = '4.5' WHERE `stat_key` = 'avg_rating';

-- ===== VIEWS PARA FACILITAR CONSULTAS =====

-- Receitas com estatísticas completas
CREATE VIEW `recipes_with_stats` AS
SELECT 
    r.*,
    rs.total_views,
    rs.unique_views,
    rs.total_favorites,
    rs.total_made,
    rs.total_ratings,
    rs.average_rating,
    rs.popularity_score,
    c.name as category_name,
    u.username as chef_name
FROM recipes r
LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id
LEFT JOIN categories c ON r.category_id = c.id
LEFT JOIN users u ON r.user_id = u.id
WHERE r.is_active = 1;

-- Pesquisas trending da semana
CREATE VIEW `weekly_trending` AS
SELECT 
    search_query,
    COUNT(*) as search_count,
    COUNT(DISTINCT user_id) as unique_users,
    MAX(created_at) as last_searched
FROM search_history
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY search_query
HAVING search_count > 1
ORDER BY search_count DESC
LIMIT 20;

COMMIT;