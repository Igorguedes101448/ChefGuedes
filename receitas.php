<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'chefguedes';

function db_connect() {
    global $db_host, $db_user, $db_pass, $db_name;
    $m = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($m->connect_errno) return null;
    return $m;
}

$errors = [];
$success = '';

// Função para validar categoria
function validate_category($category) {
    // Lista de palavras proibidas (insultos e palavras obscenas)
    $forbidden_words = [
        'merda', 'puta', 'caralho', 'foda', 'cu', 'porra', 'bosta', 'buceta', 
        'cacete', 'pênis', 'vagina', 'sexo', 'pornografia', 'droga', 'cocaína',
        'heroína', 'maconha', 'shit', 'fuck', 'bitch', 'ass', 'dick', 'pussy',
        'cunt', 'bastard', 'damn', 'hell', 'whore', 'slut'
    ];
    
    $category_lower = mb_strtolower($category, 'UTF-8');
    
    // Verificar palavras proibidas
    foreach ($forbidden_words as $word) {
        if (strpos($category_lower, $word) !== false) {
            return false;
        }
    }
    
    // Verificar se contém apenas letras, espaços e acentos
    if (!preg_match('/^[a-záàâãéèêíïóôõöúçñ\s]+$/iu', $category)) {
        return false;
    }
    
    // Verificar tamanho
    if (strlen($category) < 3 || strlen($category) > 50) {
        return false;
    }
    
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_recipe'])) {
    $mysqli = db_connect();
    if (!$mysqli) {
        $errors[] = 'Erro de conexão com o banco de dados.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['summary'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $difficulty = trim($_POST['difficulty'] ?? '');
        $ingredients = trim($_POST['ingredients'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;

        if (empty($title) || empty($category) || empty($difficulty) || empty($ingredients) || empty($instructions)) {
            $errors[] = 'Preencha os campos obrigatórios.';
        } elseif (!validate_category($category)) {
            $errors[] = 'Categoria inválida. Use apenas nomes de alimentos ou tipos de pratos (sem números, símbolos ou palavras inadequadas).';
        } else {
            $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9\-]/', '', $title)));
            $stmt = $mysqli->prepare("INSERT INTO recipes (user_id, title, slug, description, ingredients, instructions) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param('isssss', $_SESSION['user_id'], $title, $slug, $description, $ingredients, $instructions);
                if ($stmt->execute()) {
                    $success = 'Receita adicionada com sucesso!';
                } else {
                    $errors[] = 'Erro ao adicionar receita.';
                }
                $stmt->close();
            } else {
                $errors[] = 'Erro na preparação da consulta.';
            }
        }
        $mysqli->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receitas - Chef Guedes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/culinary.css">
</head>
<body class="main-layout">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">ChefGuedes</a>
            <div class="nav-links">
                <a href="index.php">Início</a>
                <a href="receitas.php">Receitas</a>
            </div>
            <div class="user-menu">
                <span>Olá, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" style="color: #ff6b6b;">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <header class="header">
            <h1>Minhas Receitas</h1>
        </header>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <section class="add-recipe">
            <h2>Compartilhar Nova Receita</h2>
            
            <form method="post">
                <input type="hidden" name="add_recipe" value="1">
                
                <label for="title">Título da Receita:</label>
                <input type="text" id="title" name="title" placeholder="Ex: Lasanha da Vovó" required>

                <label for="summary">Resumo (opcional):</label>
                <textarea id="summary" name="summary" placeholder="Uma breve descrição da sua receita..."></textarea>
                
                <label for="category">Categoria:</label>
                <input type="text" id="category" name="category" placeholder="Ex: Prato Principal, Entrada, Sobremesa, Sopa, Bebida..." required 
                       maxlength="50" 
                       style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;"
                       oninput="validateCategory(this)">
                <div id="category-suggestions" style="display: none; margin-top: 5px; padding: 10px; background: #f9f9f9; border-radius: 8px; font-size: 0.85rem; color: #666;">
                    <strong>Sugestões:</strong> Prato Principal, Entrada, Sobremesa, Sopa, Bebida, Lanche, Salada, Acompanhamento
                </div>
                <div id="category-error" style="display: none; margin-top: 5px; padding: 8px; background: #ffe5e5; border-left: 3px solid #ff4444; border-radius: 4px; font-size: 0.85rem; color: #cc0000;"></div>
                
                <label for="difficulty">Dificuldade:</label>
                <select id="difficulty" name="difficulty" required style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                    <option value="">Selecione a dificuldade</option>
                    <option value="facil">Fácil</option>
                    <option value="medio">Médio</option>
                    <option value="dificil">Difícil</option>
                </select>

                <label for="ingredients">Ingredientes:</label>
                <textarea id="ingredients" name="ingredients" placeholder="Liste todos os ingredientes necessários..." required></textarea>

                <label for="instructions">Modo de Preparo:</label>
                <textarea id="instructions" name="instructions" placeholder="Descreva passo a passo como preparar..." required></textarea>

                <div style="margin: 20px 0;">
                    <label style="display: block; font-weight: 600; margin-bottom: 10px; color: #555;">Restrições Alimentares:</label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 8px;">
                        <input type="checkbox" name="is_vegetarian" style="transform: scale(1.2);"> 
                        Esta receita é vegetariana
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 8px;">
                        <input type="checkbox" name="is_vegan" style="transform: scale(1.2);"> 
                        Esta receita é vegana
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_gluten_free" style="transform: scale(1.2);"> 
                        Esta receita é sem glúten
                    </label>
                </div>

                <button type="submit" class="btn-primary">Adicionar Receita</button>
            </form>
        </section>
        
        <script>
        // Validação de categoria em tempo real
        function validateCategory(input) {
            const value = input.value.trim();
            const suggestionsDiv = document.getElementById('category-suggestions');
            const errorDiv = document.getElementById('category-error');
            
            // Lista de palavras proibidas
            const forbiddenWords = [
                'merda', 'puta', 'caralho', 'foda', 'cu', 'porra', 'bosta', 'buceta',
                'cacete', 'pênis', 'vagina', 'sexo', 'pornografia', 'droga', 'cocaína',
                'heroína', 'maconha', 'shit', 'fuck', 'bitch', 'ass', 'dick', 'pussy',
                'cunt', 'bastard', 'damn', 'hell', 'whore', 'slut'
            ];
            
            // Mostrar sugestões quando vazio ou pouco texto
            if (value.length < 3) {
                suggestionsDiv.style.display = 'block';
                errorDiv.style.display = 'none';
                input.style.borderColor = '#ddd';
                return;
            } else {
                suggestionsDiv.style.display = 'none';
            }
            
            const valueLower = value.toLowerCase();
            
            // Verificar palavras proibidas
            let hasForbiddenWord = false;
            forbiddenWords.forEach(word => {
                if (valueLower.includes(word)) {
                    hasForbiddenWord = true;
                }
            });
            
            if (hasForbiddenWord) {
                errorDiv.textContent = '❌ Categoria inválida. Por favor, use termos apropriados.';
                errorDiv.style.display = 'block';
                input.style.borderColor = '#ff4444';
                return;
            }
            
            // Verificar se contém apenas letras e espaços (com acentos)
            const validPattern = /^[a-záàâãéèêíïóôõöúçñ\s]+$/i;
            if (!validPattern.test(value)) {
                errorDiv.textContent = '❌ Use apenas letras e espaços (sem números ou símbolos).';
                errorDiv.style.display = 'block';
                input.style.borderColor = '#ff4444';
                return;
            }
            
            // Se passou todas as validações
            errorDiv.style.display = 'none';
            input.style.borderColor = '#4caf50';
        }
        </script>
    </div>
</body>
</html>
