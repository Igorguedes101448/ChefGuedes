<?php
require_once 'includes/config.php';

$success_message = '';
$error_message = '';

// Processar formulário de contato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Por favor, preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Por favor, insira um email válido.';
    } else {
        try {
            // Salvar mensagem na base de dados
            $stmt = $pdo->prepare("
                INSERT INTO support_messages (name, email, subject, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $email, $subject, $message]);
            
            $success_message = 'Mensagem enviada com sucesso! Entraremos em contato em breve.';
            
            // Limpar campos
            $name = $email = $subject = $message = '';
            
        } catch (Exception $e) {
            $error_message = 'Erro ao enviar mensagem. Tente novamente mais tarde.';
        }
    }
}

// FAQ Items
$faq_items = [
    [
        'question' => 'Como posso criar uma conta no ChefGuedes?',
        'answer' => 'Para criar uma conta, clique em "Registar" no menu superior, preencha seus dados e confirme o email. É rápido e gratuito!'
    ],
    [
        'question' => 'Como publico uma receita?',
        'answer' => 'Após fazer login, clique em "Nova Receita" no seu dashboard. Preencha todos os campos, adicione fotos e publique. Sua receita ficará disponível para toda a comunidade.'
    ],
    [
        'question' => 'Posso editar minhas receitas depois de publicadas?',
        'answer' => 'Sim! Acesse sua receita e clique no botão "Editar". Você pode modificar qualquer informação, adicionar novas fotos ou atualizar os ingredientes.'
    ],
    [
        'question' => 'Como funciona o sistema de avaliações?',
        'answer' => 'Os usuários podem avaliar receitas de 1 a 5 estrelas e deixar comentários. As avaliações ajudam outros usuários a descobrir as melhores receitas.'
    ],
    [
        'question' => 'Posso salvar receitas como favoritas?',
        'answer' => 'Sim! Clique no ícone de coração em qualquer receita para adicioná-la aos seus favoritos. Acesse suas receitas favoritas no seu dashboard.'
    ],
    [
        'question' => 'Como posso pesquisar receitas específicas?',
        'answer' => 'Use a barra de pesquisa no topo da página ou visite a página "Explorar" onde pode filtrar por categoria, dificuldade, tempo de preparo e muito mais.'
    ],
    [
        'question' => 'O ChefGuedes é gratuito?',
        'answer' => 'Sim! O ChefGuedes é completamente gratuito. Pode criar conta, publicar receitas e explorar todo o conteúdo sem nenhum custo.'
    ],
    [
        'question' => 'Como denuncio conteúdo inadequado?',
        'answer' => 'Se encontrar conteúdo inadequado, entre em contato connosco através do formulário de suporte ou envie email para suporte@chefguedes.com.'
    ]
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte - ChefGuedes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .support-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .support-header {
            text-align: center;
            margin-bottom: 50px;
            padding: 40px 20px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            border-radius: 15px;
        }
        
        .support-header h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .support-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .support-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
        }
        
        .contact-section,
        .faq-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--color-text);
        }
        
        .form-input,
        .form-select,
        .form-textarea {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--color-primary);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .submit-btn {
            background: var(--color-primary);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background: var(--color-accent);
            transform: translateY(-2px);
        }
        
        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .faq-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .faq-question {
            background: #f8f9fa;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            color: var(--color-text);
            transition: background 0.3s ease;
        }
        
        .faq-question:hover {
            background: #e9ecef;
        }
        
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
            color: var(--color-text-light);
            line-height: 1.6;
        }
        
        .faq-answer.active {
            padding: 15px 20px;
            max-height: 200px;
        }
        
        .faq-toggle {
            transition: transform 0.3s ease;
            font-size: 1.2rem;
        }
        
        .faq-toggle.active {
            transform: rotate(180deg);
        }
        
        .contact-info {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
        }
        
        .contact-info h3 {
            color: var(--color-text);
            margin-bottom: 15px;
        }
        
        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
            color: var(--color-text-light);
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .help-links {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .help-card {
            text-align: center;
            padding: 20px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--color-text);
        }
        
        .help-card:hover {
            border-color: var(--color-primary);
            transform: translateY(-3px);
            color: var(--color-primary);
        }
        
        .help-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .help-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .help-desc {
            font-size: 0.9rem;
            color: var(--color-text-light);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--color-text-light);
            text-decoration: none;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--color-primary);
        }
        
        @media (max-width: 768px) {
            .support-content {
                grid-template-columns: 1fr;
            }
            
            .support-header h1 {
                font-size: 2rem;
            }
            
            .help-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="support-container">
        <a href="index.php" class="back-link">
            ← Voltar à página inicial
        </a>
        
        <div class="support-header">
            <h1>🛠️ Centro de Suporte</h1>
            <p>Estamos aqui para ajudar! Encontre respostas às suas dúvidas ou entre em contato connosco.</p>
        </div>
        
        <div class="support-content">
            <!-- Formulário de Contato -->
            <div class="contact-section">
                <h2 class="section-title">
                    <i class="icon-mail"></i> Entre em Contato
                </h2>
                
                <?php if ($success_message): ?>
                    <div class="success-message">
                        <?= htmlspecialchars($success_message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="error-message">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="contact-form">
                    <input type="hidden" name="contact_form" value="1">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" id="name" name="name" class="form-input" 
                               value="<?= htmlspecialchars($name ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">Assunto</label>
                        <select id="subject" name="subject" class="form-select" required>
                            <option value="">Selecione um assunto</option>
                            <option value="Suporte Técnico" <?= ($subject ?? '') === 'Suporte Técnico' ? 'selected' : '' ?>>Suporte Técnico</option>
                            <option value="Problema com Receita" <?= ($subject ?? '') === 'Problema com Receita' ? 'selected' : '' ?>>Problema com Receita</option>
                            <option value="Problema de Conta" <?= ($subject ?? '') === 'Problema de Conta' ? 'selected' : '' ?>>Problema de Conta</option>
                            <option value="Sugestão de Melhoria" <?= ($subject ?? '') === 'Sugestão de Melhoria' ? 'selected' : '' ?>>Sugestão de Melhoria</option>
                            <option value="Denúncia de Conteúdo" <?= ($subject ?? '') === 'Denúncia de Conteúdo' ? 'selected' : '' ?>>Denúncia de Conteúdo</option>
                            <option value="Outro" <?= ($subject ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Mensagem</label>
                        <textarea id="message" name="message" class="form-textarea" 
                                  placeholder="Descreva sua dúvida ou problema em detalhes..." required><?= htmlspecialchars($message ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        Enviar Mensagem
                    </button>
                </form>
            </div>
            
            <!-- FAQ -->
            <div class="faq-section">
                <h2 class="section-title">
                    ❓ Perguntas Frequentes
                </h2>
                
                <div class="faq-list">
                    <?php foreach ($faq_items as $index => $faq): ?>
                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(<?= $index ?>)">
                                <span><?= htmlspecialchars($faq['question']) ?></span>
                                <span class="faq-toggle" id="toggle-<?= $index ?>">▼</span>
                            </div>
                            <div class="faq-answer" id="answer-<?= $index ?>">
                                <?= htmlspecialchars($faq['answer']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Informações de Contato -->
        <div class="contact-info">
            <h3><i class="icon-phone"></i> Outras Formas de Contato</h3>
            <div class="contact-details">
                <div class="contact-item">
                    <i class="icon-mail"></i>
                    <span>Email: suporte@chefguedes.com</span>
                </div>
                <div class="contact-item">
                    <i class="icon-clock"></i>
                    <span>Horário: Segunda a Sexta, 9h às 18h</span>
                </div>
                <div class="contact-item">
                    <i class="icon-flash"></i>
                    <span>Tempo de resposta: Até 24 horas</span>
                </div>
            </div>
        </div>
        
        <!-- Links de Ajuda -->
        <div class="help-links">
            <h2 class="section-title">
                <i class="icon-link"></i> Links Úteis
            </h2>
            
            <div class="help-grid">
                <a href="index.php" class="help-card">
                    <div class="help-icon"><i class="icon-home"></i></div>
                    <div class="help-title">Página Inicial</div>
                    <div class="help-desc">Voltar ao ChefGuedes</div>
                </a>
                
                <a href="explorar.php" class="help-card">
                    <div class="help-icon"><i class="icon-search"></i></div>
                    <div class="help-title">Explorar Receitas</div>
                    <div class="help-desc">Descubra novas receitas</div>
                </a>
                
                <a href="register.php" class="help-card">
                    <div class="help-icon"><i class="icon-user-add"></i></div>
                    <div class="help-title">Criar Conta</div>
                    <div class="help-desc">Junte-se à comunidade</div>
                </a>
                
                <a href="login.php" class="help-card">
                    <div class="help-icon"><i class="icon-login"></i></div>
                    <div class="help-title">Fazer Login</div>
                    <div class="help-desc">Aceder à sua conta</div>
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function toggleFaq(index) {
            const answer = document.getElementById(`answer-${index}`);
            const toggle = document.getElementById(`toggle-${index}`);
            
            answer.classList.toggle('active');
            toggle.classList.toggle('active');
        }
        
        // Auto-focus no primeiro campo do formulário
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            if (nameInput && !nameInput.value) {
                nameInput.focus();
            }
        });
        
        // Validação do formulário em tempo real
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#e0e0e0';
            }
        });
        
        // Contador de caracteres para a mensagem
        const messageTextarea = document.getElementById('message');
        const maxLength = 1000;
        
        messageTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            
            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }
        });
    </script>
</body>
</html>