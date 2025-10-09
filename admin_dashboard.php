<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-dashboard {
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
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-left: 4px solid var(--color-primary);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: var(--color-text);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--color-primary);
            margin: 0;
        }
        
        .stat-change {
            font-size: 0.85rem;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .stat-change.positive {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .stat-change.negative {
            background: #ffebee;
            color: #c62828;
        }
        
        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .chart-card h3 {
            margin: 0 0 20px 0;
            color: var(--color-text);
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .tables-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table-card h3 {
            margin: 0;
            padding: 20px 25px;
            background: var(--color-light);
            color: var(--color-text);
            font-size: 1.1rem;
            font-weight: 600;
            border-bottom: 1px solid #eee;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px 25px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .data-table th {
            background: #fafafa;
            font-weight: 600;
            color: var(--color-text-light);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--color-text-light);
        }
        
        .refresh-btn {
            background: var(--color-primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .refresh-btn:hover {
            background: var(--color-accent);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .charts-section,
            .tables-section {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php
    require_once 'includes/config.php';
    
    // Verificar se é admin
    if (!isLoggedIn() || !isAdmin()) {
        redirect('login.php');
    }
    ?>
    
    <div class="admin-dashboard">
        <div class="dashboard-header">
            <h1>Dashboard Administrativo</h1>
            <p>Visão geral das estatísticas do ChefGuedes</p>
            <small>Última atualização: <span id="lastUpdate">Carregando...</span></small>
        </div>
        
        <button class="refresh-btn" onclick="loadAllData()">
            Atualizar Dados
        </button>
        
        <!-- Estatísticas Principais -->
        <div class="stats-grid" id="statsGrid">
            <div class="loading">Carregando estatísticas...</div>
        </div>
        
        <!-- Gráficos -->
        <div class="charts-section">
            <div class="chart-card">
                <h3>Receitas por Categoria</h3>
                <div id="categoryChart" class="loading">Carregando gráfico...</div>
            </div>
            
            <div class="chart-card">
                <h3>Pesquisas Populares</h3>
                <div id="searchChart" class="loading">Carregando gráfico...</div>
            </div>
        </div>
        
        <!-- Tabelas -->
        <div class="tables-section">
            <div class="table-card">
                <h3>Receitas Mais Populares</h3>
                <div id="popularRecipes" class="loading">Carregando dados...</div>
            </div>
            
            <div class="table-card">
                <h3>Usuários Mais Ativos</h3>
                <div id="activeUsers" class="loading">Carregando dados...</div>
            </div>
        </div>
    </div>
    
    <script>
        // Carregar todos os dados do dashboard
        async function loadAllData() {
            document.getElementById('lastUpdate').textContent = new Date().toLocaleString('pt-PT');
            
            try {
                await Promise.all([
                    loadOverview(),
                    loadRecipeAnalytics(),
                    loadUserAnalytics(),
                    loadSearchAnalytics(),
                    loadPopularContent()
                ]);
            } catch (error) {
                console.error('Erro ao carregar dados:', error);
            }
        }
        
        // Carregar visão geral
        async function loadOverview() {
            try {
                const response = await fetch('api/admin_dashboard.php?action=overview');
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                renderStatsGrid(data);
            } catch (error) {
                document.getElementById('statsGrid').innerHTML = 
                    '<div class="loading">Erro ao carregar estatísticas</div>';
            }
        }
        
        // Renderizar grid de estatísticas
        function renderStatsGrid(data) {
            const { totals, growth } = data;
            
            const statsHTML = `
                <div class="stat-card">
                    <h3>Total de Usuários</h3>
                    <div class="stat-number">${totals.total_users || 0}</div>
                    <div class="stat-change positive">+${growth.new_users_30d || 0} últimos 30 dias</div>
                </div>
                
                <div class="stat-card">
                    <h3>Total de Receitas</h3>
                    <div class="stat-number">${totals.total_recipes || 0}</div>
                    <div class="stat-change positive">+${growth.new_recipes_30d || 0} últimos 30 dias</div>
                </div>
                
                <div class="stat-card">
                    <h3>Visualizações Hoje</h3>
                    <div class="stat-number">${totals.views_today || 0}</div>
                    <div class="stat-change positive">${growth.views_30d || 0} últimos 30 dias</div>
                </div>
                
                <div class="stat-card">
                    <h3>Pesquisas Hoje</h3>
                    <div class="stat-number">${totals.searches_today || 0}</div>
                </div>
                
                <div class="stat-card">
                    <h3>Favoritos Hoje</h3>
                    <div class="stat-number">${totals.favorites_today || 0}</div>
                </div>
                
                <div class="stat-card">
                    <h3>Avaliação Média</h3>
                    <div class="stat-number">${parseFloat(totals.avg_rating || 0).toFixed(1)}</div>
                    <div class="stat-change positive">⭐ de 5 estrelas</div>
                </div>
            `;
            
            document.getElementById('statsGrid').innerHTML = statsHTML;
        }
        
        // Carregar analytics de receitas
        async function loadRecipeAnalytics() {
            try {
                const response = await fetch('api/admin_dashboard.php?action=recipe_analytics');
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);
                
                renderCategoryChart(data.by_category);
            } catch (error) {
                document.getElementById('categoryChart').innerHTML = 'Erro ao carregar dados';
            }
        }
        
        // Renderizar gráfico de categorias
        function renderCategoryChart(categories) {
            const chartHTML = categories.map(cat => `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span>${cat.name}</span>
                    <div style="display: flex; align-items: center;">
                        <div style="width: 100px; height: 8px; background: #f0f0f0; border-radius: 4px; margin-right: 10px;">
                            <div style="width: ${Math.min(100, (cat.count / Math.max(...categories.map(c => c.count))) * 100)}%; height: 100%; background: var(--color-primary); border-radius: 4px;"></div>
                        </div>
                        <strong>${cat.count}</strong>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('categoryChart').innerHTML = chartHTML;
        }
        
        // Carregar analytics de pesquisa
        async function loadSearchAnalytics() {
            try {
                const response = await fetch('api/admin_dashboard.php?action=search_analytics');
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);
                
                renderSearchChart(data.popular_searches);
            } catch (error) {
                document.getElementById('searchChart').innerHTML = 'Erro ao carregar dados';
            }
        }
        
        // Renderizar gráfico de pesquisas
        function renderSearchChart(searches) {
            const maxCount = Math.max(...searches.map(s => s.search_count));
            
            const chartHTML = searches.slice(0, 8).map(search => `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 0.9rem;">${search.search_query}</span>
                    <div style="display: flex; align-items: center;">
                        <div style="width: 80px; height: 6px; background: #f0f0f0; border-radius: 3px; margin-right: 8px;">
                            <div style="width: ${(search.search_count / maxCount) * 100}%; height: 100%; background: var(--color-accent); border-radius: 3px;"></div>
                        </div>
                        <strong style="font-size: 0.85rem; min-width: 30px;">${search.search_count}</strong>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('searchChart').innerHTML = chartHTML;
        }
        
        // Carregar conteúdo popular
        async function loadPopularContent() {
            try {
                const response = await fetch('api/admin_dashboard.php?action=popular_content');
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);
                
                renderPopularRecipes(data.most_viewed);
            } catch (error) {
                document.getElementById('popularRecipes').innerHTML = 'Erro ao carregar dados';
            }
        }
        
        // Renderizar receitas populares
        function renderPopularRecipes(recipes) {
            const tableHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Receita</th>
                            <th>Visualizações</th>
                            <th>Favoritos</th>
                            <th>Avaliação</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${recipes.map(recipe => `
                            <tr>
                                <td>${recipe.title}</td>
                                <td>${recipe.total_views || 0}</td>
                                <td>${recipe.total_favorites || 0}</td>
                                <td>${parseFloat(recipe.average_rating || 0).toFixed(1)} ⭐</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            
            document.getElementById('popularRecipes').innerHTML = tableHTML;
        }
        
        // Carregar analytics de usuários
        async function loadUserAnalytics() {
            try {
                const response = await fetch('api/admin_dashboard.php?action=user_analytics');
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);
                
                renderActiveUsers(data.most_active);
            } catch (error) {
                document.getElementById('activeUsers').innerHTML = 'Erro ao carregar dados';
            }
        }
        
        // Renderizar usuários ativos
        function renderActiveUsers(users) {
            const tableHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Favoritos</th>
                            <th>Feitas</th>
                            <th>Avaliações</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${users.map(user => `
                            <tr>
                                <td>${user.username}</td>
                                <td>${user.favorites_count || 0}</td>
                                <td>${user.made_count || 0}</td>
                                <td>${user.ratings_count || 0}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            
            document.getElementById('activeUsers').innerHTML = tableHTML;
        }
        
        // Carregar dados ao carregar a página
        document.addEventListener('DOMContentLoaded', loadAllData);
        
        // Atualizar dados a cada 5 minutos
        setInterval(loadAllData, 5 * 60 * 1000);
    </script>
</body>
</html>