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
                <span class="brand-icon">CG</span>
                ChefGuedes
                <div class="brand-underline"></div>
            </div>
            <div class="brand-tagline">Sabores Autênticos</div>
        </div>
        <div class="header-actions">
            <?php if($username): ?>
                <div class="user-welcome">
                    <span class="welcome-text">Olá, <span class="username-highlight"><?=htmlspecialchars($username)?></span></span>
                    <div class="user-status">Online</div>
                </div>
                <form method="post" action="logout.php" style="display:inline">
                    <button class="logout-btn premium" type="submit">
                        <span class="btn-icon">↪</span>
                        Logout
                    </button>
                </form>
            <?php else: ?>
                <a href="login.php" class="login-btn premium" style="text-decoration:none">
                    <span class="btn-icon"></span>
                    Entrar
                </a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Bem-vindo ao ChefGuedes</h1>
                <p class="hero-subtitle">Onde cada receita conta uma história e cada sabor cria memórias</p>
                <p class="hero-description">Explore receitas autênticas, compartilhe suas criações culinárias e descubra os favoritos da nossa comunidade gastronômica.</p>
                <div class="hero-actions">
                    <?php if($username): ?>
                        <a href="receitas.php" class="btn-hero-primary">Explorar Receitas</a>
                        <a href="receita.php" class="btn-hero-secondary">Adicionar Receita</a>
                    <?php else: ?>
                        <a href="register.php" class="btn-hero-primary">Começar Agora</a>
                        <a href="login.php" class="btn-hero-secondary">Entrar</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-visual">
                <div class="floating-emojis">
                    <span class="emoji-float">🍕</span>
                    <span class="emoji-float">☕</span>
                    <span class="emoji-float">�</span>
                    <span class="emoji-float">🍰</span>
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
                <div class="stat-number">4.8</div>
                <div class="stat-label">Avaliação</div>
            </div>
        </div>
    </section>

    <main class="content">
        <!-- Sistema de Pesquisa e Filtros -->
        <section class="search-section">
            <div class="search-container">
                <div class="search-header">
                    <h3>Encontre sua Receita Perfeita</h3>
                    <p>Explore nossa coleção de receitas portuguesas autênticas</p>
                </div>
                
                <div class="search-box">
                    <input type="text" id="recipeSearch" placeholder="Pesquisar receitas..." class="search-input">
                    <button class="search-btn">Pesquisar</button>
                </div>
                
                <div class="filter-categories">
                    <button class="category-btn active" data-category="all">Todas</button>
                    <button class="category-btn" data-category="pratos-principais">Pratos Principais</button>
                    <button class="category-btn" data-category="sopas">Sopas</button>
                    <button class="category-btn" data-category="peixe-marisco">Peixe & Marisco</button>
                    <button class="category-btn" data-category="doces">Doces</button>
                </div>
            </div>
        </section>

        <section class="featured-recipes">
            <div class="section-header">
                <h3>Receitas em Destaque</h3>
                <p>Descobertas culinárias selecionadas pela nossa comunidade</p>
            </div>
            
            <div class="recipes-grid">
                <div class="recipe-card premium glow">
                    <div class="recipe-image gradient-orange">
                        <div class="recipe-badge premium">Premium</div>
                        <div class="recipe-difficulty easy">Fácil</div>
                        <div class="recipe-icon">S</div>
                        <div class="recipe-overlay"></div>
                    </div>
                    <div class="recipe-content">
                        <h4>Salada de Quinoa Mediterrânea</h4>
                        <p class="recipe-description">Uma explosão de sabores frescos com quinoa, tomates cherry, azeitonas e queijo feta em um molho de limão siciliano</p>
                        <div class="recipe-meta">
                            <span class="prep-time">20min</span>
                            <span class="current-rating">4.8 <span class="rating-count">(234)</span></span>
                            <span class="servings">4 porções</span>
                        </div>
                        
                        <!-- Sistema de Avaliação Interativo -->
                        <?php if($username): ?>
                        <div class="rating-section">
                            <div class="rating-header">
                                <span class="rating-label">Sua Avaliação:</span>
                            </div>
                            <div class="star-rating" data-recipe-id="1">
                                <div class="stars-container">
                                    <span class="star" data-rating="1">★</span>
                                    <span class="star" data-rating="2">★</span>
                                    <span class="star" data-rating="3">★</span>
                                    <span class="star" data-rating="4">★</span>
                                    <span class="star" data-rating="5">★</span>
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
                                <span><a href="login.php">Entre</a> para avaliar esta receita</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="recipe-tags">
                            <span class="tag vegetarian">Vegetariana</span>
                            <span class="tag healthy">Saudável</span>
                            <span class="tag trending">Trending</span>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">CG</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef Maria</div>
                                <div class="chef-level">⭐ Nível Expert</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="recipe-card popular">
                    <div class="recipe-image gradient-red">
                        <div class="recipe-badge popular">Popular</div>
                        <div class="recipe-difficulty medium">Médio</div>
                        <div class="recipe-icon">F</div>
                        <div class="recipe-overlay"></div>
                    </div>
                    <div class="recipe-content">
                        <h4>Frango Assado com Ervas</h4>
                        <p class="recipe-description">Receita clássica com blend especial de ervas aromáticas, batatas douradas e molho de alecrim</p>
                        <div class="recipe-meta">
                            <span class="prep-time">1h 15min</span>
                            <span class="current-rating">4.9 <span class="rating-count">(456)</span></span>
                            <span class="servings">6 porções</span>
                        </div>
                        
                        <!-- Sistema de Avaliação Interativo -->
                        <?php if($username): ?>
                        <div class="rating-section">
                            <div class="rating-header">
                                <span class="rating-label">✨ Sua Avaliação:</span>
                            </div>
                            <div class="star-rating" data-recipe-id="2">
                                <div class="stars-container">
                                    <span class="star" data-rating="1">★</span>
                                    <span class="star" data-rating="2">★</span>
                                    <span class="star" data-rating="3">★</span>
                                    <span class="star" data-rating="4">★</span>
                                    <span class="star" data-rating="5">★</span>
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
                                <span><a href="login.php">Entre</a> para avaliar esta receita</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="recipe-tags">
                            <span class="tag protein">Rica em Proteína</span>
                            <span class="tag comfort">Caseira</span>
                            <span class="tag favorite">Favorita</span>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">CG</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef João</div>
                                <div class="chef-level">⭐ Nível Master</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="recipe-card featured exclusive">
                    <div class="recipe-image gradient-purple">
                        <div class="recipe-badge exclusive">Chef's Choice</div>
                        <div class="recipe-difficulty medium">Médio</div>
                        <div class="recipe-icon">B</div>
                        <div class="recipe-overlay"></div>
                    </div>
                    <div class="recipe-content">
                        <h4>Bolo de Chocolate Belga Premium</h4>
                        <p class="recipe-description">Sobremesa irresistível com chocolate 70% importado da Bélgica e ganache sedosa com toque de baunilha</p>
                        <div class="recipe-meta">
                            <span class="prep-time">45min</span>
                            <span class="current-rating">5.0 <span class="rating-count">(189)</span></span>
                            <span class="servings">8 porções</span>
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
                                <span><a href="login.php">Entre</a> para avaliar esta receita</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="recipe-tags">
                            <span class="tag dessert">🍰 Sobremesa</span>
                            <span class="tag special">Especial</span>
                            <span class="tag exclusive">Exclusiva</span>
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

        <!-- Receitas Portuguesas -->
        <section class="portuguese-recipes">
            <div class="section-header">
                <h3>Receitas Portuguesas Autênticas</h3>
                <p>Sabores tradicionais da nossa terra</p>
            </div>
            
            <div class="recipes-grid" id="portugueseRecipesGrid">
                <!-- Pratos Principais -->
                <div class="recipe-card" data-category="pratos-principais">
                    <div class="recipe-image">
                        <div class="recipe-badge">Tradicional</div>
                        <div class="recipe-difficulty medium">Médio</div>
                        <div class="recipe-icon">🍕</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Bacalhau à Brás</h4>
                        <p class="recipe-description">Bacalhau desfiado misturado com batata palha, ovos mexidos e cebola, salteado em azeite e alho.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">45min</span>
                            <span class="current-rating">4.7 <span class="rating-count">(324)</span></span>
                            <span class="servings">4 porções</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag traditional">Tradicional</span>
                            <span class="tag protein">Proteína</span>
                        </div>
                    </div>
                </div>

                <div class="recipe-card" data-category="pratos-principais">
                    <div class="recipe-image">
                        <div class="recipe-badge popular">Popular</div>
                        <div class="recipe-difficulty easy">Fácil</div>
                        <div class="recipe-icon">🍕</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Arroz de Pato</h4>
                        <p class="recipe-description">Arroz cozido com caldo de pato, misturado com carne desfiada e rodelas de chouriço, levado ao forno.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">90min</span>
                            <span class="current-rating">4.8 <span class="rating-count">(256)</span></span>
                            <span class="servings">6 porções</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag comfort">Caseira</span>
                            <span class="tag protein">Rica em Proteína</span>
                        </div>
                    </div>
                </div>

                <div class="recipe-card" data-category="pratos-principais">
                    <div class="recipe-image">
                        <div class="recipe-badge">Clássico</div>
                        <div class="recipe-difficulty medium">Médio</div>
                        <div class="recipe-icon">🍕</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Cozido à Portuguesa</h4>
                        <p class="recipe-description">Mistura de carnes, enchidos, batatas, couve e cenouras, cozidos juntos até ficarem tenros.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">2h</span>
                            <span class="current-rating">4.6 <span class="rating-count">(189)</span></span>
                            <span class="servings">8 porções</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag traditional">Tradicional</span>
                            <span class="tag comfort">Caseira</span>
                        </div>
                    </div>
                </div>

                <!-- Sopas -->
                <div class="recipe-card" data-category="sopas">
                    <div class="recipe-image">
                        <div class="recipe-badge popular">Popular</div>
                        <div class="recipe-difficulty easy">Fácil</div>
                        <div class="recipe-icon">🥣</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Caldo Verde</h4>
                        <p class="recipe-description">Sopa simples de couve-galega finamente cortada, batata e rodelas de chouriço.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">30min</span>
                            <span class="current-rating">4.9 <span class="rating-count">(412)</span></span>
                            <span class="servings">4 porções</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag healthy">Saudável</span>
                            <span class="tag vegetarian">Vegetariana</span>
                        </div>
                    </div>
                </div>

                <div class="recipe-card" data-category="sopas">
                    <div class="recipe-image">
                        <div class="recipe-badge">Tradicional</div>
                        <div class="recipe-difficulty medium">Médio</div>
                        <div class="recipe-icon">🥣</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Sopa da Pedra</h4>
                        <p class="recipe-description">Sopa espessa de feijão, carnes e enchidos, originária de Almeirim, cozida lentamente.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">75min</span>
                            <span class="current-rating">4.5 <span class="rating-count">(167)</span></span>
                            <span class="servings">6 porções</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag traditional">Tradicional</span>
                            <span class="tag protein">Rica em Proteína</span>
                        </div>
                    </div>
                </div>

                <!-- Peixe e Marisco -->
                <div class="recipe-card" data-category="peixe-marisco">
                    <div class="recipe-image">
                        <div class="recipe-badge">Tradicional</div>
                        <div class="recipe-difficulty easy">Fácil</div>
                        <div class="recipe-icon">🐟</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Sardinhas Assadas</h4>
                        <p class="recipe-description">Sardinhas temperadas com sal grosso e grelhadas, servidas com pão e batatas.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">20min</span>
                            <span class="current-rating">4.4 <span class="rating-count">(298)</span></span>
                            <span class="servings">4 porções</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag healthy">Saudável</span>
                            <span class="tag summer">Verão</span>
                        </div>
                    </div>
                </div>

                <div class="recipe-card" data-category="peixe-marisco">
                    <div class="recipe-image">
                        <div class="recipe-badge premium">Premium</div>
                        <div class="recipe-difficulty medium">Médio</div>
                        <div class="recipe-icon">🦐</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Arroz de Marisco</h4>
                        <p class="recipe-description">Arroz cozinhado num caldo de camarão e amêijoas, com tomate, pimentos e coentros.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">60min</span>
                            <span class="current-rating">4.8 <span class="rating-count">(234)</span></span>
                            <span class="servings">4 porções</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag premium">Premium</span>
                            <span class="tag seafood">Marisco</span>
                        </div>
                    </div>
                </div>

                <!-- Doces -->
                <div class="recipe-card" data-category="doces">
                    <div class="recipe-image">
                        <div class="recipe-badge popular">Popular</div>
                        <div class="recipe-difficulty medium">Médio</div>
                        <div class="recipe-icon">🍰</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Pastéis de Nata</h4>
                        <p class="recipe-description">Pequenas tartes de massa folhada com creme de ovos e natas, assadas até dourar.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">45min</span>
                            <span class="current-rating">4.9 <span class="rating-count">(567)</span></span>
                            <span class="servings">12 unidades</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag dessert">🍰 Sobremesa</span>
                            <span class="tag traditional">Tradicional</span>
                        </div>
                    </div>
                </div>

                <div class="recipe-card" data-category="doces">
                    <div class="recipe-image">
                        <div class="recipe-badge">Tradicional</div>
                        <div class="recipe-difficulty easy">Fácil</div>
                        <div class="recipe-icon">🍰</div>
                    </div>
                    <div class="recipe-content">
                        <h4>Arroz Doce</h4>
                        <p class="recipe-description">Arroz cozido em leite com açúcar, limão e canela.</p>
                        <div class="recipe-meta">
                            <span class="prep-time">35min</span>
                            <span class="current-rating">4.6 <span class="rating-count">(345)</span></span>
                            <span class="servings">6 porções</span>
                        </div>
                        <div class="recipe-tags">
                            <span class="tag dessert">🍰 Sobremesa</span>
                            <span class="tag comfort">Caseira</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção de Testemunhos -->
        <section class="testimonials-section">
            <div class="section-header">
                <h3>O que nossos Chefs dizem</h3>
                <p>Histórias reais de uma comunidade apaixonada por culinária</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-stars">★★★★★</div>
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
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">"Nunca pensei que cozinhar pudesse ser tão fácil e divertido. As instruções são claras e os resultados sempre surpreendem!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">CG</div>
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
                <div class="newsletter-icon">@</div>
                <h3>Receitas Exclusivas Semanais</h3>
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
                <h3>Pronto para Começar sua Jornada Culinária?</h3>
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