<?php
// Script para criar as tabelas do sistema de Grupos Familiares
require_once 'includes/config.php';

echo "<h2>Instalação do Sistema de Grupos Familiares</h2>";
echo "<p>Criando tabelas...</p>";

try {
    // Ler o ficheiro SQL
    $sql = file_get_contents('db/family_groups_schema.sql');
    
    // Separar e executar cada comando
    $commands = explode(';', $sql);
    
    foreach ($commands as $command) {
        $command = trim($command);
        if (!empty($command)) {
            $pdo->exec($command);
        }
    }
    
    echo "<p style='color: green; font-weight: bold;'>✓ Todas as tabelas foram criadas com sucesso!</p>";
    echo "<h3>Tabelas criadas:</h3>";
    echo "<ul>";
    echo "<li>family_groups - Grupos familiares</li>";
    echo "<li>family_members - Membros dos grupos</li>";
    echo "<li>family_invites - Convites pendentes</li>";
    echo "<li>family_recipes - Receitas agendadas</li>";
    echo "<li>family_tasks - Tarefas do grupo</li>";
    echo "</ul>";
    
    echo "<h3>Próximos passos:</h3>";
    echo "<p>1. Acesse <a href='grupos_familiares.php'>Grupos Familiares</a></p>";
    echo "<p>2. Crie um novo grupo</p>";
    echo "<p>3. Convide membros para o grupo</p>";
    echo "<p>4. Comece a planear as suas refeições!</p>";
    
    echo "<br><p><strong>IMPORTANTE:</strong> Pode apagar este ficheiro (setup_family_groups.php) após a instalação.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>✗ Erro ao criar tabelas:</p>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Verifique se o ficheiro db/family_groups_schema.sql existe e se a conexão com a base de dados está correta.</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2 {
        color: #c96b3e;
    }
    a {
        color: #c96b3e;
        font-weight: bold;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    ul {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>
