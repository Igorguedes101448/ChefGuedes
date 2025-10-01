<?php
/**
 * API para buscar avaliação do usuário para uma receita específica
 * Endpoint: GET /api/get_user_rating.php?recipe_id=X
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Iniciar sessão
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['username'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não autenticado',
        'user_rating' => 0
    ]);
    exit;
}

// Verificar se recipe_id foi fornecido
if (!isset($_GET['recipe_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID da receita não fornecido'
    ]);
    exit;
}

$recipeId = (int)$_GET['recipe_id'];
$username = $_SESSION['username'];

// Incluir arquivo de conexão com banco
require_once '../includes/db.php';

try {
    $pdo = connectDB();
    
    // Buscar ID do usuário
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('Usuário não encontrado');
    }
    
    $userId = $user['id'];
    
    // Buscar avaliação do usuário para esta receita
    $stmt = $pdo->prepare("
        SELECT score, created_at 
        FROM ratings 
        WHERE recipe_id = ? AND user_id = ?
    ");
    $stmt->execute([$recipeId, $userId]);
    $rating = $stmt->fetch();
    
    if ($rating) {
        echo json_encode([
            'success' => true,
            'user_rating' => (int)$rating['score'],
            'rated_at' => $rating['created_at'],
            'has_rated' => true
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'user_rating' => 0,
            'has_rated' => false
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar avaliação',
        'user_rating' => 0
    ]);
}
?>