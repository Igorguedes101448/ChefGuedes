<?php
// Simple centered login & register (no animations)
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
		$action = $_POST['action'] ?? 'login';
		$mysqli = db_connect();
		if(!$mysqli){ $errors[] = 'Erro de conexão com o banco de dados.'; }
		else {
				if($action === 'login'){
						$identifier = trim($_POST['identifier'] ?? '');
						$password = $_POST['password'] ?? '';
						if($identifier === '' || $password === ''){ $errors[] = 'Preencha todos os campos.'; }
						else {
								$stmt = $mysqli->prepare("SELECT id, username, password_hash, role FROM users WHERE email = ? OR username = ? LIMIT 1");
								if($stmt){
										$stmt->bind_param('ss',$identifier,$identifier);
										$stmt->execute();
										$res = $stmt->get_result();
										if($res && $res->num_rows === 1){
												$user = $res->fetch_assoc();
												if(password_verify($password,$user['password_hash'])){
														session_regenerate_id(true);
														$_SESSION['user_id'] = $user['id'];
														$_SESSION['username'] = $user['username'];
														$_SESSION['role'] = $user['role'];
														header('Location: index.php');
														exit;
												} else { $errors[] = 'Credenciais inválidas.'; }
										} else { $errors[] = 'Usuário não encontrado.'; }
										$stmt->close();
								} else { $errors[] = 'Erro na consulta.'; }
						}
				} elseif($action === 'register'){
						$username = trim($_POST['username'] ?? '');
						$email = trim($_POST['email'] ?? '');
						$password = $_POST['password'] ?? '';
						$password2 = $_POST['password2'] ?? '';
						if($username === '' || $email === '' || $password === ''){ $errors[] = 'Preencha todos os campos de registro.'; }
						elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) { $errors[] = 'Email inválido.'; }
						elseif($password !== $password2) { $errors[] = 'As senhas não coincidem.'; }
						elseif(strlen($password) < 6) { $errors[] = 'Senha deve ter ao menos 6 caracteres.'; }
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
						}
				}
				$mysqli->close();
		}
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Entrar / Registrar - ChefGuedes</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
		<div class="login-card">
			<div class="login-head">
				<h1>ChefGuedes</h1>
				<p>Compartilhe e descubra receitas deliciosas</p>
			</div>
			<div class="login-body">
				<?php if(!empty($errors)): ?>
					<div class="error"><?=htmlspecialchars(implode(' | ',$errors))?></div>
				<?php endif; ?>
				<form method="post">
					<input type="hidden" name="action" value="login">
					<input class="input" name="identifier" placeholder="✉️ Email ou usuário" value="<?=isset($_POST['identifier'])?htmlspecialchars($_POST['identifier']):''?>">
					<div style="display:flex;gap:8px;align-items:end;">
						<input class="input" name="password" id="password" type="password" placeholder="🔒 Senha" style="flex:1;">
						<button type="button" id="togglePassword" class="toggle-pwd">👁️</button>
					</div>
					<div class="actions">
						<button class="btn-primary" type="submit">🍳 Entrar na Cozinha</button>
						<a href="register.php" class="btn-secondary">Criar conta</a>
					</div>
				</form>
			</div>
			<div class="form-footer">🔑 Esqueceu a senha? Entre em contato conosco!</div>
		</div>
		<script src="js/login.js"></script>
	</body>
	</html>
