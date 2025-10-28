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
$group_id = (int)($_GET['id'] ?? 0);

if (!$group_id) {
    header('Location: grupos_familiares.php');
    exit;
}

try {
    // Buscar informações do grupo e permissões do utilizador
    $stmt = $pdo->prepare("
        SELECT 
            fg.*,
            fm.role,
            fm.can_add_recipes,
            fm.can_assign_tasks,
            u.username as owner_name
        FROM family_groups fg
        INNER JOIN family_members fm ON fg.id = fm.group_id
        INNER JOIN users u ON fg.owner_id = u.id
        WHERE fg.id = ? AND fm.user_id = ?
    ");
    $stmt->execute([$group_id, $user_id]);
    $group = $stmt->fetch();
    
    if (!$group) {
        $_SESSION['error'] = 'Grupo não encontrado ou você não é membro.';
        header('Location: grupos_familiares.php');
        exit;
    }
    
    // Buscar membros do grupo
    $stmt = $pdo->prepare("
        SELECT 
            fm.*,
            u.username,
            u.avatar
        FROM family_members fm
        INNER JOIN users u ON fm.user_id = u.id
        WHERE fm.group_id = ?
        ORDER BY 
            CASE fm.role
                WHEN 'owner' THEN 1
                WHEN 'admin' THEN 2
                WHEN 'member' THEN 3
                ELSE 4
            END,
            fm.joined_at
    ");
    $stmt->execute([$group_id]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar receitas da semana atual
    $week_start = date('Y-m-d', strtotime('monday this week'));
    $week_end = date('Y-m-d', strtotime('sunday this week'));
    
    $stmt = $pdo->prepare("
        SELECT 
            fr.*,
            u.username as assigned_name,
            r.title as recipe_title
        FROM family_recipes fr
        LEFT JOIN users u ON fr.assigned_to = u.id
        LEFT JOIN recipes r ON fr.recipe_id = r.id
        WHERE fr.group_id = ? AND fr.meal_date BETWEEN ? AND ?
        ORDER BY fr.meal_date, 
            CASE fr.meal_type
                WHEN 'breakfast' THEN 1
                WHEN 'lunch' THEN 2
                WHEN 'dinner' THEN 3
                WHEN 'snack' THEN 4
            END
    ");
    $stmt->execute([$group_id, $week_start, $week_end]);
    $week_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar receitas por dia
    $week_plan = [];
    $days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime($week_start . ' +' . $i . ' days'));
        $week_plan[$date] = [
            'day_name' => $days[$i],
            'date' => $date,
            'breakfast' => null,
            'lunch' => null,
            'dinner' => null,
            'snack' => null
        ];
    }
    
    foreach ($week_recipes as $recipe) {
        $week_plan[$recipe['meal_date']][$recipe['meal_type']] = $recipe;
    }
    
    // Buscar tarefas pendentes
    $stmt = $pdo->prepare("
        SELECT 
            ft.*,
            u1.username as assigned_name,
            u2.username as assigned_by_name
        FROM family_tasks ft
        LEFT JOIN users u1 ON ft.assigned_to = u1.id
        LEFT JOIN users u2 ON ft.assigned_by = u2.id
        WHERE ft.group_id = ? AND ft.status != 'completed'
        ORDER BY ft.due_date ASC, ft.created_at DESC
    ");
    $stmt->execute([$group_id]);
    $pending_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erro ao carregar grupo: ' . $e->getMessage();
    header('Location: grupos_familiares.php');
    exit;
}

$is_owner = $group['role'] === 'owner';
$is_admin = in_array($group['role'], ['owner', 'admin']);
$can_add_recipes = $group['can_add_recipes'];
$can_assign_tasks = $group['can_assign_tasks'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($group['name']); ?> - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/culinary.css">
    <style>
        .group-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .group-header {
            background: linear-gradient(135deg, #fff9f5, #ffffff);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #c96b3e;
        }
        
        [data-theme="dark"] .group-header {
            background: linear-gradient(135deg, #2d2d2d, #252525);
            border-left-color: #ff8c5a;
        }
        
        .group-title {
            font-size: 2rem;
            color: var(--color-text);
            font-family: 'Lora', serif;
            margin-bottom: 10px;
        }
        
        .group-description {
            color: var(--color-text-light);
            font-size: 1.05rem;
            margin-bottom: 20px;
        }
        
        .group-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            padding: 10px 20px;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(201, 107, 62, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #666, #888);
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(201, 107, 62, 0.2);
        }
        
        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            color: var(--color-text-light);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            font-size: 1rem;
        }
        
        .tab:hover {
            color: var(--color-primary);
        }
        
        .tab.active {
            color: var(--color-primary);
            border-bottom-color: var(--color-primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Week Planner */
        .week-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .week-navigation {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .week-title {
            font-size: 1.4rem;
            color: var(--color-text);
            font-family: 'Lora', serif;
        }
        
        .btn-nav {
            padding: 8px 15px;
            background: var(--color-surface);
            border: 2px solid rgba(201, 107, 62, 0.3);
            border-radius: 8px;
            color: var(--color-text);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-nav:hover {
            background: rgba(201, 107, 62, 0.1);
            border-color: var(--color-primary);
        }
        
        .week-grid {
            display: grid;
            gap: 20px;
        }
        
        .day-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        [data-theme="dark"] .day-card {
            background: #2d2d2d;
        }
        
        .day-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(201, 107, 62, 0.2);
        }
        
        .day-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--color-text);
        }
        
        .day-date {
            color: var(--color-text-light);
            font-size: 0.9rem;
        }
        
        .meals {
            display: grid;
            gap: 12px;
        }
        
        .meal-slot {
            padding: 12px;
            background: var(--color-surface-muted);
            border-radius: 8px;
            border-left: 3px solid transparent;
        }
        
        [data-theme="dark"] .meal-slot {
            background: #3a3a3a;
        }
        
        .meal-slot.breakfast { border-left-color: #FFB74D; }
        .meal-slot.lunch { border-left-color: #4CAF50; }
        .meal-slot.dinner { border-left-color: #9C27B0; }
        .meal-slot.snack { border-left-color: #03A9F4; }
        
        .meal-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-text-light);
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .meal-name {
            font-size: 1rem;
            color: var(--color-text);
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .meal-assigned {
            font-size: 0.85rem;
            color: var(--color-text-light);
        }
        
        .meal-empty {
            color: var(--color-text-light);
            font-style: italic;
            font-size: 0.9rem;
        }
        
        .btn-add-meal {
            padding: 6px 12px;
            background: rgba(201, 107, 62, 0.1);
            color: var(--color-primary);
            border: 1px dashed var(--color-primary);
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            margin-top: 8px;
            width: 100%;
        }
        
        .btn-add-meal:hover {
            background: rgba(201, 107, 62, 0.2);
        }
        
        /* Members Section */
        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .member-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        [data-theme="dark"] .member-card {
            background: #2d2d2d;
        }
        
        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .member-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 15px;
            border: 3px solid rgba(201, 107, 62, 0.3);
        }
        
        .member-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .member-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 5px;
        }
        
        .member-role {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(201, 107, 62, 0.1);
            color: var(--color-primary);
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        [data-theme="dark"] .member-role {
            background: rgba(255, 140, 90, 0.2);
            color: #ff8c5a;
        }
        
        .member-permissions {
            font-size: 0.85rem;
            color: var(--color-text-light);
            margin-top: 10px;
        }
        
        .btn-invite {
            padding: 12px 24px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .btn-invite:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        
        /* Tasks Section */
        .tasks-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .task-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #4CAF50;
        }
        
        [data-theme="dark"] .task-card {
            background: #2d2d2d;
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }
        
        .task-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--color-text);
        }
        
        .task-status {
            padding: 4px 12px;
            background: rgba(76, 175, 80, 0.1);
            color: #4CAF50;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .task-description {
            color: var(--color-text-light);
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .task-meta {
            display: flex;
            gap: 20px;
            font-size: 0.9rem;
            color: var(--color-text-light);
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
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        
        [data-theme="dark"] .modal-content {
            background: #2d2d2d;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .modal-header h2 {
            font-size: 1.6rem;
            color: var(--color-text);
            font-family: 'Lora', serif;
        }
        
        .close-modal {
            font-size: 2rem;
            color: var(--color-text-light);
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .close-modal:hover {
            background: rgba(201, 107, 62, 0.1);
            color: #c96b3e;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--color-text);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid rgba(201, 107, 62, 0.2);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--color-surface);
            color: var(--color-text);
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #c96b3e;
            box-shadow: 0 0 0 3px rgba(201, 107, 62, 0.1);
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201, 107, 62, 0.4);
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

    <div class="group-container">
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
        
        <!-- Group Header -->
        <div class="group-header">
            <h1 class="group-title"><?php echo htmlspecialchars($group['name']); ?></h1>
            <?php if ($group['description']): ?>
                <p class="group-description"><?php echo htmlspecialchars($group['description']); ?></p>
            <?php endif; ?>
            <div class="group-actions">
                <a href="grupos_familiares.php" class="btn-action btn-secondary">Voltar aos Grupos</a>
                <?php if ($is_owner): ?>
                    <button class="btn-action" onclick="openSettingsModal()">Configurações</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="switchTab('planner')">Planeamento Semanal</button>
            <button class="tab" onclick="switchTab('tasks')">Tarefas</button>
            <button class="tab" onclick="switchTab('members')">Membros</button>
            <button class="tab" onclick="switchTab('history')">Histórico</button>
        </div>

        <!-- Tab: Planner -->
        <div id="tab-planner" class="tab-content active">
            <div class="week-header">
                <h2 class="week-title">Semana de <?php echo date('d/m', strtotime($week_start)); ?> a <?php echo date('d/m/Y', strtotime($week_end)); ?></h2>
                <div class="week-navigation">
                    <button class="btn-nav" onclick="changeWeek(-1)">Semana Anterior</button>
                    <button class="btn-nav" onclick="changeWeek(1)">Próxima Semana</button>
                </div>
            </div>
            
            <div class="week-grid">
                <?php foreach ($week_plan as $date => $day): ?>
                    <div class="day-card">
                        <div class="day-header">
                            <span class="day-name"><?php echo $day['day_name']; ?></span>
                            <span class="day-date"><?php echo date('d/m', strtotime($date)); ?></span>
                        </div>
                        
                        <div class="meals">
                            <?php 
                            $meal_types = [
                                'breakfast' => 'Pequeno-almoço',
                                'lunch' => 'Almoço',
                                'dinner' => 'Jantar',
                                'snack' => 'Lanche'
                            ];
                            
                            foreach ($meal_types as $type => $label): 
                                $meal = $day[$type];
                            ?>
                                <div class="meal-slot <?php echo $type; ?>">
                                    <div class="meal-label"><?php echo $label; ?></div>
                                    <?php if ($meal): ?>
                                        <div class="meal-name"><?php echo htmlspecialchars($meal['recipe_name']); ?></div>
                                        <?php if ($meal['assigned_name']): ?>
                                            <div class="meal-assigned">Responsável: <?php echo htmlspecialchars($meal['assigned_name']); ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="meal-empty">Nenhuma receita planeada</div>
                                        <?php if ($can_add_recipes): ?>
                                            <button class="btn-add-meal" onclick="openAddMealModal('<?php echo $date; ?>', '<?php echo $type; ?>')">+ Adicionar</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tab: Tasks -->
        <div id="tab-tasks" class="tab-content">
            <div class="tasks-header">
                <h2>Tarefas Pendentes</h2>
                <?php if ($can_assign_tasks): ?>
                    <button class="btn-action" onclick="openAddTaskModal()">Nova Tarefa</button>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($pending_tasks)): ?>
                <?php foreach ($pending_tasks as $task): ?>
                    <div class="task-card">
                        <div class="task-header">
                            <h3 class="task-title"><?php echo htmlspecialchars($task['task_name']); ?></h3>
                            <span class="task-status"><?php echo htmlspecialchars($task['status']); ?></span>
                        </div>
                        <?php if ($task['description']): ?>
                            <p class="task-description"><?php echo htmlspecialchars($task['description']); ?></p>
                        <?php endif; ?>
                        <div class="task-meta">
                            <?php if ($task['assigned_name']): ?>
                                <span>Atribuído a: <strong><?php echo htmlspecialchars($task['assigned_name']); ?></strong></span>
                            <?php endif; ?>
                            <?php if ($task['due_date']): ?>
                                <span>Prazo: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Sem tarefas pendentes</h3>
                    <p>Todas as tarefas estão completas!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab: Members -->
        <div id="tab-members" class="tab-content">
            <?php if ($is_admin): ?>
                <button class="btn-invite" onclick="openInviteModal()">Convidar Membro</button>
            <?php endif; ?>
            
            <div class="members-grid">
                <?php foreach ($members as $member): ?>
                    <div class="member-card">
                        <div class="member-avatar">
                            <?php if (!empty($member['avatar'])): ?>
                                <img src="<?php echo htmlspecialchars($member['avatar']); ?>" alt="Avatar">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($member['username']); ?>&background=c96b3e&color=fff" alt="Avatar">
                            <?php endif; ?>
                        </div>
                        <div class="member-name"><?php echo htmlspecialchars($member['username']); ?></div>
                        <span class="member-role"><?php echo htmlspecialchars($member['role']); ?></span>
                        <div class="member-permissions">
                            <?php if ($member['can_add_recipes']): ?>✓ Adicionar receitas<br><?php endif; ?>
                            <?php if ($member['can_assign_tasks']): ?>✓ Atribuir tarefas<?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tab: History -->
        <div id="tab-history" class="tab-content">
            <h2>Semanas Anteriores</h2>
            <p style="color: var(--color-text-light); padding: 40px 0; text-align: center;">Funcionalidade em desenvolvimento...</p>
        </div>
    </div>

    <!-- Modals -->
    <!-- Modal: Add Meal -->
    <div id="addMealModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Adicionar Receita</h2>
                <button class="close-modal" onclick="closeModal('addMealModal')">&times;</button>
            </div>
            
            <form method="POST" action="api/family_add_meal.php">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                <input type="hidden" id="meal_date" name="meal_date" value="">
                <input type="hidden" id="meal_type" name="meal_type" value="">
                
                <div class="form-group">
                    <label for="recipe_name">Nome da Receita *</label>
                    <input type="text" id="recipe_name" name="recipe_name" required>
                </div>
                
                <div class="form-group">
                    <label for="assigned_to">Responsável</label>
                    <select id="assigned_to" name="assigned_to">
                        <option value="">Sem responsável</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?php echo $member['user_id']; ?>"><?php echo htmlspecialchars($member['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notas</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Adicionar Receita</button>
            </form>
        </div>
    </div>

    <!-- Modal: Invite Member -->
    <div id="inviteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Convidar Membro</h2>
                <button class="close-modal" onclick="closeModal('inviteModal')">&times;</button>
            </div>
            
            <form method="POST" action="api/family_send_invite.php">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                
                <div class="form-group">
                    <label for="username">Nome de Utilizador *</label>
                    <input type="text" id="username" name="username" required placeholder="Digite o nome de utilizador">
                </div>
                
                <button type="submit" class="btn-submit">Enviar Convite</button>
            </form>
        </div>
    </div>

    <!-- Modal: Add Task -->
    <div id="addTaskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nova Tarefa</h2>
                <button class="close-modal" onclick="closeModal('addTaskModal')">&times;</button>
            </div>
            
            <form method="POST" action="api/family_add_task.php">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                
                <div class="form-group">
                    <label for="task_name">Nome da Tarefa *</label>
                    <input type="text" id="task_name" name="task_name" required>
                </div>
                
                <div class="form-group">
                    <label for="task_description">Descrição</label>
                    <textarea id="task_description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="task_assigned_to">Atribuir a</label>
                    <select id="task_assigned_to" name="assigned_to">
                        <option value="">Sem atribuição</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?php echo $member['user_id']; ?>"><?php echo htmlspecialchars($member['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="due_date">Prazo</label>
                    <input type="date" id="due_date" name="due_date">
                </div>
                
                <button type="submit" class="btn-submit">Criar Tarefa</button>
            </form>
        </div>
    </div>

    <script src="js/theme.js"></script>
    <script>
        // Tab switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        }
        
        // Week navigation
        function changeWeek(offset) {
            const currentWeek = '<?php echo $week_start; ?>';
            const newDate = new Date(currentWeek);
            newDate.setDate(newDate.getDate() + (offset * 7));
            const newWeek = newDate.toISOString().split('T')[0];
            window.location.href = '?id=<?php echo $group_id; ?>&week=' + newWeek;
        }
        
        // Modal functions
        function openAddMealModal(date, type) {
            document.getElementById('meal_date').value = date;
            document.getElementById('meal_type').value = type;
            document.getElementById('addMealModal').style.display = 'block';
        }
        
        function openInviteModal() {
            document.getElementById('inviteModal').style.display = 'block';
        }
        
        function openAddTaskModal() {
            document.getElementById('addTaskModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
