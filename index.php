<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChefGuedes - Compartilhe Sabores, Crie Memórias</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/culinary.css">
    <link rel="stylesheet" href="css/animations.css">
        </style>
</head>
<body>
<?php
session_start();
$username = $_SESSION['username'] ?? null;
?>
<div class="app-shell">
    <header class="app-header">
        <div class="header-bg"></div>
        <div class="brand-container">
            <div class="brand">
                <span class="brand-icon">👨‍🍳</span>
                ChefGuedes
                <div class="brand-underline"></div>
            </div>
            <div class="brand-tagline">Sabores Autênticos</div>
        </div>
        <div class="header-actions">
            <?php if($username): ?>
                <div class="user-welcome">
                    <span class="welcome-text">Olá, <span class="username-highlight"><?=htmlspecialchars($username)?></span></span>
                    <div class="user-status">🟢 Online</div>
                </div>
                <form method="post" action="logout.php" style="display:inline">
                    <button class="logout-btn premium" type="submit">
                        <span class="btn-icon">🚪</span>
                        Logout
                    </button>
                </form>
            <?php else: ?>
                <a href="login.php" class="login-btn premium" style="text-decoration:none">
                    <span class="btn-icon">✨</span>
                    Entrar
                </a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>🍳 Bem-vindo ao ChefGuedes</h1>
                <p class="hero-subtitle">Onde cada receita conta uma história e cada sabor cria memórias</p>
                <p class="hero-description">Explore receitas autênticas, compartilhe suas criações culinárias e descubra os favoritos da nossa comunidade gastronômica.</p>
                <div class="hero-actions">
                    <?php if($username): ?>
                        <a href="receitas.php" class="btn-hero-primary">🔍 Explorar Receitas</a>
                        <a href="receita.php" class="btn-hero-secondary">➕ Adicionar Receita</a>
                    <?php else: ?>
                        <a href="register.php" class="btn-hero-primary">🚀 Começar Agora</a>
                        <a href="login.php" class="btn-hero-secondary">👨‍🍳 Entrar</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-visual">
                <div class="floating-emojis">
                    <span class="emoji-float">🍕</span>
                    <span class="emoji-float">🍝</span>
                    <span class="emoji-float">🥘</span>
                    <span class="emoji-float">🍰</span>
                    <span class="emoji-float">🥗</span>
                    <span class="emoji-float">🍳</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Estatísticas -->
    <section class="stats-section">
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number">1,200+</div>
                <div class="stat-label">Receitas</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">850+</div>
                <div class="stat-label">Chefs</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">15,000+</div>
                <div class="stat-label">Pratos Feitos</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">4.8⭐</div>
                <div class="stat-label">Avaliação</div>
            </div>
        </div>
    </section>

    <main class="content">
        <section class="featured-recipes">
            <div class="section-header">
                <h3>🌟 Receitas em Destaque</h3>
                <p>Descobertas culinárias selecionadas pela nossa comunidade</p>
            </div>
            
            <div class="recipes-grid">
                <div class="recipe-card premium glow">
                    <div class="recipe-image gradient-orange">
                        <div class="recipe-badge premium">✨ Premium</div>
                        <div class="recipe-difficulty easy">🟢 Fácil</div>
                        <div class="recipe-icon">🥗</div>
                        <div class="recipe-overlay"></div>
                    </div>
                    <div class="recipe-content">
                        <h4>🥗 Salada de Quinoa Mediterrânea</h4>
                        <p class="recipe-description">Uma explosão de sabores frescos com quinoa, tomates cherry, azeitonas e queijo feta em um molho de limão siciliano</p>
                        <div class="recipe-meta">
                            <span class="prep-time">⏱️ 20min</span>
                            <span class="current-rating">⭐ 4.8 <span class="rating-count">(234)</span></span>
                            <span class="servings">👥 4 porções</span>
                        </div>
                        
                        <!-- Sistema de Avaliação Interativo -->
                        <?php if($username): ?>
                        <div class="rating-section">
                            <div class="rating-header">
                                <span class="rating-label">✨ Sua Avaliação:</span>
                            </div>
                            <div class="star-rating" data-recipe-id="1">
                                <div class="stars-container">
                                    <span class="star" data-rating="1">⭐</span>
                                    <span class="star" data-rating="2">⭐</span>
                                    <span class="star" data-rating="3">⭐</span>
                                    <span class="star" data-rating="4">⭐</span>
                                    <span class="star" data-rating="5">⭐</span>
                                </div>
                                <div class="rating-feedback">
                                    <span class="rating-text">Clique para avaliar</span>
                                    <button class="rating-submit" style="display:none;">Confirmar</button>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="rating-section guest">
                            <div class="rating-login-prompt">
                                <span>💫 <a href="login.php">Entre</a> para avaliar esta receita</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="recipe-tags">
                            <span class="tag vegetarian">🌱 Vegetariana</span>
                            <span class="tag healthy">💚 Saudável</span>
                            <span class="tag trending">🔥 Trending</span>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">👩‍🍳</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef Maria</div>
                                <div class="chef-level">⭐ Nível Expert</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="recipe-card popular">
                    <div class="recipe-image gradient-red">
                        <div class="recipe-badge popular">🔥 Popular</div>
                        <div class="recipe-difficulty medium">🟡 Médio</div>
                        <div class="recipe-icon">🍗</div>
                        <div class="recipe-overlay"></div>
                    </div>
                    <div class="recipe-content">
                        <h4>🍗 Frango Assado com Ervas</h4>
                        <p class="recipe-description">Receita clássica com blend especial de ervas aromáticas, batatas douradas e molho de alecrim</p>
                        <div class="recipe-meta">
                            <span class="prep-time">⏱️ 1h 15min</span>
                            <span class="current-rating">⭐ 4.9 <span class="rating-count">(456)</span></span>
                            <span class="servings">👥 6 porções</span>
                        </div>
                        
                        <!-- Sistema de Avaliação Interativo -->
                        <?php if($username): ?>
                        <div class="rating-section">
                            <div class="rating-header">
                                <span class="rating-label">✨ Sua Avaliação:</span>
                            </div>
                            <div class="star-rating" data-recipe-id="2">
                                <div class="stars-container">
                                    <span class="star" data-rating="1">⭐</span>
                                    <span class="star" data-rating="2">⭐</span>
                                    <span class="star" data-rating="3">⭐</span>
                                    <span class="star" data-rating="4">⭐</span>
                                    <span class="star" data-rating="5">⭐</span>
                                </div>
                                <div class="rating-feedback">
                                    <span class="rating-text">Clique para avaliar</span>
                                    <button class="rating-submit" style="display:none;">Confirmar</button>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="rating-section guest">
                            <div class="rating-login-prompt">
                                <span>💫 <a href="login.php">Entre</a> para avaliar esta receita</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="recipe-tags">
                            <span class="tag protein">🥩 Rica em Proteína</span>
                            <span class="tag comfort">🏠 Caseira</span>
                            <span class="tag favorite">❤️ Favorita</span>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">👨‍🍳</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef João</div>
                                <div class="chef-level">⭐ Nível Master</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="recipe-card featured exclusive">
                    <div class="recipe-image gradient-purple">
                        <div class="recipe-badge exclusive">👑 Chef's Choice</div>
                        <div class="recipe-difficulty medium">🟡 Médio</div>
                        <div class="recipe-icon">🍰</div>
                        <div class="recipe-overlay"></div>
                    </div>
                    <div class="recipe-content">
                        <h4>🍰 Bolo de Chocolate Belga Premium</h4>
                        <p class="recipe-description">Sobremesa irresistível com chocolate 70% importado da Bélgica e ganache sedosa com toque de baunilha</p>
                        <div class="recipe-meta">
                            <span class="prep-time">⏱️ 45min</span>
                            <span class="current-rating">⭐ 5.0 <span class="rating-count">(189)</span></span>
                            <span class="servings">👥 8 porções</span>
                        </div>
                        
                        <!-- Sistema de Avaliação Interativo -->
                        <?php if($username): ?>
                        <div class="rating-section">
                            <div class="rating-header">
                                <span class="rating-label">✨ Sua Avaliação:</span>
                            </div>
                            <div class="star-rating" data-recipe-id="3">
                                <div class="stars-container">
                                    <span class="star" data-rating="1">⭐</span>
                                    <span class="star" data-rating="2">⭐</span>
                                    <span class="star" data-rating="3">⭐</span>
                                    <span class="star" data-rating="4">⭐</span>
                                    <span class="star" data-rating="5">⭐</span>
                                </div>
                                <div class="rating-feedback">
                                    <span class="rating-text">Clique para avaliar</span>
                                    <button class="rating-submit" style="display:none;">Confirmar</button>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="rating-section guest">
                            <div class="rating-login-prompt">
                                <span>💫 <a href="login.php">Entre</a> para avaliar esta receita</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="recipe-tags">
                            <span class="tag dessert">🧁 Sobremesa</span>
                            <span class="tag special">✨ Especial</span>
                            <span class="tag exclusive">👑 Exclusiva</span>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">🧑‍🍳</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef Ana</div>
                                <div class="chef-level">⭐ Nível Legendary</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="view-more">
                <a href="receitas.php" class="btn-view-more">
                    Ver Todas as Receitas
                    <span class="arrow">→</span>
                </a>
            </div>
        </section>

        <!-- Seção de Testemunhos -->
        <section class="testimonials-section">
            <div class="section-header">
                <h3>💬 O que nossos Chefs dizem</h3>
                <p>Histórias reais de uma comunidade apaixonada por culinária</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                    <p class="testimonial-text">"O ChefGuedes transformou minha paixão por cozinhar em algo extraordinário. As receitas são autênticas e a comunidade é incrível!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">👩‍🍳</div>
                        <div class="author-info">
                            <div class="author-name">Maria Silva</div>
                            <div class="author-title">Chef Caseira • 127 receitas</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                    <p class="testimonial-text">"Nunca pensei que cozinhar pudesse ser tão fácil e divertido. As instruções são claras e os resultados sempre surpreendem!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">👨‍🍳</div>
                        <div class="author-info">
                            <div class="author-name">João Santos</div>
                            <div class="author-title">Iniciante • 23 pratos feitos</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
                    <p class="testimonial-text">"A variedade de receitas é impressionante. Desde pratos simples até creações gourmet, sempre encontro inspiração aqui!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">🧑‍🍳</div>
                        <div class="author-info">
                            <div class="author-name">Ana Costa</div>
                            <div class="author-title">Chef Profissional • 89 receitas</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="newsletter-section">
            <div class="newsletter-content">
                <div class="newsletter-icon">📧</div>
                <h3>🍴 Receitas Exclusivas Semanais</h3>
                <p>Receba as melhores receitas da semana diretamente no seu email, com dicas de chefs profissionais e ingredientes especiais.</p>
                <div class="newsletter-form">
                    <input type="email" placeholder="Seu melhor email..." class="newsletter-input">
                    <button class="newsletter-btn">
                        <span>Quero Receber!</span>
                        <span class="btn-icon">🚀</span>
                    </button>
                </div>
                <div class="newsletter-benefits">
                    <span class="benefit">✨ Receitas exclusivas</span>
                    <span class="benefit">🎯 Dicas de profissionais</span>
                    <span class="benefit">🆓 Completamente grátis</span>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="cta-section">
            <div class="cta-content">
                <h3>🚀 Pronto para Começar sua Jornada Culinária?</h3>
                <p>Junte-se à nossa comunidade de chefs apaixonados e transforme sua cozinha em um laboratório de sabores!</p>
                <?php if(!$username): ?>
                    <div class="cta-actions">
                        <a href="register.php" class="btn-cta-primary">Criar Conta Grátis</a>
                        <a href="login.php" class="btn-cta-secondary">Já tenho conta</a>
                    </div>
                <?php else: ?>
                    <div class="cta-actions">
                        <a href="receita.php" class="btn-cta-primary">Compartilhar Receita</a>
                        <a href="receitas.php" class="btn-cta-secondary">Explorar Mais</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<script src="js/index.js"></script>
</body>
</html>