<?php
/**
 * API para processar avaliações de receitas
 * Endpoint: POST /api/rate_recipe.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Iniciar sessão
session_start();

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
    exit;
}

// Verificar se usuário está logado
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não autenticado'
    ]);
    exit;
}

// Incluir arquivo de conexão com banco
require_once '../includes/db.php';

try {
    // Ler dados JSON da requisição
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Validar dados recebidos
    if (!isset($data['recipe_id']) || !isset($data['rating'])) {
        throw new Exception('Dados incompletos');
    }
    
    $recipeId = (int)$data['recipe_id'];
    $rating = (int)$data['rating'];
    $username = $_SESSION['username'];
    
    // Validar rating (0-5)
    if ($rating < 0 || $rating > 5) {
        throw new Exception('Avaliação deve estar entre 0 e 5');
    }
    
    // Conectar ao banco
    $pdo = connectDB();
    
    // Buscar ID do usuário
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('Usuário não encontrado');
    }
    
    $userId = $user['id'];
    
    // Verificar se a receita existe
    $stmt = $pdo->prepare("SELECT id FROM recipes WHERE id = ?");
    $stmt->execute([$recipeId]);
    if (!$stmt->fetch()) {
        throw new Exception('Receita não encontrada');
    }
    
    // Verificar se usuário já avaliou esta receita
    $stmt = $pdo->prepare("SELECT id FROM ratings WHERE recipe_id = ? AND user_id = ?");
    $stmt->execute([$recipeId, $userId]);
    $existingRating = $stmt->fetch();
    
    if ($existingRating) {
        // Atualizar avaliação existente
        $stmt = $pdo->prepare("
            UPDATE ratings 
            SET score = ?, created_at = CURRENT_TIMESTAMP 
            WHERE recipe_id = ? AND user_id = ?
        ");
        $stmt->execute([$rating, $recipeId, $userId]);
        $action = 'updated';
    } else {
        // Inserir nova avaliação
        $stmt = $pdo->prepare("
            INSERT INTO ratings (recipe_id, user_id, score, created_at) 
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([$recipeId, $userId, $rating]);
        $action = 'created';
    }
    
    // Calcular nova média e total de avaliações
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(AVG(score), 0) as avg_rating,
            COUNT(*) as total_ratings
        FROM ratings 
        WHERE recipe_id = ?
    ");
    $stmt->execute([$recipeId]);
    $stats = $stmt->fetch();
    
    // Atualizar tabela de estatísticas
    $stmt = $pdo->prepare("
        REPLACE INTO recipe_stats (recipe_id, avg_rating, rating_count, made_count)
        SELECT 
            r.id,
            COALESCE(AVG(rt.score), 0),
            COUNT(rt.id),
            COALESCE((SELECT COUNT(*) FROM recipe_mades m WHERE m.recipe_id = r.id), 0)
        FROM recipes r
        LEFT JOIN ratings rt ON rt.recipe_id = r.id
        WHERE r.id = ?
        GROUP BY r.id
    ");
    $stmt->execute([$recipeId]);
    
    // Log da ação (opcional)
    error_log("Rating {$action}: User {$username} rated recipe {$recipeId} with {$rating} stars");
    
    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => $action === 'updated' ? 'Avaliação atualizada com sucesso!' : 'Avaliação registrada com sucesso!',
        'data' => [
            'recipe_id' => $recipeId,
            'user_rating' => $rating,
            'new_average' => round($stats['avg_rating'], 1),
            'total_ratings' => $stats['total_ratings'],
            'action' => $action
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in rate_recipe.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>