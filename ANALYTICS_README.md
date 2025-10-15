# ChefGuedes - Sistema Completo de Analytics e Estatísticas

## 🎯 Visão Geral do Sistema

Criei um sistema completo de analytics e estatísticas para o ChefGuedes que inclui:

### 📊 Funcionalidades Implementadas

1. **Banco de Dados Avançado**
   - Tabelas para receitas, usuários, categorias, tags
   - Sistema de avaliações com estrelas
   - Histórico de pesquisas e visualizações
   - Favoritos e receitas feitas pelos usuários
   - Estatísticas pré-calculadas para performance

2. **APIs para Analytics**
   - `api/recipe_stats.php` - Gestão de estatísticas de receitas
   - `api/admin_dashboard.php` - Dashboard administrativo
   - Endpoints para registrar views, favoritos, pesquisas

3. **Dashboard Administrativo**
   - `admin_dashboard.php` - Visão geral das estatísticas
   - `analytics.php` - Analytics avançado em tempo real
   - Métricas de performance e engagement

4. **Sistema de Interação do Usuário**
   - Botões de favoritos nas receitas
   - Sistema de avaliação com 5 estrelas
   - Tracking automático de visualizações
   - Registro de pesquisas e cliques

## Como Configurar

### 1. Configurar o Banco de Dados

```sql
-- 1. Execute o script principal:
SOURCE db/chefguedes_enhanced.sql;

-- 2. Popule com dados de exemplo:
SOURCE db/sample_data.sql;
```

### 2. Configurar as Credenciais

Edite `includes/config.php` se necessário:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'chefguedes');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 3. Acessar os Dashboards

- **Admin Dashboard**: `http://localhost/ChefGuedes/admin_dashboard.php`
- **Analytics Avançado**: `http://localhost/ChefGuedes/analytics.php`
- **Site Principal**: `http://localhost/ChefGuedes/index.php`

### 4. Login Admin Padrão

```
Email: admin@chefguedes.pt
Password: password (será o hash padrão do Laravel)
```

## 📈 Funcionalidades do Sistema

### Analytics Coletados Automaticamente:

1. **Visualizações de Receitas**
   - IP do visitante
   - Duração da visualização
   - Referrer
   - User agent

2. **Pesquisas**
   - Termos pesquisados
   - Tipo de pesquisa (texto, categoria, tag)
   - Número de resultados
   - Cliques nos resultados

3. **Interações do Usuário**
   - Favoritos adicionados/removidos
   - Receitas marcadas como feitas
   - Avaliações com estrelas
   - Comentários

4. **Estatísticas Calculadas**
   - Score de popularidade
   - Média de avaliações
   - Taxa de conversão
   - Receitas trending

### Dashboard Features:

1. **Métricas em Tempo Real**
   - Total de visualizações
   - Pesquisas realizadas
   - Favoritos adicionados
   - Avaliação média
   - Usuários ativos
   - Taxa de conversão

2. **Gráficos e Análises**
   - Atividade por horário
   - Receitas mais populares
   - Pesquisas trending
   - Feed de atividade em tempo real

3. **Tabelas de Dados**
   - Top receitas por visualizações
   - Pesquisas mais populares
   - Usuários mais ativos
   - Tendências de crescimento

## 🎨 Sistema de Interface

### Botões de Favoritos
- Automaticamente adicionados a todas as receitas
- Animações suaves e feedback visual
- Sincronização com banco de dados

### Sistema de Avaliação
- 5 estrelas interativas
- Efeitos especiais para 5 estrelas
- Cálculo automático de médias
- Comentários opcionais

### Notificações Toast
- Feedback visual para ações do usuário
- Animações de entrada/saída
- Diferentes tipos (sucesso/erro)

## 🔧 APIs Disponíveis

### Recipe Stats API (`api/recipe_stats.php`)

**GET Endpoints:**
- `?recipe_id=1` - Estatísticas de uma receita
- `?popular=10` - Top 10 receitas populares
- `?trending=10` - Top 10 pesquisas trending

