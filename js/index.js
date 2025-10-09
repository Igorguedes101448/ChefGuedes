// JavaScript para melhorar a interatividade da página principal
document.addEventListener('DOMContentLoaded', function() {
    
    // Sistema de partículas interativas
    function createInteractiveParticles() {
        const container = document.body;
        const particles = [];
        
        for (let i = 0; i < 15; i++) {
            const particle = document.createElement('div');
            particle.className = 'interactive-particle';
            particle.style.cssText = `
                position: fixed;
                width: 6px;
                height: 6px;
                background: radial-gradient(circle, rgba(255, 165, 0, 0.8), rgba(255, 107, 107, 0.4));
                border-radius: 50%;
                pointer-events: none;
                z-index: 1;
                box-shadow: 0 0 10px rgba(255, 165, 0, 0.5);
                left: ${Math.random() * window.innerWidth}px;
                top: ${Math.random() * window.innerHeight}px;
            `;
            
            container.appendChild(particle);
            particles.push({
                element: particle,
                x: Math.random() * window.innerWidth,
                y: Math.random() * window.innerHeight,
                vx: (Math.random() - 0.5) * 2,
                vy: (Math.random() - 0.5) * 2,
                life: Math.random() * 100
            });
        }
        
        function updateParticles() {
            particles.forEach(particle => {
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.life += 1;
                
                // Bounce off edges
                if (particle.x <= 0 || particle.x >= window.innerWidth) particle.vx *= -1;
                if (particle.y <= 0 || particle.y >= window.innerHeight) particle.vy *= -1;
                
                // Opacity based on life
                const opacity = Math.sin(particle.life * 0.05) * 0.5 + 0.5;
                
                particle.element.style.left = particle.x + 'px';
                particle.element.style.top = particle.y + 'px';
                particle.element.style.opacity = opacity * 0.3;
            });
            
            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                requestAnimationFrame(updateParticles);
            }
        }
        
        updateParticles();
    }
    
    // Ativar partículas apenas em dispositivos com boa performance
    if (window.innerWidth > 768 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        createInteractiveParticles();
    }
    
    // Animação de contagem para as estatísticas
    function animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Formatação especial para diferentes tipos de números
            if (element.textContent.includes('+')) {
                element.textContent = Math.floor(current).toLocaleString() + '+';
            } else if (element.textContent.includes('⭐')) {
                element.textContent = current.toFixed(1) + '⭐';
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    }
    
    // Observer para detectar quando a seção de stats entra na view
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                
                statNumbers.forEach((stat, index) => {
                    const originalText = stat.textContent;
                    let targetNumber;
                    
                    // Extrair número do texto
                    if (originalText.includes('4.8⭐')) {
                        targetNumber = 4.8;
                    } else {
                        targetNumber = parseInt(originalText.replace(/[^\d]/g, ''));
                    }
                    
                    // Delay baseado no índice para efeito cascata
                    setTimeout(() => {
                        animateCounter(stat, targetNumber, 1500);
                    }, index * 200);
                });
                
                // Remover observer após executar
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    // Aplicar observer à seção de stats
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
    
    // Efeito de parallax suave no scroll
    let ticking = false;
    
    function updateParallax() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.floating-emojis .emoji-float');
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.1 + (index * 0.05);
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px) rotate(${scrolled * 0.1}deg)`;
        });
        
        ticking = false;
    }
    
    function requestParallaxUpdate() {
        if (!ticking) {
            requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }
    
    // Aplicar parallax apenas em dispositivos com boa performance
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        window.addEventListener('scroll', requestParallaxUpdate);
    }
    
    // Efeito hover aprimorado para cards de receitas
    const recipeCards = document.querySelectorAll('.recipe-card');
    
    recipeCards.forEach(card => {
        card.addEventListener('mouseenter', function(e) {
            // Adicionar efeito de tilt 3D
            const rect = card.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            card.addEventListener('mousemove', function(e) {
                const mouseX = e.clientX - centerX;
                const mouseY = e.clientY - centerY;
                
                const rotateX = (mouseY / rect.height) * 10;
                const rotateY = -(mouseX / rect.width) * 10;
                
                card.style.transform = `
                    translateY(-10px) 
                    rotateX(${rotateX}deg) 
                    rotateY(${rotateY}deg)
                    scale(1.02)
                `;
            });
        });
        
        card.addEventListener('mouseleave', function() {
            card.style.transform = 'translateY(0) rotateX(0) rotateY(0) scale(1)';
            card.removeEventListener('mousemove', arguments.callee);
        });
    });
    
    // Smooth scroll para links internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Efeito de typing mais realista (com velocidade variável)
    function improvedTypingEffect(element, text, speed = 100) {
        element.textContent = '';
        element.style.borderRight = '3px solid white';
        
        let i = 0;
        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                
                // Velocidade variável para parecer mais humano
                const randomSpeed = speed + (Math.random() * 50 - 25);
                setTimeout(type, randomSpeed);
            } else {
                // Piscar cursor por alguns segundos
                setTimeout(() => {
                    element.style.borderRight = 'none';
                }, 3000);
            }
        }
        
        setTimeout(type, 1000); // Delay inicial
    }
    
    // Aplicar efeito de typing ao título principal
    const heroTitle = document.querySelector('.hero-text h1');
    if (heroTitle && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const originalText = heroTitle.textContent;
        improvedTypingEffect(heroTitle, originalText, 80);
    }
    
    // Easter egg: Konami code
    let konamiCode = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];
    let konamiIndex = 0;
    
    document.addEventListener('keydown', function(e) {
        if (e.keyCode === konamiCode[konamiIndex]) {
            konamiIndex++;
            if (konamiIndex === konamiCode.length) {
                // Ativar modo "chef especial"
                document.body.style.filter = 'hue-rotate(180deg)';
                
                // Adicionar emojis especiais
                const specialEmojis = ['👨‍🍳', '🔥', '⭐', '🎉'];
                for (let i = 0; i < 20; i++) {
                    setTimeout(() => {
                        createFloatingEmoji(specialEmojis[Math.floor(Math.random() * specialEmojis.length)]);
                    }, i * 100);
                }
                
                konamiIndex = 0;
                
                // Voltar ao normal após 5 segundos
                setTimeout(() => {
                    document.body.style.filter = 'none';
                }, 5000);
            }
        } else {
            konamiIndex = 0;
        }
    });
    
    function createFloatingEmoji(emoji) {
        const emojiElement = document.createElement('div');
        emojiElement.textContent = emoji;
        emojiElement.style.cssText = `
            position: fixed;
            font-size: 2rem;
            pointer-events: none;
            z-index: 1000;
            left: ${Math.random() * window.innerWidth}px;
            top: ${window.innerHeight + 50}px;
            animation: floatUp 3s ease-out forwards;
        `;
        
        document.body.appendChild(emojiElement);
        
        setTimeout(() => {
            emojiElement.remove();
        }, 3000);
    }
    
    // Adicionar CSS para animação floatUp
    const style = document.createElement('style');
    style.textContent = `
        @keyframes floatUp {
            to {
                transform: translateY(-${window.innerHeight + 100}px) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Performance monitoring e lazy loading para imagens futuras
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    console.log('🍳 ChefGuedes - Página principal carregada com sucesso!');
    console.log('💡 Dica: Tente o Konami Code para uma surpresa! ↑↑↓↓←→←→BA');
    
    // ===========================================
    // SISTEMA DE AVALIAÇÃO COM ESTRELAS
    // ===========================================
    
    // Inicializar sistema de avaliação
    function initStarRating() {
        const starRatings = document.querySelectorAll('.star-rating');
        
        starRatings.forEach(rating => {
            const stars = rating.querySelectorAll('.star');
            const submitBtn = rating.querySelector('.rating-submit');
            const ratingText = rating.querySelector('.rating-text');
            const recipeId = rating.dataset.recipeId;
            let currentRating = 0;
            let isSubmitting = false;
            
            // Carregar avaliação existente do usuário
            loadUserRating(recipeId, stars, ratingText, submitBtn).then(rating => {
                currentRating = rating;
            });
            
            // Textos para diferentes avaliações
            const ratingTexts = {
                0: 'Clique para avaliar',
                1: '⭐ Não gostei',
                2: '⭐⭐ Poderia ser melhor', 
                3: '⭐⭐⭐ Boa receita',
                4: '⭐⭐⭐⭐ Muito boa!',
                5: '⭐⭐⭐⭐⭐ Perfeita!'
            };
            
            // Adicionar eventos às estrelas
            stars.forEach((star, index) => {
                const starValue = index + 1;
                
                // Hover effect
                star.addEventListener('mouseenter', function() {
                    if (isSubmitting) return;
                    
                    // Highlight stars up to this one
                    highlightStars(stars, starValue);
                    ratingText.textContent = ratingTexts[starValue];
                    ratingText.classList.add('selected');
                });
                
                // Click event
                star.addEventListener('click', function() {
                    if (isSubmitting) return;
                    
                    currentRating = starValue;
                    setRating(stars, starValue);
                    ratingText.textContent = ratingTexts[starValue];
                    ratingText.classList.add('selected');
                    
                    // Show submit button with animation
                    submitBtn.style.display = 'inline-block';
                    setTimeout(() => {
                        submitBtn.classList.add('show');
                    }, 10);
                    
                    // Efeito especial para 5 estrelas
                    if (starValue === 5) {
                        createStarExplosion(rating);
                    }
                });
            });
            
            // Reset on mouse leave
            rating.addEventListener('mouseleave', function() {
                if (isSubmitting) return;
                
                if (currentRating === 0) {
                    resetStars(stars);
                    ratingText.textContent = ratingTexts[0];
                    ratingText.classList.remove('selected');
                } else {
                    setRating(stars, currentRating);
                    ratingText.textContent = ratingTexts[currentRating];
                }
            });
            
            // Submit rating
            submitBtn.addEventListener('click', function() {
                if (isSubmitting || currentRating === 0) return;
                
                submitRating(recipeId, currentRating, submitBtn, rating);
            });
        });
    }
    
    // Carregar avaliação existente do usuário
    async function loadUserRating(recipeId, stars, ratingText, submitBtn) {
        try {
            const response = await fetch(`api/get_user_rating.php?recipe_id=${recipeId}`);
            const result = await response.json();
            
            if (result.success && result.has_rated && result.user_rating > 0) {
                const userRating = result.user_rating;
                setRating(stars, userRating);
                
                const ratingTexts = {
                    1: '⭐ Você avaliou: Não gostei',
                    2: '⭐⭐ Você avaliou: Poderia ser melhor', 
                    3: '⭐⭐⭐ Você avaliou: Boa receita',
                    4: '⭐⭐⭐⭐ Você avaliou: Muito boa!',
                    5: '⭐⭐⭐⭐⭐ Você avaliou: Perfeita!'
                };
                
                ratingText.textContent = ratingTexts[userRating];
                ratingText.classList.add('selected');
                
                // Mostrar opção de alterar avaliação
                submitBtn.textContent = 'Alterar';
                submitBtn.style.display = 'inline-block';
                submitBtn.classList.add('show');
                
                // Return the user rating to set in parent scope
                return userRating;
            }
        } catch (error) {
            console.log('Erro ao carregar avaliação do usuário:', error);
        }
        return 0;
    }
    
    // Highlight stars (hover effect)
    function highlightStars(stars, rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('hovered');
                star.classList.remove('active');
            } else {
                star.classList.remove('hovered', 'active');
            }
        });
    }
    
    // Set permanent rating
    function setRating(stars, rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
                star.classList.remove('hovered');
            } else {
                star.classList.remove('active', 'hovered');
            }
        });
    }
    
    // Reset all stars
    function resetStars(stars) {
        stars.forEach(star => {
            star.classList.remove('active', 'hovered');
        });
    }
    
    // Create star explosion effect
    function createStarExplosion(container) {
        const explosionContainer = document.createElement('div');
        explosionContainer.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            pointer-events: none;
            z-index: 1000;
        `;
        
        container.style.position = 'relative';
        container.appendChild(explosionContainer);
        
        // Create multiple star particles
        for (let i = 0; i < 8; i++) {
            const particle = document.createElement('span');
            particle.textContent = '⭐';
            particle.style.cssText = `
                position: absolute;
                font-size: 1.2rem;
                pointer-events: none;
                animation: starExplosion 1s ease-out forwards;
                animation-delay: ${i * 0.1}s;
                left: -10px;
                top: -10px;
            `;
            
            const angle = (i / 8) * 360;
            const distance = 80;
            const endX = Math.cos(angle * Math.PI / 180) * distance;
            const endY = Math.sin(angle * Math.PI / 180) * distance;
            
            particle.style.setProperty('--end-x', endX + 'px');
            particle.style.setProperty('--end-y', endY + 'px');
            
            explosionContainer.appendChild(particle);
            
            // Remove particle after animation
            setTimeout(() => {
                particle.remove();
            }, 1500);
        }
        
        // Remove explosion container
        setTimeout(() => {
            explosionContainer.remove();
        }, 2000);
    }
    
    // Submit rating to server
    async function submitRating(recipeId, rating, submitBtn, container) {
        const originalText = submitBtn.textContent;
        
        // Set loading state
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Enviando';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('api/rate_recipe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    recipe_id: recipeId,
                    rating: rating
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Success state
                submitBtn.classList.remove('loading');
                submitBtn.classList.add('success');
                submitBtn.textContent = '✓ Avaliado!';
                
                // Show success message
                showRatingMessage(container, 'Obrigado pela sua avaliação! 🎉', 'success');
                
                // Update the current rating display if available
                updateCurrentRating(container, result.data.new_average, result.data.total_ratings);
                
                // Hide the rating section after success
                setTimeout(() => {
                    const ratingSection = container.closest('.rating-section');
                    ratingSection.style.opacity = '0.7';
                    ratingSection.style.pointerEvents = 'none';
                }, 2000);
                
            } else {
                throw new Error(result.message || 'Erro ao enviar avaliação');
            }
            
        } catch (error) {
            console.error('Erro ao enviar avaliação:', error);
            
            // Error state
            submitBtn.classList.remove('loading');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            
            showRatingMessage(container, 'Erro ao enviar avaliação. Tente novamente.', 'error');
        }
    }
    
    // Show rating message
    function showRatingMessage(container, message, type = 'success') {
        // Remove existing message
        const existingMessage = container.querySelector('.rating-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        const messageEl = document.createElement('div');
        messageEl.className = `rating-message ${type}`;
        messageEl.textContent = message;
        
        container.appendChild(messageEl);
        
        // Show with animation
        setTimeout(() => {
            messageEl.classList.add('show');
        }, 10);
        
        // Hide after 3 seconds
        setTimeout(() => {
            messageEl.classList.remove('show');
            setTimeout(() => {
                messageEl.remove();
            }, 300);
        }, 3000);
    }
    
    // Update current rating display
    function updateCurrentRating(container, newAverage, totalRatings) {
        const card = container.closest('.recipe-card');
        const currentRatingEl = card.querySelector('.current-rating');
        
        if (currentRatingEl) {
            const roundedAverage = Math.round(newAverage * 10) / 10;
            currentRatingEl.innerHTML = `⭐ ${roundedAverage} <span class="rating-count">(${totalRatings})</span>`;
            
            // Add update animation
            currentRatingEl.style.transform = 'scale(1.1)';
            currentRatingEl.style.color = '#f39c12';
            
            setTimeout(() => {
                currentRatingEl.style.transform = 'scale(1)';
                currentRatingEl.style.color = '';
            }, 500);
        }
    }
    
    // Add CSS for star explosion animation
    const explosionCSS = `
        @keyframes starExplosion {
            0% {
                transform: translate(0, 0) scale(1) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translate(var(--end-x), var(--end-y)) scale(0.3) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    
    const explosionStyle = document.createElement('style');
    explosionStyle.textContent = explosionCSS;
    document.head.appendChild(explosionStyle);
    
    // Initialize star rating system
    initStarRating();
    
    // Initialize search and filter system
    initSearchAndFilter();
});

// Sistema de Pesquisa e Filtros
function initSearchAndFilter() {
    const searchInput = document.getElementById('recipeSearch');
    const categoryBtns = document.querySelectorAll('.category-btn');
    const recipeCards = document.querySelectorAll('.portuguese-recipes .recipe-card');
    
    let currentCategory = 'all';
    let currentSearch = '';
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            currentSearch = e.target.value.toLowerCase();
            filterRecipes();
        });
    }
    
    // Category filter functionality
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            currentCategory = this.dataset.category;
            filterRecipes();
        });
    });
    
    function filterRecipes() {
        recipeCards.forEach(card => {
            const cardCategory = card.dataset.category;
            const cardTitle = card.querySelector('h4').textContent.toLowerCase();
            const cardDescription = card.querySelector('.recipe-description').textContent.toLowerCase();
            
            const matchesCategory = currentCategory === 'all' || cardCategory === currentCategory;
            const matchesSearch = currentSearch === '' || 
                                cardTitle.includes(currentSearch) || 
                                cardDescription.includes(currentSearch);
            
            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
                card.style.animation = 'fadeInUp 0.5s ease forwards';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show message if no recipes found
        const visibleCards = Array.from(recipeCards).filter(card => card.style.display !== 'none');
        const recipesGrid = document.getElementById('portugueseRecipesGrid');
        
        // Remove existing no-results message
        const existingMessage = document.querySelector('.no-results-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        if (visibleCards.length === 0) {
            const noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'no-results-message';
            noResultsMessage.style.cssText = `
                grid-column: 1 / -1;
                text-align: center;
                padding: 60px 20px;
                color: #6c757d;
                font-size: 1.1rem;
            `;
            noResultsMessage.innerHTML = `
                <div style="font-size: 3rem; margin-bottom: 20px;">🔍</div>
                <h4 style="margin-bottom: 10px;">Nenhuma receita encontrada</h4>
                <p>Tente ajustar sua pesquisa ou filtros.</p>
            `;
            recipesGrid.appendChild(noResultsMessage);
        }
    }
    
    // Add fade animation CSS
    const fadeCSS = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    
    const fadeStyle = document.createElement('style');
    fadeStyle.textContent = fadeCSS;
    document.head.appendChild(fadeStyle);
}

// ===== SISTEMA DE ANALYTICS E ESTATÍSTICAS =====

class RecipeAnalytics {
    constructor() {
        this.sessionViews = new Set();
        this.searchQueries = [];
    }
    
    // Registrar visualização de receita
    async recordView(recipeId) {
        const viewKey = `view_${recipeId}`;
        if (this.sessionViews.has(viewKey)) return;
        
        this.sessionViews.add(viewKey);
        
        try {
            await fetch('api/recipe_stats.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'view',
                    recipe_id: recipeId
                })
            });
        } catch (error) {
            console.error('Erro ao registrar visualização:', error);
        }
    }
    
    // Registrar pesquisa
    async recordSearch(query, type = 'text', resultsCount = 0) {
        this.searchQueries.push({ query, type, timestamp: new Date() });
        
        try {
            await fetch('api/recipe_stats.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'search',
                    query: query,
                    type: type,
                    results_count: resultsCount
                })
            });
        } catch (error) {
            console.error('Erro ao registrar pesquisa:', error);
        }
    }
    
    // Alternar favorito
    async toggleFavorite(recipeId, button) {
        try {
            const response = await fetch('api/recipe_stats.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'favorite',
                    recipe_id: recipeId
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Atualizar UI do botão
                if (result.action === 'added') {
                    button.innerHTML = '❤️';
                    button.classList.add('favorited');
                    button.title = 'Remover dos Favoritos';
                } else {
                    button.innerHTML = '🤍';
                    button.classList.remove('favorited');
                    button.title = 'Adicionar aos Favoritos';
                }
                
                // Mostrar feedback
                this.showToast(`Receita ${result.action === 'added' ? 'adicionada aos' : 'removida dos'} favoritos!`);
            }
        } catch (error) {
            console.error('Erro ao alterar favorito:', error);
            this.showToast('Erro ao alterar favorito. Tente novamente.', 'error');
        }
    }
    
    // Mostrar notificação toast
    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#4caf50' : '#f44336'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 10000;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        // Animar entrada
        setTimeout(() => toast.style.transform = 'translateX(0)', 100);
        
        // Remover após 3 segundos
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Instanciar analytics globalmente
const analytics = new RecipeAnalytics();

