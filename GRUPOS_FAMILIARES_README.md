# Sistema de Grupos Familiares - ChefGuedes

## Descrição
Sistema completo para gestão de grupos familiares com planeamento de refeições, atribuição de tarefas e gestão de membros.

## Funcionalidades Implementadas

### ✅ Gestão de Grupos
- Criar grupos familiares
- Sistema de convites controlado
- Gestão de membros com diferentes níveis de permissão
- Roles: Owner, Admin, Member, Viewer

### ✅ Planeamento Semanal
- Calendário semanal com 4 tipos de refeições:
  - Pequeno-almoço (Breakfast)
  - Almoço (Lunch)
  - Jantar (Dinner)
  - Lanche (Snack)
- Atribuir responsável por cada refeição
- Adicionar notas às receitas
- Navegação entre semanas (anterior/próxima)

### ✅ Sistema de Tarefas
- Criar tarefas relacionadas ao grupo
- Atribuir tarefas a membros específicos
- Definir prazos
- Status de tarefas: Pending, In Progress, Completed, Cancelled

### ✅ Sistema de Convites
- Enviar convites apenas para utilizadores registados
- Convite único (não permite duplicados)
- Aceitar ou recusar convites
- Notificações de convites pendentes

### ✅ Gestão de Permissões
- Owner pode gerir todas as configurações
- Controle de quem pode adicionar receitas
- Controle de quem pode atribuir tarefas
- Sistema de roles hierárquico

## Instalação

### Passo 1: Criar as Tabelas
Acesse no navegador:
```
http://localhost/ChefGuedes/setup_family_groups.php
```

Este script irá criar automaticamente todas as tabelas necessárias:
- `family_groups`
- `family_members`
- `family_invites`
- `family_recipes`
- `family_tasks`

### Passo 2: Verificar Instalação
Após executar o setup, verifique se todas as 5 tabelas foram criadas com sucesso.

### Passo 3: Remover Script de Instalação
Por segurança, apague o ficheiro `setup_family_groups.php` após a instalação.

## Como Usar

### Criar um Grupo
1. Acesse **Grupos Familiares** no menu
2. Clique em **Criar Novo Grupo**
3. Preencha o nome e descrição
4. Submeta o formulário

### Convidar Membros
1. Entre no grupo criado
2. Vá para a tab **Membros**
3. Clique em **Convidar Membro**
4. Digite o nome de utilizador exato
5. O convite será enviado

### Aceitar Convites
1. Os convites aparecem automaticamente na página **Grupos Familiares**
2. Clique em **Aceitar** ou **Recusar**
3. Após aceitar, terá acesso ao grupo

### Planear Refeições
1. Entre no grupo
2. Na tab **Planeamento Semanal**, veja o calendário da semana
3. Clique em **+ Adicionar** na refeição desejada
4. Preencha o nome da receita e selecione o responsável
5. Submeta para adicionar ao planeamento

### Criar Tarefas
1. Entre no grupo
2. Vá para a tab **Tarefas**
3. Clique em **Nova Tarefa**
4. Preencha os detalhes e atribua a um membro
5. Defina um prazo (opcional)

### Navegar entre Semanas
- Use os botões **Semana Anterior** e **Próxima Semana**
- O sistema mantém o histórico de todas as semanas
- (Funcionalidade de histórico detalhado em desenvolvimento)

## Estrutura de Ficheiros

### Páginas Principais
- `grupos_familiares.php` - Lista de grupos e convites pendentes
- `grupo.php` - Gestão completa de um grupo específico

### APIs
- `api/family_create_group.php` - Criar novo grupo
- `api/family_send_invite.php` - Enviar convite
- `api/family_invite_response.php` - Aceitar/recusar convite
- `api/family_add_meal.php` - Adicionar receita ao planeamento
- `api/family_add_task.php` - Criar nova tarefa

### Base de Dados
- `db/family_groups_schema.sql` - Schema completo
- `setup_family_groups.php` - Script de instalação

## Esquema de Base de Dados

### family_groups
- `id` - ID do grupo
- `name` - Nome do grupo
- `description` - Descrição
- `owner_id` - ID do dono
- `created_at` - Data de criação

### family_members
- `id` - ID do membro
- `group_id` - ID do grupo
- `user_id` - ID do utilizador
- `role` - Função (owner/admin/member/viewer)
- `can_add_recipes` - Permissão para adicionar receitas
- `can_assign_tasks` - Permissão para atribuir tarefas
- `joined_at` - Data de entrada

### family_invites
- `id` - ID do convite
- `group_id` - ID do grupo
- `inviter_id` - Quem convidou
- `invitee_id` - Quem foi convidado
- `status` - Status (pending/accepted/declined/cancelled)
- `invited_at` - Data do convite
- `responded_at` - Data da resposta

### family_recipes
- `id` - ID da receita agendada
- `group_id` - ID do grupo
- `recipe_id` - ID da receita (opcional, FK para recipes)
- `recipe_name` - Nome da receita
- `meal_date` - Data da refeição
- `meal_type` - Tipo (breakfast/lunch/dinner/snack)
- `assigned_to` - Responsável
- `added_by` - Quem adicionou
- `notes` - Notas
- `created_at` - Data de criação

### family_tasks
- `id` - ID da tarefa
- `group_id` - ID do grupo
- `family_recipe_id` - Receita relacionada (opcional)
- `task_name` - Nome da tarefa
- `description` - Descrição
- `assigned_to` - Atribuído a
- `assigned_by` - Atribuído por
- `due_date` - Prazo
- `status` - Status (pending/in_progress/completed/cancelled)
- `created_at` - Data de criação
- `completed_at` - Data de conclusão

## Características Técnicas

### Segurança
- Verificação de sessão em todas as páginas
- Validação de permissões antes de cada ação
- Proteção contra SQL injection (PDO prepared statements)
- Verificação de propriedade antes de ações sensíveis
- Convites únicos (não permite duplicados)

### Performance
- Índices otimizados em todas as tabelas
- Queries eficientes com JOINs apropriados
- Cache de dados do utilizador em sessão

### UX/UI
- Visual consistente com o resto do site
- Tema escuro/claro integrado
- Modais para ações rápidas
- Mensagens de feedback (sucesso/erro)
- Navegação intuitiva com tabs
- Responsive design

### Compatibilidade
- PHP 7.0+
- MySQL 5.7+
- Compatível com WAMP/XAMPP
- Não usa emojis (apenas no theme switcher)

## Funcionalidades Futuras (Sugeridas)

### Histórico Detalhado
- Visualizar semanas anteriores com filtros
- Exportar planeamento em PDF
- Estatísticas de receitas mais usadas

### Notificações
- Alertas de tarefas próximas do prazo
- Notificações de novos convites
- Lembretes de refeições planeadas

### Lista de Compras
- Gerar lista de ingredientes automaticamente
- Marcar itens como comprados
- Partilhar lista com membros

### Integração com Receitas
- Vincular receitas existentes do site
- Copiar ingredientes automaticamente
- Ver receita diretamente do planeamento

## Suporte
Para questões ou problemas, verifique:
1. Se todas as tabelas foram criadas corretamente
2. Se o utilizador está logado
3. Se as permissões estão configuradas
4. Logs de erro do PHP/MySQL

## Autor
Sistema desenvolvido para ChefGuedes
Data: 2025