**POST Actions:**
- `action: view` - Registrar visualização
- `action: favorite` - Alternar favorito
- `action: made` - Marcar como feita
- `action: search` - Registrar pesquisa

### Admin Dashboard API (`api/admin_dashboard.php`)

**Endpoints:**
- `?action=overview` - Visão geral do sistema
- `?action=recipe_analytics` - Analytics de receitas
- `?action=user_analytics` - Analytics de usuários
- `?action=search_analytics` - Analytics de pesquisas
- `?action=popular_content` - Conteúdo popular

## 📊 Estrutura do Banco de Dados

### Tabelas Principais:
- `users` - Usuários do sistema
- `recipes` - Receitas com metadados completos
- `categories` - Categorias de receitas
- `tags` - Tags para categorização
- `recipe_tags` - Relacionamento receitas-tags

### Tabelas de Interação:
- `recipe_ratings` - Avaliações dos usuários
- `recipe_favorites` - Favoritos dos usuários
- `recipe_made` - Receitas feitas pelos usuários
- `recipe_views` - Histórico de visualizações
- `search_history` - Histórico de pesquisas

### Tabelas de Estatísticas:
- `recipe_stats` - Estatísticas pré-calculadas
- `system_stats` - Estatísticas gerais do sistema

### Views Úteis:
- `popular_recipes` - Receitas ordenadas por popularidade
- `trending_searches` - Pesquisas trending
- `recipes_with_stats` - Receitas com estatísticas
- `weekly_trending` - Trending da semana

## 🎯 Como Usar

### Para Administradores:
1. Acesse o dashboard admin para visão geral
2. Use o analytics avançado para análises detalhadas
3. Monitore métricas em tempo real
4. Acompanhe tendências e padrões de uso

### Para Desenvolvedores:
1. Use as APIs para integrar com outras plataformas
2. Adicione novos tipos de tracking conforme necessário
3. Expanda as métricas com novos KPIs
4. Integre com ferramentas de BI externas

### Para Usuários:
1. As funcionalidades são automáticas e transparentes
2. Favoritem receitas para acompanhar
3. Avaliem receitas para ajudar outros usuários
4. Marquem receitas como feitas para histórico pessoal

## Recursos Avançados

### Auto-refresh
- Dashboard atualiza automaticamente a cada 30s
- Pode ser habilitado/desabilitado
- Contador regressivo visual

### Notificações em Tempo Real
- Feed de atividades ao vivo
- Indicadores de mudanças
- Alertas para métricas importantes

### Performance Otimizada
- Estatísticas pré-calculadas
- Índices otimizados no banco
- Consultas eficientes
- Cache de resultados

## 🔒 Segurança

### Proteção de APIs
- Validação de sessão para endpoints sensíveis
- Sanitização de inputs
- Proteção contra SQL injection
- Rate limiting recomendado

### Dashboard Admin
- Acesso restrito a administradores
- Validação de permissões
- Logs de acesso (implementar se necessário)

## 📱 Responsividade

Todo o sistema é totalmente responsivo:
- Mobile-first design
- Grids adaptativos
- Navegação touch-friendly
- Performance otimizada para mobile

## 🎨 Customização

### Cores e Tema
- Variáveis CSS centralizadas
- Fácil mudança de paleta
- Suporte a modo escuro (pode ser implementado)

### Métricas Personalizadas
- Adicione novos KPIs facilmente
- Sistema modular e extensível
- APIs flexíveis para novos dados

## 📊 Métricas Incluídas

1. **Engagement**
   - Tempo na página
   - Taxa de cliques
   - Interações por usuário
   - Taxa de retorno

2. **Conteúdo**
   - Receitas mais populares
   - Categorias preferidas
   - Tags mais usadas
   - Avaliações médias

3. **Pesquisa**
   - Termos mais pesquisados
   - Taxa de conversão de pesquisa
   - Resultados sem cliques
   - Padrões de busca

4. **Usuários**
   - Usuários ativos
   - Novos registros
   - Atividade por período
   - Retenção de usuários

Este sistema fornece uma base sólida para analytics avançados no ChefGuedes, com possibilidade de expansão conforme suas necessidades crescem!