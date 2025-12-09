<?php
// EasyMenu - Cardápio Público Premium
// cardapio.php - Cardápio para clientes

// Incluir configurações e classes
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/helpers/functions.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
} catch (Exception $e) {
    die('<h1>Erro ao inicializar o sistema</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>');
}

// =============================================================================
// PROCESSAMENTO DO PEDIDO (manter igual do seu código original)
// =============================================================================

$sucesso = $erro = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'fazer_pedido') {
        
        // Validar dados
        $restaurante_id = filter_var($_POST['restaurante_id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$restaurante_id || $restaurante_id < 1) {
            throw new Exception('ID do restaurante inválido.');
        }
        
        // Validar JSON dos itens
        $itens_json = $_POST['itens_json'] ?? '[]';
        $itens = json_decode($itens_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Erro ao processar os itens do pedido.');
        }
        
        if (!$itens || count($itens) === 0) {
            throw new Exception('Nenhum item no pedido.');
        }
        
        // Calcular totais
        $subtotal = 0;
        foreach ($itens as $item) {
            if (!isset($item['preco'], $item['quantidade'])) {
                throw new Exception('Item do pedido inválido.');
            }
            $subtotal += floatval($item['preco']) * intval($item['quantidade']);
        }
        
        // Obter dados do restaurante
        $stmt = $pdo->prepare("SELECT taxa_entrega, pedido_minimo FROM restaurantes WHERE id = ? AND ativo = 1");
        $stmt->execute([$restaurante_id]);
        $restaurante = $stmt->fetch();
        
        if (!$restaurante) {
            throw new Exception('Restaurante não encontrado ou inativo.');
        }
        
        // Validar dados do pedido
        $tipo_pedido = $_POST['tipo_pedido'] ?? '';
        $forma_pagamento = $_POST['forma_pagamento'] ?? '';
        $cliente_nome = trim($_POST['cliente_nome'] ?? '');
        $cliente_telefone = trim($_POST['cliente_telefone'] ?? '');
        
        if (empty($cliente_nome) || empty($cliente_telefone)) {
            throw new Exception('Nome e telefone são obrigatórios.');
        }
        
        $taxa_entrega = ($tipo_pedido === 'delivery') ? floatval($restaurante['taxa_entrega']) : 0;
        $total = $subtotal + $taxa_entrega;
        
        // Verificar pedido mínimo
        if ($subtotal < floatval($restaurante['pedido_minimo'])) {
            throw new Exception('Pedido mínimo não atingido. Valor mínimo: ' . formatarMoeda($restaurante['pedido_minimo']));
        }
        
        // Gerar código único
        $codigo = gerarCodigoPedido();
        
        // Preparar e executar a query
        $stmt = $pdo->prepare("
            INSERT INTO pedidos (
                codigo, restaurante_id, itens_json, subtotal, taxa_entrega, total,
                tipo_pedido, forma_pagamento, cliente_nome, cliente_telefone,
                cliente_email, endereco_entrega, complemento, ponto_referencia,
                cep, troco_para, observacoes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $codigo,
            $restaurante_id,
            $itens_json,
            $subtotal,
            $taxa_entrega,
            $total,
            $tipo_pedido,
            $forma_pagamento,
            $cliente_nome,
            $cliente_telefone,
            trim($_POST['cliente_email'] ?? ''),
            trim($_POST['endereco_entrega'] ?? ''),
            trim($_POST['complemento'] ?? ''),
            trim($_POST['ponto_referencia'] ?? ''),
            trim($_POST['cep'] ?? ''),
            !empty($_POST['troco_para']) ? floatval($_POST['troco_para']) : null,
            trim($_POST['observacoes'] ?? '')
        ]);
        
        $sucesso = 'Pedido realizado com sucesso! Código: ' . $codigo;
        
        // Script para limpar carrinho
        echo '<script>localStorage.removeItem("carrinhoEasyMenu");</script>';
    }
} catch (Exception $e) {
    $erro = $e->getMessage();
}

// =============================================================================
// OBTER DADOS DO RESTAURANTE (manter igual do seu código original)
// =============================================================================

try {
    // Aceitar ambos os parâmetros
    $restaurante_id = $_GET['id'] ?? $_GET['restaurante_id'] ?? null;
    
    // Converter para inteiro
    $restaurante_id = filter_var($restaurante_id, FILTER_VALIDATE_INT);
    
    if (!$restaurante_id || $restaurante_id < 1) {
        // Tentar pegar o primeiro restaurante ativo
        $stmt = $pdo->query("SELECT id FROM restaurantes WHERE ativo = 1 LIMIT 1");
        $result = $stmt->fetch();
        
        if ($result && isset($result['id'])) {
            $restaurante_id = intval($result['id']);
        } else {
            throw new Exception('Nenhum restaurante disponível no momento.');
        }
    }
    
    // Obter dados do restaurante
    $stmt = $pdo->prepare("SELECT * FROM restaurantes WHERE id = ? AND ativo = 1");
    $stmt->execute([$restaurante_id]);
    $restaurante_cardapio = $stmt->fetch();
    
    if (!$restaurante_cardapio) {
        throw new Exception('Restaurante não encontrado ou inativo.');
    }
    
    // Obter categorias ativas
    $stmt = $pdo->prepare("
        SELECT * FROM categorias 
        WHERE restaurante_id = ? AND ativo = 1 
        ORDER BY ordem, nome
    ");
    $stmt->execute([$restaurante_id]);
    $categorias_cardapio = $stmt->fetchAll();
    
    // Obter produtos disponíveis
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome, c.id as categoria_id
        FROM produtos p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE c.restaurante_id = ? AND p.disponivel = 1 AND c.ativo = 1
        ORDER BY c.ordem, p.ordem, p.nome
    ");
    $stmt->execute([$restaurante_id]);
    $produtos_cardapio = $stmt->fetchAll();
    
    // Obter produtos em destaque
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE c.restaurante_id = ? AND p.disponivel = 1 AND c.ativo = 1 AND p.destaque = 1
        ORDER BY p.ordem, p.nome
        LIMIT 8
    ");
    $stmt->execute([$restaurante_id]);
    $produtos_destaque = $stmt->fetchAll();
    
    // Agrupar produtos por categoria
    $produtos_por_categoria = [];
    foreach ($produtos_cardapio as $produto) {
        $categoria_id = $produto['categoria_id'];
        if (!isset($produtos_por_categoria[$categoria_id])) {
            $produtos_por_categoria[$categoria_id] = [
                'categoria' => $produto['categoria_nome'],
                'produtos' => []
            ];
        }
        $produtos_por_categoria[$categoria_id]['produtos'][] = $produto;
    }
    
} catch (Exception $e) {
    die('<h1>Erro ao carregar cardápio</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>');
}

// =============================================================================
// RENDERIZAÇÃO DO HTML (manter TODO o HTML do seu código original)
// =============================================================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- TODO o HEAD do seu cardapio.php original -->
</head>
<body>
    <!-- TODO o BODY do seu cardapio.php original -->
</body>
</html>