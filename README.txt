# 📋 **README - EasyMenu Sistema de Cardápio Online**

## 🚀 **VISÃO GERAL**

O **EasyMenu** é um sistema completo de cardápio online para restaurantes, bares e estabelecimentos alimentícios. Oferece dashboard administrativo e cardápio público integrados.

---

## 📊 **FLUXO COMPLETO DO SISTEMA**

### **1. 🏁 INÍCIO - Primeiro Acesso**
```
1. Acesse index.php
2. Clique em "Cadastrar"
3. Preencha dados pessoais e do restaurante
4. Sistema cria conta automaticamente
5. Faça login com email/senha
```

### **2. ⚙️ CONFIGURAÇÃO INICIAL**
```
Painel → Configurações → Preencher:
- Nome e dados do restaurante
- Horários de funcionamento  
- Taxas e valores (entrega, pedido mínimo)
- Formas de pagamento
- Serviços (delivery/retirada/local)
```

### **3. 📁 ESTRUTURA DO CARDÁPIO**
```
1. Criar Categorias
   - Ex: "Pratos Principais", "Bebidas", "Sobremesas"
   - Definir ícone e ordem de exibição

2. Cadastrar Produtos
   - Vincular à categoria
   - Adicionar foto, preço, descrição
   - Configurar estoque (opcional)
   - Definir se é destaque
```

### **4. 🛒 FLUXO DE PEDIDOS**
```
CLIENTE (Cardápio Público):
1. Acessa cardapio.php?id=SEU_ID
2. Navega pelas categorias
3. Adiciona itens ao carrinho
4. Preenche dados e finaliza pedido

RESTAURANTE (Dashboard):
1. Pedido aparece em "Pedidos" com status "Recebido"
2. Clique em ✅ para "Aceitar"
3. Clique em 🍳 para "Em Preparo" 
4. Clique em ✅✅ para "Pronto"
5. Clique em 🚗 para "Entregue"
```

### **5. 📈 ACOMPANHAMENTO**
```
Painel Principal mostra:
- Pedidos do dia/mês
- Faturamento em tempo real
- Produtos mais vendidos
- Estatísticas completas
```

---

## 🎯 **REQUISITOS FUNCIONAIS (RF)**

### **RF01 - Autenticação**
- [x] Cadastro de usuário/restaurante
- [x] Login/logout seguro
- [x] Recuperação de senha (futuro)

### **RF02 - Gestão do Restaurante**
- [x] Cadastro de dados completos
- [x] Configuração de horários
- [x] Definição de taxas e valores
- [x] Personalização de serviços

### **RF03 - Gestão de Cardápio**
- [x] CRUD de categorias
- [x] CRUD de produtos
- [x] Upload de imagens
- [x] Ordenação personalizada
- [x] Controle de disponibilidade

### **RF04 - Sistema de Pedidos**
- [x] Recebimento de pedidos
- [x] Fluxo de status
- [x] Detalhes completos do pedido
- [x] Histórico de pedidos

### **RF05 - Dashboard Analytics**
- [x] Estatísticas em tempo real
- [x] Relatórios de vendas
- [x] Métricas de performance
- [x] Produtos mais vendidos

### **RF06 - Controle de Estoque**
- [x] Controle por produto
- [x] Alertas de estoque baixo
- [x] Atualização automática

### **RF07 - Backup e Segurança**
- [x] Backup manual dos dados
- [x] Proteção contra SQL injection
- [x] Validação de uploads

---

## 🛡️ **REQUISITOS NÃO FUNCIONAIS (RNF)**

### **RNF01 - Performance**
- [x] Carregamento rápido (<3s)
- [x] Banco SQLite otimizado
- [x] Índices para consultas frequentes
- [x] Cache de estatísticas

### **RNF02 - Usabilidade**
- [x] Interface intuitiva
- [x] Design responsivo
- [x] Navegação simplificada
- [x] Feedback visual imediato

### **RNF03 - Confiabilidade**
- [x] Sistema 99% disponível
- [x] Tolerância a falhas de upload
- [x] Validação de dados robusta
- [x] Backup regular

### **RNF04 - Segurança**
- [x] Senhas hash com bcrypt
- [x] Proteção XSS
- [x] Validação de arquivos upload
- [x] SQL injection prevention

