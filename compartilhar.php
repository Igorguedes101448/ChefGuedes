<?php
session_start();
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartilhar Receita - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .form-header h1 {
            font-size: 2.5rem;
            color: var(--color-primary);
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: var(--color-text-light);
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--color-text);
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(201, 107, 62, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-row-thirds {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .difficulty-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 8px;
        }
        
        .difficulty-option {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .difficulty-option:hover {
            border-color: var(--color-primary);
        }
        
        .difficulty-option.selected {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }
        
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 8px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkbox-item input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(201, 107, 62, 0.3);
        }
        
        .login-required {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            margin: 40px auto;
            max-width: 500px;
        }
        
        .login-required h2 {
            color: var(--color-primary);
            margin-bottom: 15px;
        }
        
        .login-required p {
            color: var(--color-text-light);
            margin-bottom: 25px;
        }
        
        .login-btn {
            background: var(--color-primary);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-btn:hover {
            background: var(--color-accent);
            transform: translateY(-2px);
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .form-row,
            .form-row-thirds {
                grid-template-columns: 1fr;
            }
            
            .checkbox-group {
                grid-template-columns: 1fr;
            }
            
            .difficulty-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php
    require_once 'includes/config.php';
    
    $errors = [];
    $success = '';
    
    // Verificar se o usuário está logado
    if (!isLoggedIn()) {
        ?>
        <div class="login-required">
            <h2>Login Necessário</h2>
            <p>Para compartilhar uma receita, você precisa estar logado na sua conta.</p>
            <a href="login.php" class="login-btn">Fazer Login</a>
            <p style="margin-top: 15px;">
                <small>Não tem uma conta? <a href="register.php" style="color: var(--color-primary);">Registre-se aqui</a></small>
            </p>
        </div>
        <?php
        exit;
    }
    
    // Processar formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['share_recipe'])) {
        $title = trim($_POST['title'] ?? '');
        $summary = trim($_POST['summary'] ?? '');
        $ingredients = trim($_POST['ingredients'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $prep_time = (int)($_POST['prep_time'] ?? 0);
        $cook_time = (int)($_POST['cook_time'] ?? 0);
        $servings = (int)($_POST['servings'] ?? 0);
        $difficulty = $_POST['difficulty'] ?? 'medio';
        $category_id = (int)($_POST['category_id'] ?? 1);
        
        // Opções especiais
        $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;
        $is_vegan = isset($_POST['is_vegan']) ? 1 : 0;
        $is_gluten_free = isset($_POST['is_gluten_free']) ? 1 : 0;
        
        // Validações
        if (empty($title)) $errors[] = 'O título é obrigatório.';
        if (empty($ingredients)) $errors[] = 'Os ingredientes são obrigatórios.';
        if (empty($instructions)) $errors[] = 'As instruções são obrigatórias.';
        if ($prep_time <= 0) $errors[] = 'O tempo de preparo deve ser maior que zero.';
        if ($servings <= 0) $errors[] = 'O número de porções deve ser maior que zero.';
        
        if (empty($errors)) {
            try {
                // Criar slug
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                $slug = preg_replace('/-+/', '-', $slug);
                $slug = trim($slug, '-');
                
                // Verificar se slug já existe
                $stmt = $pdo->prepare("SELECT id FROM recipes WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetch()) {
                    $slug .= '-' . time();
                }
                
                $total_time = $prep_time + $cook_time;
                
                // Inserir receita
                $stmt = $pdo->prepare("
                    INSERT INTO recipes (
                        user_id, category_id, title, slug, summary, ingredients, instructions,
                        prep_time, cook_time, total_time, servings, difficulty,
                        is_vegetarian, is_vegan, is_gluten_free, published_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                
                $stmt->execute([
                    $_SESSION['user_id'], $category_id, $title, $slug, $summary,
                    $ingredients, $instructions, $prep_time, $cook_time, $total_time,
                    $servings, $difficulty, $is_vegetarian, $is_vegan, $is_gluten_free
                ]);
                
                $recipe_id = $pdo->lastInsertId();
                
                // Inicializar estatísticas
                $stmt = $pdo->prepare("
                    INSERT INTO recipe_stats (recipe_id, total_views, unique_views, total_favorites, total_made, total_ratings, average_rating, popularity_score)
                    VALUES (?, 0, 0, 0, 0, 0, 0.00, 0.0000)
                ");
                $stmt->execute([$recipe_id]);
                
                $success = 'Receita compartilhada com sucesso! <a href="receita.php?slug=' . $slug . '" style="color: var(--color-primary);">Ver receita</a>';
                
                // Limpar campos
                $_POST = [];
                
            } catch (Exception $e) {
                $errors[] = 'Erro ao salvar receita: ' . $e->getMessage();
            }
        }
    }
    
    // Buscar categorias
    $categories = [];
    try {
        $stmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY order_position");
        $categories = $stmt->fetchAll();
    } catch (Exception $e) {
        $categories = [['id' => 1, 'name' => 'Pratos Principais']];
    }
    ?>
    
    <div class="form-container">
        <div class="form-header">
            <h1>Compartilhar Receita</h1>
            <p>Compartilhe suas receitas favoritas com a comunidade ChefGuedes</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">
                <?= $success ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Título da Receita *</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="summary">Descrição Breve</label>
                <textarea id="summary" name="summary" placeholder="Uma breve descrição da sua receita..."><?= htmlspecialchars($_POST['summary'] ?? '') ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Categoria</label>
                    <select id="category_id" name="category_id">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= (($_POST['category_id'] ?? 1) == $category['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="servings">Número de Porções *</label>
                    <input type="number" id="servings" name="servings" value="<?= htmlspecialchars($_POST['servings'] ?? '4') ?>" min="1" required>
                </div>
            </div>
            
            <div class="form-row-thirds">
                <div class="form-group">
                    <label for="prep_time">Tempo de Preparo (min) *</label>
                    <input type="number" id="prep_time" name="prep_time" value="<?= htmlspecialchars($_POST['prep_time'] ?? '30') ?>" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="cook_time">Tempo de Cozimento (min)</label>
                    <input type="number" id="cook_time" name="cook_time" value="<?= htmlspecialchars($_POST['cook_time'] ?? '30') ?>" min="0">
                </div>
                
                <div class="form-group">
                    <label>Dificuldade</label>
                    <div class="difficulty-options">
                        <div class="difficulty-option <?= (($_POST['difficulty'] ?? 'facil') === 'facil') ? 'selected' : '' ?>" data-value="facil">
                            Fácil
                        </div>
                        <div class="difficulty-option <?= (($_POST['difficulty'] ?? 'medio') === 'medio') ? 'selected' : '' ?>" data-value="medio">
                            Médio
                        </div>
                        <div class="difficulty-option <?= (($_POST['difficulty'] ?? 'dificil') === 'dificil') ? 'selected' : '' ?>" data-value="dificil">
                            Difícil
                        </div>
                    </div>
                    <input type="hidden" name="difficulty" value="<?= $_POST['difficulty'] ?? 'medio' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="ingredients">Ingredientes *</label>
                <textarea id="ingredients" name="ingredients" placeholder="Liste os ingredientes, um por linha..." required><?= htmlspecialchars($_POST['ingredients'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="instructions">Modo de Preparo *</label>
                <textarea id="instructions" name="instructions" placeholder="Descreva o passo a passo para preparar a receita..." required><?= htmlspecialchars($_POST['instructions'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Opções Especiais</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="is_vegetarian" name="is_vegetarian" <?= isset($_POST['is_vegetarian']) ? 'checked' : '' ?>>
                        <label for="is_vegetarian">Vegetariano</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="is_vegan" name="is_vegan" <?= isset($_POST['is_vegan']) ? 'checked' : '' ?>>
                        <label for="is_vegan">Vegano</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="is_gluten_free" name="is_gluten_free" <?= isset($_POST['is_gluten_free']) ? 'checked' : '' ?>>
                        <label for="is_gluten_free">Sem Glúten</label>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="share_recipe" class="submit-btn">
                Compartilhar Receita
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" style="color: var(--color-text-light); text-decoration: none;">
                ← Voltar à página inicial
            </a>
        </div>
    </div>
    
    <script>
        // Funcionalidade para seleção de dificuldade
        document.querySelectorAll('.difficulty-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove seleção de todos
                document.querySelectorAll('.difficulty-option').forEach(opt => opt.classList.remove('selected'));
                
                // Adiciona seleção ao clicado
                this.classList.add('selected');
                
                // Atualiza campo hidden
                document.querySelector('input[name="difficulty"]').value = this.dataset.value;
            });
        });
        
        // Auto-crescimento dos textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    </script>
</body>
</html>