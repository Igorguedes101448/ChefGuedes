<?php
// Página de registro separada
session_start();
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'chefguedes';
$errors = [];

if(isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit;
}

function db_connect(){
    global $db_host,$db_user,$db_pass,$db_name;
    $m = new mysqli($db_host,$db_user,$db_pass,$db_name);
    if($m->connect_errno) return null;
    return $m;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if($username === '' || $email === '' || $password === ''){ $errors[] = 'Preencha todos os campos.'; }
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) { $errors[] = 'Email inválido.'; }
    elseif($password !== $password2) { $errors[] = 'As senhas não coincidem.'; }
    elseif(strlen($password) < 6) { $errors[] = 'Senha deve ter ao menos 6 caracteres.'; }
    else {
        $mysqli = db_connect();
        if(!$mysqli){ $errors[] = 'Erro de conexão com o banco.'; }
        else {
            $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
            $stmt->bind_param('ss',$username,$email);
            $stmt->execute();
            $res = $stmt->get_result();
            if($res && $res->num_rows > 0){ $errors[] = 'Usuário ou email já cadastrado.'; }
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $mysqli->prepare('INSERT INTO users (username,email,password_hash,role,created_at) VALUES (?,?,?,"user",NOW())');
                $ins->bind_param('sss',$username,$email,$hash);
                if($ins->execute()){
                    // auto-login
                    $newid = $ins->insert_id;
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $newid;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'user';
                    header('Location: index.php');
                    exit;
                } else { $errors[] = 'Erro ao criar usuário.'; }
            }
            $stmt->close();
            $mysqli->close();
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Registrar - ChefGuedes</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
  <div class="login-card register-card">
    <div class="login-head register-head">
      <h1>Junte-se à ChefGuedes</h1>
      <p>Compartilhe suas receitas e descubra sabores únicos</p>
    </div>
    <div class="login-body">
      <?php if(!empty($errors)): ?>
        <div class="error"><?=htmlspecialchars(implode(' | ',$errors))?></div>
      <?php endif; ?>
      <form method="post">
        <input class="input" name="username" placeholder="Seu nome de chef" value="<?=isset($_POST['username'])?htmlspecialchars($_POST['username']):''?>">
        <input class="input" name="email" placeholder="Email para receitas" value="<?=isset($_POST['email'])?htmlspecialchars($_POST['email']):''?>">
        <div style="display:flex;gap:8px;align-items:end;">
          <input class="input" name="password" id="password_reg" type="password" placeholder="Crie uma senha" style="flex:1;">
          <button type="button" class="toggle-pwd" data-target="#password_reg">👁</button>
        </div>
        <input class="input" name="password2" type="password" placeholder="Confirme sua senha">
        <div class="actions">
          <button class="btn-primary" type="submit">Começar a Cozinhar</button>
          <a href="login.php" class="btn-secondary">← Voltar</a>
        </div>
      </form>
    </div>
    <div class="form-footer">Ao se registrar, você se torna parte da nossa família culinária!</div>
  </div>
  <script src="js/login.js"></script>
</body>
</html>