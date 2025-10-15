<?php
session_start();
$username = $_SESSION['username'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChefGuedes - Sabores Autênticos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/culinary.css">
    <link rel="stylesheet" href="css/animations.css">
</head>
<body>
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
        
        <div class="auth-container">
            <?php if ($username): ?>
                <div class="user-menu">
                    <span class="user-avatar"><?= strtoupper(substr($username, 0, 2)) ?></span>
                    <span class="user-name"><?= htmlspecialchars($username) ?></span>
                    <a href="logout.php" class="logout-btn">Sair</a>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="login.php" class="login-btn">Entrar</a>
                    <a href="register.php" class="register-btn">Registar</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="app-main">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-container">
                <div class="hero-content">
                    <h1 class="hero-title">Descubra Sabores Únicos</h1>
                    <p class="hero-subtitle">Receitas tradicionais portuguesas e criações modernas que despertam os sentidos</p>
                    
                    <div class="hero-buttons">
                        <a href="compartilhar.php" class="btn-cta-primary">Compartilhar Receita</a>
                        <a href="explorar.php" class="btn-cta-secondary">Explorar Mais</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="floating-emojis">
                        <span class="emoji-float"></span>
                        <span class="emoji-float"></span>
                        <span class="emoji-float"></span>
                        <span class="emoji-float"></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Search Section -->
        <section class="search-section">
            <div class="search-container">
                <h2>Encontre a Receita Perfeita</h2>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Procurar receitas, ingredientes..." class="search-input">
                    <button class="search-btn">Pesquisar</button>
                </div>
                
                <div class="category-filters">
                    <button class="filter-btn active" data-category="all">Todas</button>
                    <button class="filter-btn" data-category="pratos">Pratos</button>
                    <button class="filter-btn" data-category="sopas">Sopas</button>
                    <button class="filter-btn" data-category="marisco">Marisco</button>
                    <button class="filter-btn" data-category="doces">Doces</button>
                </div>
            </div>
        </section>

        <!-- Featured Recipes -->
        <section class="featured-section">
            <div class="container">
                <h2 class="section-title">Receitas em Destaque</h2>
                
                <div class="recipe-grid" id="recipeGrid">
                    <!-- Receita 1: Francesinha -->
                    <div class="recipe-card" data-category="pratos">
                        <div class="recipe-image">
                            <div class="recipe-badge popular">Popular</div>
                            <div class="recipe-difficulty easy">Fácil</div>
                            <div class="recipe-icon">P</div>
                        </div>
                        <div class="recipe-content">
                            <h4>Francesinha Tradicional</h4>
                            <p class="recipe-description">A icónica sandes portuense com linguiça, fiambre e molho especial.</p>
                            <div class="recipe-meta">
                                <span class="prep-time">45min</span>
                                <span class="current-rating">4.7 <span class="rating-count">(189)</span></span>
                                <span class="servings">4 porções</span>
                            </div>
                            <div class="recipe-tags">
                                <span class="tag traditional">Tradicional</span>
                                <span class="tag comfort">Caseira</span>
                            </div>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">CG</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef João</div>
                                <div class="chef-level">Nível Master</div>
                            </div>
                        </div>
                    </div>

                    <!-- Receita 2: Caldo Verde -->
                    <div class="recipe-card" data-category="sopas">
                        <div class="recipe-image">
                            <div class="recipe-badge trending">Trending</div>
                            <div class="recipe-difficulty easy">Fácil</div>
                            <div class="recipe-icon">S</div>
                        </div>
                        <div class="recipe-content">
                            <h4>Caldo Verde Minhoto</h4>
                            <p class="recipe-description">A sopa mais portuguesa, com couve galega, batata e chouriço.</p>
                            <div class="recipe-meta">
                                <span class="prep-time">30min</span>
                                <span class="current-rating">4.5 <span class="rating-count">(234)</span></span>
                                <span class="servings">6 porções</span>
                            </div>
                            <div class="recipe-tags">
                                <span class="tag traditional">Tradicional</span>
                                <span class="tag healthy">Saudável</span>
                            </div>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">Chef</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef Ana</div>
                                <div class="chef-level">Nível Legendary</div>
                            </div>
                        </div>
                    </div>

                    <!-- Receita 3: Arroz de Pato -->
                    <div class="recipe-card" data-category="pratos">
                        <div class="recipe-image">
                            <div class="recipe-badge popular">Popular</div>
                            <div class="recipe-difficulty easy">Fácil</div>
                            <div class="recipe-icon">P</div>
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
                                <span class="tag traditional">Tradicional</span>
                                <span class="tag comfort">Caseira</span>
                            </div>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">CG</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef Pedro</div>
                                <div class="chef-level">Nível Expert</div>
                            </div>
                        </div>
                    </div>

                    <!-- Receita 4: Sopa da Pedra -->
                    <div class="recipe-card" data-category="sopas">
                        <div class="recipe-image">
                            <div class="recipe-badge trending">Trending</div>
                            <div class="recipe-difficulty medium">Médio</div>
                            <div class="recipe-icon">S</div>
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
                                <span class="tag comfort">Caseira</span>
                            </div>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">Chef</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef Maria</div>
                                <div class="chef-level">Nível Master</div>
                            </div>
                        </div>
                    </div>

                    <!-- Receita 5: Pastéis de Nata -->
                    <div class="recipe-card" data-category="doces">
                        <div class="recipe-image">
                            <div class="recipe-badge featured">Destaque</div>
                            <div class="recipe-difficulty medium">Médio</div>
                            <div class="recipe-icon">D</div>
                        </div>
                        <div class="recipe-content">
                            <h4>Pastéis de Nata</h4>
                            <p class="recipe-description">Os famosos pastéis de nata portugueses, com massa folhada crocante e creme suave.</p>
                            <div class="recipe-meta">
                                <span class="prep-time">50min</span>
                                <span class="current-rating">4.9 <span class="rating-count">(312)</span></span>
                                <span class="servings">12 unidades</span>
                            </div>
                            <div class="recipe-tags">
                                <span class="tag dessert">Sobremesa</span>
                                <span class="tag traditional">Tradicional</span>
                            </div>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">Chef</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef Luís</div>
                                <div class="chef-level">Nível Legendary</div>
                            </div>
                        </div>
                    </div>

                    <!-- Receita 6: Cataplana de Marisco -->
                    <div class="recipe-card" data-category="marisco">
                        <div class="recipe-image">
                            <div class="recipe-badge premium">Premium</div>
                            <div class="recipe-difficulty hard">Difícil</div>
                            <div class="recipe-icon">M</div>
                        </div>
                        <div class="recipe-content">
                            <h4>Cataplana de Marisco</h4>
                            <p class="recipe-description">Cataplana tradicional algarvia com amêijoas, conquilhas, camarão e lingueirão.</p>
                            <div class="recipe-meta">
                                <span class="prep-time">40min</span>
                                <span class="current-rating">4.6 <span class="rating-count">(89)</span></span>
                                <span class="servings">4 porções</span>
                            </div>
                            <div class="recipe-tags">
                                <span class="tag seafood">Marisco</span>
                                <span class="tag traditional">Tradicional</span>
                            </div>
                        </div>
                        <div class="recipe-chef">
                            <div class="chef-avatar">Chef</div>
                            <div class="chef-info">
                                <div class="chef-name">Chef Carlos</div>
                                <div class="chef-level">Nível Expert</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="view-more">
                    <a href="explorar.php" class="btn-view-more">Ver Todas as Receitas</a>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="testimonials-section">
            <div class="container">
                <h2 class="section-title">O que dizem os nossos Chefs</h2>
                
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">★★★★★</div>
                        <p class="testimonial-text">"A variedade de receitas é impressionante. Desde pratos simples até creações gourmet, sempre encontro inspiração aqui!"</p>
                        <div class="testimonial-author">
                            <div class="author-avatar">Chef</div>
                            <div class="author-info">
                                <div class="author-name">Ana Costa</div>
                                <div class="author-title">Chef Profissional • 89 receitas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="newsletter-section">
            <div class="newsletter-container">
                <div class="newsletter-icon">@</div>
                <h3>Receitas Exclusivas Semanais</h3>
                <p>Receba as melhores receitas da semana diretamente no seu email, com dicas de chefs profissionais e ingredientes especiais.</p>
                <div class="newsletter-form">
                    <input type="email" placeholder="Seu melhor email..." class="newsletter-input">
                    <button class="newsletter-btn">
                        <span>Quero Receber!</span>
                        <span class="btn-icon">→</span>
                    </button>
                </div>
                <div class="newsletter-benefits">
                    <span class="benefit">Receitas exclusivas</span>
                    <span class="benefit">Dicas de profissionais</span>
                    <span class="benefit">Completamente grátis</span>
                </div>
            </div>
        </section>
    </main>

    <footer class="app-footer">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="brand">
                    <span class="brand-icon">CG</span>
                    ChefGuedes
                </div>
                <p>Compartilhando sabores autênticos e criando memórias culinárias inesquecíveis.</p>
            </div>
            
            <div class="footer-links">
                <div class="link-group">
                    <h4>Receitas</h4>
                    <a href="explorar.php">Explorar</a>
                    <a href="compartilhar.php">Compartilhar</a>
                    <a href="receitas.php">Todas</a>
                </div>
                
                <div class="link-group">
                    <h4>Comunidade</h4>
                    <a href="login.php">Entrar</a>
                    <a href="register.php">Registar</a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 ChefGuedes. Todos os direitos reservados.</p>
        </div>
    </footer>
</div>

<!-- Rating Modal -->
<div id="ratingModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Avaliar Receita</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="rating-stars" id="ratingStars">
                <span class="star" data-rating="1">★</span>
                <span class="star" data-rating="2">★</span>
                <span class="star" data-rating="3">★</span>
                <span class="star" data-rating="4">★</span>
                <span class="star" data-rating="5">★</span>
            </div>
            <div class="rating-text" id="ratingText">Clique para avaliar</div>
            <button class="rating-submit" id="submitRating" disabled>Enviar Avaliação</button>
        </div>
    </div>
</div>

<script src="js/index.js"></script>
</body>
</html>