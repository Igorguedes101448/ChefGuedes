<?php
session_start();
require_once '../includes/config.php';

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Você precisa estar logado.';
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método inválido.';
    header('Location: ../grupos_familiares.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = (int)($_POST['group_id'] ?? 0);
$recipe_name = trim($_POST['recipe_name'] ?? '');
$meal_date = $_POST['meal_date'] ?? '';
$meal_type = $_POST['meal_type'] ?? '';
$assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
$notes = trim($_POST['notes'] ?? '');

// Validar
if (!$group_id || empty($recipe_name) || empty($meal_date) || empty($meal_type)) {
    $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos.';
    header('Location: ../grupo.php?id=' . $group_id);
    exit;
}

if (!in_array($meal_type, ['breakfast', 'lunch', 'dinner', 'snack'])) {
    $_SESSION['error'] = 'Tipo de refeição inválido.';
    header('Location: ../grupo.php?id=' . $group_id);
    exit;
}

try {
    // Verificar se o utilizador tem permissão
    $stmt = $pdo->prepare("
        SELECT can_add_recipes 
        FROM family_members 
        WHERE group_id = ? AND user_id = ?
    ");
    $stmt->execute([$group_id, $user_id]);
    $member = $stmt->fetch();
    
    if (!$member || !$member['can_add_recipes']) {
        $_SESSION['error'] = 'Você não tem permissão para adicionar receitas.';
        header('Location: ../grupo.php?id=' . $group_id);
        exit;
    }
    
    // Verificar se já existe receita para essa data/tipo
    $stmt = $pdo->prepare("
        SELECT id FROM family_recipes 
        WHERE group_id = ? AND meal_date = ? AND meal_type = ?
    ");
    $stmt->execute([$group_id, $meal_date, $meal_type]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Já existe uma receita planeada para esta refeição.';
        header('Location: ../grupo.php?id=' . $group_id);
        exit;
    }
    
    // Adicionar receita
    $stmt = $pdo->prepare("
        INSERT INTO family_recipes (group_id, recipe_name, meal_date, meal_type, assigned_to, added_by, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$group_id, $recipe_name, $meal_date, $meal_type, $assigned_to, $user_id, $notes]);
    
    $_SESSION['success'] = 'Receita adicionada ao planeamento!';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erro ao adicionar receita: ' . $e->getMessage();
}

header('Location: ../grupo.php?id=' . $group_id);
exit;
?>
