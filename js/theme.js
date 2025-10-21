// Theme Switcher - Global para todo o site
(function() {
    'use strict';
    
    // Aplicar tema salvo imediatamente ao carregar
    const savedTheme = localStorage.getItem('chefguedes-theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Aguardar DOM carregar para adicionar event listeners
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemeSwitcher);
    } else {
        initThemeSwitcher();
    }
    
    function initThemeSwitcher() {
        const themeButtons = document.querySelectorAll('.theme-btn');
        
        if (themeButtons.length === 0) {
            return; // Não há botões de tema nesta página
        }
        
        // Atualizar estado visual dos botões
        updateActiveButton(savedTheme);
        
        // Adicionar event listeners aos botões
        themeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const theme = this.getAttribute('data-theme');
                setTheme(theme);
            });
        });
    }
    
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('chefguedes-theme', theme);
        updateActiveButton(theme);
    }
    
    function updateActiveButton(theme) {
        const themeButtons = document.querySelectorAll('.theme-btn');
        themeButtons.forEach(function(btn) {
            if (btn.getAttribute('data-theme') === theme) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }
    
    // Expor função global para uso externo se necessário
    window.ChefGuedesTheme = {
        setTheme: setTheme,
        getTheme: function() {
            return localStorage.getItem('chefguedes-theme') || 'light';
        }
    };
})();
