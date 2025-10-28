<?php
session_start();
require_once '../includes/config.php';

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Você precisa estar logado.';
    header('Location: ../login.php');
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método inválido.';
    header('Location: ../grupos_familiares.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

// Validar
if (empty($name)) {
    $_SESSION['error'] = 'O nome do grupo é obrigatório.';
    header('Location: ../grupos_familiares.php');
    exit;
}

if (strlen($name) > 100) {
    $_SESSION['error'] = 'O nome do grupo não pode ter mais de 100 caracteres.';
    header('Location: ../grupos_familiares.php');
    exit;
}

try {
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Criar grupo
    $stmt = $pdo->prepare("
        INSERT INTO family_groups (name, description, owner_id)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$name, $description, $user_id]);
    $group_id = $pdo->lastInsertId();
    
    // Adicionar o criador como membro owner
    $stmt = $pdo->prepare("
        INSERT INTO family_members (group_id, user_id, role, can_add_recipes, can_assign_tasks)
        VALUES (?, ?, 'owner', 1, 1)
    ");
    $stmt->execute([$group_id, $user_id]);
    
    // Commit
    $pdo->commit();
    
    $_SESSION['success'] = 'Grupo criado com sucesso!';
    header('Location: ../grupo.php?id=' . $group_id);
    exit;
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = 'Erro ao criar grupo: ' . $e->getMessage();
    header('Location: ../grupos_familiares.php');
    exit;
}
?>
