<?php
// helpers/functions.php - Funções auxiliares usadas por ambos sistemas

require_once __DIR__ . '/../config.php';

function formatarMoeda($valor) {
    if (!is_numeric($valor)) {
        return 'R$ 0,00';
    }
    return 'R$ ' . number_format(floatval($valor), 2, ',', '.');
}

function gerarCodigoPedido() {
    return 'PED' . date('YmdHis') . mt_rand(100, 999);
}

function getRestauranteUsuario($pdo, $usuario_id) {
    $stmt = $pdo->prepare("
        SELECT r.*, u.nome as usuario_nome, u.email as usuario_email 
        FROM restaurantes r 
        LEFT JOIN usuarios u ON r.usuario_id = u.id 
        WHERE r.usuario_id = ?
    ");
    $stmt->execute([$usuario_id]);
    $restaurante = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($restaurante) {
        // Garantir que os campos de arrays sejam arrays
        $restaurante['dias_funcionamento'] = explode(',', $restaurante['dias_funcionamento'] ?? '');
        $restaurante['formas_pagamento'] = explode(',', $restaurante['formas_pagamento'] ?? '');
    }
    
    return $restaurante;
}

function validarESanitizarConfiguracoes($dados) {
    $sanitizado = [];
    
    // 1. Sanitização de Strings
    $sanitizado['nome'] = trim($dados['nome'] ?? '');
    $sanitizado['slogan'] = trim($dados['slogan'] ?? '');
    $sanitizado['descricao'] = trim($dados['descricao'] ?? '');
    $sanitizado['telefone'] = preg_replace('/[^0-9]/', '', $dados['telefone'] ?? '');
    $sanitizado['whatsapp'] = preg_replace('/[^0-9]/', '', $dados['whatsapp'] ?? '');
    $sanitizado['endereco'] = trim($dados['endereco'] ?? '');
    $sanitizado['numero'] = trim($dados['numero'] ?? '');
    $sanitizado['bairro'] = trim($dados['bairro'] ?? '');
    $sanitizado['cidade'] = trim($dados['cidade'] ?? '');
    $sanitizado['estado'] = trim($dados['estado'] ?? '');
    $sanitizado['cep'] = preg_replace('/[^0-9]/', '', $dados['cep'] ?? '');
    $sanitizado['instagram'] = trim($dados['instagram'] ?? '');
    $sanitizado['site'] = trim($dados['site'] ?? '');
    
    // Validar campos obrigatórios
    if (empty($sanitizado['nome'])) {
        throw new Exception('O nome do restaurante é obrigatório.');
    }
    
    if (empty($sanitizado['telefone'])) {
        throw new Exception('O telefone é obrigatório.');
    }
    
    // 2. Validação e Sanitização de Horários
    $horario_abertura = trim($dados['horario_abertura'] ?? '10:00');
    $horario_fechamento = trim($dados['horario_fechamento'] ?? '22:00');
    
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $horario_abertura)) {
        throw new Exception('Formato de horário de abertura inválido. Use HH:MM.');
    }
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $horario_fechamento)) {
        throw new Exception('Formato de horário de fechamento inválido. Use HH:MM.');
    }
    
    $sanitizado['horario_abertura'] = $horario_abertura;
    $sanitizado['horario_fechamento'] = $horario_fechamento;
    
    // 3. Validação e Sanitização de Dias de Funcionamento
    $dias_validos = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
    $dias_selecionados = $dados['dias_funcionamento'] ?? [];
    
    if (empty($dias_selecionados)) {
        throw new Exception('Selecione pelo menos um dia de funcionamento.');
    }
    
    if (!is_array($dias_selecionados)) {
        $dias_selecionados = explode(',', $dias_selecionados);
    }
    
    $dias_filtrados = array_filter($dias_selecionados, function($dia) use ($dias_validos) {
        return in_array($dia, $dias_validos);
    });
    
    $sanitizado['dias_funcionamento'] = implode(',', $dias_filtrados);
    
    // 4. Sanitização de Valores Monetários
    $taxa_entrega_str = str_replace(['R$', ' ', '.'], '', $dados['taxa_entrega'] ?? '0');
    $taxa_entrega_str = str_replace(',', '.', $taxa_entrega_str);
    
    $pedido_minimo_str = str_replace(['R$', ' ', '.'], '', $dados['pedido_minimo'] ?? '0');
    $pedido_minimo_str = str_replace(',', '.', $pedido_minimo_str);
    
    if (!is_numeric($taxa_entrega_str) || $taxa_entrega_str < 0) {
        throw new Exception('Valor de Taxa de Entrega inválido.');
    }
    if (!is_numeric($pedido_minimo_str) || $pedido_minimo_str < 0) {
        throw new Exception('Valor de Pedido Mínimo inválido.');
    }
    
    $sanitizado['taxa_entrega'] = (float) $taxa_entrega_str;
    $sanitizado['pedido_minimo'] = (float) $pedido_minimo_str;
    
    // 5. Sanitização de Inteiros
    $raio_entrega = filter_var($dados['raio_entrega'] ?? 5, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($raio_entrega === false) {
        throw new Exception('Valor de Raio de Entrega inválido.');
    }
    $sanitizado['raio_entrega'] = $raio_entrega;
    
    // 6. Sanitização de Booleans
    $sanitizado['aceita_retirada'] = isset($dados['aceita_retirada']) ? 1 : 0;
    $sanitizado['aceita_delivery'] = isset($dados['aceita_delivery']) ? 1 : 0;
    $sanitizado['aceita_local'] = isset($dados['aceita_local']) ? 1 : 0;
    
    // 7. Sanitização de Formas de Pagamento
    $formas_validas = ['dinheiro', 'pix', 'credito', 'debito', 'vale'];
    $formas_selecionadas = $dados['formas_pagamento'] ?? [];
    
    if (empty($formas_selecionadas)) {
        throw new Exception('Selecione pelo menos uma forma de pagamento.');
    }
    
    if (!is_array($formas_selecionadas)) {
        $formas_selecionadas = explode(',', $formas_selecionadas);
    }
    
    $formas_filtradas = array_filter($formas_selecionadas, function($forma) use ($formas_validas) {
        return in_array($forma, $formas_validas);
    });
    
    if (empty($formas_filtradas)) {
        throw new Exception('Selecione formas de pagamento válidas.');
    }
    
    $sanitizado['formas_pagamento'] = implode(',', $formas_filtradas);
    
    return $sanitizado;
}

