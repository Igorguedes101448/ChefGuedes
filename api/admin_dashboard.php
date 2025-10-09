<?php
// api/admin_dashboard.php - API para dashboard administrativo

require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verificar se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'overview':
            echo json_encode(getSystemOverview());
            break;
        case 'recipe_analytics':
            echo json_encode(getRecipeAnalytics());
            break;
        case 'user_analytics':
            echo json_encode(getUserAnalytics());
            break;
        case 'search_analytics':
            echo json_encode(getSearchAnalytics());
            break;
        case 'popular_content':
            echo json_encode(getPopularContent());
            break;
        default:
            echo json_encode(['error' => 'Ação inválida']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getSystemOverview() {
    global $pdo;
    
    // Estatísticas gerais
    $stats = [];
    
    // Totais principais
    $stmt = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_users,
            (SELECT COUNT(*) FROM recipes WHERE is_active = 1) as total_recipes,
            (SELECT COUNT(*) FROM recipe_views WHERE viewed_at >= CURDATE()) as views_today,
            (SELECT COUNT(*) FROM search_history WHERE created_at >= CURDATE()) as searches_today,
            (SELECT COUNT(*) FROM recipe_favorites WHERE created_at >= CURDATE()) as favorites_today,
            (SELECT AVG(rating) FROM recipe_ratings) as avg_rating
    ");
    $totals = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Crescimento nos últimos 30 dias
    $stmt = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_users_30d,
            (SELECT COUNT(*) FROM recipes WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_recipes_30d,
            (SELECT COUNT(*) FROM recipe_views WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as views_30d
    ");
    $growth = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Top receitas por visualizações
    $stmt = $pdo->query("
        SELECT r.title, rs.total_views, r.created_at
        FROM recipes r
        JOIN recipe_stats rs ON r.id = rs.recipe_id
        ORDER BY rs.total_views DESC
        LIMIT 5
    ");
    $top_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'totals' => $totals,
        'growth' => $growth,
        'top_recipes' => $top_recipes,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

function getRecipeAnalytics() {
    global $pdo;
    
    // Receitas por categoria
    $stmt = $pdo->query("
        SELECT c.name, COUNT(r.id) as count
        FROM categories c
        LEFT JOIN recipes r ON c.id = r.category_id AND r.is_active = 1
        GROUP BY c.id, c.name
        ORDER BY count DESC
    ");
    $by_category = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Receitas por dificuldade
    $stmt = $pdo->query("
        SELECT difficulty, COUNT(*) as count
        FROM recipes
        WHERE is_active = 1
        GROUP BY difficulty
    ");
    $by_difficulty = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Estatísticas de tempo de preparo
    $stmt = $pdo->query("
        SELECT 
            AVG(prep_time) as avg_prep_time,
            MIN(prep_time) as min_prep_time,
            MAX(prep_time) as max_prep_time,
            AVG(total_time) as avg_total_time
        FROM recipes 
        WHERE is_active = 1 AND prep_time IS NOT NULL
    ");
    $time_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Receitas mais avaliadas
    $stmt = $pdo->query("
        SELECT r.title, rs.total_ratings, rs.average_rating
        FROM recipes r
        JOIN recipe_stats rs ON r.id = rs.recipe_id
        WHERE rs.total_ratings > 0
        ORDER BY rs.total_ratings DESC
        LIMIT 10
    ");
    $most_rated = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'by_category' => $by_category,
        'by_difficulty' => $by_difficulty,
        'time_stats' => $time_stats,
        'most_rated' => $most_rated
    ];
}

function getUserAnalytics() {
    global $pdo;
    
    // Usuários ativos por mês
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(last_login, '%Y-%m') as month,
            COUNT(*) as active_users
        FROM users 
        WHERE last_login >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY month
        ORDER BY month
    ");
    $monthly_active = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Novos registros por mês
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as new_users
        FROM users 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY month
        ORDER BY month
    ");
    $monthly_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Usuários mais ativos
    $stmt = $pdo->query("
        SELECT 
            u.username,
            COUNT(DISTINCT rf.id) as favorites_count,
            COUNT(DISTINCT rm.id) as made_count,
            COUNT(DISTINCT rr.id) as ratings_count
        FROM users u
        LEFT JOIN recipe_favorites rf ON u.id = rf.user_id
        LEFT JOIN recipe_made rm ON u.id = rm.user_id
        LEFT JOIN recipe_ratings rr ON u.id = rr.user_id
        GROUP BY u.id, u.username
        HAVING (favorites_count + made_count + ratings_count) > 0
        ORDER BY (favorites_count + made_count + ratings_count) DESC
        LIMIT 10
    ");
    $most_active = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'monthly_active' => $monthly_active,
        'monthly_registrations' => $monthly_registrations,
        'most_active' => $most_active
    ];
}

function getSearchAnalytics() {
    global $pdo;
    
    // Pesquisas mais populares (últimos 30 dias)
    $stmt = $pdo->query("
        SELECT search_query, COUNT(*) as search_count
        FROM search_history
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY search_query
        ORDER BY search_count DESC
        LIMIT 20
    ");
    $popular_searches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pesquisas por tipo
    $stmt = $pdo->query("
        SELECT search_type, COUNT(*) as count
        FROM search_history
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY search_type
    ");
    $by_type = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pesquisas por dia (últimos 30 dias)
    $stmt = $pdo->query("
        SELECT 
            DATE(created_at) as search_date,
            COUNT(*) as search_count
        FROM search_history
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY search_date
        ORDER BY search_date
    ");
    $daily_searches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Taxa de cliques (CTR)
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_searches,
            COUNT(clicked_recipe_id) as searches_with_clicks,
            ROUND((COUNT(clicked_recipe_id) / COUNT(*)) * 100, 2) as ctr_percentage
        FROM search_history
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $ctr_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'popular_searches' => $popular_searches,
        'by_type' => $by_type,
        'daily_searches' => $daily_searches,
        'ctr_stats' => $ctr_stats
    ];
}

function getPopularContent() {
    global $pdo;
    
    // Receitas mais visualizadas
    $stmt = $pdo->query("
        SELECT r.title, rs.total_views, rs.total_favorites, rs.average_rating
        FROM recipes r
        JOIN recipe_stats rs ON r.id = rs.recipe_id
        ORDER BY rs.total_views DESC
        LIMIT 10
    ");
    $most_viewed = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Receitas mais favoritadas
    $stmt = $pdo->query("
        SELECT r.title, rs.total_favorites, rs.total_views, rs.average_rating
        FROM recipes r
        JOIN recipe_stats rs ON r.id = rs.recipe_id
        ORDER BY rs.total_favorites DESC
        LIMIT 10
    ");
    $most_favorited = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Receitas mais bem avaliadas
    $stmt = $pdo->query("
        SELECT r.title, rs.average_rating, rs.total_ratings, rs.total_views
        FROM recipes r
        JOIN recipe_stats rs ON r.id = rs.recipe_id
        WHERE rs.total_ratings >= 3
        ORDER BY rs.average_rating DESC, rs.total_ratings DESC
        LIMIT 10
    ");
    $best_rated = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Receitas mais feitas
    $stmt = $pdo->query("
        SELECT r.title, rs.total_made, rs.total_views, rs.average_rating
        FROM recipes r
        JOIN recipe_stats rs ON r.id = rs.recipe_id
        ORDER BY rs.total_made DESC
        LIMIT 10
    ");
    $most_made = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'most_viewed' => $most_viewed,
        'most_favorited' => $most_favorited,
        'best_rated' => $best_rated,
        'most_made' => $most_made
    ];
}
?>