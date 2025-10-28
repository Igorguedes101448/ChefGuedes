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
$task_name = trim($_POST['task_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
$due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

// Validar
if (!$group_id || empty($task_name)) {
    $_SESSION['error'] = 'O nome da tarefa é obrigatório.';
    header('Location: ../grupo.php?id=' . $group_id);
    exit;
}

try {
    // Verificar se o utilizador tem permissão
    $stmt = $pdo->prepare("
        SELECT can_assign_tasks 
        FROM family_members 
        WHERE group_id = ? AND user_id = ?
    ");
    $stmt->execute([$group_id, $user_id]);
    $member = $stmt->fetch();
    
    if (!$member || !$member['can_assign_tasks']) {
        $_SESSION['error'] = 'Você não tem permissão para criar tarefas.';
        header('Location: ../grupo.php?id=' . $group_id);
        exit;
    }
    
    // Criar tarefa
    $stmt = $pdo->prepare("
        INSERT INTO family_tasks (group_id, task_name, description, assigned_to, assigned_by, due_date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$group_id, $task_name, $description, $assigned_to, $user_id, $due_date]);
    
    $_SESSION['success'] = 'Tarefa criada com sucesso!';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erro ao criar tarefa: ' . $e->getMessage();
}

header('Location: ../grupo.php?id=' . $group_id);
exit;
?>
