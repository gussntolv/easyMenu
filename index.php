<?php
// EasyMenu - Dashboard Administrativo
// index.php - Arquivo principal do sistema admin

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/render.php';

// Inicialização do sistema
$db = Database::getInstance();
$auth = new Auth($db);
$pdo = $db->getPdo();

// Processamento das ações
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$pagina_atual = $_GET['pagina'] ?? 'painel';
$sucesso = $erro = null;

// CSRF Token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verificar CSRF para ações que modificam dados
$acoes_csrf = ['salvar_configuracoes', 'adicionar_categoria', 'editar_categoria', 'excluir_categoria', 
               'adicionar_produto', 'editar_produto', 'excluir_produto', 'atualizar_status_pedido', 
               'adicionar_funcionario', 'editar_funcionario', 'excluir_funcionario'];

if (in_array($acao, $acoes_csrf) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $erro = 'Token de segurança inválido.';
        $acao = ''; // Cancelar ação
    }
}

try {
    switch ($acao) {
        case 'registrar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $dados = [
                    'nome' => trim($_POST['nome'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'telefone' => trim($_POST['telefone'] ?? ''),
                    'nome_restaurante' => trim($_POST['nome_restaurante'] ?? ''),
                    'senha' => $_POST['senha'] ?? '',
                    'confirmar_senha' => $_POST['confirmar_senha'] ?? ''
                ];
                
                $usuario_id = $auth->registrar($dados);
                
                // Login automático após registro
                if ($auth->login($dados['email'], $dados['senha'])) {
                    header("Location: index.php");
                    exit;
                }
            }
            break;
            
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = trim($_POST['email'] ?? '');
                $senha = $_POST['senha'] ?? '';
                
                if (empty($email) || empty($senha)) {
                    $erro = 'Por favor, preencha todos os campos.';
                } elseif ($auth->login($email, $senha)) {
                    header("Location: index.php");
                    exit;
                } else {
                    $erro = 'E-mail ou senha incorretos. Verifique suas credenciais.';
                }
            }
            break;
            
        case 'logout':
            $auth->logout();
            header("Location: index.php");
            exit;
            break;
            
        case 'salvar_configuracoes':
            if ($auth->isLogged() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $usuario_logado = $auth->getUser();
                $restaurante = getRestauranteUsuario($pdo, $usuario_logado['id']);
                
                if (!$restaurante) {
                    throw new Exception('Restaurante não encontrado.');
                }
                
                $dados_sanitizados = validarESanitizarConfiguracoes($_POST);
                
                // Processar uploads
                $logo = $restaurante['logo'];
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    if ($logo && file_exists($logo)) {
                        unlink($logo);
                    }
                    $logo = processarUpload($_FILES['logo'], UPLOAD_DIR . 'logos/', 'logo_' . $restaurante['id']);
                }
                
                $banner = $restaurante['banner'];
                if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                    if ($banner && file_exists($banner)) {
                        unlink($banner);
                    }
                    $banner = processarUpload($_FILES['banner'], UPLOAD_DIR . 'banners/', 'banner_' . $restaurante['id']);
                }
                
                $stmt = $pdo->prepare("
                    UPDATE restaurantes 
                    SET nome = ?, slogan = ?, descricao = ?, telefone = ?, whatsapp = ?, 
                        endereco = ?, numero = ?, bairro = ?, cidade = ?, estado = ?, cep = ?,
                        instagram = ?, site = ?, horario_abertura = ?, horario_fechamento = ?,
                        dias_funcionamento = ?, taxa_entrega = ?, pedido_minimo = ?, 
                        raio_entrega = ?, aceita_retirada = ?, aceita_delivery = ?, aceita_local = ?,
                        formas_pagamento = ?, logo = ?, banner = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $dados_sanitizados['nome'],
                    $dados_sanitizados['slogan'],
                    $dados_sanitizados['descricao'],
                    $dados_sanitizados['telefone'],
                    $dados_sanitizados['whatsapp'],
                    $dados_sanitizados['endereco'],
                    $dados_sanitizados['numero'],
                    $dados_sanitizados['bairro'],
                    $dados_sanitizados['cidade'],
                    $dados_sanitizados['estado'],
                    $dados_sanitizados['cep'],
                    $dados_sanitizados['instagram'],
                    $dados_sanitizados['site'],
                    $dados_sanitizados['horario_abertura'],
                    $dados_sanitizados['horario_fechamento'],
                    $dados_sanitizados['dias_funcionamento'],
                    $dados_sanitizados['taxa_entrega'],
                    $dados_sanitizados['pedido_minimo'],
                    $dados_sanitizados['raio_entrega'],
                    $dados_sanitizados['aceita_retirada'],
                    $dados_sanitizados['aceita_delivery'],
                    $dados_sanitizados['aceita_local'],
                    $dados_sanitizados['formas_pagamento'],
                    $logo,
                    $banner,
                    $restaurante['id']
                ]);
                
                $sucesso = 'Configurações salvas com sucesso!';
            }
            break;
            
        case 'adicionar_categoria':
            if ($auth->isLogged() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $restaurante = getRestauranteUsuario($pdo, $auth->getUser()['id']);
                
                if (!$restaurante) {
                    throw new Exception('Restaurante não encontrado.');
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO categorias (restaurante_id, nome, descricao, icone, ordem, ativo)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $restaurante['id'],
                    trim($_POST['nome'] ?? ''),
                    trim($_POST['descricao'] ?? ''),
                    $_POST['icone'] ?? 'fas fa-utensils',
                    (int) ($_POST['ordem'] ?? 0),
                    isset($_POST['ativo']) ? 1 : 0
                ]);
                
                $sucesso = 'Categoria adicionada com sucesso!';
            }
            break;
            
        case 'editar_categoria':
            if ($auth->isLogged() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = (int) ($_POST['id'] ?? 0);
                $nome = trim($_POST['nome'] ?? '');
                
                if ($id < 1 || empty($nome)) {
                    throw new Exception('Dados inválidos para edição.');
                }
                
                $stmt = $pdo->prepare("
                    UPDATE categorias 
                    SET nome = ?, descricao = ?, icone = ?, ordem = ?, ativo = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $nome,
                    trim($_POST['descricao'] ?? ''),
                    $_POST['icone'] ?? 'fas fa-utensils',
                    (int) ($_POST['ordem'] ?? 0),
                    isset($_POST['ativo']) ? 1 : 0,
                    $id
                ]);
                
                $sucesso = 'Categoria atualizada com sucesso!';
            }
            break;
            
        case 'excluir_categoria':
            if ($auth->isLogged()) {
                $id = (int) ($_GET['id'] ?? 0);
                
                if ($id > 0) {
                    // Verificar se existem produtos na categoria
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = ?");
                    $stmt->execute([$id]);
                    $total_produtos = $stmt->fetchColumn();
                    
                    if ($total_produtos > 0) {
                        $erro = 'Não é possível excluir a categoria pois existem produtos vinculados a ela.';
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
                        $stmt->execute([$id]);
                        $sucesso = 'Categoria excluída com sucesso!';
                    }
                }
            }
            break;
            
        case 'adicionar_produto':
            if ($auth->isLogged() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $imagem = null;
                
                // Processar upload da imagem
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                    $imagem = processarUpload($_FILES['imagem'], UPLOAD_DIR . 'produtos/');
                }
                
                // Validar preço
                $preco = str_replace(['R$', ' ', '.'], '', $_POST['preco'] ?? '0');
                $preco = str_replace(',', '.', $preco);
                $preco = (float) $preco;
                
                if ($preco <= 0) {
                    throw new Exception('O preço do produto deve ser maior que zero.');
                }
                
                $preco_promocional = null;
                if (!empty($_POST['preco_promocional'])) {
                    $preco_promocional = str_replace(['R$', ' ', '.'], '', $_POST['preco_promocional']);
                    $preco_promocional = str_replace(',', '.', $preco_promocional);
                    $preco_promocional = (float) $preco_promocional;
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO produtos (categoria_id, nome, descricao, preco, preco_promocional, 
                                        ingredientes, informacoes_nutricionais, tempo_preparo, 
                                        imagem, destaque, disponivel, controle_estoque, 
                                        estoque_atual, estoque_minimo, ordem, tags)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    (int) ($_POST['categoria_id'] ?? 0),
                    trim($_POST['nome'] ?? ''),
                    trim($_POST['descricao'] ?? ''),
                    $preco,
                    $preco_promocional,
                    trim($_POST['ingredientes'] ?? ''),
                    trim($_POST['informacoes_nutricionais'] ?? ''),
                    (int) ($_POST['tempo_preparo'] ?? 20),
                    $imagem,
                    isset($_POST['destaque']) ? 1 : 0,
                    isset($_POST['disponivel']) ? 1 : 0,
                    isset($_POST['controle_estoque']) ? 1 : 0,
                    (int) ($_POST['estoque_atual'] ?? 0),
                    (int) ($_POST['estoque_minimo'] ?? 0),
                    (int) ($_POST['ordem'] ?? 0),
                    trim($_POST['tags'] ?? '')
                ]);
                
                $sucesso = 'Produto adicionado com sucesso!';
            }
            break;
            
        case 'editar_produto':
            if ($auth->isLogged() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $produto_id = (int) ($_POST['id'] ?? 0);
                
                if ($produto_id < 1) {
                    throw new Exception('ID do produto inválido.');
                }
                
                // Obter dados atuais do produto
                $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
                $stmt->execute([$produto_id]);
                $produto_atual = $stmt->fetch(PDO::FETCH_ASSOC);
                $imagem = $produto_atual['imagem'] ?? null;
                
                // Processar upload da nova imagem
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                    // Remover imagem antiga se existir
                    if ($imagem && file_exists($imagem)) {
                        unlink($imagem);
                    }
                    $imagem = processarUpload($_FILES['imagem'], UPLOAD_DIR . 'produtos/');
                }
                
                // Validar preço
                $preco = str_replace(['R$', ' ', '.'], '', $_POST['preco'] ?? '0');
                $preco = str_replace(',', '.', $preco);
                $preco = (float) $preco;
                
                if ($preco <= 0) {
                    throw new Exception('O preço do produto deve ser maior que zero.');
                }
                
                $preco_promocional = null;
                if (!empty($_POST['preco_promocional'])) {
                    $preco_promocional = str_replace(['R$', ' ', '.'], '', $_POST['preco_promocional']);
                    $preco_promocional = str_replace(',', '.', $preco_promocional);
                    $preco_promocional = (float) $preco_promocional;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE produtos 
                    SET categoria_id = ?, nome = ?, descricao = ?, preco = ?, preco_promocional = ?,
                        ingredientes = ?, informacoes_nutricionais = ?, tempo_preparo = ?,
                        imagem = ?, destaque = ?, disponivel = ?, controle_estoque = ?,
                        estoque_atual = ?, estoque_minimo = ?, ordem = ?, tags = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    (int) ($_POST['categoria_id'] ?? 0),
                    trim($_POST['nome'] ?? ''),
                    trim($_POST['descricao'] ?? ''),
                    $preco,
                    $preco_promocional,
                    trim($_POST['ingredientes'] ?? ''),
                    trim($_POST['informacoes_nutricionais'] ?? ''),
                    (int) ($_POST['tempo_preparo'] ?? 20),
                    $imagem,
                    isset($_POST['destaque']) ? 1 : 0,
                    isset($_POST['disponivel']) ? 1 : 0,
                    isset($_POST['controle_estoque']) ? 1 : 0,
                    (int) ($_POST['estoque_atual'] ?? 0),
                    (int) ($_POST['estoque_minimo'] ?? 0),
                    (int) ($_POST['ordem'] ?? 0),
                    trim($_POST['tags'] ?? ''),
                    $produto_id
                ]);
                
                $sucesso = 'Produto atualizado com sucesso!';
            }
            break;
            
        case 'excluir_produto':
            if ($auth->isLogged()) {
                $id = (int) ($_GET['id'] ?? 0);
                
                if ($id > 0) {
                    // Obter dados do produto para excluir a imagem
                    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
                    $stmt->execute([$id]);
                    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($produto && $produto['imagem'] && file_exists($produto['imagem'])) {
                        unlink($produto['imagem']);
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
                    $stmt->execute([$id]);
                    $sucesso = 'Produto excluído com sucesso!';
                }
            }
            break;
            
        case 'atualizar_status_pedido':
            if ($auth->isLogged()) {
                $pedido_id = (int) ($_POST['pedido_id'] ?? 0);
                $novo_status = $_POST['novo_status'] ?? '';
                
                if ($pedido_id > 0 && !empty($novo_status)) {
                    // Verificar se o pedido pertence ao restaurante do usuário
                    $usuario_id = $auth->getUserId();
                    $restaurante = getRestauranteUsuario($pdo, $usuario_id);
                    
                    $stmt = $pdo->prepare("SELECT id FROM pedidos WHERE id = ? AND restaurante_id = ?");
                    $stmt->execute([$pedido_id, $restaurante['id']]);
                    
                    if (!$stmt->fetch()) {
                        throw new Exception('Pedido não encontrado ou acesso negado.');
                    }
                    
                    $campo_data = '';
                    switch ($novo_status) {
                        case 'aceito': $campo_data = 'data_aceito'; break;
                        case 'preparo': $campo_data = 'data_preparo'; break;
                        case 'pronto': $campo_data = 'data_pronto'; break;
                        case 'entregue': $campo_data = 'data_entrega'; break;
                        case 'finalizado': $campo_data = 'data_finalizado'; break;
                    }
                    
                    $sql = "UPDATE pedidos SET status = ?";
                    if ($campo_data) {
                        $sql .= ", $campo_data = CURRENT_TIMESTAMP";
                    }
                    $sql .= " WHERE id = ?";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$novo_status, $pedido_id]);
                    
                    $sucesso = 'Status do pedido atualizado com sucesso!';
                }
            }
            break;
            
        case 'adicionar_funcionario':
            if ($auth->isLogged() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $restaurante = getRestauranteUsuario($pdo, $auth->getUser()['id']);
                
                if (!$restaurante) {
                    throw new Exception('Restaurante não encontrado.');
                }
                
                // Validar email
                $email = trim($_POST['email'] ?? '');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('E-mail inválido.');
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO funcionarios (restaurante_id, nome, email, telefone, cargo, permissoes, ativo)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $permissoes = is_array($_POST['permissoes'] ?? []) ? implode(',', $_POST['permissoes']) : '';
                
                $stmt->execute([
                    $restaurante['id'],
                    trim($_POST['nome'] ?? ''),
                    $email,
                    trim($_POST['telefone'] ?? ''),
                    $_POST['cargo'] ?? 'atendente',
                    $permissoes,
                    isset($_POST['ativo']) ? 1 : 0
                ]);
                
                $sucesso = 'Funcionário adicionado com sucesso!';
            }
            break;
            
        case 'editar_funcionario':
            if ($auth->isLogged() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $funcionario_id = (int) ($_POST['id'] ?? 0);
                
                if ($funcionario_id < 1) {
                    throw new Exception('ID do funcionário inválido.');
                }
                
                // Validar email
                $email = trim($_POST['email'] ?? '');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('E-mail inválido.');
                }
                
                $stmt = $pdo->prepare("
                    UPDATE funcionarios 
                    SET nome = ?, email = ?, telefone = ?, cargo = ?, permissoes = ?, ativo = ?
                    WHERE id = ?
                ");
                
                $permissoes = is_array($_POST['permissoes'] ?? []) ? implode(',', $_POST['permissoes']) : '';
                
                $stmt->execute([
                    trim($_POST['nome'] ?? ''),
                    $email,
                    trim($_POST['telefone'] ?? ''),
                    $_POST['cargo'] ?? 'atendente',
                    $permissoes,
                    isset($_POST['ativo']) ? 1 : 0,
                    $funcionario_id
                ]);
                
                $sucesso = 'Funcionário atualizado com sucesso!';
            }
            break;
            
        case 'excluir_funcionario':
            if ($auth->isLogged()) {
                $id = (int) ($_GET['id'] ?? 0);
                
                if ($id > 0) {
                    $stmt = $pdo->prepare("DELETE FROM funcionarios WHERE id = ?");
                    $stmt->execute([$id]);
                    $sucesso = 'Funcionário excluído com sucesso!';
                }
            }
            break;
        
        default:
            // Nenhuma ação específica
            break;
    }
} catch (Exception $e) {
    $erro = $e->getMessage();
    error_log("Erro no sistema: " . $e->getMessage());
}

// Se não estiver logado, mostrar página de login
if (!$auth->isLogged()) {
    include __DIR__ . '/templates/login.php';
    exit;
}

// Página do dashboard
$usuario_logado = $auth->getUser();
$restaurante = getRestauranteUsuario($pdo, $usuario_logado['id']);

if (!$restaurante) {
    $auth->logout();
    header("Location: index.php");
    exit;
}

// Obter estatísticas
$stats = obterEstatisticasRestaurante($pdo, $restaurante['id']);
$pedidos_recebidos = getPedidos($pdo, $restaurante['id'], ['status' => 'recebido']);

// Incluir template do dashboard
include __DIR__ . '/templates/dashboard.php';
?>