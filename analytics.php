<?php
require_once 'includes/config.php';

// Verificar se o usuário é administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Buscar estatísticas gerais
$stats = [];

try {
    // Total de usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Total de receitas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM recipes WHERE is_active = 1");
    $stats['total_recipes'] = $stmt->fetchColumn();
    
    // Total de visualizações
    $stmt = $pdo->query("SELECT SUM(total_views) as total FROM recipe_stats");
    $stats['total_views'] = $stmt->fetchColumn() ?: 0;
    
    // Total de avaliações
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM recipe_ratings");
    $stats['total_ratings'] = $stmt->fetchColumn();
    
    // Usuários ativos nos últimos 30 dias
    $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as active FROM user_activity WHERE activity_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['active_users'] = $stmt->fetchColumn() ?: 0;
    
    // Receitas mais populares
    $stmt = $pdo->query("
        SELECT r.title, r.slug, rs.total_views, rs.average_rating, rs.total_ratings 
        FROM recipes r 
        LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id 
        WHERE r.is_active = 1 
        ORDER BY rs.total_views DESC 
        LIMIT 10
    ");
    $popular_recipes = $stmt->fetchAll();
    
    // Categorias mais populares
    $stmt = $pdo->query("
        SELECT c.name, COUNT(r.id) as recipe_count, SUM(rs.total_views) as total_views
        FROM categories c
        LEFT JOIN recipes r ON c.id = r.category_id AND r.is_active = 1
        LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id
        GROUP BY c.id, c.name
        ORDER BY total_views DESC
        LIMIT 8
    ");
    $popular_categories = $stmt->fetchAll();
    
    // Atividade dos últimos 7 dias
    $stmt = $pdo->query("
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM recipes 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date DESC
    ");
    $recent_activity = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = 'Erro ao carregar estatísticas: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .analytics-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            border-radius: 15px;
        }
        
        .analytics-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .analytics-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1rem;
            color: var(--color-text-light);
            font-weight: 500;
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.7;
        }
        
        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .chart-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .tables-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
        }
        
        .table-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .table-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--color-text);
        }
        
        .data-table td {
            color: var(--color-text-light);
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        .rating-stars {
            color: #ffc107;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--color-text-light);
            text-decoration: none;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--color-primary);
        }
        
        @media (max-width: 768px) {
            .charts-section,
            .tables-section {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                height: 250px;
            }
            
            .analytics-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="analytics-container">
        <a href="admin_dashboard.php" class="back-link">
            ← Voltar ao Dashboard Admin
        </a>
        
        <div class="analytics-header">
            <h1>📊 Analytics</h1>
            <p>Métricas e estatísticas detalhadas da plataforma ChefGuedes</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">�</div>
                <div class="stat-number"><?= number_format($stats['total_users']) ?></div>
                <div class="stat-label">Usuários Registrados</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">�</div>
                <div class="stat-number"><?= number_format($stats['total_recipes']) ?></div>
                <div class="stat-label">Receitas Publicadas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👁️</div>
                <div class="stat-number"><?= number_format($stats['total_views']) ?></div>
                <div class="stat-label">Visualizações Totais</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-number"><?= number_format($stats['total_ratings']) ?></div>
                <div class="stat-label">Avaliações</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">�</div>
                <div class="stat-number"><?= number_format($stats['active_users']) ?></div>
                <div class="stat-label">Usuários Ativos (30d)</div>
            </div>
        </div>
        
        <div class="charts-section">
            <div class="chart-card">
                <h3 class="chart-title">Categorias Mais Populares</h3>
                <div class="chart-container">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h3 class="chart-title">Atividade dos Últimos 7 Dias</h3>
                <div class="chart-container">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="tables-section">
            <div class="table-card">
                <h3 class="table-title">🏆 Receitas Mais Populares</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Receita</th>
                            <th>Visualizações</th>
                            <th>Avaliação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($popular_recipes as $recipe): ?>
                            <tr>
                                <td>
                                    <a href="receita.php?slug=<?= urlencode($recipe['slug']) ?>" 
                                       style="color: var(--color-text); text-decoration: none;">
                                        <?= htmlspecialchars($recipe['title']) ?>
                                    </a>
                                </td>
                                <td><?= number_format($recipe['total_views'] ?: 0) ?></td>
                                <td>
                                    <span class="rating-stars">★</span>
                                    <?= number_format($recipe['average_rating'] ?: 0, 1) ?>
                                    (<?= $recipe['total_ratings'] ?: 0 ?>)
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="table-card">
                <h3 class="table-title">📊 Categorias por Popularidade</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Receitas</th>
                            <th>Visualizações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($popular_categories as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= number_format($category['recipe_count']) ?></td>
                                <td><?= number_format($category['total_views'] ?: 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Gráfico de Categorias
        const categoriesData = <?= json_encode(array_slice($popular_categories, 0, 6)) ?>;
        const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
        
        new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: categoriesData.map(cat => cat.name),
                datasets: [{
                    data: categoriesData.map(cat => cat.total_views || 0),
                    backgroundColor: [
                        '#C96B3E',
                        '#E8B86D',
                        '#F4E4C1',
                        '#8FBC8F',
                        '#DDA0DD',
                        '#87CEEB'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
        
        // Gráfico de Atividade
        const activityData = <?= json_encode($recent_activity) ?>;
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: activityData.map(day => {
                    const date = new Date(day.date);
                    return date.toLocaleDateString('pt-PT', { day: '2-digit', month: '2-digit' });
                }),
                datasets: [{
                    label: 'Receitas Publicadas',
                    data: activityData.map(day => day.count),
                    borderColor: '#C96B3E',
                    backgroundColor: 'rgba(201, 107, 62, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#C96B3E',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>