function obterEstatisticasRestaurante($pdo, $restaurante_id) {
    $hoje = date('Y-m-d');
    $mes_atual = date('Y-m-01');
    $semana_passada = date('Y-m-d', strtotime('-7 days'));
    
    $stats = [
        'pedidos_hoje' => 0,
        'pedidos_mes' => 0,
        'faturamento_hoje' => 0,
        'faturamento_mes' => 0,
        'produtos_ativos' => 0,
        'avaliacao_media' => 5.0,
        'total_categorias' => 0,
        'total_produtos' => 0,
        'pedidos_semana' => 0,
        'produtos_mais_vendidos' => [],
        'horarios_pico' => [],
        'clientes_recorrentes' => 0
    ];
    
    try {
        // Pedidos hoje
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as faturamento 
            FROM pedidos 
            WHERE restaurante_id = ? AND DATE(data_pedido) = ?
        ");
        $stmt->execute([$restaurante_id, $hoje]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['pedidos_hoje'] = (int) ($result['total'] ?? 0);
        $stats['faturamento_hoje'] = (float) ($result['faturamento'] ?? 0);
        
        // Pedidos mês
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as faturamento 
            FROM pedidos 
            WHERE restaurante_id = ? AND data_pedido >= ?
        ");
        $stmt->execute([$restaurante_id, $mes_atual]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['pedidos_mes'] = (int) ($result['total'] ?? 0);
        $stats['faturamento_mes'] = (float) ($result['faturamento'] ?? 0);
        
        // Pedidos semana
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM pedidos 
            WHERE restaurante_id = ? AND data_pedido >= ?
        ");
        $stmt->execute([$restaurante_id, $semana_passada]);
        $stats['pedidos_semana'] = (int) $stmt->fetchColumn();
        
        // Produtos ativos
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM produtos p 
            JOIN categorias c ON p.categoria_id = c.id 
            WHERE c.restaurante_id = ? AND p.disponivel = 1
        ");
        $stmt->execute([$restaurante_id]);
        $stats['produtos_ativos'] = (int) $stmt->fetchColumn();
        
        // Total de categorias
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE restaurante_id = ? AND ativo = 1");
        $stmt->execute([$restaurante_id]);
        $stats['total_categorias'] = (int) $stmt->fetchColumn();
        
        // Total de produtos
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM produtos p 
            JOIN categorias c ON p.categoria_id = c.id 
            WHERE c.restaurante_id = ?
        ");
        $stmt->execute([$restaurante_id]);
        $stats['total_produtos'] = (int) $stmt->fetchColumn();
        
        // Avaliação média
        $stmt = $pdo->prepare("SELECT COALESCE(avaliacao_media, 5.0) FROM restaurantes WHERE id = ?");
        $stmt->execute([$restaurante_id]);
        $stats['avaliacao_media'] = (float) $stmt->fetchColumn();
        
        // Produtos mais vendidos
        $stmt = $pdo->prepare("
            SELECT p.id, p.nome, 
                   (SELECT COUNT(*) FROM pedidos ped 
                    WHERE ped.restaurante_id = ? AND ped.itens_json LIKE '%\"id\":' || p.id || '%') as vendas
            FROM produtos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE c.restaurante_id = ?
            ORDER BY vendas DESC
            LIMIT 5
        ");
        $stmt->execute([$restaurante_id, $restaurante_id]);
        $stats['produtos_mais_vendidos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Erro ao obter estatísticas: " . $e->getMessage());
    }
    
    return $stats;
}

function processarUpload($arquivo, $diretorio, $nome_arquivo = null) {
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro no upload do arquivo. Código: ' . $arquivo['error']);
    }
    
    if ($arquivo['size'] > MAX_FILE_SIZE) {
        throw new Exception('Arquivo muito grande. Tamanho máximo: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB');
    }
    
    // Verificar tipo de arquivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_arquivo = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($tipo_arquivo, ALLOWED_IMAGE_TYPES)) {
        throw new Exception('Tipo de arquivo não permitido. Use apenas imagens JPEG, PNG, GIF ou WebP.');
    }
    
    // Validar extensão
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extensao, $extensoes_permitidas)) {
        throw new Exception('Extensão de arquivo não permitida.');
    }
    
    // Gerar nome único
    $nome_final = $nome_arquivo ? $nome_arquivo . '.' . $extensao : uniqid() . '.' . $extensao;
    $caminho_final = $diretorio . $nome_final;
    
    // Verificar se diretório existe
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }
    
    if (!move_uploaded_file($arquivo['tmp_name'], $caminho_final)) {
        throw new Exception('Falha ao mover o arquivo enviado.');
    }
    
    return $caminho_final;
}

