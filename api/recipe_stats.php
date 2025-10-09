<?php
// api/recipe_stats.php - API para estatísticas de receitas

require_once '../includes/config.php';
require_once '../includes/functions.php';

// Headers para API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['recipe_id'])) {
                echo json_encode(getRecipeStats($_GET['recipe_id']));
            } elseif (isset($_GET['popular'])) {
                echo json_encode(getPopularRecipes($_GET['limit'] ?? 10));
            } elseif (isset($_GET['trending'])) {
                echo json_encode(getTrendingSearches($_GET['limit'] ?? 10));
            } else {
                echo json_encode(['error' => 'Parâmetro inválido']);
            }
            break;
            
        case 'POST':
            if (isset($input['action'])) {
                switch ($input['action']) {
                    case 'view':
                        echo json_encode(recordView($input));
                        break;
                    case 'favorite':
                        echo json_encode(toggleFavorite($input));
                        break;
                    case 'made':
                        echo json_encode(recordMade($input));
                        break;
                    case 'search':
                        echo json_encode(recordSearch($input));
                        break;
                    default:
                        echo json_encode(['error' => 'Ação inválida']);
                }
            }
            break;
            
        default:
            echo json_encode(['error' => 'Método não permitido']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getRecipeStats($recipe_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT 
            rs.*,
            r.title,
            r.created_at as recipe_created,
            (SELECT COUNT(*) FROM recipe_ratings WHERE recipe_id = r.id) as rating_count
        FROM recipe_stats rs
        JOIN recipes r ON rs.recipe_id = r.id
        WHERE rs.recipe_id = ?
    ");
    $stmt->execute([$recipe_id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['error' => 'Receita não encontrada'];
}

function getPopularRecipes($limit = 10) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM popular_recipes
        WHERE published_at IS NOT NULL
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTrendingSearches($limit = 10) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM trending_searches LIMIT ?");
    $stmt->execute([$limit]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function recordView($data) {
    global $pdo;
    
    $recipe_id = $data['recipe_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $referrer = $_SERVER['HTTP_REFERER'] ?? null;
    
    if (!$recipe_id) {
        return ['error' => 'recipe_id obrigatório'];
    }
    
    // Registrar visualização
    $stmt = $pdo->prepare("
        INSERT INTO recipe_views (recipe_id, user_id, ip_address, user_agent, referrer)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$recipe_id, $user_id, $ip_address, $user_agent, $referrer]);
    
    // Atualizar estatísticas
    updateRecipeStats($recipe_id);
    
    return ['success' => true, 'message' => 'Visualização registrada'];
}

function toggleFavorite($data) {
    global $pdo;
    
    $recipe_id = $data['recipe_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    
    if (!$recipe_id || !$user_id) {
        return ['error' => 'Dados insuficientes'];
    }
    
    // Verificar se já é favorito
    $stmt = $pdo->prepare("
        SELECT id FROM recipe_favorites 
        WHERE recipe_id = ? AND user_id = ?
    ");
    $stmt->execute([$recipe_id, $user_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Remover dos favoritos
        $stmt = $pdo->prepare("
            DELETE FROM recipe_favorites 
            WHERE recipe_id = ? AND user_id = ?
        ");
        $stmt->execute([$recipe_id, $user_id]);
        $action = 'removed';
    } else {
        // Adicionar aos favoritos
        $stmt = $pdo->prepare("
            INSERT INTO recipe_favorites (recipe_id, user_id)
            VALUES (?, ?)
        ");
        $stmt->execute([$recipe_id, $user_id]);
        $action = 'added';
    }
    
    // Atualizar estatísticas
    updateRecipeStats($recipe_id);
    
    return ['success' => true, 'action' => $action];
}

function recordMade($data) {
    global $pdo;
    
    $recipe_id = $data['recipe_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    $notes = $data['notes'] ?? null;
    $difficulty_rating = $data['difficulty_rating'] ?? null;
    $would_make_again = $data['would_make_again'] ?? null;
    
    if (!$recipe_id || !$user_id) {
        return ['error' => 'Dados insuficientes'];
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO recipe_made (recipe_id, user_id, notes, difficulty_rating, would_make_again)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$recipe_id, $user_id, $notes, $difficulty_rating, $would_make_again]);
    
    // Atualizar estatísticas
    updateRecipeStats($recipe_id);
    
    return ['success' => true, 'message' => 'Receita marcada como feita'];
}

function recordSearch($data) {
    global $pdo;
    
    $search_query = $data['query'] ?? null;
    $search_type = $data['type'] ?? 'text';
    $results_count = $data['results_count'] ?? 0;
    $clicked_recipe_id = $data['clicked_recipe_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    if (!$search_query) {
        return ['error' => 'Query obrigatória'];
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO search_history (user_id, search_query, search_type, results_count, clicked_recipe_id, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $search_query, $search_type, $results_count, $clicked_recipe_id, $ip_address, $user_agent]);
    
    // Atualizar contador de pesquisas
    updateSystemStats('total_searches');
    
    return ['success' => true, 'message' => 'Pesquisa registrada'];
}

function updateRecipeStats($recipe_id) {
    global $pdo;
    
    // Calcular estatísticas
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT rv.id) as total_views,
            COUNT(DISTINCT CONCAT(rv.ip_address, rv.user_id)) as unique_views,
            COUNT(DISTINCT rf.id) as total_favorites,
            COUNT(DISTINCT rm.id) as total_made,
            COUNT(DISTINCT rr.id) as total_ratings,
            COALESCE(AVG(rr.rating), 0) as average_rating
        FROM recipes r
        LEFT JOIN recipe_views rv ON r.id = rv.recipe_id
        LEFT JOIN recipe_favorites rf ON r.id = rf.recipe_id
        LEFT JOIN recipe_made rm ON r.id = rm.recipe_id
        LEFT JOIN recipe_ratings rr ON r.id = rr.recipe_id
        WHERE r.id = ?
        GROUP BY r.id
    ");
    $stmt->execute([$recipe_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($stats) {
        // Calcular score de popularidade
        $popularity_score = calculatePopularityScore($stats);
        
        // Atualizar ou inserir estatísticas
        $stmt = $pdo->prepare("
            INSERT INTO recipe_stats (
                recipe_id, total_views, unique_views, total_favorites, 
                total_made, total_ratings, average_rating, popularity_score
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                total_views = VALUES(total_views),
                unique_views = VALUES(unique_views),
                total_favorites = VALUES(total_favorites),
                total_made = VALUES(total_made),
                total_ratings = VALUES(total_ratings),
                average_rating = VALUES(average_rating),
                popularity_score = VALUES(popularity_score)
        ");
        
        $stmt->execute([
            $recipe_id,
            $stats['total_views'],
            $stats['unique_views'],
            $stats['total_favorites'],
            $stats['total_made'],
            $stats['total_ratings'],
            $stats['average_rating'],
            $popularity_score
        ]);
    }
}

function calculatePopularityScore($stats) {
    // Algoritmo de popularidade baseado em múltiplos fatores
    $views_weight = 1.0;
    $favorites_weight = 3.0;
    $ratings_weight = 2.0;
    $made_weight = 4.0;
    $rating_quality_weight = 1.5;
    
    $score = 
        ($stats['total_views'] * $views_weight) +
        ($stats['total_favorites'] * $favorites_weight) +
        ($stats['total_ratings'] * $ratings_weight) +
        ($stats['total_made'] * $made_weight) +
        ($stats['average_rating'] * $rating_quality_weight);
    
    return round($score, 4);
}

function updateSystemStats($key, $increment = 1) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO system_stats (stat_key, stat_value, stat_type)
        VALUES (?, ?, 'counter')
        ON DUPLICATE KEY UPDATE
            stat_value = CAST(stat_value AS UNSIGNED) + ?
    ");
    $stmt->execute([$key, $increment, $increment]);
}
?>