// Adicionar funcionalidades de favoritos aos cards
function addInteractivityToCards() {
    const cards = document.querySelectorAll('.recipe-card');
    
    cards.forEach(card => {
        // Adicionar botão de favorito se não existir
        if (!card.querySelector('.favorite-btn')) {
            const favoriteBtn = document.createElement('button');
            favoriteBtn.className = 'favorite-btn';
            favoriteBtn.innerHTML = '🤍';
            favoriteBtn.title = 'Adicionar aos Favoritos';
            favoriteBtn.style.cssText = `
                position: absolute;
                top: 15px;
                right: 15px;
                background: rgba(255,255,255,0.9);
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                backdrop-filter: blur(4px);
            `;
            
            // Hover effects
            favoriteBtn.addEventListener('mouseenter', () => {
                favoriteBtn.style.transform = 'scale(1.1)';
                favoriteBtn.style.boxShadow = '0 4px 15px rgba(0,0,0,0.25)';
            });
            
            favoriteBtn.addEventListener('mouseleave', () => {
                favoriteBtn.style.transform = 'scale(1)';
                favoriteBtn.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
            });
            
            // Adicionar evento de clique
            favoriteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const recipeId = card.dataset.recipeId;
                if (recipeId) {
                    analytics.toggleFavorite(recipeId, favoriteBtn);
                } else {
                    // Para receitas estáticas, simular comportamento
                    if (favoriteBtn.innerHTML === '🤍') {
                        favoriteBtn.innerHTML = '❤️';
                        favoriteBtn.title = 'Remover dos Favoritos';
                        analytics.showToast('Receita adicionada aos favoritos!');
                    } else {
                        favoriteBtn.innerHTML = '🤍';
                        favoriteBtn.title = 'Adicionar aos Favoritos';
                        analytics.showToast('Receita removida dos favoritos!');
                    }
                }
            });
            
            card.style.position = 'relative';
            card.appendChild(favoriteBtn);
        }
        
        // Registrar visualização ao clicar no card
        card.addEventListener('click', (e) => {
            if (e.target.classList.contains('favorite-btn')) return;
            
            const recipeId = card.dataset.recipeId;
            if (recipeId) {
                analytics.recordView(recipeId);
            }
        });
    });
}

// Integrar analytics após o carregamento
setTimeout(() => {
    addInteractivityToCards();
    analytics.recordSearch('homepage_view', 'page_view', document.querySelectorAll('.recipe-card').length);
    console.log('🔍 Sistema de analytics ativado!');
}, 1000);