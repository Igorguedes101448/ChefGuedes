<?php
require_once 'includes/config.php';

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getCurrentUser();

// Buscar estatísticas do usuário
$user_stats = [];

try {
    // Receitas do usuário
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM recipes WHERE user_id = ? AND is_active = 1");
    $stmt->execute([$user['id']]);
    $user_stats['total_recipes'] = $stmt->fetchColumn();
    
    // Visualizações totais das receitas do usuário
    $stmt = $pdo->prepare("
        SELECT SUM(rs.total_views) as total 
        FROM recipes r 
        LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id 
        WHERE r.user_id = ? AND r.is_active = 1
    ");
    $stmt->execute([$user['id']]);
    $user_stats['total_views'] = $stmt->fetchColumn() ?: 0;
    
    // Favoritos recebidos
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM recipe_favorites rf 
        JOIN recipes r ON rf.recipe_id = r.id 
        WHERE r.user_id = ?
    ");
    $stmt->execute([$user['id']]);
    $user_stats['total_favorites'] = $stmt->fetchColumn();
    
    // Avaliação média das receitas
    $stmt = $pdo->prepare("
        SELECT AVG(rs.average_rating) as avg_rating 
        FROM recipes r 
        LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id 
        WHERE r.user_id = ? AND r.is_active = 1 AND rs.average_rating > 0
    ");
    $stmt->execute([$user['id']]);
    $user_stats['avg_rating'] = $stmt->fetchColumn() ?: 0;
    
    // Receitas recentes do usuário
    $stmt = $pdo->prepare("
        SELECT r.*, rs.total_views, rs.average_rating, rs.total_ratings 
        FROM recipes r 
        LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id 
        WHERE r.user_id = ? AND r.is_active = 1 
        ORDER BY r.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$user['id']]);
    $recent_recipes = $stmt->fetchAll();
    
    // Receitas favoritas do usuário
    $stmt = $pdo->prepare("
        SELECT r.*, u.username as chef_name, rs.total_views, rs.average_rating 
        FROM recipe_favorites rf 
        JOIN recipes r ON rf.recipe_id = r.id 
        JOIN users u ON r.user_id = u.id 
        LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id 
        WHERE rf.user_id = ? AND r.is_active = 1 
        ORDER BY rf.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$user['id']]);
    $favorite_recipes = $stmt->fetchAll();
    
    // Atividade recente no site
    $stmt = $pdo->query("
        SELECT r.title, r.slug, u.username as chef_name, r.created_at 
        FROM recipes r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.is_active = 1 
        ORDER BY r.created_at DESC 
        LIMIT 8
    ");
    $recent_activity = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = 'Erro ao carregar dados: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .welcome-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
            overflow: hidden;
            border: 3px solid rgba(255,255,255,0.5);
            flex-shrink: 0;
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .welcome-text h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .welcome-text p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .action-btn:hover {
            background: white;
            color: var(--color-primary);
            transform: translateY(-2px);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--color-text-light);
            font-weight: 500;
        }
        
        .dashboard-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .section-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .section-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--color-text);
            margin: 0;
        }
        
        .section-content {
            padding: 25px;
        }
        
        .recipe-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s ease;
        }
        
        .recipe-item:last-child {
            border-bottom: none;
        }
        
        .recipe-item:hover {
            background: #f8f9fa;
            margin: 0 -25px;
            padding-left: 25px;
            padding-right: 25px;
        }
        
        .recipe-image {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .recipe-info {
            flex: 1;
        }
        
        .recipe-title {
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 5px;
            text-decoration: none;
        }
        
        .recipe-title:hover {
            color: var(--color-primary);
        }
        
        .recipe-meta {
            font-size: 0.9rem;
            color: var(--color-text-light);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background: var(--color-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 500;
            color: var(--color-text);
            margin-bottom: 3px;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: var(--color-text-light);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--color-text-light);
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: var(--color-text);
        }
        
        .cta-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-top: 20px;
        }
        
        .cta-btn {
            background: var(--color-primary);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .cta-btn:hover {
            background: var(--color-accent);
            transform: translateY(-2px);
        }
        
        @media (max-width: 968px) {
            .dashboard-content {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="welcome-section">
                <a href="profile.php" style="text-decoration: none;">
                    <div class="user-avatar">
                        <?php if (!empty($user['avatar']) && file_exists($user['avatar'])): ?>
                            <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= htmlspecialchars($user['username']) ?>">
                        <?php else: ?>
                            <?= strtoupper(substr($user['username'], 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="welcome-text">
                    <h1>Olá, <?= htmlspecialchars($user['username']) ?>!</h1>
                    <p>Bem-vindo ao seu painel pessoal do ChefGuedes</p>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 15px;">
                <!-- Theme Switcher -->
                <div class="theme-switcher">
                    <button class="theme-btn" data-theme="light" title="Tema Claro">
                        <span class="theme-icon">☀️</span>
                    </button>
                    <button class="theme-btn" data-theme="dark" title="Tema Escuro">
                        <span class="theme-icon">🌙</span>
                    </button>
                </div>
                
                <div class="quick-actions">
                    <a href="profile.php" class="action-btn">Meu Perfil</a>
                    <a href="receitas.php" class="action-btn">Nova Receita</a>
                    <a href="explorar.php" class="action-btn">Explorar</a>
                    <a href="grupos_familiares.php" class="action-btn">Grupos Familiares</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin_dashboard.php" class="action-btn">Área Admin</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">R</div>
                <div class="stat-number"><?= $user_stats['total_recipes'] ?></div>
                <div class="stat-label">Suas Receitas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">V</div>
                <div class="stat-number"><?= number_format($user_stats['total_views']) ?></div>
                <div class="stat-label">Visualizações</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">F</div>
                <div class="stat-number"><?= $user_stats['total_favorites'] ?></div>
                <div class="stat-label">Favoritos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">A</div>
                <div class="stat-number"><?= number_format($user_stats['avg_rating'], 1) ?></div>
                <div class="stat-label">Avaliação Média</div>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="main-content">
                <!-- Suas Receitas Recentes -->
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">Suas Receitas Recentes</h2>
                        <a href="minhas_receitas.php" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">Ver todas</a>
                    </div>
                    <div class="section-content">
                        <?php if (empty($recent_recipes)): ?>
                            <div class="empty-state">
                                <h3>Nenhuma receita ainda</h3>
                                <p>Comece a compartilhar suas receitas incríveis!</p>
                                <div class="cta-section">
                                    <a href="receitas.php" class="cta-btn">Criar Primeira Receita</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_recipes as $recipe): ?>
                                <div class="recipe-item">
                                    <div class="recipe-image">
                                        <?= strtoupper(substr($recipe['title'], 0, 1)) ?>
                                    </div>
                                    <div class="recipe-info">
                                        <a href="receita.php?slug=<?= urlencode($recipe['slug']) ?>" class="recipe-title">
                                            <?= htmlspecialchars($recipe['title']) ?>
                                        </a>
                                        <div class="recipe-meta">
                                            <span>👁️ <?= number_format($recipe['total_views'] ?: 0) ?></span>
                                            <span>⭐ <?= number_format($recipe['average_rating'] ?: 0, 1) ?></span>
                                            <span>📅 <?= date('d/m/Y', strtotime($recipe['created_at'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Receitas Favoritas -->
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">Suas Receitas Favoritas</h2>
                        <a href="explorar.php" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">Explorar mais</a>
                    </div>
                    <div class="section-content">
                        <?php if (empty($favorite_recipes)): ?>
                            <div class="empty-state">
                                <h3>Nenhuma receita favorita</h3>
                                <p>Explore receitas e adicione suas favoritas!</p>
                                <div class="cta-section">
                                    <a href="explorar.php" class="cta-btn">Explorar Receitas</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($favorite_recipes as $recipe): ?>
                                <div class="recipe-item">
                                    <div class="recipe-image">
                                        <?= strtoupper(substr($recipe['title'], 0, 1)) ?>
                                    </div>
                                    <div class="recipe-info">
                                        <a href="receita.php?slug=<?= urlencode($recipe['slug']) ?>" class="recipe-title">
                                            <?= htmlspecialchars($recipe['title']) ?>
                                        </a>
                                        <div class="recipe-meta">
                                            <span>👨‍🍳 <?= htmlspecialchars($recipe['chef_name']) ?></span>
                                            <span>👁️ <?= number_format($recipe['total_views'] ?: 0) ?></span>
                                            <span>⭐ <?= number_format($recipe['average_rating'] ?: 0, 1) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="sidebar">
                <!-- Atividade Recente -->
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">Atividade Recente</h2>
                    </div>
                    <div class="section-content">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">📝</div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        <strong><?= htmlspecialchars($activity['chef_name']) ?></strong> 
                                        publicou "<?= htmlspecialchars($activity['title']) ?>"
                                    </div>
                                    <div class="activity-time">
                                        <?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Dicas Rápidas -->
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">Dicas do Chef</h2>
                    </div>
                    <div class="section-content">
                        <div style="color: var(--color-text-light); line-height: 1.6;">
                            <p><strong>Fotos atraentes:</strong> Use boa iluminação e mostre o prato finalizado.</p>
                            <p><strong>Instruções claras:</strong> Detalhe cada passo para facilitar o preparo.</p>
                            <p><strong>Tempos realistas:</strong> Informe tempos de preparo e cozimento precisos.</p>
                            <p><strong>Tags úteis:</strong> Use categorias e tags para facilitar a descoberta.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="index.php" style="color: var(--color-text-light); text-decoration: none;">
                ← Voltar à página inicial
            </a>
        </div>
    </div>
    <script src="js/theme.js"></script>
</body>
</html>