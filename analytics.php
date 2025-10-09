<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Avançado - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .analytics-dashboard {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .dashboard-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .control-btn {
            background: white;
            border: 2px solid #ddd;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .control-btn.active {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .metric-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
        }
        
        .metric-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }
        
        .metric-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--color-primary);
            margin: 10px 0 5px 0;
        }
        
        .metric-label {
            color: var(--color-text-light);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .metric-change {
            font-size: 0.8rem;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 12px;
            display: inline-block;
        }
        
        .change-positive {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .change-negative {
            background: #ffebee;
            color: #c62828;
        }
        
        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .chart-panel {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .chart-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--color-text);
            margin: 0;
        }
        
        .chart-body {
            padding: 25px;
            min-height: 300px;
        }
        
        .realtime-feed {
            max-height: 400px;
            overflow-y: auto;
            padding: 0;
        }
        
        .feed-item {
            padding: 15px 25px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: background 0.3s ease;
        }
        
        .feed-item:hover {
            background: #f8f9fa;
        }
        
        .feed-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
        }
        
        .feed-icon.view { background: #3498db; }
        .feed-icon.search { background: #f39c12; }
        .feed-icon.favorite { background: #e74c3c; }
        .feed-icon.rating { background: #9b59b6; }
        
        .feed-content {
            flex: 1;
        }
        
        .feed-title {
            font-weight: 500;
            color: var(--color-text);
            margin: 0 0 4px 0;
        }
        
        .feed-meta {
            font-size: 0.8rem;
            color: var(--color-text-light);
        }
        
        .data-tables {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .table-panel {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background: #f8f9fa;
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--color-text-light);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #eee;
        }
        
        .data-table td {
            padding: 12px 20px;
            border-bottom: 1px solid #f5f5f5;
            color: var(--color-text);
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        .trend-indicator {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
            padding: 2px 6px;
            border-radius: 10px;
        }
        
        .trend-up {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .trend-down {
            background: #ffebee;
            color: #c62828;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--color-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .refresh-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--color-primary);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            display: none;
            z-index: 1000;
        }
        
        @media (max-width: 1200px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .data-tables {
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
    
    <div class="analytics-dashboard">
        <div class="dashboard-header">
            <h1>Analytics Avançado ChefGuedes</h1>
            <p>Monitoramento em tempo real das atividades do site</p>
            <div style="margin-top: 15px;">
                <button id="autoRefreshBtn" class="control-btn active">Auto-refresh ON</button>
                <span style="margin-left: 15px;">Próxima atualização: <span id="nextUpdate">30s</span></span>
            </div>
        </div>
        
        <div class="dashboard-controls">
            <button class="control-btn active" data-period="today">Hoje</button>
            <button class="control-btn" data-period="week">Esta Semana</button>
            <button class="control-btn" data-period="month">Este Mês</button>
            <button class="control-btn" data-period="year">Este Ano</button>
        </div>
        
        <!-- Métricas Principais -->
        <div class="metrics-grid" id="metricsGrid">
            <div class="metric-card">
                <div class="metric-icon">👁️</div>
                <div class="metric-value" id="totalViews">0</div>
                <div class="metric-label">Visualizações</div>
                <div class="metric-change change-positive" id="viewsChange">+0%</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">🔍</div>
                <div class="metric-value" id="totalSearches">0</div>
                <div class="metric-label">Pesquisas</div>
                <div class="metric-change change-positive" id="searchesChange">+0%</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">❤️</div>
                <div class="metric-value" id="totalFavorites">0</div>
                <div class="metric-label">Favoritos</div>
                <div class="metric-change change-positive" id="favoritesChange">+0%</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">⭐</div>
                <div class="metric-value" id="avgRating">0.0</div>
                <div class="metric-label">Avaliação Média</div>
                <div class="metric-change change-positive" id="ratingChange">+0.0</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">👨‍🍳</div>
                <div class="metric-value" id="activeUsers">0</div>
                <div class="metric-label">Usuários Ativos</div>
                <div class="metric-change change-positive" id="usersChange">+0</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">📈</div>
                <div class="metric-value" id="conversionRate">0%</div>
                <div class="metric-label">Taxa de Conversão</div>
                <div class="metric-change change-positive" id="conversionChange">+0%</div>
            </div>
        </div>
        
        <!-- Gráficos e Feed -->
        <div class="charts-container">
            <div class="chart-panel">
                <div class="chart-header">
                    <h3 class="chart-title">Atividade por Horário</h3>
                    <button onclick="refreshChart()" style="background: none; border: none; cursor: pointer;">🔄</button>
                </div>
                <div class="chart-body">
                    <canvas id="activityChart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <div class="chart-panel">
                <div class="chart-header">
                    <h3 class="chart-title">Atividade em Tempo Real</h3>
                    <span class="loading-spinner" id="feedSpinner" style="display: none;"></span>
                </div>
                <div class="realtime-feed" id="realtimeFeed">
                    <!-- Feed items será carregado via JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- Tabelas de Dados -->
        <div class="data-tables">
            <div class="table-panel">
                <div class="chart-header">
                    <h3 class="chart-title">Receitas Mais Populares</h3>
                </div>
                <table class="data-table" id="popularRecipesTable">
                    <thead>
                        <tr>
                            <th>Receita</th>
                            <th>Visualizações</th>
                            <th>Favoritos</th>
                            <th>Tendência</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <div class="table-panel">
                <div class="chart-header">
                    <h3 class="chart-title">Pesquisas Trending</h3>
                </div>
                <table class="data-table" id="trendingSearchesTable">
                    <thead>
                        <tr>
                            <th>Termo</th>
                            <th>Pesquisas</th>
                            <th>Resultados</th>
                            <th>CTR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="refresh-indicator" id="refreshIndicator">
        Atualizando dados...
    </div>
    
    <script>
        class AdvancedAnalytics {
            constructor() {
                this.autoRefresh = true;
                this.refreshInterval = 30000; // 30 segundos
                this.currentPeriod = 'today';
                this.refreshTimer = null;
                this.countdownTimer = null;
                
                this.init();
            }
            
            init() {
                this.setupEventListeners();
                this.loadAllData();
                this.startAutoRefresh();
            }
            
            setupEventListeners() {
                // Controles de período
                document.querySelectorAll('[data-period]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
                        e.target.classList.add('active');
                        this.currentPeriod = e.target.dataset.period;
                        this.loadAllData();
                    });
                });
                
                // Toggle auto-refresh
                document.getElementById('autoRefreshBtn').addEventListener('click', (e) => {
                    this.autoRefresh = !this.autoRefresh;
                    e.target.textContent = `Auto-refresh ${this.autoRefresh ? 'ON' : 'OFF'}`;
                    e.target.classList.toggle('active', this.autoRefresh);
                    
                    if (this.autoRefresh) {
                        this.startAutoRefresh();
                    } else {
                        this.stopAutoRefresh();
                    }
                });
            }
            
            async loadAllData() {
                this.showRefreshIndicator();
                
                try {
                    await Promise.all([
                        this.loadMetrics(),
                        this.loadPopularRecipes(),
                        this.loadTrendingSearches(),
                        this.loadRealtimeFeed()
                    ]);
                } catch (error) {
                    console.error('Erro ao carregar dados:', error);
                } finally {
                    this.hideRefreshIndicator();
                }
            }
            
            async loadMetrics() {
                try {
                    const response = await fetch(`api/admin_dashboard.php?action=overview&period=${this.currentPeriod}`);
                    const data = await response.json();
                    
                    if (data.error) throw new Error(data.error);
                    
                    this.updateMetrics(data);
                } catch (error) {
                    console.error('Erro ao carregar métricas:', error);
                }
            }
            
            updateMetrics(data) {
                const { totals } = data;
                
                document.getElementById('totalViews').textContent = this.formatNumber(totals.total_views || 0);
                document.getElementById('totalSearches').textContent = this.formatNumber(totals.total_searches || 0);
                document.getElementById('totalFavorites').textContent = this.formatNumber(totals.total_favorites || 0);
                document.getElementById('avgRating').textContent = parseFloat(totals.avg_rating || 0).toFixed(1);
                document.getElementById('activeUsers').textContent = this.formatNumber(totals.active_users || 0);
                document.getElementById('conversionRate').textContent = `${(totals.conversion_rate || 0).toFixed(1)}%`;
                
                // Simular mudanças (em um sistema real, viria do backend)
                this.updateChangeIndicators();
            }
            
            updateChangeIndicators() {
                const changes = {
                    viewsChange: Math.floor(Math.random() * 20) + 5,
                    searchesChange: Math.floor(Math.random() * 15) + 3,
                    favoritesChange: Math.floor(Math.random() * 10) + 2,
                    ratingChange: (Math.random() * 0.3).toFixed(1),
                    usersChange: Math.floor(Math.random() * 5) + 1,
                    conversionChange: (Math.random() * 2).toFixed(1)
                };
                
                Object.entries(changes).forEach(([key, value]) => {
                    const element = document.getElementById(key);
                    element.textContent = `+${value}${key.includes('Change') && !key.includes('rating') ? '%' : ''}`;
                });
            }
            
            async loadPopularRecipes() {
                try {
                    const response = await fetch(`api/admin_dashboard.php?action=popular_content&period=${this.currentPeriod}`);
                    const data = await response.json();
                    
                    if (data.error) throw new Error(data.error);
                    
                    this.updatePopularRecipesTable(data.most_viewed || []);
                } catch (error) {
                    console.error('Erro ao carregar receitas populares:', error);
                }
            }
            
            updatePopularRecipesTable(recipes) {
                const tbody = document.querySelector('#popularRecipesTable tbody');
                tbody.innerHTML = recipes.slice(0, 8).map((recipe, index) => `
                    <tr>
                        <td><strong>${recipe.title}</strong></td>
                        <td>${this.formatNumber(recipe.total_views || 0)}</td>
                        <td>${this.formatNumber(recipe.total_favorites || 0)}</td>
                        <td>
                            <span class="trend-indicator ${index < 3 ? 'trend-up' : 'trend-down'}">
                                ${index < 3 ? '↗' : '↘'} ${Math.floor(Math.random() * 20 + 5)}%
                            </span>
                        </td>
                    </tr>
                `).join('');
            }
            
            async loadTrendingSearches() {
                try {
                    const response = await fetch(`api/admin_dashboard.php?action=search_analytics&period=${this.currentPeriod}`);
                    const data = await response.json();
                    
                    if (data.error) throw new Error(data.error);
                    
                    this.updateTrendingSearchesTable(data.popular_searches || []);
                } catch (error) {
                    console.error('Erro ao carregar pesquisas trending:', error);
                }
            }
            
            updateTrendingSearchesTable(searches) {
                const tbody = document.querySelector('#trendingSearchesTable tbody');
                tbody.innerHTML = searches.slice(0, 8).map(search => `
                    <tr>
                        <td><strong>${search.search_query}</strong></td>
                        <td>${this.formatNumber(search.search_count || 0)}</td>
                        <td>${this.formatNumber(search.results_count || 0)}</td>
                        <td>${((search.clicks || 0) / (search.search_count || 1) * 100).toFixed(1)}%</td>
                    </tr>
                `).join('');
            }
            
            async loadRealtimeFeed() {
                // Simular feed em tempo real
                const feedItems = [
                    { type: 'view', title: 'Usuário visualizou "Bacalhau à Brás"', time: '2 min atrás' },
                    { type: 'search', title: 'Pesquisa por "pastéis de nata"', time: '3 min atrás' },
                    { type: 'favorite', title: 'Receita favoritada: "Francesinha"', time: '5 min atrás' },
                    { type: 'rating', title: 'Nova avaliação 5⭐ para "Caldo Verde"', time: '7 min atrás' },
                    { type: 'view', title: 'Usuário visualizou "Cataplana de Marisco"', time: '8 min atrás' },
                    { type: 'search', title: 'Pesquisa por "sobremesas portuguesas"', time: '10 min atrás' }
                ];
                
                this.updateRealtimeFeed(feedItems);
            }
            
            updateRealtimeFeed(items) {
                const feed = document.getElementById('realtimeFeed');
                feed.innerHTML = items.map(item => `
                    <div class="feed-item">
                        <div class="feed-icon ${item.type}">
                            ${this.getFeedIcon(item.type)}
                        </div>
                        <div class="feed-content">
                            <div class="feed-title">${item.title}</div>
                            <div class="feed-meta">${item.time}</div>
                        </div>
                    </div>
                `).join('');
            }
            
            getFeedIcon(type) {
                const icons = {
                    view: '👁️',
                    search: '🔍',
                    favorite: '❤️',
                    rating: '⭐'
                };
                return icons[type] || '📝';
            }
            
            formatNumber(num) {
                if (num >= 1000000) {
                    return (num / 1000000).toFixed(1) + 'M';
                } else if (num >= 1000) {
                    return (num / 1000).toFixed(1) + 'K';
                }
                return num.toString();
            }
            
            startAutoRefresh() {
                if (!this.autoRefresh) return;
                
                this.refreshTimer = setInterval(() => {
                    this.loadAllData();
                }, this.refreshInterval);
                
                this.startCountdown();
            }
            
            stopAutoRefresh() {
                if (this.refreshTimer) {
                    clearInterval(this.refreshTimer);
                    this.refreshTimer = null;
                }
                
                if (this.countdownTimer) {
                    clearInterval(this.countdownTimer);
                    this.countdownTimer = null;
                }
            }
            
            startCountdown() {
                let seconds = this.refreshInterval / 1000;
                
                this.countdownTimer = setInterval(() => {
                    seconds--;
                    document.getElementById('nextUpdate').textContent = `${seconds}s`;
                    
                    if (seconds <= 0) {
                        seconds = this.refreshInterval / 1000;
                    }
                }, 1000);
            }
            
            showRefreshIndicator() {
                document.getElementById('refreshIndicator').style.display = 'block';
            }
            
            hideRefreshIndicator() {
                document.getElementById('refreshIndicator').style.display = 'none';
            }
        }
        
        // Inicializar analytics quando a página carregar
        document.addEventListener('DOMContentLoaded', () => {
            new AdvancedAnalytics();
        });
        
        function refreshChart() {
            console.log('Atualizando gráfico...');
            // Implementar atualização do gráfico
        }
    </script>
</body>
</html>