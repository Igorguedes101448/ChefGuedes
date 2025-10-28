<?php
// Script para instalar TODA a base de dados do ChefGuedes
// Inclui sistema base + Grupos Familiares
require_once 'includes/config.php';

echo "<!DOCTYPE html>
<html lang='pt'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Instalação ChefGuedes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #c96b3e;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .progress {
            background: #f0f0f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #17a2b8;
        }
        ul {
            list-style: none;
            margin: 20px 0;
        }
        li {
            padding: 10px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #c96b3e;
        }
        li.created {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #c96b3e, #d88a5c);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201, 107, 62, 0.4);
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #ffc107;
        }
        .step {
            font-weight: bold;
            color: #c96b3e;
            margin-top: 20px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🍳 ChefGuedes - Instalação Completa</h1>";
echo "<p class='subtitle'>Instalando base de dados com Sistema de Grupos Familiares</p>";

$tables_created = [];
$errors = [];

try {
    // Ler o ficheiro SQL completo
    $sql_file = 'db/chefguedes_complete_with_family_groups.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("Ficheiro SQL não encontrado: " . $sql_file);
    }
    
    $sql = file_get_contents($sql_file);
    
    echo "<div class='info'>📁 Ficheiro SQL carregado com sucesso!</div>";
    
    // Separar comandos SQL
    $commands = [];
    $current_command = '';
    $lines = explode("\n", $sql);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Ignorar comentários e linhas vazias
        if (empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 2) === '/*') {
            continue;
        }
        
        $current_command .= $line . ' ';
        
        // Se termina com ;, é o fim do comando
        if (substr(trim($line), -1) === ';') {
            $commands[] = trim($current_command);
            $current_command = '';
        }
    }
    
    echo "<div class='progress'>";
    echo "<p class='step'>Executando " . count($commands) . " comandos SQL...</p>";
    
    $executed = 0;
    
    foreach ($commands as $command) {
        if (!empty($command)) {
            try {
                $pdo->exec($command);
                $executed++;
                
                // Detectar qual tabela foi criada
                if (stripos($command, 'CREATE TABLE') !== false) {
                    preg_match('/CREATE TABLE.*`([^`]+)`/i', $command, $matches);
                    if (isset($matches[1])) {
                        $tables_created[] = $matches[1];
                    }
                }
            } catch (PDOException $e) {
                // Ignorar erros de "já existe" mas registar outros
                if (stripos($e->getMessage(), 'already exists') === false && 
                    stripos($e->getMessage(), 'Duplicate') === false) {
                    $errors[] = substr($command, 0, 100) . "... - " . $e->getMessage();
                }
            }
        }
    }
    
    echo "<div class='success'>✅ " . $executed . " comandos executados com sucesso!</div>";
    echo "</div>";
    
    // Listar tabelas criadas
    if (!empty($tables_created)) {
        echo "<div class='success'>";
        echo "<p class='step'>✅ Tabelas Criadas/Verificadas:</p>";
        echo "<ul>";
        
        // Agrupar tabelas por sistema
        $base_tables = ['users', 'categories', 'tags', 'recipes', 'recipe_categories', 
                        'recipe_tags', 'recipe_ratings', 'recipe_favorites', 'recipe_stats'];
        $family_tables = ['family_groups', 'family_members', 'family_invites', 
                          'family_recipes', 'family_tasks'];
        
        echo "<li><strong>📊 Sistema Base:</strong></li>";
        foreach ($base_tables as $table) {
            if (in_array($table, $tables_created)) {
                echo "<li class='created'>✓ " . $table . "</li>";
            }
        }
        
        echo "<li style='margin-top: 15px;'><strong>👨‍👩‍👧‍👦 Grupos Familiares:</strong></li>";
        foreach ($family_tables as $table) {
            if (in_array($table, $tables_created)) {
                echo "<li class='created'>✓ " . $table . "</li>";
            }
        }
        
        echo "</ul>";
        echo "</div>";
    }
    
    // Verificar se todas as tabelas principais existem
    $stmt = $pdo->query("SHOW TABLES");
    $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $required_tables = [
        'users', 'recipes', 'categories', 'tags',
        'family_groups', 'family_members', 'family_invites', 
        'family_recipes', 'family_tasks'
    ];
    
    $missing_tables = array_diff($required_tables, $existing_tables);
    
    if (empty($missing_tables)) {
        echo "<div class='success'>";
        echo "<h2 style='color: #28a745; margin-bottom: 15px;'>🎉 Instalação Completa!</h2>";
        echo "<p><strong>Total de tabelas na base de dados:</strong> " . count($existing_tables) . "</p>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<p class='step'>📋 Próximos Passos:</p>";
        echo "<ol style='list-style: decimal; margin-left: 25px;'>";
        echo "<li style='margin: 10px 0;'>Acesse a página inicial: <a href='index.php'>index.php</a></li>";
        echo "<li style='margin: 10px 0;'>Faça login ou crie uma conta</li>";
        echo "<li style='margin: 10px 0;'>Acesse <strong>Grupos Familiares</strong> no menu</li>";
        echo "<li style='margin: 10px 0;'>Crie seu primeiro grupo familiar</li>";
        echo "<li style='margin: 10px 0;'>Convide membros e comece a planear!</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<a href='grupos_familiares.php' class='btn'>🚀 Ir para Grupos Familiares</a> ";
        echo "<a href='index.php' class='btn'>🏠 Ir para Início</a>";
        
        echo "<div class='warning' style='margin-top: 30px;'>";
        echo "<p><strong>⚠️ IMPORTANTE:</strong></p>";
        echo "<p>Por segurança, pode apagar este ficheiro após a instalação:</p>";
        echo "<p><code>install_complete_database.php</code></p>";
        echo "</div>";
        
    } else {
        echo "<div class='error'>";
        echo "<p><strong>❌ Faltam " . count($missing_tables) . " tabelas:</strong></p>";
        echo "<ul>";
        foreach ($missing_tables as $table) {
            echo "<li>" . $table . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
    if (!empty($errors)) {
        echo "<div class='warning'>";
        echo "<p class='step'>⚠️ Avisos durante instalação:</p>";
        echo "<ul style='font-size: 0.9rem;'>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Erro Fatal</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<p><strong>Possíveis soluções:</strong></p>";
    echo "<ul>";
    echo "<li>Verifique se o WAMP está em execução</li>";
    echo "<li>Verifique as credenciais em includes/config.php</li>";
    echo "<li>Verifique se a base de dados 'chefguedes' existe</li>";
    echo "<li>Verifique se o ficheiro db/chefguedes_complete_with_family_groups.sql existe</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div>";
echo "</body></html>";
?>