function getCategorias($pdo, $restaurante_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM categorias 
        WHERE restaurante_id = ? 
        ORDER BY ordem ASC, nome ASC
    ");
    $stmt->execute([$restaurante_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoria($pdo, $categoria_id) {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$categoria_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProdutos($pdo, $restaurante_id) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome 
        FROM produtos p
        JOIN categorias c ON p.categoria_id = c.id
        WHERE c.restaurante_id = ?
        ORDER BY p.ordem ASC, p.nome ASC
    ");
    $stmt->execute([$restaurante_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProdutosPorCategoria($pdo, $categoria_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM produtos 
        WHERE categoria_id = ? 
        ORDER BY ordem ASC, nome ASC
    ");
    $stmt->execute([$categoria_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProduto($pdo, $produto_id) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPedidos($pdo, $restaurante_id, $filtros = []) {
    $sql = "SELECT * FROM pedidos WHERE restaurante_id = ?";
    $params = [$restaurante_id];
    
    if (!empty($filtros['status'])) {
        $sql .= " AND status = ?";
        $params[] = $filtros['status'];
    }
    
    if (!empty($filtros['data_inicio'])) {
        $sql .= " AND DATE(data_pedido) >= ?";
        $params[] = $filtros['data_inicio'];
    }
    
    if (!empty($filtros['data_fim'])) {
        $sql .= " AND DATE(data_pedido) <= ?";
        $params[] = $filtros['data_fim'];
    }
    
    if (!empty($filtros['tipo_pedido'])) {
        $sql .= " AND tipo_pedido = ?";
        $params[] = $filtros['tipo_pedido'];
    }
    
    $sql .= " ORDER BY data_pedido DESC LIMIT 100";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPedido($pdo, $pedido_id) {
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pedido) {
        // Decodificar JSON com tratamento de erros
        $itens = json_decode($pedido['itens_json'], true);
        $pedido['itens'] = is_array($itens) ? $itens : [];
    }
    
    return $pedido;
}

function getFuncionarios($pdo, $restaurante_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM funcionarios 
        WHERE restaurante_id = ? 
        ORDER BY nome ASC
    ");
    $stmt->execute([$restaurante_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFuncionario($pdo, $funcionario_id) {
    $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE id = ?");
    $stmt->execute([$funcionario_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>