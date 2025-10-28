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
$username = trim($_POST['username'] ?? '');

if (!$group_id || empty($username)) {
    $_SESSION['error'] = 'Dados inválidos.';
    header('Location: ../grupo.php?id=' . $group_id);
    exit;
}

try {
    // Verificar se o utilizador é membro do grupo e tem permissão
    $stmt = $pdo->prepare("
        SELECT fg.*, fm.role 
        FROM family_groups fg
        INNER JOIN family_members fm ON fg.id = fm.group_id
        WHERE fg.id = ? AND fm.user_id = ? AND fm.role IN ('owner', 'admin')
    ");
    $stmt->execute([$group_id, $user_id]);
    $group = $stmt->fetch();
    
    if (!$group) {
        $_SESSION['error'] = 'Você não tem permissão para convidar membros.';
        header('Location: ../grupo.php?id=' . $group_id);
        exit;
    }
    
    // Buscar utilizador a convidar
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $invitee = $stmt->fetch();
    
    if (!$invitee) {
        $_SESSION['error'] = 'Utilizador não encontrado.';
        header('Location: ../grupo.php?id=' . $group_id);
        exit;
    }
    
    $invitee_id = $invitee['id'];
    
    // Não pode convidar a si próprio
    if ($invitee_id == $user_id) {
        $_SESSION['error'] = 'Você já é membro do grupo.';
        header('Location: ../grupo.php?id=' . $group_id);
        exit;
    }
    
    // Verificar se já é membro
    $stmt = $pdo->prepare("
        SELECT id FROM family_members 
        WHERE group_id = ? AND user_id = ?
    ");
    $stmt->execute([$group_id, $invitee_id]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Este utilizador já é membro do grupo.';
        header('Location: ../grupo.php?id=' . $group_id);
        exit;
    }
    
    // Verificar se já existe convite pendente
    $stmt = $pdo->prepare("
        SELECT id FROM family_invites 
        WHERE group_id = ? AND invitee_id = ? AND status = 'pending'
    ");
    $stmt->execute([$group_id, $invitee_id]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Já existe um convite pendente para este utilizador.';
        header('Location: ../grupo.php?id=' . $group_id);
        exit;
    }
    
    // Criar convite
    $stmt = $pdo->prepare("
        INSERT INTO family_invites (group_id, inviter_id, invitee_id)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$group_id, $user_id, $invitee_id]);
    
    $_SESSION['success'] = 'Convite enviado com sucesso!';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erro ao enviar convite: ' . $e->getMessage();
}

header('Location: ../grupo.php?id=' . $group_id);
exit;
?>
