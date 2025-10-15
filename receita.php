<?php
session_start();

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

$slug = $_GET['slug'] ?? '';
$recipe = null;
$comments = [];
$user_rating = null;
$avg_rating = 0;

if ($slug) {
    $mysqli = db_connect();
    if ($mysqli) {
        // Get recipe details
        $stmt = $mysqli->prepare("SELECT r.*, u.username, rs.average_rating as avg_rating, rs.total_ratings as rating_count, rs.total_views as made_count FROM recipes r JOIN users u ON r.user_id = u.id LEFT JOIN recipe_stats rs ON r.id = rs.recipe_id WHERE r.slug = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('s', $slug);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows === 1) {
                $recipe = $result->fetch_assoc();
                $avg_rating = $recipe['avg_rating'] ?: 0;
                
                // Get comments
                $comments_query = $mysqli->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.recipe_id = {$recipe['id']} ORDER BY c.created_at DESC");
                if ($comments_query) {
                    $comments = $comments_query->fetch_all(MYSQLI_ASSOC);
                }
                
                // Get user's rating if logged in
                if (isset($_SESSION['user_id'])) {
                    $user_rating_query = $mysqli->query("SELECT score FROM ratings WHERE recipe_id = {$recipe['id']} AND user_id = {$_SESSION['user_id']} LIMIT 1");
                    if ($user_rating_query && $user_rating_query->num_rows === 1) {
                        $user_rating = $user_rating_query->fetch_assoc()['score'];
                    }
                }
            }
            $stmt->close();
        }
        $mysqli->close();
    }
}

// Handle form submissions
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $mysqli = db_connect();
    if ($mysqli && $recipe) {
        if (isset($_POST['add_comment'])) {
            $comment = trim($_POST['comment'] ?? '');
            if (!empty($comment)) {
                $stmt = $mysqli->prepare("INSERT INTO comments (recipe_id, user_id, content) VALUES (?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param('iis', $recipe['id'], $_SESSION['user_id'], $comment);
                    if ($stmt->execute()) {
                        $success = 'Comentário adicionado com sucesso!';
                        header("Location: receita.php?slug=" . $slug);
                        exit;
                    } else {
                        $errors[] = 'Erro ao adicionar comentário.';
                    }
                    $stmt->close();
                }
            } else {
                $errors[] = 'Comentário não pode estar vazio.';
            }
        }
        
        if (isset($_POST['rate_recipe'])) {
            $rating = (int)($_POST['rating'] ?? 0);
            if ($rating >= 1 && $rating <= 5) {
                $stmt = $mysqli->prepare("REPLACE INTO ratings (recipe_id, user_id, score) VALUES (?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param('iii', $recipe['id'], $_SESSION['user_id'], $rating);
                    if ($stmt->execute()) {
                        // Update stats
                        $mysqli->query("REPLACE INTO recipe_stats (recipe_id, avg_rating, rating_count, made_count) SELECT r.id, IFNULL(AVG(rt.score),0), COUNT(rt.id), (SELECT COUNT(*) FROM recipe_mades m WHERE m.recipe_id = r.id) FROM recipes r LEFT JOIN ratings rt ON rt.recipe_id = r.id WHERE r.id = {$recipe['id']} GROUP BY r.id");
                        
                        $success = 'Avaliação adicionada com sucesso!';
                        header("Location: receita.php?slug=" . $slug);
                        exit;
                    } else {
                        $errors[] = 'Erro ao adicionar avaliação.';
                    }
                    $stmt->close();
                }
            } else {
                $errors[] = 'Avaliação deve ser entre 1 e 5 estrelas.';
            }
        }
        
        if (isset($_POST['mark_made'])) {
            $stmt = $mysqli->prepare("INSERT INTO recipe_mades (recipe_id, user_id) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param('ii', $recipe['id'], $_SESSION['user_id']);
                if ($stmt->execute()) {
                    // Update stats
                    $mysqli->query("REPLACE INTO recipe_stats (recipe_id, avg_rating, rating_count, made_count) SELECT r.id, IFNULL(AVG(rt.score),0), COUNT(rt.id), (SELECT COUNT(*) FROM recipe_mades m WHERE m.recipe_id = r.id) FROM recipes r LEFT JOIN ratings rt ON rt.recipe_id = r.id WHERE r.id = {$recipe['id']} GROUP BY r.id");
                    
                    $success = 'Receita marcada como feita!';
                    header("Location: receita.php?slug=" . $slug);
                    exit;
                } else {
                    $errors[] = 'Erro ao marcar receita como feita.';
                }
                $stmt->close();
            }
        }
        $mysqli->close();
    }
}

