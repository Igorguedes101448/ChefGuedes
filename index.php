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
            <!-- Theme Switcher -->
            <div class="theme-switcher">
                <button class="theme-btn" data-theme="light" title="Tema Claro">
                    <span class="theme-icon">☀️</span>
                </button>
                <button class="theme-btn" data-theme="dark" title="Tema Escuro">
                    <span class="theme-icon">🌙</span>
                </button>
            </div>
            
            <?php if ($username): ?>
                <div class="user-menu">
                    <a href="profile.php" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
                        <span class="user-avatar">
                            <?php if ($user && !empty($user['avatar']) && file_exists($user['avatar'])): ?>
                                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= htmlspecialchars($username) ?>">
                            <?php else: ?>
                                <?= strtoupper(substr($username, 0, 2)) ?>
                            <?php endif; ?>
                        </span>
                        <span class="user-name"><?= htmlspecialchars($username) ?></span>
                    </a>
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

    <!-- Quick Navigation -->
    <nav class="quick-nav">
        <div class="container">
            <div class="nav-links">
                <a href="explorar.php" class="nav-link primary">
                    <i class="icon-search"></i> Explorar Receitas
                </a>
                <?php if ($username): ?>
                    <a href="dashboard.php" class="nav-link">
                        <i class="icon-chart"></i> Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="app-main">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-container">
                <div class="hero-content">
                    <h1 class="hero-title">Descubra Sabores Únicos</h1>
                    <p class="hero-subtitle">Receitas tradicionais portuguesas e criações modernas que despertam os sentidos</p>
                    
                    <div class="hero-buttons">
                        <a href="explorar.php" class="btn-cta-primary">Explorar Mais</a>
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
        <section class="search-section" style="padding: 60px 0; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
            <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <h2 style="text-align: center; font-size: 2.2rem; color: #2c3e50; margin-bottom: 15px; font-family: 'Lora', serif;">Encontre a Receita Perfeita</h2>
                <p style="text-align: center; color: #666; margin-bottom: 40px; font-size: 1.1rem;">Pesquise por ingredientes, pratos ou categorias</p>
                
                <!-- Search Bar -->
                <div class="search-bar" style="max-width: 800px; margin: 0 auto 40px; position: relative;">
                    <input type="text" id="recipeSearch" placeholder="Pesquisar receitas, ingredientes..." 
                           style="width: 100%; padding: 18px 180px 18px 25px; border: 2px solid #e0e0e0; border-radius: 50px; font-size: 1rem; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                    <button class="search-btn" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); padding: 12px 35px; background: #c96b3e; color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 1rem;">
                        Pesquisar
                    </button>
                </div>
                
                <!-- Category Filters -->
                <div class="category-filters" style="margin-bottom: 30px;">
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                        <button class="category-filter-btn active" data-category="todas" 
                                style="padding: 12px 28px; border: none; border-radius: 50px; background: #c96b3e; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 0.95rem; box-shadow: 0 4px 10px rgba(201,107,62,0.3);">
                            Todas
                        </button>
                        <button class="category-filter-btn" data-category="prato principal" 
                                style="padding: 12px 28px; border: 2px solid #c96b3e; border-radius: 50px; background: white; color: #c96b3e; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 0.95rem;">
                            Pratos Principais
                        </button>
                        <button class="category-filter-btn" data-category="bebidas" 
                                style="padding: 12px 28px; border: 2px solid #c96b3e; border-radius: 50px; background: white; color: #c96b3e; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 0.95rem;">
                            Bebidas
                        </button>
                    </div>
                </div>
                
                <!-- Difficulty & Diet Filters -->
                <div class="additional-filters" style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                    <button class="diet-filter-btn" data-filter="facil" 
                            style="padding: 10px 22px; border: 2px solid #ddd; border-radius: 50px; background: white; color: #666; font-weight: 500; cursor: pointer; transition: all 0.3s; font-size: 0.9rem;">
                        Fácil
                    </button>
                    <button class="diet-filter-btn" data-filter="medio" 
                            style="padding: 10px 22px; border: 2px solid #ddd; border-radius: 50px; background: white; color: #666; font-weight: 500; cursor: pointer; transition: all 0.3s; font-size: 0.9rem;">
                        Médio
                    </button>
                    <button class="diet-filter-btn" data-filter="dificil" 
                            style="padding: 10px 22px; border: 2px solid #ddd; border-radius: 50px; background: white; color: #666; font-weight: 500; cursor: pointer; transition: all 0.3s; font-size: 0.9rem;">
                        Difícil
                    </button>
                    <button class="diet-filter-btn" data-filter="vegetariano" 
                            style="padding: 10px 22px; border: 2px solid #ddd; border-radius: 50px; background: white; color: #666; font-weight: 500; cursor: pointer; transition: all 0.3s; font-size: 0.9rem;">
                        Vegetariano
                    </button>
                    <button class="diet-filter-btn" data-filter="vegano" 
                            style="padding: 10px 22px; border: 2px solid #ddd; border-radius: 50px; background: white; color: #666; font-weight: 500; cursor: pointer; transition: all 0.3s; font-size: 0.9rem;">
                        Vegano
                    </button>
                    <button class="diet-filter-btn" data-filter="sem-gluten" 
                            style="padding: 10px 22px; border: 2px solid #ddd; border-radius: 50px; background: white; color: #666; font-weight: 500; cursor: pointer; transition: all 0.3s; font-size: 0.9rem;">
                        Sem Glúten
                    </button>
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

