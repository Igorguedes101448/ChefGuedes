<?php
session_start();
require_once 'includes/config.php';

$username = $_SESSION['username'] ?? null;
$user = null;

if ($username && isset($_SESSION['user_id'])) {
    $user = getCurrentUser();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChefGuedes - Sabores Autênticos</title>
    <link rel="stylesheet" href="css/hero-modern.css">
    <link rel="stylesheet" href="css/style-enhanced.css">
    <link rel="stylesheet" href="css/culinary-enhanced.css">
    <link rel="stylesheet" href="css/animations-enhanced.css">
</head>
<body>
<div class="app-shell">
    <!-- Hero Header com Background -->
    <div class="hero-wrapper">
        <!-- Header Transparente sobre Hero -->
        <header class="hero-header">
            <div class="header-container">
                <!-- Logo -->
                <div class="logo-brand">
                    <svg class="chef-icon" width="48" height="48" viewBox="0 0 48 48" fill="none">
                        <path d="M24 8C18 8 14 12 14 18V24H34V18C34 12 30 8 24 8Z" fill="currentColor" opacity="0.2"/>
                        <path d="M14 24H34V38C34 39.1 33.1 40 32 40H16C14.9 40 14 39.1 14 38V24Z" fill="currentColor"/>
                        <circle cx="20" cy="30" r="1.5" fill="white"/>
                        <circle cx="24" cy="32" r="1.5" fill="white"/>
                        <circle cx="28" cy="30" r="1.5" fill="white"/>
                    </svg>
                    <span class="brand-text">O Chefeguedes</span>
                </div>

                <!-- Search Bar Central -->
                <div class="hero-search-container">
                    <form action="explorar.php" method="GET" class="hero-search-form">
                        <input 
                            type="text" 
                            name="search" 
                            class="hero-search-input" 
                            placeholder="Pesquisar receitas..."
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        >
                        <button type="submit" class="hero-search-btn">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Navigation Links -->
                <nav class="hero-nav">
                    <a href="explorar.php" class="nav-item">Receitas</a>
                    <a href="grupos_familiares.php" class="nav-item">Comunidade</a>
                    <a href="suporte.php" class="nav-item">Sobre Nós</a>
                    <a href="suporte.php" class="nav-item">Contacto</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="hero-auth">
                    <!-- Theme Switcher -->
                    <div class="theme-switcher-modern">
                        <button class="theme-toggle-btn" data-theme="light" title="Tema Claro">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <circle cx="10" cy="10" r="4" fill="currentColor"/>
                                <path d="M10 1v2M10 17v2M19 10h-2M3 10H1M16.364 3.636l-1.414 1.414M5.05 14.95l-1.414 1.414M16.364 16.364l-1.414-1.414M5.05 5.05L3.636 3.636" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <button class="theme-toggle-btn" data-theme="dark" title="Tema Escuro">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>
                    
                    <?php if ($username): ?>
                        <a href="profile.php" class="user-profile-link">
                            <span class="user-avatar-small">
                                <?php if ($user && !empty($user['avatar']) && file_exists($user['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= htmlspecialchars($username) ?>">
                                <?php else: ?>
                                    <?= strtoupper(substr($username, 0, 1)) ?>
                                <?php endif; ?>
                            </span>
                        </a>
                        <a href="logout.php" class="btn-header-secondary">Sair</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-header-secondary">Entrar</a>
                        <a href="register.php" class="btn-header-primary">Registar</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Hero Content -->
        <section class="hero-main">
            <div class="hero-overlay"></div>
            <div class="hero-background"></div>
            
            <div class="hero-content-center">
                <!-- Chef Hat Icon -->
                <div class="hero-icon">
                    <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                        <path d="M60 20C42 20 35 30 35 45V60H85V45C85 30 78 20 60 20Z" fill="white" opacity="0.2"/>
                        <path d="M35 60H85V95C85 97.75 82.75 100 80 100H40C37.25 100 35 97.75 35 95V60Z" fill="white"/>
                        <circle cx="50" cy="75" r="3" fill="#D4A574"/>
                        <circle cx="60" cy="80" r="3" fill="#D4A574"/>
                        <circle cx="70" cy="75" r="3" fill="#D4A574"/>
                    </svg>
                </div>

                <h1 class="hero-title-main">Bem-vindo ao O Chefeguedes</h1>
                <p class="hero-subtitle-main">
                    Partilhe, explore e descubra receitas culinárias numa comunidade apaixonada por cozinha
                </p>

                <div class="hero-cta">
                    <a href="explorar.php" class="btn-hero-primary">Explorar Receitas</a>
                </div>
            </div>
        </section>
    </div>

    <main class="app-main">

        <!-- Featured Recipes -->
        <section class="featured-section">
            <div class="container">
                <h2 class="section-title animate-fade-in">Receitas em Destaque</h2>
                
                <div class="recipe-grid cascade-fade-in" id="recipeGrid">
                    <!-- Receita 1: Francesinha -->
                    <div class="recipe-card hover-lift" data-category="pratos">
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
                    <div class="recipe-card hover-lift" data-category="sopas">
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
                    <div class="recipe-card hover-lift" data-category="pratos">
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
                    <div class="recipe-card hover-lift" data-category="sopas">
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
                    <div class="recipe-card hover-lift" data-category="doces">
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
                    <div class="recipe-card hover-lift" data-category="marisco">
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
        <section class="testimonials-section animate-fade-in">
            <div class="container">
                <h2 class="section-title">O que dizem os nossos Chefs</h2>
                
                <div class="testimonials-grid">
                    <div class="testimonial-card hover-lift">
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
        <section class="newsletter-section animate-scale-in">
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
                    <span class="benefit">✓ Receitas exclusivas</span>
                    <span class="benefit">✓ Dicas de profissionais</span>
                    <span class="benefit">✓ Completamente grátis</span>
                </div>
            </div>
        </section>
    </main>
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

<script src="js/theme.js"></script>
<script src="js/index.js"></script>
</body>
</html>