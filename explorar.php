<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorar Receitas - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .explore-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .explore-header {
            text-align: center;
            margin-bottom: 50px;
            padding: 40px 20px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            border-radius: 15px;
        }
        
        .explore-header h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .explore-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .filters-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        
        .filters-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .search-container {
            position: relative;
            margin-bottom: 25px;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(201, 107, 62, 0.1);
        }
        
        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--color-primary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            background: var(--color-accent);
        }
        
        .filter-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .category-filter {
            background: #f8f9fa;
            border: 2px solid transparent;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .category-filter:hover {
            background: var(--color-light);
            border-color: var(--color-primary);
        }
        
        .category-filter.active {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }
        
        .additional-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        
        .filter-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: #f8f9fa;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-option:hover {
            background: var(--color-light);
        }
        
        .filter-option.active {
            background: var(--color-primary);
            color: white;
        }
        
        .filter-option input[type="checkbox"] {
            margin: 0;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .results-count {
            font-size: 1.1rem;
            color: var(--color-text);
        }
        
        .sort-options {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .sort-select {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
        }
        
        .recipes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .recipe-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 40px rgba(0,0,0,0.15);
        }
        
        .recipe-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }
        
        .recipe-content {
            padding: 20px;
        }
        
        .recipe-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .recipe-summary {
            color: var(--color-text-light);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .recipe-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--color-text-light);
        }
        
        .recipe-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .recipe-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }
        
        .recipe-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            color: var(--color-text-light);
        }
        
        .recipe-difficulty {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .difficulty-facil {
            background: #d4edda;
            color: #155724;
        }
        
        .difficulty-medio {
            background: #fff3cd;
            color: #856404;
        }
        
        .difficulty-dificil {
            background: #f8d7da;
            color: #721c24;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-text-light);
        }
        
        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        
        .pagination button {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .pagination button:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }
        
        .pagination button.active {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }
        
        @media (max-width: 768px) {
            .explore-header h1 {
                font-size: 2rem;
            }
            
            .recipes-grid {
                grid-template-columns: 1fr;
            }
            
            .results-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-categories {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?php
    require_once 'includes/config.php';
    
    // Parâmetros de filtro
    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? 'all';
    $difficulty = $_GET['difficulty'] ?? '';
    $vegetarian = $_GET['vegetarian'] ?? '';
    $vegan = $_GET['vegan'] ?? '';
    $gluten_free = $_GET['gluten_free'] ?? '';
    $sort = $_GET['sort'] ?? 'recent';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per_page = 12;
    $offset = ($page - 1) * $per_page;
    
    // Construir query de busca
    $where_conditions = ['r.is_active = 1'];
    $params = [];
    
    if (!empty($search)) {
        $where_conditions[] = '(r.title LIKE ? OR r.description LIKE ? OR r.ingredients LIKE ?)';
        $search_param = '%' . $search . '%';
        $params = array_merge($params, [$search_param, $search_param, $search_param]);
    }
    
    if ($category !== 'all') {
        $where_conditions[] = 'r.category_id = ?';
        $params[] = $category;
    }
    
    if (!empty($difficulty)) {
        $where_conditions[] = 'r.difficulty = ?';
        $params[] = $difficulty;
    }
    
    if ($vegetarian === '1') {
        $where_conditions[] = 'r.is_vegetarian = 1';
    }
    
    if ($vegan === '1') {
        $where_conditions[] = 'r.is_vegan = 1';
    }
    
    if ($gluten_free === '1') {
        $where_conditions[] = 'r.is_gluten_free = 1';
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Definir ordenação
    $order_by = match($sort) {
        'recent' => 'r.created_at DESC',
        'popular' => 'rs.total_views DESC',
        'rating' => 'rs.average_rating DESC',
        'time' => 'r.total_time ASC',
        default => 'r.created_at DESC'
    };
    
    // Buscar receitas
    $recipes = [];
    $total_recipes = 0;
    
    try {
        // Contar total
        $count_sql = "
            SELECT COUNT(*)
            FROM recipes r
            LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id
            WHERE $where_clause
        ";
        $stmt = $pdo->prepare($count_sql);
        $stmt->execute($params);
        $total_recipes = $stmt->fetchColumn();
        
        // Buscar receitas
        $sql = "
            SELECT 
                r.*,
                c.name as category_name,
                u.username as chef_name,
                rs.total_views,
                rs.total_favorites,
                rs.average_rating,
                rs.total_ratings
            FROM recipes r
            LEFT JOIN categories c ON r.category_id = c.id
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id
            WHERE $where_clause
            ORDER BY $order_by
            LIMIT $per_page OFFSET $offset
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $recipes = $stmt->fetchAll();
        
    } catch (Exception $e) {
        $error = 'Erro ao buscar receitas: ' . $e->getMessage();
    }
    
    // Buscar categorias para filtros
    $categories = [];
    try {
        $stmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY order_position");
        $categories = $stmt->fetchAll();
    } catch (Exception $e) {
        $categories = [];
    }
    
    $total_pages = ceil($total_recipes / $per_page);
    ?>
    
    <div class="explore-container">
        <div class="explore-header">
            <h1>Explorar Receitas</h1>
            <p>Descubra receitas incríveis compartilhadas pela nossa comunidade de chefs</p>
        </div>
        
        <div class="filters-section">
            <h2 class="filters-title">Encontre a Receita Perfeita</h2>
            
            <form method="GET" action="" id="filterForm">
                <div class="search-container">
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="Pesquisar receitas, ingredientes..." 
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="search-btn">Pesquisar</button>
                </div>
                
                <div class="filter-categories">
                    <div class="category-filter <?= $category === 'all' ? 'active' : '' ?>" 
                         onclick="setCategory('all')">
                        Todas
                    </div>
                    <?php foreach ($categories as $cat): ?>
                        <div class="category-filter <?= $category == $cat['id'] ? 'active' : '' ?>" 
                             onclick="setCategory('<?= $cat['id'] ?>')">
                            <?= htmlspecialchars($cat['name']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="additional-filters">
                    <div class="filter-option <?= $difficulty === 'facil' ? 'active' : '' ?>" 
                         onclick="toggleFilter('difficulty', 'facil')">
                        Fácil
                    </div>
                    <div class="filter-option <?= $difficulty === 'medio' ? 'active' : '' ?>" 
                         onclick="toggleFilter('difficulty', 'medio')">
                        Médio
                    </div>
                    <div class="filter-option <?= $difficulty === 'dificil' ? 'active' : '' ?>" 
                         onclick="toggleFilter('difficulty', 'dificil')">
                        Difícil
                    </div>
                    <div class="filter-option <?= $vegetarian === '1' ? 'active' : '' ?>" 
                         onclick="toggleFilter('vegetarian', '1')">
                        Vegetariano
                    </div>
                    <div class="filter-option <?= $vegan === '1' ? 'active' : '' ?>" 
                         onclick="toggleFilter('vegan', '1')">
                        Vegano
                    </div>
                    <div class="filter-option <?= $gluten_free === '1' ? 'active' : '' ?>" 
                         onclick="toggleFilter('gluten_free', '1')">
                        Sem Glúten
                    </div>
                </div>
                
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <input type="hidden" name="difficulty" value="<?= htmlspecialchars($difficulty) ?>">
                <input type="hidden" name="vegetarian" value="<?= htmlspecialchars($vegetarian) ?>">
                <input type="hidden" name="vegan" value="<?= htmlspecialchars($vegan) ?>">
                <input type="hidden" name="gluten_free" value="<?= htmlspecialchars($gluten_free) ?>">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
            </form>
        </div>
        
        <div class="results-header">
            <div class="results-count">
                <?= $total_recipes ?> receita<?= $total_recipes !== 1 ? 's' : '' ?> encontrada<?= $total_recipes !== 1 ? 's' : '' ?>
            </div>
            
            <div class="sort-options">
                <label for="sortSelect">Ordenar por:</label>
                <select id="sortSelect" class="sort-select" onchange="changeSort(this.value)">
                    <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Mais Recentes</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Mais Populares</option>
                    <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Melhor Avaliadas</option>
                    <option value="time" <?= $sort === 'time' ? 'selected' : '' ?>>Mais Rápidas</option>
                </select>
            </div>
        </div>
        
        <?php if (empty($recipes)): ?>
            <div class="no-results">
                <h3>Nenhuma receita encontrada</h3>
                <p>Tente ajustar os filtros ou usar termos de pesquisa diferentes.</p>
            </div>
        <?php else: ?>
            <div class="recipes-grid">
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-card" onclick="window.location.href='receita.php?slug=<?= urlencode($recipe['slug']) ?>'">
                        <div class="recipe-image">
                            <?= strtoupper(substr($recipe['title'], 0, 1)) ?>
                        </div>
                        
                        <div class="recipe-content">
                            <h3 class="recipe-title"><?= htmlspecialchars($recipe['title']) ?></h3>
                            
                            <?php if (!empty($recipe['description'])): ?>
                                <p class="recipe-summary"><?= htmlspecialchars(truncateText($recipe['description'], 100)) ?></p>
                            <?php endif; ?>
                            
                            <div class="recipe-meta">
                                <span>⏱️ <?= formatTime($recipe['total_time'] ?? $recipe['prep_time']) ?></span>
                                <span>👥 <?= $recipe['servings'] ?> porções</span>
                                <span>👨‍🍳 <?= htmlspecialchars($recipe['chef_name']) ?></span>
                            </div>
                            
                            <div class="recipe-stats">
                                <div class="recipe-rating">
                                    <span>★ <?= number_format($recipe['average_rating'] ?? 0, 1) ?></span>
                                    <span>(<?= $recipe['total_ratings'] ?? 0 ?>)</span>
                                    <span>• 👁️ <?= $recipe['total_views'] ?? 0 ?></span>
                                </div>
                                
                                <div class="recipe-difficulty difficulty-<?= $recipe['difficulty'] ?>">
                                    <?= getDifficultyText($recipe['difficulty']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <button onclick="changePage(<?= $page - 1 ?>)">← Anterior</button>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <button class="<?= $i === $page ? 'active' : '' ?>" onclick="changePage(<?= $i ?>)">
                            <?= $i ?>
                        </button>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <button onclick="changePage(<?= $page + 1 ?>)">Próxima →</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="index.php" style="color: var(--color-text-light); text-decoration: none;">
                ← Voltar à página inicial
            </a>
        </div>
    </div>
    
    <script>
        function setCategory(categoryId) {
            document.querySelector('input[name="category"]').value = categoryId;
            document.getElementById('filterForm').submit();
        }
        
        function toggleFilter(filterName, value) {
            const input = document.querySelector(`input[name="${filterName}"]`);
            if (input.value === value) {
                input.value = '';
            } else {
                input.value = value;
            }
            document.getElementById('filterForm').submit();
        }
        
        function changeSort(sortValue) {
            document.querySelector('input[name="sort"]').value = sortValue;
            document.getElementById('filterForm').submit();
        }
        
        function changePage(pageNum) {
            const form = document.getElementById('filterForm');
            const pageInput = document.createElement('input');
            pageInput.type = 'hidden';
            pageInput.name = 'page';
            pageInput.value = pageNum;
            form.appendChild(pageInput);
            form.submit();
        }
        
        // Auto-submit search on enter
        document.querySelector('.search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    </script>
</body>
</html>