### **RNF05 - Compatibilidade**
- [x] Navegadores modernos
- [x] Dispositivos móveis
- [x] Tablets e desktop

### **RNF06 - Escalabilidade**
- [x] Múltiplos restaurantes
- [x] Arquitetura modular
- [x] Futura API REST

---

## 🛠️ **INSTALAÇÃO E CONFIGURAÇÃO**

### **Pré-requisitos**
```
- PHP 7.4 ou superior
- SQLite habilitado
- Extensão PDO
- Permissão de escrita nas pastas:
  /data
  /uploads
  /uploads/logos
  /uploads/banners  
  /uploads/produtos
  /data/backups
```

### **Passo a Passo**
```bash
1. Faça upload dos arquivos para seu servidor
2. Configure permissões:
   chmod 755 data/ uploads/
   chmod 644 index.php
3. Acesse seu-domínio.com/index.php
4. O sistema cria automaticamente:
   - Banco de dados
   - Estrutura de pastas
   - Tabelas necessárias
```

### **Configuração do Servidor**
```apache
# Exemplo .htaccess (Apache)
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d  
RewriteRule ^(.*)$ index.php [QSA,L]

# Configuração PHP recomendada
memory_limit = 128M
upload_max_filesize = 5M
post_max_size = 6M
max_execution_time = 30
```

---

## 📱 **FUNCIONALIDADES POR MÓDULO**

### **📊 PAINEL PRINCIPAL**
- Visão geral do negócio
- Métricas em tempo real
- Ações rápidas
- Gráfico de vendas

### **🍽️ CARDÁPIO**
- **Categorias**: Organização hierárquica
- **Produtos**: Gestão completa com fotos
- **Controles**: Disponibilidade, destaque, estoque

### **🛒 PEDIDOS**
- Listagem com filtros
- Fluxo visual de status
- Detalhes completos
- Impressão de comandas

### **⚙️ CONFIGURAÇÕES**
- Dados do estabelecimento
- Horários e funcionamento
- Configurações comerciais
- Backup do sistema

---

## 🔄 **FLUXOS ESPECIAIS**

### **Controle de Estoque**
```
1. Ativar "Controlar Estoque" no produto
2. Definir estoque atual e mínimo
3. Sistema reduz automaticamente ao aceitar pedido
4. Alertas visuais quando estoque baixo
```

### **Backup Manual**
```
1. Vá em Configurações
2. Clique em "Fazer Backup"  
3. Sistema gera arquivo JSON
4. Download automático
```

### **Produtos em Destaque**
```
1. Marcar produto como "Destaque"
2. Aparece com badge especial
3. Ideal para promoções
```

---

## 🚨 **SOLUÇÃO DE PROBLEMAS COMUNS**

### **Erro de Permissão**
```
Problema: Não consegue criar banco/pastas
Solução: chmod 755 data/ uploads/
```

### **Upload Não Funciona**
```
Problema: Erro ao enviar imagens
Solução: Verificar php.ini:
- upload_max_filesize = 5M
- post_max_size = 6M
```

### **Página em Branco**
```
Problema: Página não carrega
Solução: Verificar logs de erro PHP
- error_reporting(E_ALL)
- ini_set('display_errors', 1)
```

---

## 📞 **SUPORTE**

### **Canais de Ajuda**
- 📧 Email: suporte@easymenu.com
- 📚 Documentação: docs.easymenu.com
- 🐛 Reportar Bug: GitHub Issues

### **Checklist de Verificação**
- [ ] PHP 7.4+
- [ ] SQLite habilitado
- [ ] Pastas com permissão
- [ ] Extensão PDO ativa
- [ ] Uploads funcionando

---

## 🔮 **ROADMAP FUTURO**

### **Versão 2.1**
- [ ] App mobile para clientes
- [ ] Integração com WhatsApp
- [ ] Cupons de desconto

### **Versão 2.2**  
- [ ] Múltiplos usuários
- [ ] Relatórios avançados
- [ ] API REST

### **Versão 3.0**
- [ ] SaaS multi-tenant
- [ ] Marketplace de temas
- [ ] App nativo iOS/Android