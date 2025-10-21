<?php
require_once 'includes/config.php';

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getCurrentUser();
$success = '';
$errors = [];

// Processar atualização de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $location = trim($_POST['location'] ?? '');
    
    // Validações
    if (empty($username)) {
        $errors[] = 'O nome de utilizador é obrigatório.';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email inválido.';
    }
    
    // Verificar se username ou email já existem (exceto o próprio)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $user['id']]);
        if ($stmt->fetch()) {
            $errors[] = 'Este nome de utilizador já está em uso.';
        }
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user['id']]);
        if ($stmt->fetch()) {
            $errors[] = 'Este email já está em uso.';
        }
    }
    
    // Processar upload de avatar
    $avatar_path = $user['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['avatar']['type'];
        $file_size = $_FILES['avatar']['size'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Tipo de ficheiro não permitido. Use JPG, PNG, GIF ou WEBP.';
        } elseif ($file_size > MAX_FILE_SIZE) {
            $errors[] = 'Ficheiro muito grande. Tamanho máximo: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB.';
        } else {
            $upload_dir = UPLOAD_PATH . 'profiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $user['id'] . '_' . uniqid() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filepath)) {
                // Remover avatar antigo se existir
                if (!empty($user['avatar']) && file_exists($user['avatar'])) {
                    unlink($user['avatar']);
                }
                $avatar_path = 'uploads/profiles/' . $filename;
            } else {
                $errors[] = 'Erro ao fazer upload do ficheiro.';
            }
        }
    }
    
    // Atualizar base de dados
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET username = ?, email = ?, bio = ?, location = ?, avatar = ?
                WHERE id = ?
            ");
            $stmt->execute([$username, $email, $bio, $location, $avatar_path, $user['id']]);
            
            // Atualizar sessão
            $_SESSION['username'] = $username;
            
            $success = 'Perfil atualizado com sucesso!';
            
            // Recarregar dados do utilizador
            $user = getCurrentUser();
            
        } catch (Exception $e) {
            $errors[] = 'Erro ao atualizar perfil: ' . $e->getMessage();
        }
    }
}

// Processar mudança de password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = 'Preencha todos os campos de password.';
    } elseif ($new_password !== $confirm_password) {
        $errors[] = 'As passwords não coincidem.';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'A password deve ter pelo menos 6 caracteres.';
    } elseif (!password_verify($current_password, $user['password_hash'])) {
        $errors[] = 'Password atual incorreta.';
    } else {
        try {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$new_hash, $user['id']]);
            
            $success = 'Password alterada com sucesso!';
        } catch (Exception $e) {
            $errors[] = 'Erro ao alterar password: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .profile-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .profile-content {
            display: grid;
            gap: 30px;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--color-text);
            margin: 0;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .avatar-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            font-weight: 700;
            overflow: hidden;
            position: relative;
            box-shadow: 0 8px 25px rgba(201, 107, 62, 0.3);
        }
        
        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-upload {
            margin-top: 15px;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }
        
        .file-input-label {
            background: var(--color-primary);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            font-weight: 500;
        }
        
        .file-input-label:hover {
            background: var(--color-accent);
            transform: translateY(-2px);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--color-text);
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(201, 107, 62, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn-primary {
            background: var(--color-primary);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
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
        
        .back-link {
            text-align: center;
            margin-top: 30px;
        }
        
        .back-link a {
            color: var(--color-text-light);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: var(--color-primary);
        }
        
        .help-text {
            font-size: 0.85rem;
            color: var(--color-text-light);
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                padding: 10px;
            }
            
            .card-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>Meu Perfil</h1>
            <p>Gerir as suas informações pessoais e preferências</p>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="profile-content">
            <!-- Informações do Perfil -->
            <div class="profile-card">
                <div class="card-header">
                    <h2 class="card-title">Informações do Perfil</h2>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <!-- Avatar -->
                        <div class="avatar-section">
                            <div class="avatar-preview" id="avatarPreview">
                                <?php if (!empty($user['avatar']) && file_exists($user['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar">
                                <?php else: ?>
                                    <?= strtoupper(substr($user['username'], 0, 2)) ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="avatar-upload">
                                <div class="file-input-wrapper">
                                    <label for="avatar" class="file-input-label">
                                        Escolher Foto de Perfil
                                    </label>
                                    <input type="file" 
                                           id="avatar" 
                                           name="avatar" 
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           onchange="previewAvatar(this)">
                                </div>
                                <p class="help-text">JPG, PNG, GIF ou WEBP. Máximo 5MB.</p>
                            </div>
                        </div>
                        
                        <!-- Nome de utilizador -->
                        <div class="form-group">
                            <label for="username">Nome de Utilizador *</label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="<?= htmlspecialchars($user['username']) ?>" 
                                   required>
                        </div>
                        
                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" 
                                   required>
                        </div>
                        
                        <!-- Biografia -->
                        <div class="form-group">
                            <label for="bio">Biografia</label>
                            <textarea id="bio" 
                                      name="bio" 
                                      placeholder="Conte-nos um pouco sobre você..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                            <p class="help-text">Máximo 500 caracteres</p>
                        </div>
                        
                        <!-- Localização -->
                        <div class="form-group">
                            <label for="location">Localização</label>
                            <input type="text" 
                                   id="location" 
                                   name="location" 
                                   value="<?= htmlspecialchars($user['location'] ?? '') ?>" 
                                   placeholder="Cidade, País">
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn-primary">
                            Guardar Alterações
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Alterar Password -->
            <div class="profile-card">
                <div class="card-header">
                    <h2 class="card-title">Alterar Password</h2>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="current_password">Password Atual *</label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Nova Password *</label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   required>
                            <p class="help-text">Mínimo 6 caracteres</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Nova Password *</label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn-primary">
                            Alterar Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="back-link">
            <a href="dashboard.php">← Voltar ao Dashboard</a>
        </div>
    </div>
    
    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script src="js/theme.js"></script>
</body>
</html>
