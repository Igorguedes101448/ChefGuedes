<?php
session_start();
require_once 'includes/config.php';

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$user_id = $_SESSION['user_id'];

// Buscar grupos do utilizador
try {
    $stmt = $pdo->prepare("
        SELECT 
            fg.*,
            fm.role,
            fm.can_add_recipes,
            fm.can_assign_tasks,
            u.username as owner_name,
            (SELECT COUNT(*) FROM family_members WHERE group_id = fg.id) as member_count
        FROM family_groups fg
        INNER JOIN family_members fm ON fg.id = fm.group_id
        INNER JOIN users u ON fg.owner_id = u.id
        WHERE fm.user_id = ?
        ORDER BY fg.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar convites pendentes
    $stmt = $pdo->prepare("
        SELECT 
            fi.*,
            fg.name as group_name,
            u.username as inviter_name
        FROM family_invites fi
        INNER JOIN family_groups fg ON fi.group_id = fg.id
        INNER JOIN users u ON fi.inviter_id = u.id
        WHERE fi.invitee_id = ? AND fi.status = 'pending'
        ORDER BY fi.invited_at DESC
    ");
    $stmt->execute([$user_id]);
    $pending_invites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $groups = [];
    $pending_invites = [];
    error_log("Erro ao buscar grupos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos Familiares - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/culinary.css">
    <style>
        .family-container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            padding: 40px;
            background: linear-gradient(135deg, rgba(201, 107, 62, 0.05), rgba(216, 138, 92, 0.05));
            border-radius: 20px;
            border: 2px solid rgba(201, 107, 62, 0.15);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(201, 107, 62, 0.1), transparent);
            border-radius: 50%;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: 'Lora', serif;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        [data-theme="dark"] .page-header h1 {
            background: linear-gradient(135deg, #ff8c5a, #ffb088);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-create-group {
            padding: 16px 35px;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 20px rgba(201, 107, 62, 0.3);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        .btn-create-group::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-create-group:hover::before {
            left: 100%;
        }
        
        .btn-create-group:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 30px rgba(201, 107, 62, 0.5);
        }
        
        .btn-create-group:active {
            transform: translateY(-1px);
        }
        
        /* Invites Section */
        .invites-section {
            background: linear-gradient(135deg, #fff9f5 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 35px;
            margin-bottom: 45px;
            border-left: 5px solid #c96b3e;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            position: relative;
        }
        
        .invites-section::after {
            content: '📬';
            position: absolute;
            top: 30px;
            right: 30px;
            font-size: 3rem;
            opacity: 0.1;
        }
        
        [data-theme="dark"] .invites-section {
            background: linear-gradient(135deg, #2d2d2d 0%, #252525 100%);
            border-left-color: #ff8c5a;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .invites-section h2 {
            font-size: 1.6rem;
            color: var(--color-text);
            margin-bottom: 25px;
            font-family: 'Lora', serif;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .invites-section h2::before {
            content: '';
            width: 4px;
            height: 28px;
            background: linear-gradient(180deg, #c96b3e, #d88a5c);
            border-radius: 2px;
        }
        
        .invite-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .invite-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #c96b3e, #d88a5c);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .invite-card:hover::before {
            transform: scaleY(1);
        }
        
        [data-theme="dark"] .invite-card {
            background: #3a3a3a;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.2);
        }
        
        .invite-card:hover {
            transform: translateX(8px);
            box-shadow: 0 6px 20px rgba(201, 107, 62, 0.15);
            border-color: rgba(201, 107, 62, 0.2);
        }
        
        .invite-info h3 {
            font-size: 1.2rem;
            color: var(--color-text);
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .invite-info p {
            color: var(--color-text-light);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .invite-info p strong {
            color: #c96b3e;
        }
        
        [data-theme="dark"] .invite-info p strong {
            color: #ff8c5a;
        }
        
        .invite-info h3 {
            font-size: 1.1rem;
            color: var(--color-text);
            margin-bottom: 5px;
        }
        
        .invite-info p {
            color: var(--color-text-light);
            font-size: 0.9rem;
        }
        
        .invite-actions {
            display: flex;
            gap: 12px;
        }
        
        .btn-accept {
            padding: 12px 24px;
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
        }
        
        .btn-accept:hover {
            background: linear-gradient(135deg, #45a049, #5cb860);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
        }
        
        .btn-decline {
            padding: 12px 24px;
            background: linear-gradient(135deg, #f44336, #ef5350);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 3px 10px rgba(244, 67, 54, 0.3);
        }
        
        .btn-decline:hover {
            background: linear-gradient(135deg, #da190b, #e53935);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(244, 67, 54, 0.4);
        }
        
        /* Groups Grid */
        .groups-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .group-card {
            position: relative;
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            background-image: linear-gradient(white, white), 
                              linear-gradient(135deg, #c96b3e, #ff8c5a);
            background-origin: border-box;
            background-clip: padding-box, border-box;
            cursor: pointer;
            overflow: hidden;
        }
        
        .group-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(201, 107, 62, 0.05), transparent);
            transition: left 0.5s ease;
        }
        
        .group-card:hover::before {
            left: 100%;
        }
        
        [data-theme="dark"] .group-card {
            background-image: linear-gradient(#2d2d2d, #2d2d2d), 
                              linear-gradient(135deg, #ff8c5a, #ffa574);
            background-origin: border-box;
            background-clip: padding-box, border-box;
        }
        
        .group-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 30px rgba(201, 107, 62, 0.25);
        }
        
        .group-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .group-name {
            font-size: 1.4rem;
            color: var(--color-text);
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .group-role {
            padding: 8px 16px;
            background: linear-gradient(135deg, rgba(201, 107, 62, 0.15), rgba(255, 140, 90, 0.15));
            color: #c96b3e;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1.5px solid rgba(201, 107, 62, 0.3);
            box-shadow: 0 2px 8px rgba(201, 107, 62, 0.2);
            transition: all 0.3s ease;
        }
        
        .group-role:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(201, 107, 62, 0.3);
        }
        
        [data-theme="dark"] .group-role {
            background: linear-gradient(135deg, rgba(255, 140, 90, 0.25), rgba(255, 165, 116, 0.25));
            color: #ff8c5a;
            border-color: rgba(255, 140, 90, 0.4);
        }
        
        .group-description {
            color: var(--color-text-light);
            font-size: 0.95rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .group-meta {
            display: flex;
            gap: 20px;
            padding-top: 20px;
            border-top: 2px solid transparent;
            border-image: linear-gradient(90deg, transparent, rgba(201, 107, 62, 0.2), transparent) 1;
        }
        
        [data-theme="dark"] .group-meta {
            border-image: linear-gradient(90deg, transparent, rgba(255, 140, 90, 0.3), transparent) 1;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--color-text-light);
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .meta-item:hover {
            color: var(--color-primary);
            transform: translateX(3px);
        }
        
        .meta-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, rgba(201, 107, 62, 0.1), rgba(255, 140, 90, 0.15));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #c96b3e;
            border: 2px solid rgba(201, 107, 62, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .meta-item:hover .meta-icon {
            background: linear-gradient(135deg, rgba(201, 107, 62, 0.2), rgba(255, 140, 90, 0.25));
            transform: rotate(360deg) scale(1.1);
            box-shadow: 0 4px 12px rgba(201, 107, 62, 0.3);
        }
        
        [data-theme="dark"] .meta-icon {
            background: linear-gradient(135deg, rgba(255, 140, 90, 0.15), rgba(255, 165, 116, 0.2));
            color: #ff8c5a;
            border-color: rgba(255, 140, 90, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--color-text-light);
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--color-text);
            margin-bottom: 10px;
            font-family: 'Lora', serif;
        }
        
        .empty-state p {
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal-content {
            background: white;
            margin: 8% auto;
            padding: 40px;
            border-radius: 20px;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .modal-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #c96b3e, #ff8c5a, #c96b3e);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        [data-theme="dark"] .modal-content {
            background: #2d2d2d;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid transparent;
            border-image: linear-gradient(90deg, transparent, rgba(201, 107, 62, 0.3), transparent) 1;
        }
        
        .modal-header h2 {
            font-size: 1.8rem;
            color: var(--color-text);
            font-family: 'Lora', serif;
            background: linear-gradient(135deg, #c96b3e, #ff8c5a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .close-modal {
            font-size: 2rem;
            color: var(--color-text-light);
            cursor: pointer;
            border: none;
            background: rgba(201, 107, 62, 0.05);
            padding: 0;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .close-modal:hover {
            background: linear-gradient(135deg, rgba(244, 67, 54, 0.1), rgba(244, 67, 54, 0.15));
            color: #f44336;
            transform: rotate(90deg) scale(1.1);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--color-text);
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid rgba(201, 107, 62, 0.2);
            border-radius: 12px;
            font-size: 1rem;
            background: var(--color-surface);
            color: var(--color-text);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #c96b3e;
            box-shadow: 0 0 0 4px rgba(201, 107, 62, 0.15);
            transform: translateY(-2px);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }
        
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #c96b3e, #ff8c5a);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(201, 107, 62, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-submit:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(201, 107, 62, 0.5);
        }
        
        .btn-submit:active {
            transform: translateY(-1px);
        }
        
        .success-message {
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error-message {
            background: #f44336;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">ChefGuedes</a>
                
                <nav class="main-nav">
                    <a href="explorar.php">Explorar</a>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="grupos_familiares.php" style="color: var(--color-primary);">Grupos Familiares</a>
                </nav>
                
                <div class="header-actions">
                    <div class="theme-switcher">
                        <button class="theme-btn" data-theme="light" title="Modo Claro">
                            <span class="theme-icon">☀️</span>
                        </button>
                        <button class="theme-btn" data-theme="dark" title="Modo Escuro">
                            <span class="theme-icon">🌙</span>
                        </button>
                    </div>
                    
                    <?php if ($user): ?>
                        <div class="user-menu">
                            <span class="user-avatar">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=c96b3e&color=fff" alt="Avatar">
                                <?php endif; ?>
                            </span>
                            <div class="dropdown-menu">
                                <a href="profile.php">Meu Perfil</a>
                                <a href="minhas_receitas.php">Minhas Receitas</a>
                                <a href="logout.php">Sair</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="family-container">
        <!-- Mensagens -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Grupos Familiares</h1>
            <button class="btn-create-group" onclick="openCreateGroupModal()">Criar Novo Grupo</button>
        </div>

        <!-- Pending Invites -->
        <?php if (!empty($pending_invites)): ?>
            <div class="invites-section">
                <h2>Convites Pendentes (<?php echo count($pending_invites); ?>)</h2>
                <?php foreach ($pending_invites as $invite): ?>
                    <div class="invite-card">
                        <div class="invite-info">
                            <h3><?php echo htmlspecialchars($invite['group_name']); ?></h3>
                            <p>Convidado por <strong><?php echo htmlspecialchars($invite['inviter_name']); ?></strong> em <?php echo date('d/m/Y', strtotime($invite['invited_at'])); ?></p>
                        </div>
                        <div class="invite-actions">
                            <form method="POST" action="api/family_invite_response.php" style="display: inline;">
                                <input type="hidden" name="invite_id" value="<?php echo $invite['id']; ?>">
                                <input type="hidden" name="action" value="accept">
                                <button type="submit" class="btn-accept">Aceitar</button>
                            </form>
                            <form method="POST" action="api/family_invite_response.php" style="display: inline;">
                                <input type="hidden" name="invite_id" value="<?php echo $invite['id']; ?>">
                                <input type="hidden" name="action" value="decline">
                                <button type="submit" class="btn-decline">Recusar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Groups List -->
        <?php if (!empty($groups)): ?>
            <div class="groups-grid">
                <?php foreach ($groups as $group): ?>
                    <div class="group-card" onclick="window.location.href='grupo.php?id=<?php echo $group['id']; ?>'">
                        <div class="group-header">
                            <div>
                                <h3 class="group-name"><?php echo htmlspecialchars($group['name']); ?></h3>
                            </div>
                            <span class="group-role"><?php echo htmlspecialchars($group['role']); ?></span>
                        </div>
                        
                        <?php if ($group['description']): ?>
                            <p class="group-description"><?php echo htmlspecialchars($group['description']); ?></p>
                        <?php endif; ?>
                        
                        <div class="group-meta">
                            <div class="meta-item">
                                <span class="meta-icon">👥</span>
                                <span><?php echo $group['member_count']; ?> membros</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon">👤</span>
                                <span><?php echo htmlspecialchars($group['owner_name']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>Ainda não faz parte de nenhum grupo</h3>
                <p>Crie um grupo familiar ou aguarde um convite!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Create Group -->
    <div id="createGroupModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Criar Grupo Familiar</h2>
                <button class="close-modal" onclick="closeCreateGroupModal()">&times;</button>
            </div>
            
            <form method="POST" action="api/family_create_group.php">
                <div class="form-group">
                    <label for="group_name">Nome do Grupo *</label>
                    <input type="text" id="group_name" name="name" required placeholder="Ex: Família Silva">
                </div>
                
                <div class="form-group">
                    <label for="group_description">Descrição</label>
                    <textarea id="group_description" name="description" placeholder="Descreva o propósito do grupo..."></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Criar Grupo</button>
            </form>
        </div>
    </div>

    <script src="js/theme.js"></script>
    <script>
        function openCreateGroupModal() {
            document.getElementById('createGroupModal').style.display = 'block';
        }
        
        function closeCreateGroupModal() {
            document.getElementById('createGroupModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('createGroupModal');
            if (event.target === modal) {
                closeCreateGroupModal();
            }
        }
    </script>
</body>
</html>