<script>
// Interatividade dos filtros de pesquisa
document.addEventListener('DOMContentLoaded', function() {
    // Filtros de categoria
    const categoryBtns = document.querySelectorAll('.category-filter-btn');
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remover active de todos
            categoryBtns.forEach(b => {
                b.classList.remove('active');
                b.style.background = 'white';
                b.style.color = '#c96b3e';
                b.style.border = '2px solid #c96b3e';
                b.style.boxShadow = 'none';
            });
            
            // Adicionar active ao clicado
            this.classList.add('active');
            this.style.background = '#c96b3e';
            this.style.color = 'white';
            this.style.border = 'none';
            this.style.boxShadow = '0 4px 10px rgba(201,107,62,0.3)';
        });
        
        // Hover effect
        btn.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.background = '#fef5f0';
                this.style.transform = 'translateY(-2px)';
            }
        });
        
        btn.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.background = 'white';
                this.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Filtros de dificuldade/dieta
    const dietBtns = document.querySelectorAll('.diet-filter-btn');
    dietBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Toggle active
            if (this.classList.contains('active')) {
                this.classList.remove('active');
                this.style.background = 'white';
                this.style.color = '#666';
                this.style.borderColor = '#ddd';
            } else {
                this.classList.add('active');
                this.style.background = '#c96b3e';
                this.style.color = 'white';
                this.style.borderColor = '#c96b3e';
            }
        });
        
        // Hover effect
        btn.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.background = '#f5f5f5';
                this.style.transform = 'translateY(-2px)';
            }
        });
        
        btn.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.background = 'white';
                this.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Search input focus effect
    const searchInput = document.getElementById('recipeSearch');
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            this.style.borderColor = '#c96b3e';
            this.style.boxShadow = '0 6px 20px rgba(201,107,62,0.2)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.borderColor = '#e0e0e0';
            this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.08)';
        });
    }
    
    // Search button hover
    const searchBtn = document.querySelector('.search-btn');
    if (searchBtn) {
        searchBtn.addEventListener('mouseenter', function() {
            this.style.background = '#b55d33';
            this.style.transform = 'translateY(-50%) scale(1.05)';
        });
        
        searchBtn.addEventListener('mouseleave', function() {
            this.style.background = '#c96b3e';
            this.style.transform = 'translateY(-50%) scale(1)';
        });
    }
});
</script>

<script src="js/theme.js"></script>
<script src="js/index.js"></script>
</body>
</html>