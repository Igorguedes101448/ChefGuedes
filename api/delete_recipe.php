<?php
session_start();
require_once '../includes/config.php';

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Verificar se foi enviado o ID da receita
if (!isset($_POST['recipe_id']) || empty($_POST['recipe_id'])) {
    $_SESSION['error'] = 'ID da receita não fornecido.';
    header('Location: ../minhas_receitas.php');
    exit;
}

$recipe_id = (int)$_POST['recipe_id'];
$user_id = $_SESSION['user_id'];

try {
    // Verificar se a receita pertence ao utilizador
    $stmt = $pdo->prepare("SELECT id, user_id FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch();
    
    if (!$recipe) {
        $_SESSION['error'] = 'Receita não encontrada.';
        header('Location: ../minhas_receitas.php');
        exit;
    }
    
    // Verificar se a receita pertence ao utilizador (segurança)
    if ($recipe['user_id'] != $user_id) {
        $_SESSION['error'] = 'Você não tem permissão para apagar esta receita.';
        header('Location: ../minhas_receitas.php');
        exit;
    }
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Apagar dados relacionados
    // 1. Apagar tags da receita
    $stmt = $pdo->prepare("DELETE FROM recipe_tags WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    
    // 2. Apagar categorias da receita
    $stmt = $pdo->prepare("DELETE FROM recipe_categories WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    
    // 3. Apagar avaliações
    $stmt = $pdo->prepare("DELETE FROM recipe_ratings WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    
    // 4. Apagar favoritos
    $stmt = $pdo->prepare("DELETE FROM recipe_favorites WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    
    // 5. Apagar estatísticas
    $stmt = $pdo->prepare("DELETE FROM recipe_stats WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    
    // 6. Apagar comentários (se existir a tabela)
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'recipe_comments'");
    $stmt->execute();
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM recipe_comments WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
    }
    
    // 7. Apagar a receita
    $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    
    // Commit da transação
    $pdo->commit();
    
    $_SESSION['success'] = 'Receita apagada com sucesso!';
    
} catch (Exception $e) {
    // Rollback em caso de erro
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = 'Erro ao apagar receita: ' . $e->getMessage();
}

// Redirecionar de volta para a página de receitas
header('Location: ../minhas_receitas.php');
exit;
?>
