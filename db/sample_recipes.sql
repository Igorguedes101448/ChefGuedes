-- Adicionar receitas de exemplo para testar o site
-- Execute este arquivo APÓS importar a base de dados principal

USE `chefguedes`;

-- Inserir receitas de exemplo
INSERT INTO `recipes` (`user_id`, `title`, `slug`, `description`, `ingredients`, `instructions`, `prep_time`, `cook_time`, `servings`, `difficulty`, `category_id`, `is_featured`, `created_at`) VALUES

-- Receita 1: Francesinha
(1, 'Francesinha Tradicional do Porto', 'francesinha-tradicional-porto', 
'A icónica sandes portuense com linguiça, fiambre, salsicha fresca e bife, coberta com queijo derretido e molho especial.', 
'8 fatias de pão de forma\n4 bifes de vitela\n4 salsichas frescas\n8 fatias de fiambre\n4 fatias de linguiça\n200g queijo flamengo\n4 ovos\n\nMolho:\n500ml cerveja\n200ml natas\n2 colheres sopa tomate concentrado\n1 colher café piri-piri\nSal e pimenta q.b.', 
'1. Grelhe os bifes, salsichas e linguiça\n2. Torre ligeiramente o pão\n3. Monte as sandes: pão, fiambre, bife, linguiça, salsicha, mais fiambre, pão\n4. Cubra com queijo\n5. Leve ao forno até derreter\n6. Frite os ovos\n7. Para o molho: misture todos os ingredientes e cozinhe 10 min\n8. Sirva com ovo por cima e molho abundante', 
20, 25, 4, 'medium', 1, 1, NOW()),

-- Receita 2: Pastéis de Nata
(1, 'Pastéis de Nata Caseiros', 'pasteis-nata-caseiros',
'Os famosos pastéis de nata portugueses, com massa folhada crocante e creme suave com canela.',
'500g massa folhada\n6 gemas\n200g açúcar\n50g farinha Maizena\n500ml leite\n1 pau canela\nRaspa 1 limão\nCanela em pó q.b.',
'1. Aqueça o leite com casca de limão e canela\n2. Misture açúcar e Maizena\n3. Junte o leite quente mexendo sempre\n4. Leve ao lume até engrossar\n5. Retire do lume e junte as gemas\n6. Forre formas com massa folhada\n7. Deite o creme\n8. Asse a 220°C por 15-20 min\n9. Polvilhe com canela',
30, 20, 12, 'medium', 3, 1, NOW()),

-- Receita 3: Caldo Verde
(1, 'Caldo Verde Minhoto', 'caldo-verde-minhoto',
'A sopa mais portuguesa de todas, com couve galega, batata, chouriço e azeite.',
'1kg batatas\n300g couve galega\n200g chouriço\n1 cebola\n2 dentes alho\n100ml azeite\n1.5L água\nSal q.b.',
'1. Descasque e corte as batatas\n2. Refogue cebola e alho no azeite\n3. Junte batatas e água\n4. Cozinhe 20 min\n5. Passe tudo a puré\n6. Corte a couve bem fininha\n7. Junte couve e chouriço\n8. Cozinhe mais 5 min\n9. Tempere com sal e azeite',
15, 30, 6, 'easy', 2, 1, NOW()),

-- Receita 4: Arroz de Pato
(1, 'Arroz de Pato à Antiga', 'arroz-pato-antiga',
'Arroz cremoso com pato desfiado, chouriço e ovo, gratinado no forno.',
'1 pato\n400g arroz\n200g chouriço\n1 cebola\n2 dentes alho\n50ml azeite\n1L caldo pato\n3 ovos\nSalsa q.b.',
'1. Coza o pato em água temperada\n2. Desfie a carne\n3. Refogue cebola e alho\n4. Junte arroz e caldo\n5. Cozinhe 15 min\n6. Misture pato e chouriço\n7. Ponha numa forma\n8. Cubra com ovo batido\n9. Gratine no forno',
40, 60, 8, 'hard', 1, 1, NOW()),

-- Receita 5: Bifana
(1, 'Bifana Tradicional', 'bifana-tradicional',
'O lanche português por excelência - febras de porco temperadas em pão.',
'4 febras porco\n4 pães bifana\n2 dentes alho\n1 folha louro\n50ml vinho branco\n50ml azeite\nSal, pimenta, colorau\nMostarda q.b.',
'1. Tempere as febras com sal, pimenta e colorau\n2. Aqueça azeite numa frigideira\n3. Junte alho e louro\n4. Frite as febras 3 min cada lado\n5. Regue com vinho branco\n6. Deixe apurar o molho\n7. Torre o pão\n8. Monte com mostarda',
10, 10, 4, 'easy', 1, 0, NOW());

-- Inserir estatísticas para as receitas
INSERT INTO `recipe_stats` (`recipe_id`, `total_views`, `total_favorites`, `total_ratings`, `average_rating`, `popularity_score`) VALUES
(1, 1250, 89, 45, 4.7, 95.8),
(2, 2100, 156, 78, 4.9, 98.2),
(3, 980, 67, 34, 4.5, 89.3),
(4, 1450, 98, 52, 4.6, 92.1),
(5, 876, 43, 28, 4.3, 85.7);

-- Inserir algumas avaliações de exemplo
INSERT INTO `recipe_ratings` (`recipe_id`, `user_id`, `rating`, `review`) VALUES
(1, 1, 5, 'Receita perfeita! Igual às francinhas do Porto.'),
(2, 1, 5, 'Ficaram deliciosos, melhor que na pastelaria.'),
(3, 1, 4, 'Caldo verde autêntico, como a minha avó fazia.'),
(4, 1, 5, 'Trabalhoso mas vale a pena, espetacular!'),
(5, 1, 4, 'Bifana caseira saborosa, recomendo.');

-- Atualizar estatísticas do sistema
UPDATE `system_stats` SET `stat_value` = '5' WHERE `stat_key` = 'total_recipes';
UPDATE `system_stats` SET `stat_value` = '5456' WHERE `stat_key` = 'total_views';
UPDATE `system_stats` SET `stat_value` = '4.6' WHERE `stat_key` = 'avg_rating';