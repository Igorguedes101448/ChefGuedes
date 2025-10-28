<?php
session_start();
require_once 'includes/config.php';

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$user_id = $_SESSION['user_id'];

// Buscar receitas do utilizador
try {
    
    // Query para buscar receitas do utilizador com estatísticas
    $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.title,
            r.slug,
            r.description,
            r.created_at,
            COALESCE(rs.total_views, 0) as views,
            COALESCE(rs.average_rating, 0) as rating,
            COALESCE(rs.total_ratings, 0) as rating_count
        FROM recipes r
        LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    
    $stmt->execute([$user_id]);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $recipes = [];
    error_log("Erro ao buscar receitas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Receitas - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/culinary.css">
    <style>
        /* Header Improvements */
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        
        .logo {
            font-family: 'Lora', serif;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            letter-spacing: -0.5px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .logo::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #c96b3e, #d88a5c);
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        
        .logo:hover::after {
            width: 100%;
        }
        
        [data-theme="dark"] .logo {
            background: linear-gradient(135deg, #ff8c5a, #ffb088);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .main-nav {
            display: flex;
            gap: 35px;
            align-items: center;
        }
        
        .main-nav a {
            color: var(--color-text);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .main-nav a:hover {
            background: rgba(201, 107, 62, 0.1);
            color: var(--color-primary);
            transform: translateY(-2px);
        }
        
        .main-nav a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--color-primary);
            transition: width 0.3s ease;
        }
        
        .main-nav a:hover::before {
            width: 80%;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            padding: 2px;
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(201, 107, 62, 0.3);
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            background: white;
        }
        
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        [data-theme="dark"] .dropdown-menu {
            background: #2d2d2d;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
        .user-menu:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-menu a {
            display: block;
            padding: 12px 20px;
            color: var(--color-text);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
        
        .dropdown-menu a:first-child {
            border-radius: 12px 12px 0 0;
        }
        
        .dropdown-menu a:last-child {
            border-radius: 0 0 12px 12px;
        }
        
        .dropdown-menu a:hover {
            background: rgba(201, 107, 62, 0.1);
            color: var(--color-primary);
            padding-left: 25px;
        }
        
        /* Container Styles */
        .recipes-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .page-header h1 {
            font-size: 2rem;
            color: #2c3e50;
            font-family: 'Lora', serif;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn-add-recipe {
            padding: 12px 25px;
            background: #c96b3e;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-add-recipe:hover {
            background: #b55d33;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(201, 107, 62, 0.3);
        }
        
        .recipe-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .recipe-item {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .recipe-item:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .recipe-icon {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }
        
        .recipe-info {
            flex: 1;
            min-width: 0;
        }
        
        .recipe-title {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .recipe-description {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        
        .recipe-stats {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #777;
            font-size: 0.9rem;
        }
        
        .stat-icon {
            font-size: 1rem;
        }
        
        .recipe-date {
            color: #999;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .recipe-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }
        
        .btn-view {
            padding: 10px 20px;
            background: #f0f0f0;
            color: #555;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.9rem;
            text-align: center;
        }
        
        .btn-view:hover {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-delete {
            padding: 10px 20px;
            background: #ff4444;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.9rem;
            cursor: pointer;
            text-align: center;
        }
        
        .btn-delete:hover {
            background: #cc0000;
        }
        
        .btn-edit {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .btn-edit:hover {
            background: #45a049;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            color: #666;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .summary-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #c96b3e;
            margin-bottom: 5px;
        }
        
        .summary-label {
            color: #666;
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        /* Dark Mode Styles */
        [data-theme="dark"] .recipes-container {
            background: #1a1a1a;
        }
        
        [data-theme="dark"] .page-header h1,
        [data-theme="dark"] .recipe-title {
            color: #e8e8e8;
        }
        
        [data-theme="dark"] .page-header {
            border-bottom-color: #333;
        }
        
        [data-theme="dark"] .summary-card,
        [data-theme="dark"] .recipe-item {
            background: #2d2d2d;
        }
        
        [data-theme="dark"] .summary-label,
        [data-theme="dark"] .recipe-description,
        [data-theme="dark"] .stat-item,
        [data-theme="dark"] .recipe-date {
            color: #b0b0b0;
        }
        
        [data-theme="dark"] .btn-view {
            background: #3a3a3a;
            color: #e8e8e8;
        }
        
        [data-theme="dark"] .btn-view:hover {
            background: #4a4a4a;
        }
        
        [data-theme="dark"] .empty-state h3 {
            color: #b0b0b0;
        }
        
        [data-theme="dark"] .empty-state p {
            color: #808080;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">ChefGuedes</a>
                
                <nav class="main-nav">
                    <a href="explorar.php">Explorar</a>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="grupos_familiares.php">Grupos Familiares</a>
                </nav>
                
                <div class="header-actions">
                    <div class="theme-switcher">
                        <button class="theme-btn" data-theme="light" title="Modo Claro">
                            <span class="theme-icon">☀️</span>
                        </button>
                        <button class="theme-btn" data-theme="dark" title="Modo Escuro">
                            <span class="theme-icon">🌙</span>
                        </button>
                    </div>
                    
                    <?php if ($user): ?>
                        <div class="user-menu">
                            <span class="user-avatar">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                <?php endif; ?>
                            </span>
                            <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                            <div class="dropdown-menu">
                                <a href="profile.php">Meu Perfil</a>
                                <a href="minhas_receitas.php">Minhas Receitas</a>
                                <a href="logout.php">Sair</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="recipes-container">
        <!-- Mensagens de sucesso/erro -->
        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #4CAF50; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #ff4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Suas Receitas Recentes</h1>
            <div class="header-actions">
                <a href="receitas.php" class="btn-add-recipe">
                    ➕ Nova Receita
                </a>
            </div>
        </div>

        <?php if (!empty($recipes)): ?>
            <!-- Stats Summary -->
            <div class="stats-summary">
                <div class="summary-card">
                    <div class="summary-value"><?php echo count($recipes); ?></div>
                    <div class="summary-label">Receitas Criadas</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value"><?php echo array_sum(array_column($recipes, 'views')); ?></div>
                    <div class="summary-label">Visualizações Totais</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">
                        <?php 
                        $totalRatings = array_sum(array_column($recipes, 'rating_count'));
                        echo $totalRatings;
                        ?>
                    </div>
                    <div class="summary-label">Avaliações Recebidas</div>
                </div>
            </div>

            <!-- Recipe List -->
            <div class="recipe-list">
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-item">
                        <div class="recipe-icon">
                            <?php echo strtoupper(substr($recipe['title'], 0, 1)); ?>
                        </div>
                        
                        <div class="recipe-info">
                            <div class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></div>
                            <div class="recipe-description">
                                <?php 
                                echo htmlspecialchars($recipe['description'] ?: 'Sem descrição'); 
                                ?>
                            </div>
                            <div class="recipe-stats">
                                <div class="stat-item">
                                    <span class="stat-icon">👁️</span>
                                    <span><?php echo $recipe['views']; ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-icon">⭐</span>
                                    <span><?php echo number_format($recipe['rating'], 1); ?></span>
                                </div>
                                <div class="recipe-date">
                                    <span>📅</span>
                                    <?php echo date('d/m/Y', strtotime($recipe['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="recipe-actions">
                            <a href="receita.php?slug=<?php echo htmlspecialchars($recipe['slug']); ?>" class="btn-view">
                                Ver Receita
                            </a>
                            <button onclick="deleteRecipe(<?php echo $recipe['id']; ?>, '<?php echo htmlspecialchars($recipe['title'], ENT_QUOTES); ?>')" class="btn-delete">
                                Apagar Receita
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">📝</div>
                <h3>Ainda não criou nenhuma receita</h3>
                <p>Compartilhe suas receitas favoritas com a comunidade!</p>
                <a href="receitas.php" class="btn-add-recipe">
                    Criar Primeira Receita
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="js/theme.js"></script>
    <script>
        function deleteRecipe(recipeId, recipeTitle) {
            if (confirm('Tem certeza que deseja apagar a receita "' + recipeTitle + '"?\n\nEsta ação não pode ser desfeita!')) {
                // Criar formulário para enviar via POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'api/delete_recipe.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'recipe_id';
                input.value = recipeId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