if (!$recipe) {
    header('Location: receitas.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - Chef Guedes</title>
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span>Olá, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="logout.php" style="color: #ff6b6b;">Sair</a>
                <?php else: ?>
                    <a href="login.php" style="background: #ff6b6b; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none;">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
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

        <!-- Recipe Header -->
        <header class="recipe-header" style="text-align: center; padding: 40px 0; border-bottom: 2px solid rgba(255, 107, 107, 0.1); margin-bottom: 40px;">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 3rem; color: #333; margin-bottom: 20px;">
                <?php echo htmlspecialchars($recipe['title']); ?>
            </h1>
            
            <?php if ($recipe['description']): ?>
                <p style="font-size: 1.2rem; color: #666; max-width: 800px; margin: 0 auto 30px; line-height: 1.6;">
                    <?php echo htmlspecialchars($recipe['description']); ?>
                </p>
            <?php endif; ?>

            <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-bottom: 30px;">
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 5px;">CG</div>
                    <small style="color: #999;">Por <?php echo htmlspecialchars($recipe['username']); ?></small>
                </div>
                
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 5px;">★</div>
                    <small style="color: #999;"><?php echo $avg_rating > 0 ? number_format($avg_rating, 1) . '/5' : 'Sem avaliações'; ?></small>
                </div>
                
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 5px;">�</div>
                    <small style="color: #999;"><?php echo $recipe['made_count'] ?: 0; ?> vezes feita</small>
                </div>
                
                <?php if ($recipe['is_vegetarian']): ?>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 5px;">S</div>
                    <small style="color: #999;">Vegetariana</small>
                </div>
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <form method="post" style="display: inline;">
                        <button type="submit" name="mark_made" class="btn-secondary" style="border: none; cursor: pointer;">
                            ✅ Eu fiz esta receita
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </header>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 50px;">
            <!-- Ingredients -->
            <section class="ingredients-section">
                <h2 style="font-family: 'Playfair Display', serif; color: #333; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                    🥕 Ingredientes
                </h2>
                <div style="background: #fafafa; padding: 25px; border-radius: 15px; border-left: 4px solid #ff6b6b;">
                    <pre style="white-space: pre-wrap; font-family: 'Inter', sans-serif; line-height: 1.8; color: #555; margin: 0;"><?php echo htmlspecialchars($recipe['ingredients']); ?></pre>
                </div>
            </section>

            <!-- Instructions -->
            <section class="instructions-section">
                <h2 style="font-family: 'Playfair Display', serif; color: #333; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                    Modo de Preparo
                </h2>
                <div style="background: #fafafa; padding: 25px; border-radius: 15px; border-left: 4px solid #ffa500;">
                    <pre style="white-space: pre-wrap; font-family: 'Inter', sans-serif; line-height: 1.8; color: #555; margin: 0;"><?php echo htmlspecialchars($recipe['instructions']); ?></pre>
                </div>
            </section>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Rating Section -->
        <section style="background: linear-gradient(135deg, #fafafa, #f0f0f0); padding: 30px; border-radius: 15px; margin-bottom: 40px;">
            <h2 style="font-family: 'Playfair Display', serif; color: #333; margin-bottom: 25px;">
                ★ Avaliar esta Receita
            </h2>
            <form method="post" style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <label style="font-weight: 600;">Sua avaliação:</label>
                <div style="display: flex; gap: 8px;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label style="cursor: pointer;">
                            <input type="radio" name="rating" value="<?php echo $i; ?>" <?php echo ($user_rating == $i) ? 'checked' : ''; ?> style="display: none;">
                            <span style="font-size: 1.5rem; color: <?php echo ($user_rating >= $i) ? '#ff6b6b' : '#ddd'; ?>; transition: color 0.3s;">★</span>
                        </label>
                    <?php endfor; ?>
                </div>
                <button type="submit" name="rate_recipe" class="btn-primary" style="border: none; cursor: pointer;">
                    Avaliar
                </button>
            </form>
        </section>

        <!-- Comments Section -->
        <section style="margin-bottom: 40px;">
            <h2 style="font-family: 'Playfair Display', serif; color: #333; margin-bottom: 25px;">
                Comentários (<?php echo count($comments); ?>)
            </h2>
            
            <form method="post" style="margin-bottom: 30px;">
                <textarea name="comment" placeholder="Compartilhe sua experiência com esta receita..." style="width: 100%; padding: 15px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 15px; min-height: 100px; resize: vertical; font-family: 'Inter', sans-serif;"></textarea>
                <button type="submit" name="add_comment" class="btn-primary" style="margin-top: 15px; border: none; cursor: pointer;">
                    Adicionar Comentário
                </button>
            </form>

            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; border: 1px solid #eee;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <strong style="color: #333;"><?php echo htmlspecialchars($comment['username']); ?></strong>
                            <small style="color: #999;"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></small>
                        </div>
                        <p style="color: #666; line-height: 1.6; margin: 0;"><?php echo htmlspecialchars($comment['content']); ?></p>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($comments)): ?>
                    <p style="text-align: center; color: #999; font-style: italic; padding: 40px;">
                        🤔 Ainda não há comentários. Seja o primeiro a compartilhar sua experiência!
                    </p>
                <?php endif; ?>
            </div>
        </section>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; background: linear-gradient(135deg, #fafafa, #f0f0f0); border-radius: 15px;">
            <h3 style="color: #333; margin-bottom: 15px;">Quer avaliar e comentar?</h3>
            <p style="color: #666; margin-bottom: 20px;">Faça login para compartilhar sua experiência com esta receita!</p>
            <a href="login.php" class="btn-primary" style="text-decoration: none; padding: 12px 24px; border-radius: 8px;">Fazer Login</a>
        </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 40px;">
            <a href="receitas.php" class="btn-secondary" style="text-decoration: none; padding: 12px 24px;">
                ← Voltar às Receitas
            </a>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <h3>ChefGuedes</h3>
            <p>Compartilhe sabores, crie memórias</p>
            <p>&copy; 2025 - Feito com amor para amantes da culinária</p>
        </div>
    </footer>

    <script>
        // Star rating interactivity
        document.querySelectorAll('input[name="rating"]').forEach(input => {
            input.addEventListener('change', function() {
                const rating = parseInt(this.value);
                document.querySelectorAll('input[name="rating"] + span').forEach((star, index) => {
                    star.style.color = (index + 1 <= rating) ? '#ff6b6b' : '#ddd';
                });
            });
        });
    </script>
</body>
</html>