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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_recipe'])) {
    $mysqli = db_connect();
    if (!$mysqli) {
        $errors[] = 'Erro de conexão com o banco de dados.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['summary'] ?? ''); // Using description instead of summary
        $ingredients = trim($_POST['ingredients'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;

        if (empty($title) || empty($ingredients) || empty($instructions)) {
            $errors[] = 'Preencha os campos obrigatórios.';
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

function get_recipes($query) {
    $mysqli = db_connect();
    if (!$mysqli) return [];
    $result = $mysqli->query($query);
    $recipes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $mysqli->close();
    return $recipes;
}

$weekly_recipes = get_recipes("SELECT r.id, r.title, r.slug, r.description as summary, r.created_at, u.username FROM recipes r JOIN users u ON r.user_id = u.id WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY r.created_at DESC LIMIT 8");

$monthly_recipes = get_recipes("SELECT r.id, r.title, r.slug, r.description as summary, r.created_at, u.username FROM recipes r JOIN users u ON r.user_id = u.id WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER BY r.created_at DESC LIMIT 8");

$best_recipes = get_recipes("SELECT r.id, r.title, r.slug, r.description as summary, COALESCE(rs.average_rating, 0) as avg_rating FROM recipes r LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id ORDER BY rs.average_rating DESC LIMIT 8");

$most_viewed = get_recipes("SELECT r.id, r.title, r.slug, r.description as summary, COALESCE(rs.total_views, 0) as made_count FROM recipes r LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id ORDER BY rs.total_views DESC LIMIT 8");

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

                <label for="ingredients">Ingredientes:</label>
                <textarea id="ingredients" name="ingredients" placeholder="Liste todos os ingredientes necessários..." required></textarea>

                <label for="instructions">Modo de Preparo:</label>
                <textarea id="instructions" name="instructions" placeholder="Descreva passo a passo como preparar..." required></textarea>

                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_vegetarian" style="transform: scale(1.2);"> 
                    Esta receita é vegetariana
                </label>

                <button type="submit" class="btn-primary">Adicionar Receita</button>
            </form>
        </section>

        <section class="recipes-section">
            <h2>Receitas da Semana</h2>
            <div class="recipes-grid">
                <?php if (empty($weekly_recipes)): ?>
                    <p style="grid-column: 1/-1; text-align: center; color: #999; font-style: italic;">
                        🤷‍♀️ Nenhuma receita foi adicionada esta semana. Seja o primeiro!
                    </p>
                <?php else: ?>
                    <?php foreach ($weekly_recipes as $recipe): ?>
                        <div class="recipe-card">
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p><?php echo htmlspecialchars($recipe['summary'] ?: 'Uma deliciosa receita especial...'); ?></p>
                            <small>Por <?php echo htmlspecialchars($recipe['username']); ?> em <?php echo date('d/m/Y', strtotime($recipe['created_at'])); ?></small>
                            <a href="receita.php?slug=<?php echo htmlspecialchars($recipe['slug']); ?>">Ver Receita</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="recipes-section">
            <h2>Receitas do Mês</h2>
            <div class="recipes-grid">
                <?php if (empty($monthly_recipes)): ?>
                    <p style="grid-column: 1/-1; text-align: center; color: #999; font-style: italic;">
                        Nenhuma receita foi adicionada este mês.
                    </p>
                <?php else: ?>
                    <?php foreach ($monthly_recipes as $recipe): ?>
                        <div class="recipe-card">
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p><?php echo htmlspecialchars($recipe['summary'] ?: 'Uma deliciosa receita especial...'); ?></p>
                            <small>Por <?php echo htmlspecialchars($recipe['username']); ?> em <?php echo date('d/m/Y', strtotime($recipe['created_at'])); ?></small>
                            <a href="receita.php?slug=<?php echo htmlspecialchars($recipe['slug']); ?>">Ver Receita</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="recipes-section">
            <h2>Melhores Receitas</h2>
            <div class="recipes-grid">
                <?php if (empty($best_recipes)): ?>
                    <p style="grid-column: 1/-1; text-align: center; color: #999; font-style: italic;">
                        ★ Ainda não temos avaliações suficientes.
                    </p>
                <?php else: ?>
                    <?php foreach ($best_recipes as $recipe): ?>
                        <div class="recipe-card">
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p><?php echo htmlspecialchars($recipe['summary'] ?: 'Uma deliciosa receita especial...'); ?></p>
                            <small>★ Avaliação: <?php echo $recipe['avg_rating'] > 0 ? number_format($recipe['avg_rating'], 1) . '/5' : 'Sem avaliações'; ?></small>
                            <a href="receita.php?slug=<?php echo htmlspecialchars($recipe['slug']); ?>">Ver Receita</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="recipes-section">
            <h2>Mais Vistas</h2>
            <div class="recipes-grid">
                <?php if (empty($most_viewed)): ?>
                    <p style="grid-column: 1/-1; text-align: center; color: #999; font-style: italic;">
                        Nenhuma receita foi visualizada ainda.
                    </p>
                <?php else: ?>
                    <?php foreach ($most_viewed as $recipe): ?>
                        <div class="recipe-card">
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p><?php echo htmlspecialchars($recipe['summary'] ?: 'Uma deliciosa receita especial...'); ?></p>
                            <small>Visualizada <?php echo $recipe['made_count']; ?> vezes</small>
                            <a href="receita.php?slug=<?php echo htmlspecialchars($recipe['slug']); ?>">Ver Receita</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="container">
            <h3>ChefGuedes</h3>
            <p>Compartilhe sabores, crie memórias</p>
            <p>&copy; 2025 - Feito com amor para amantes da culinária</p>
        </div>
    </footer>
</body>
</html>
