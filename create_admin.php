<?php
// Script para criar usuário admin - EXECUTE UMA VEZ E DELETE ESTE ARQUIVO
require_once 'db/config.php';

// Defina a senha desejada aqui
$admin_password = "1234554321"; // ALTERE ESTA SENHA!
$admin_username = "admin";
$admin_email = "admin@chefguedes.com";

try {
    // Verificar se admin já existe
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $admin_username, $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "❌ Usuário admin já existe!<br>";
        echo "Para redefinir a senha, delete o usuário admin existente primeiro.";
    } else {
        // Criar hash da senha
        $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
        
        // Inserir admin
        $stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash, role, created_at) VALUES (?, ?, ?, 'admin', NOW())");
        $stmt->bind_param("sss", $admin_username, $admin_email, $password_hash);
        
        if ($stmt->execute()) {
            echo "<strong>Usuário admin criado com sucesso!</strong><br><br>";
            echo "<strong>Credenciais de acesso:</strong><br>";
            echo "<strong>Usuário:</strong> " . $admin_username . "<br>";
            echo "<strong>Email:</strong> " . $admin_email . "<br>";
            echo "<strong>Senha:</strong> " . $admin_password . "<br><br>";
            echo "<strong>IMPORTANTE:</strong> Delete este arquivo após usar!<br>";
            echo "<a href='login.php'>Fazer login agora</a>";
        } else {
            echo "Erro ao criar admin: " . $mysqli->error;
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}

$mysqli->close();
?>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
div { background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
</style>
<div>
<h2>Criação de Admin - ChefGuedes</h2>