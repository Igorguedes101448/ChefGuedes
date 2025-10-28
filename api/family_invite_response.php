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
$invite_id = (int)($_POST['invite_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$invite_id || !in_array($action, ['accept', 'decline'])) {
    $_SESSION['error'] = 'Dados inválidos.';
    header('Location: ../grupos_familiares.php');
    exit;
}

try {
    // Verificar se o convite existe e pertence ao utilizador
    $stmt = $pdo->prepare("
        SELECT * FROM family_invites 
        WHERE id = ? AND invitee_id = ? AND status = 'pending'
    ");
    $stmt->execute([$invite_id, $user_id]);
    $invite = $stmt->fetch();
    
    if (!$invite) {
        $_SESSION['error'] = 'Convite não encontrado ou já foi respondido.';
        header('Location: ../grupos_familiares.php');
        exit;
    }
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    if ($action === 'accept') {
        // Verificar se já é membro
        $stmt = $pdo->prepare("
            SELECT id FROM family_members 
            WHERE group_id = ? AND user_id = ?
        ");
        $stmt->execute([$invite['group_id'], $user_id]);
        
        if (!$stmt->fetch()) {
            // Adicionar como membro
            $stmt = $pdo->prepare("
                INSERT INTO family_members (group_id, user_id, role, can_add_recipes, can_assign_tasks)
                VALUES (?, ?, 'member', 1, 1)
            ");
            $stmt->execute([$invite['group_id'], $user_id]);
        }
        
        // Atualizar convite
        $stmt = $pdo->prepare("
            UPDATE family_invites 
            SET status = 'accepted', responded_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$invite_id]);
        
        $_SESSION['success'] = 'Convite aceite! Bem-vindo ao grupo.';
        
    } else {
        // Recusar convite
        $stmt = $pdo->prepare("
            UPDATE family_invites 
            SET status = 'declined', responded_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$invite_id]);
        
        $_SESSION['success'] = 'Convite recusado.';
    }
    
    // Commit
    $pdo->commit();
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = 'Erro ao processar convite: ' . $e->getMessage();
}

header('Location: ../grupos_familiares.php');
exit;
?>
