<?php
// helpers/render.php - Funções de renderização específicas do dashboard

// Incluir funções básicas
require_once __DIR__ . '/functions.php';

function render_painel($pdo, $restaurante, $stats, $pedidos_recebidos) {
    global $status_cores;
?>
<div class="space-y-6 animate-fade-in">
    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Pedidos Hoje -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-5 rounded-2xl shadow-xl transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-light opacity-90">Pedidos Hoje</p>
                    <p class="text-3xl font-bold mt-1"><?php echo $stats['pedidos_hoje']; ?></p>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
            </div>
            <div class="mt-3 text-sm opacity-80">
                <i class="fas fa-arrow-up mr-1"></i> Atualizado agora
            </div>
        </div>
        
        <!-- Card 2: Faturamento Hoje -->
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 text-white p-5 rounded-2xl shadow-xl transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-light opacity-90">Faturamento Hoje</p>
                    <p class="text-3xl font-bold mt-1"><?php echo formatarMoeda($stats['faturamento_hoje']); ?></p>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
            </div>
            <div class="mt-3 text-sm opacity-80">
                <i class="fas fa-chart-line mr-1"></i> Em tempo real
            </div>
        </div>
        
        <!-- Card 3: Avaliação Média -->
        <div class="bg-gradient-to-br from-yellow-500 to-amber-600 text-white p-5 rounded-2xl shadow-xl transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-light opacity-90">Avaliação Média</p>
                    <div class="flex items-center mt-1">
                        <p class="text-3xl font-bold"><?php echo number_format($stats['avaliacao_media'], 1, ',', '.'); ?></p>
                        <div class="ml-2 flex">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star text-sm <?php echo $i <= round($stats['avaliacao_media']) ? 'text-white' : 'text-white/40'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <i class="fas fa-star text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 4: Produtos Ativos -->
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white p-5 rounded-2xl shadow-xl transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-light opacity-90">Produtos Ativos</p>
                    <p class="text-3xl font-bold mt-1"><?php echo $stats['produtos_ativos']; ?></p>
                </div>
                <div class="bg-white/20 p-3 rounded-xl">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
            </div>
            <div class="mt-3 text-sm opacity-80">
                <i class="fas fa-tag mr-1"></i> <?php echo $stats['total_categorias']; ?> categorias
            </div>
        </div>
    </div>

    <!-- Grid de Conteúdo -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pedidos Recentes -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-4 text-white">
                    <h3 class="text-xl font-bold flex items-center">
                        <i class="fas fa-clock mr-2"></i> Pedidos Recentes
                    </h3>
                </div>
                <div class="p-4">
                    <?php if (empty($pedidos_recebidos)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 opacity-30"></i>
                            <p class="font-medium">Nenhum pedido novo</p>
                            <p class="text-sm mt-1">Novos pedidos aparecerão aqui</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            <?php foreach (array_slice($pedidos_recebidos, 0, 8) as $pedido): ?>
                                <a href="?pagina=pedidos&ver=<?php echo $pedido['id']; ?>" 
                                   class="block border rounded-xl p-3 hover:border-red-300 hover:bg-red-50 transition-all duration-200 group">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="flex items-center">
                                                <span class="font-bold text-gray-900 group-hover:text-red-700">#<?php echo htmlspecialchars($pedido['codigo']); ?></span>
                                                <span class="ml-3 text-sm px-2 py-1 rounded-full <?php echo $status_cores[$pedido['status']] ?? 'bg-gray-500'; ?> text-white">
                                                    <?php echo ucfirst($pedido['status']); ?>
                                                </span>
                                            </div>
                                            <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($pedido['cliente_nome']); ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-red-600 text-lg"><?php echo formatarMoeda($pedido['total']); ?></p>
                                            <p class="text-gray-500 text-xs"><?php echo date('H:i', strtotime($pedido['data_pedido'])); ?></p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Produtos Mais Vendidos -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4 text-white">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-fire mr-2"></i> Top Produtos
                </h3>
            </div>
            <div class="p-4">
                <?php if (empty($stats['produtos_mais_vendidos'])): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-bar text-4xl mb-3 opacity-30"></i>
                        <p class="font-medium">Sem dados de vendas</p>
                        <p class="text-sm mt-1">Comece a vender para ver estatísticas</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($stats['produtos_mais_vendidos'] as $index => $item): ?>
                            <?php if (!empty($item['nome'])): ?>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 rounded-full font-bold">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="font-medium text-gray-900 truncate"><?php echo htmlspecialchars($item['nome']); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo $item['vendas'] ?? 0; ?> vendas</p>
                                    </div>
                                    <div class="ml-2">
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden w-24">
                                            <div class="h-full bg-red-500" style="width: <?php echo min(100, (($item['vendas'] ?? 0) / 10) * 100); ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
}

function render_cardapio($pdo, $restaurante) {
    global $icones_disponiveis;
    
    $categorias = getCategorias($pdo, $restaurante['id']);
    $produtos = getProdutos($pdo, $restaurante['id']);
    
    // Verificar se é para editar uma categoria
    $editar_categoria_id = $_GET['editar_categoria'] ?? null;
    $categoria_editando = null;
    if ($editar_categoria_id) {
        $categoria_editando = getCategoria($pdo, $editar_categoria_id);
    }
    
    // Verificar se é para editar um produto
    $editar_produto_id = $_GET['editar_produto'] ?? null;
    $produto_editando = null;
    if ($editar_produto_id) {
        $produto_editando = getProduto($pdo, $editar_produto_id);
    }
?>
<div class="animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 text-white mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-utensils mr-3"></i> Gerenciar Cardápio
                </h2>
                <p class="text-red-100 mt-2 opacity-90">
                    Organize suas categorias e produtos
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                    <p class="text-sm font-medium">
                        <span class="font-bold text-xl"><?php echo count($categorias); ?></span> categorias • 
                        <span class="font-bold text-xl"><?php echo count($produtos); ?></span> produtos
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Abas -->
    <div class="flex space-x-1 bg-gray-100 rounded-2xl p-1 mb-6">
        <button id="tabCategorias" 
                onclick="showTab('categorias')"
                class="flex-1 py-3 px-4 rounded-xl font-bold transition-all duration-300 bg-white text-red-600 shadow-lg transform scale-105">
            <i class="fas fa-list mr-2"></i> Categorias
        </button>
        <button id="tabProdutos" 
                onclick="showTab('produtos')"
                class="flex-1 py-3 px-4 rounded-xl font-bold transition-all duration-300 text-gray-600 hover:text-red-600">
            <i class="fas fa-hamburger mr-2"></i> Produtos
        </button>
    </div>

    <!-- Conteúdo das Abas -->
    <div id="conteudoCategorias" class="space-y-6">
        <!-- Formulário de Categoria -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus-circle text-red-500 mr-2"></i> 
                    <?php echo $categoria_editando ? 'Editar Categoria' : 'Nova Categoria'; ?>
                </h3>
            </div>
            <form method="POST" action="index.php" class="p-5" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="<?php echo $categoria_editando ? 'editar_categoria' : 'adicionar_categoria'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <?php if ($categoria_editando): ?>
                    <input type="hidden" name="id" value="<?php echo $categoria_editando['id']; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria *</label>
                        <input type="text" name="nome" required 
                               value="<?php echo $categoria_editando ? htmlspecialchars($categoria_editando['nome']) : ''; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ícone</label>
                        <select name="icone" 
                                class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                            <option value="">Selecione um ícone</option>
                            <?php foreach ($icones_disponiveis as $valor => $label): ?>
                                <option value="<?php echo $valor; ?>" 
                                        <?php echo ($categoria_editando && $categoria_editando['icone'] == $valor) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea name="descricao" rows="3"
                              class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"><?php echo $categoria_editando ? htmlspecialchars($categoria_editando['descricao']) : ''; ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                        <input type="number" name="ordem" 
                               value="<?php echo $categoria_editando ? $categoria_editando['ordem'] : '0'; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="ativo" id="ativo" 
                               <?php echo (!$categoria_editando || $categoria_editando['ativo']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="ativo" class="ml-2 block text-sm text-gray-900">
                            Categoria ativa
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <?php if ($categoria_editando): ?>
                        <a href="?pagina=cardapio" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </a>
                    <?php endif; ?>
                    
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-medium">
                        <i class="fas fa-save mr-2"></i> 
                        <?php echo $categoria_editando ? 'Atualizar' : 'Salvar'; ?> Categoria
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Categorias -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list text-red-500 mr-2"></i> Categorias Cadastradas
                </h3>
            </div>
            <div class="p-4">
                <?php if (empty($categorias)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-list-alt text-4xl mb-3 opacity-30"></i>
                        <p class="font-medium">Nenhuma categoria cadastrada</p>
                        <p class="text-sm mt-1">Crie sua primeira categoria acima</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($categorias as $categoria): ?>
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-red-300 hover:bg-red-50 transition-all duration-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="<?php echo $categoria['icone'] ?: 'fas fa-utensils'; ?>"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($categoria['nome']); ?></h4>
                                            <span class="text-xs px-2 py-1 rounded-full <?php echo $categoria['ativo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $categoria['ativo'] ? 'Ativa' : 'Inativa'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Ordem: <?php echo $categoria['ordem']; ?>
                                    </div>
                                </div>
                                
                                <?php if ($categoria['descricao']): ?>
                                    <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($categoria['descricao']); ?></p>
                                <?php endif; ?>
                                
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-500">
                                        <?php 
                                            $produtos_categoria = getProdutosPorCategoria($pdo, $categoria['id']);
                                            echo count($produtos_categoria) . ' produto(s)';
                                        ?>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="?pagina=cardapio&editar_categoria=<?php echo $categoria['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?pagina=cardapio&acao=excluir_categoria&id=<?php echo $categoria['id']; ?>" 
                                           onclick="return confirm('Tem certeza que deseja excluir esta categoria?')"
                                           class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="conteudoProdutos" class="space-y-6 hidden">
        <!-- Formulário de Produto -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus-circle text-red-500 mr-2"></i> 
                    <?php echo $produto_editando ? 'Editar Produto' : 'Novo Produto'; ?>
                </h3>
            </div>
            <form method="POST" action="index.php" class="p-5" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="<?php echo $produto_editando ? 'editar_produto' : 'adicionar_produto'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <?php if ($produto_editando): ?>
                    <input type="hidden" name="id" value="<?php echo $produto_editando['id']; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto *</label>
                        <input type="text" name="nome" required 
                               value="<?php echo $produto_editando ? htmlspecialchars($produto_editando['nome']) : ''; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
                        <select name="categoria_id" required 
                                class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>" 
                                        <?php echo ($produto_editando && $produto_editando['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea name="descricao" rows="3"
                              class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"><?php echo $produto_editando ? htmlspecialchars($produto_editando['descricao']) : ''; ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço Normal *</label>
                        <input type="text" name="preco" required 
                               value="<?php echo $produto_editando ? formatarMoeda($produto_editando['preco']) : ''; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 input-moeda"
                               placeholder="R$ 0,00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço Promocional</label>
                        <input type="text" name="preco_promocional" 
                               value="<?php echo $produto_editando && $produto_editando['preco_promocional'] ? formatarMoeda($produto_editando['preco_promocional']) : ''; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 input-moeda"
                               placeholder="R$ 0,00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempo de Preparo (min)</label>
                        <input type="number" name="tempo_preparo" 
                               value="<?php echo $produto_editando ? $produto_editando['tempo_preparo'] : '20'; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ingredientes</label>
                        <textarea name="ingredientes" rows="3"
                                  class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"><?php echo $produto_editando ? htmlspecialchars($produto_editando['ingredientes']) : ''; ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Informações Nutricionais</label>
                        <textarea name="informacoes_nutricionais" rows="3"
                                  class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"><?php echo $produto_editando ? htmlspecialchars($produto_editando['informacoes_nutricionais']) : ''; ?></textarea>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagem do Produto</label>
                    <?php if ($produto_editando && $produto_editando['imagem']): ?>
                        <div class="mb-3">
                            <img src="<?php echo $produto_editando['imagem']; ?>" 
                                 alt="Imagem atual" 
                                 class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <p class="text-sm text-gray-500 mt-1">Imagem atual</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="imagem" 
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <p class="text-xs text-gray-500 mt-1">Formatos: JPEG, PNG, GIF, WebP. Máx: 5MB</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                        <input type="number" name="ordem" 
                               value="<?php echo $produto_editando ? $produto_editando['ordem'] : '0'; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Atual</label>
                        <input type="number" name="estoque_atual" 
                               value="<?php echo $produto_editando ? $produto_editando['estoque_atual'] : '0'; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Mínimo</label>
                        <input type="number" name="estoque_minimo" 
                               value="<?php echo $produto_editando ? $produto_editando['estoque_minimo'] : '0'; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                        <input type="text" name="tags" 
                               value="<?php echo $produto_editando ? htmlspecialchars($produto_editando['tags']) : ''; ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="tag1, tag2, tag3">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="destaque" id="destaque" 
                               <?php echo ($produto_editando && $produto_editando['destaque']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="destaque" class="ml-2 block text-sm text-gray-900">
                            Produto em Destaque
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="disponivel" id="disponivel" 
                               <?php echo (!$produto_editando || $produto_editando['disponivel']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="disponivel" class="ml-2 block text-sm text-gray-900">
                            Disponível para venda
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="controle_estoque" id="controle_estoque" 
                               <?php echo ($produto_editando && $produto_editando['controle_estoque']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="controle_estoque" class="ml-2 block text-sm text-gray-900">
                            Controlar Estoque
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <?php if ($produto_editando): ?>
                        <a href="?pagina=cardapio&tab=produtos" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </a>
                    <?php endif; ?>
                    
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-medium">
                        <i class="fas fa-save mr-2"></i> 
                        <?php echo $produto_editando ? 'Atualizar' : 'Salvar'; ?> Produto
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Produtos -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-hamburger text-red-500 mr-2"></i> Produtos Cadastrados
                </h3>
            </div>
            <div class="p-4">
                <?php if (empty($produtos)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-hamburger text-4xl mb-3 opacity-30"></i>
                        <p class="font-medium">Nenhum produto cadastrado</p>
                        <p class="text-sm mt-1">Crie seu primeiro produto acima</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($produtos as $produto): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <?php if ($produto['imagem']): ?>
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full object-cover" 
                                                             src="<?php echo $produto['imagem']; ?>" 
                                                             alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                                                    </div>
                                                <?php endif; ?>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($produto['nome']); ?></div>
                                                    <?php if ($produto['destaque']): ?>
                                                        <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">
                                                            Destaque
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($produto['categoria_nome']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">
                                                <?php echo formatarMoeda($produto['preco']); ?>
                                                <?php if ($produto['preco_promocional']): ?>
                                                    <br>
                                                    <span class="text-green-600"><?php echo formatarMoeda($produto['preco_promocional']); ?></span>
                                                    <span class="text-xs text-red-500 line-through"><?php echo formatarMoeda($produto['preco']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full <?php echo $produto['disponivel'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $produto['disponivel'] ? 'Disponível' : 'Indisponível'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php if ($produto['controle_estoque']): ?>
                                                    <?php echo $produto['estoque_atual']; ?> un.
                                                    <?php if ($produto['estoque_atual'] <= $produto['estoque_minimo']): ?>
                                                        <span class="text-red-600 text-xs">(Baixo estoque)</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-gray-400">Sem controle</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="?pagina=cardapio&editar_produto=<?php echo $produto['id']; ?>&tab=produtos" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?pagina=cardapio&acao=excluir_produto&id=<?php echo $produto['id']; ?>" 
                                               onclick="return confirm('Tem certeza que deseja excluir este produto?')"
                                               class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Esconder todas as abas
    document.getElementById('conteudoCategorias').classList.add('hidden');
    document.getElementById('conteudoProdutos').classList.add('hidden');
    
    // Remover estilo ativo de todos os botões
    document.getElementById('tabCategorias').classList.remove('bg-white', 'text-red-600', 'shadow-lg', 'scale-105');
    document.getElementById('tabCategorias').classList.add('text-gray-600');
    document.getElementById('tabProdutos').classList.remove('bg-white', 'text-red-600', 'shadow-lg', 'scale-105');
    document.getElementById('tabProdutos').classList.add('text-gray-600');
    
    // Mostrar a aba selecionada
    document.getElementById('conteudo' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.remove('hidden');
    
    // Estilizar o botão ativo
    document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.add('bg-white', 'text-red-600', 'shadow-lg', 'scale-105');
    document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.remove('text-gray-600');
}

// Verificar se há parâmetro de tab na URL
const urlParams = new URLSearchParams(window.location.search);
const tabParam = urlParams.get('tab');
if (tabParam === 'produtos') {
    showTab('produtos');
}
</script>
<?php
}

function render_configuracoes($pdo, $restaurante) {
    global $dias_da_semana, $formas_pagamento_opcoes;
?>
<div class="animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 text-white mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-cog mr-3"></i> Configurações do Restaurante
                </h2>
                <p class="text-red-100 mt-2 opacity-90">
                    Configure as informações do seu restaurante
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                    <p class="text-sm font-medium">Status: <span class="font-bold text-green-300">Ativo</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Configurações -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <form method="POST" action="index.php" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="salvar_configuracoes">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Abas de Configuração -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-1 px-6" aria-label="Tabs">
                    <button type="button" 
                            onclick="showConfigTab('info')"
                            id="tabInfo"
                            class="px-3 py-4 font-medium text-sm border-b-2 border-red-500 text-red-600">
                        <i class="fas fa-info-circle mr-2"></i> Informações
                    </button>
                    <button type="button" 
                            onclick="showConfigTab('horarios')"
                            id="tabHorarios"
                            class="px-3 py-4 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-clock mr-2"></i> Horários
                    </button>
                    <button type="button" 
                            onclick="showConfigTab('entregas')"
                            id="tabEntregas"
                            class="px-3 py-4 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-truck mr-2"></i> Entregas
                    </button>
                    <button type="button" 
                            onclick="showConfigTab('pagamentos')"
                            id="tabPagamentos"
                            class="px-3 py-4 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-credit-card mr-2"></i> Pagamentos
                    </button>
                    <button type="button" 
                            onclick="showConfigTab('design')"
                            id="tabDesign"
                            class="px-3 py-4 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-palette mr-2"></i> Design
                    </button>
                </nav>
            </div>

            <!-- Conteúdo das Abas -->
            <div class="p-6">
                <!-- Aba: Informações -->
                <div id="configInfo" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Logo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo do Restaurante</label>
                            <div class="mt-1 flex items-center space-x-4">
                                <?php if ($restaurante['logo'] && file_exists($restaurante['logo'])): ?>
                                    <img src="<?php echo $restaurante['logo']; ?>" 
                                         alt="Logo atual" 
                                         class="w-24 h-24 object-contain rounded-lg border border-gray-300">
                                <?php else: ?>
                                    <div class="w-24 h-24 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                                        <i class="fas fa-store text-gray-400 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <input type="file" name="logo" 
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                    <p class="text-xs text-gray-500 mt-1">Recomendado: 400x400px, PNG ou JPG</p>
                                </div>
                            </div>
                        </div>

                        <!-- Banner -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Banner do Restaurante</label>
                            <div class="mt-1 flex items-center space-x-4">
                                <?php if ($restaurante['banner'] && file_exists($restaurante['banner'])): ?>
                                    <img src="<?php echo $restaurante['banner']; ?>" 
                                         alt="Banner atual" 
                                         class="w-32 h-24 object-cover rounded-lg border border-gray-300">
                                <?php else: ?>
                                    <div class="w-32 h-24 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <input type="file" name="banner" 
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                    <p class="text-xs text-gray-500 mt-1">Recomendado: 1200x400px, PNG ou JPG</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Restaurante *</label>
                            <input type="text" name="nome" required 
                                   value="<?php echo htmlspecialchars($restaurante['nome']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slogan</label>
                            <input type="text" name="slogan" 
                                   value="<?php echo htmlspecialchars($restaurante['slogan'] ?? ''); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="ex: O melhor hambúrguer da cidade">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea name="descricao" rows="3"
                                  class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"><?php echo htmlspecialchars($restaurante['descricao'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                            <input type="text" name="telefone" required 
                                   value="<?php echo htmlspecialchars($restaurante['telefone']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="(11) 99999-9999">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp *</label>
                            <input type="text" name="whatsapp" required 
                                   value="<?php echo htmlspecialchars($restaurante['whatsapp']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="(11) 99999-9999">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    @
                                </span>
                                <input type="text" name="instagram" 
                                       value="<?php echo htmlspecialchars($restaurante['instagram'] ?? ''); ?>"
                                       class="flex-1 p-3 border border-gray-300 rounded-r-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                       placeholder="nomedorestaurante">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                            <input type="url" name="site" 
                                   value="<?php echo htmlspecialchars($restaurante['site'] ?? ''); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="https://meurestaurante.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                            <input type="text" name="cnpj" 
                                   value="<?php echo htmlspecialchars($restaurante['cnpj'] ?? ''); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="00.000.000/0000-00">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Endereço *</label>
                            <input type="text" name="endereco" required 
                                   value="<?php echo htmlspecialchars($restaurante['endereco']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="Rua, Avenida, etc.">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                            <input type="text" name="numero" 
                                   value="<?php echo htmlspecialchars($restaurante['numero'] ?? ''); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="123">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                            <input type="text" name="bairro" 
                                   value="<?php echo htmlspecialchars($restaurante['bairro'] ?? ''); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="Centro">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cidade *</label>
                            <input type="text" name="cidade" required 
                                   value="<?php echo htmlspecialchars($restaurante['cidade']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="São Paulo">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                            <select name="estado" required 
                                    class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                                <option value="">Selecione</option>
                                <option value="SP" <?php echo ($restaurante['estado'] == 'SP') ? 'selected' : ''; ?>>SP - São Paulo</option>
                                <option value="RJ" <?php echo ($restaurante['estado'] == 'RJ') ? 'selected' : ''; ?>>RJ - Rio de Janeiro</option>
                                <option value="MG" <?php echo ($restaurante['estado'] == 'MG') ? 'selected' : ''; ?>>MG - Minas Gerais</option>
                                <option value="ES" <?php echo ($restaurante['estado'] == 'ES') ? 'selected' : ''; ?>>ES - Espírito Santo</option>
                                <option value="PR" <?php echo ($restaurante['estado'] == 'PR') ? 'selected' : ''; ?>>PR - Paraná</option>
                                <option value="SC" <?php echo ($restaurante['estado'] == 'SC') ? 'selected' : ''; ?>>SC - Santa Catarina</option>
                                <option value="RS" <?php echo ($restaurante['estado'] == 'PE') ? 'selected' : ''; ?>>PE - Pernambuco</option>
                                <!-- Adicionar mais estados conforme necessário -->
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                            <input type="text" name="cep" 
                                   value="<?php echo htmlspecialchars($restaurante['cep'] ?? ''); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="00000-000">
                        </div>
                    </div>
                </div>

                <!-- Aba: Horários -->
                <div id="configHorarios" class="space-y-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horário de Abertura *</label>
                            <input type="time" name="horario_abertura" required 
                                   value="<?php echo htmlspecialchars($restaurante['horario_abertura']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horário de Fechamento *</label>
                            <input type="time" name="horario_fechamento" required 
                                   value="<?php echo htmlspecialchars($restaurante['horario_fechamento']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Dias de Funcionamento *</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <?php foreach ($dias_da_semana as $valor => $label): ?>
                                <div class="flex items-center">
                                    <input type="checkbox" name="dias_funcionamento[]" 
                                           value="<?php echo $valor; ?>"
                                           id="dia_<?php echo $valor; ?>"
                                           <?php echo in_array($valor, $restaurante['dias_funcionamento']) ? 'checked' : ''; ?>
                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    <label for="dia_<?php echo $valor; ?>" class="ml-2 block text-sm text-gray-900">
                                        <?php echo $label; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Importante</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Os pedidos só serão aceitos dentro do horário de funcionamento configurado.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba: Entregas -->
                <div id="configEntregas" class="space-y-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Taxa de Entrega (R$)</label>
                            <input type="text" name="taxa_entrega" 
                                   value="<?php echo formatarMoeda($restaurante['taxa_entrega']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 input-moeda"
                                   placeholder="R$ 0,00">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pedido Mínimo (R$)</label>
                            <input type="text" name="pedido_minimo" 
                                   value="<?php echo formatarMoeda($restaurante['pedido_minimo']); ?>"
                                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 input-moeda"
                                   placeholder="R$ 0,00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Raio de Entrega (km)</label>
                        <input type="number" name="raio_entrega" 
                               value="<?php echo htmlspecialchars($restaurante['raio_entrega']); ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="5">
                        <p class="text-xs text-gray-500 mt-1">Distância máxima para entregas (em quilômetros)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Tipos de Pedido Aceitos</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="aceita_delivery" id="aceita_delivery" 
                                       <?php echo $restaurante['aceita_delivery'] ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="aceita_delivery" class="ml-2 block text-sm text-gray-900">
                                    <i class="fas fa-truck mr-1"></i> Delivery
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="aceita_retirada" id="aceita_retirada" 
                                       <?php echo $restaurante['aceita_retirada'] ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="aceita_retirada" class="ml-2 block text-sm text-gray-900">
                                    <i class="fas fa-store mr-1"></i> Retirada
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="aceita_local" id="aceita_local" 
                                       <?php echo $restaurante['aceita_local'] ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="aceita_local" class="ml-2 block text-sm text-gray-900">
                                    <i class="fas fa-utensils mr-1"></i> Consumo Local
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba: Pagamentos -->
                <div id="configPagamentos" class="space-y-6 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Formas de Pagamento Aceitas *</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <?php foreach ($formas_pagamento_opcoes as $valor => $label): ?>
                                <div class="flex items-center">
                                    <input type="checkbox" name="formas_pagamento[]" 
                                           value="<?php echo $valor; ?>"
                                           id="pagamento_<?php echo $valor; ?>"
                                           <?php echo in_array($valor, $restaurante['formas_pagamento']) ? 'checked' : ''; ?>
                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    <label for="pagamento_<?php echo $valor; ?>" class="ml-2 block text-sm text-gray-900">
                                        <?php if ($valor == 'pix'): ?>
                                            <i class="fas fa-qrcode mr-1"></i>
                                        <?php elseif ($valor == 'credito' || $valor == 'debito'): ?>
                                            <i class="fas fa-credit-card mr-1"></i>
                                        <?php elseif ($valor == 'dinheiro'): ?>
                                            <i class="fas fa-money-bill-wave mr-1"></i>
                                        <?php elseif ($valor == 'vale'): ?>
                                            <i class="fas fa-ticket-alt mr-1"></i>
                                        <?php endif; ?>
                                        <?php echo $label; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Atenção</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>As formas de pagamento selecionadas serão exibidas no cardápio público.</p>
                                    <p class="mt-1">Certifique-se de que todas estão disponíveis no seu estabelecimento.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba: Design -->
                <div id="configDesign" class="space-y-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cor Principal</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" name="cor_principal" 
                                       value="<?php echo htmlspecialchars($restaurante['cor_principal']); ?>"
                                       class="w-12 h-12 rounded-lg cursor-pointer">
                                <input type="text" 
                                       value="<?php echo htmlspecialchars($restaurante['cor_principal']); ?>"
                                       class="flex-1 p-3 border border-gray-300 rounded-xl font-mono text-sm"
                                       readonly>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cor Secundária</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" name="cor_secundaria" 
                                       value="<?php echo htmlspecialchars($restaurante['cor_secundaria']); ?>"
                                       class="w-12 h-12 rounded-lg cursor-pointer">
                                <input type="text" 
                                       value="<?php echo htmlspecialchars($restaurante['cor_secundaria']); ?>"
                                       class="flex-1 p-3 border border-gray-300 rounded-xl font-mono text-sm"
                                       readonly>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cor do Texto</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" name="cor_texto" 
                                       value="<?php echo htmlspecialchars($restaurante['cor_texto']); ?>"
                                       class="w-12 h-12 rounded-lg cursor-pointer">
                                <input type="text" 
                                       value="<?php echo htmlspecialchars($restaurante['cor_texto']); ?>"
                                       class="flex-1 p-3 border border-gray-300 rounded-xl font-mono text-sm"
                                       readonly>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cor de Fundo</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" name="cor_fundo" 
                                       value="<?php echo htmlspecialchars($restaurante['cor_fundo']); ?>"
                                       class="w-12 h-12 rounded-lg cursor-pointer">
                                <input type="text" 
                                       value="<?php echo htmlspecialchars($restaurante['cor_fundo']); ?>"
                                       class="flex-1 p-3 border border-gray-300 rounded-xl font-mono text-sm"
                                       readonly>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tema do Cardápio</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div class="relative">
                                <input type="radio" name="tema" value="claro" 
                                       id="tema_claro" 
                                       <?php echo ($restaurante['tema'] == 'claro') ? 'checked' : ''; ?>
                                       class="sr-only">
                                <label for="tema_claro" 
                                       class="flex flex-col items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-red-300 transition-colors duration-200">
                                    <div class="w-full h-32 bg-white border border-gray-300 rounded-lg mb-3 flex items-center justify-center">
                                        <div class="w-1/3 h-full bg-red-500 rounded-l-lg"></div>
                                        <div class="w-2/3 h-full bg-gray-100 rounded-r-lg p-3">
                                            <div class="h-3 bg-gray-700 rounded mb-2"></div>
                                            <div class="h-3 bg-gray-400 rounded w-2/3"></div>
                                        </div>
                                    </div>
                                    <span class="font-medium text-gray-900">Tema Claro</span>
                                    <span class="text-sm text-gray-500 mt-1">Ideal para o dia</span>
                                </label>
                            </div>
                            
                            <div class="relative">
                                <input type="radio" name="tema" value="escuro" 
                                       id="tema_escuro" 
                                       <?php echo ($restaurante['tema'] == 'escuro') ? 'checked' : ''; ?>
                                       class="sr-only">
                                <label for="tema_escuro" 
                                       class="flex flex-col items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-red-300 transition-colors duration-200">
                                    <div class="w-full h-32 bg-gray-900 border border-gray-700 rounded-lg mb-3 flex items-center justify-center">
                                        <div class="w-1/3 h-full bg-red-600 rounded-l-lg"></div>
                                        <div class="w-2/3 h-full bg-gray-800 rounded-r-lg p-3">
                                            <div class="h-3 bg-gray-300 rounded mb-2"></div>
                                            <div class="h-3 bg-gray-500 rounded w-2/3"></div>
                                        </div>
                                    </div>
                                    <span class="font-medium text-gray-900">Tema Escuro</span>
                                    <span class="text-sm text-gray-500 mt-1">Ideal para a noite</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between">
                    <button type="button" onclick="resetForm()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-redo mr-2"></i> Redefinir
                    </button>
                    
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i> Salvar Todas as Configurações
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showConfigTab(tabName) {
    // Esconder todas as abas de conteúdo
    document.getElementById('configInfo').classList.add('hidden');
    document.getElementById('configHorarios').classList.add('hidden');
    document.getElementById('configEntregas').classList.add('hidden');
    document.getElementById('configPagamentos').classList.add('hidden');
    document.getElementById('configDesign').classList.add('hidden');
    
    // Remover estilo ativo de todos os botões
    document.getElementById('tabInfo').classList.remove('border-red-500', 'text-red-600');
    document.getElementById('tabInfo').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('tabHorarios').classList.remove('border-red-500', 'text-red-600');
    document.getElementById('tabHorarios').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('tabEntregas').classList.remove('border-red-500', 'text-red-600');
    document.getElementById('tabEntregas').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('tabPagamentos').classList.remove('border-red-500', 'text-red-600');
    document.getElementById('tabPagamentos').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('tabDesign').classList.remove('border-red-500', 'text-red-600');
    document.getElementById('tabDesign').classList.add('border-transparent', 'text-gray-500');
    
    // Mostrar a aba selecionada
    document.getElementById('config' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.remove('hidden');
    
    // Estilizar o botão ativo
    document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.add('border-red-500', 'text-red-600');
    document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.remove('border-transparent', 'text-gray-500');
}

function resetForm() {
    if (confirm('Tem certeza que deseja redefinir todas as alterações? Os dados serão restaurados para os valores atuais.')) {
        document.querySelector('form').reset();
    }
}

// Sincronizar inputs de cor
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('input', function() {
        this.nextElementSibling.value = this.value;
    });
});
</script>
<?php
}

function render_pedidos($pdo, $restaurante) {
    global $status_cores, $status_labels;
    
    // Verificar se é para visualizar um pedido específico
    $ver_pedido_id = $_GET['ver'] ?? null;
    $pedido_detalhe = null;
    if ($ver_pedido_id) {
        $pedido_detalhe = getPedido($pdo, $ver_pedido_id);
    }
    
    // Filtros
    $filtro_status = $_GET['status'] ?? null;
    $filtro_data_inicio = $_GET['data_inicio'] ?? null;
    $filtro_data_fim = $_GET['data_fim'] ?? null;
    $filtro_tipo_pedido = $_GET['tipo_pedido'] ?? null;
    
    $filtros = [];
    if ($filtro_status) $filtros['status'] = $filtro_status;
    if ($filtro_data_inicio) $filtros['data_inicio'] = $filtro_data_inicio;
    if ($filtro_data_fim) $filtros['data_fim'] = $filtro_data_fim;
    if ($filtro_tipo_pedido) $filtros['tipo_pedido'] = $filtro_tipo_pedido;
    
    $pedidos = getPedidos($pdo, $restaurante['id'], $filtros);
    
    // Estatísticas dos pedidos
    $total_pedidos = count($pedidos);
    $total_valor = 0;
    foreach ($pedidos as $pedido) {
        $total_valor += $pedido['total'];
    }
?>
<div class="animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 text-white mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-receipt mr-3"></i> Gestão de Pedidos
                </h2>
                <p class="text-red-100 mt-2 opacity-90">
                    Gerencie e acompanhe todos os pedidos do seu restaurante
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                    <p class="text-sm font-medium">
                        <span class="font-bold text-xl"><?php echo $total_pedidos; ?></span> pedidos • 
                        <span class="font-bold text-xl"><?php echo formatarMoeda($total_valor); ?></span> total
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if ($pedido_detalhe): ?>
        <!-- Detalhe do Pedido -->
        <div class="mb-6">
            <a href="?pagina=pedidos" 
               class="inline-flex items-center text-red-600 hover:text-red-700 font-medium mb-4">
                <i class="fas fa-arrow-left mr-2"></i> Voltar para lista de pedidos
            </a>
            
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Cabeçalho do Pedido -->
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-6 text-white">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-xl font-bold">Pedido #<?php echo htmlspecialchars($pedido_detalhe['codigo']); ?></h3>
                                <span class="px-3 py-1 text-sm rounded-full <?php echo $status_cores[$pedido_detalhe['status']] ?? 'bg-gray-500'; ?>">
                                    <?php echo $status_labels[$pedido_detalhe['status']] ?? ucfirst($pedido_detalhe['status']); ?>
                                </span>
                            </div>
                            <p class="text-gray-300">
                                <i class="far fa-clock mr-1"></i> 
                                <?php echo date('d/m/Y H:i', strtotime($pedido_detalhe['data_pedido'])); ?>
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0 text-right">
                            <p class="text-2xl font-bold"><?php echo formatarMoeda($pedido_detalhe['total']); ?></p>
                            <p class="text-sm text-gray-300">Tipo: <?php echo ucfirst($pedido_detalhe['tipo_pedido']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Conteúdo do Pedido -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Informações do Cliente -->
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-user mr-2 text-red-500"></i> Cliente
                            </h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Nome:</span> <?php echo htmlspecialchars($pedido_detalhe['cliente_nome']); ?></p>
                                <p><span class="font-medium">Telefone:</span> <?php echo htmlspecialchars($pedido_detalhe['cliente_telefone']); ?></p>
                                <?php if ($pedido_detalhe['cliente_email']): ?>
                                    <p><span class="font-medium">E-mail:</span> <?php echo htmlspecialchars($pedido_detalhe['cliente_email']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Informações de Entrega/Retirada -->
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                <?php if ($pedido_detalhe['tipo_pedido'] == 'delivery'): ?>
                                    <i class="fas fa-truck mr-2 text-red-500"></i> Entrega
                                <?php else: ?>
                                    <i class="fas fa-store mr-2 text-red-500"></i> Retirada
                                <?php endif; ?>
                            </h4>
                            <?php if ($pedido_detalhe['tipo_pedido'] == 'delivery' && $pedido_detalhe['endereco_entrega']): ?>
                                <div class="space-y-2">
                                    <p><span class="font-medium">Endereço:</span> <?php echo htmlspecialchars($pedido_detalhe['endereco_entrega']); ?></p>
                                    <?php if ($pedido_detalhe['complemento']): ?>
                                        <p><span class="font-medium">Complemento:</span> <?php echo htmlspecialchars($pedido_detalhe['complemento']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($pedido_detalhe['ponto_referencia']): ?>
                                        <p><span class="font-medium">Ponto de Referência:</span> <?php echo htmlspecialchars($pedido_detalhe['ponto_referencia']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-600">Retirada no local</p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Informações de Pagamento -->
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-credit-card mr-2 text-red-500"></i> Pagamento
                            </h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Forma:</span> <?php echo ucfirst($pedido_detalhe['forma_pagamento']); ?></p>
                                <?php if ($pedido_detalhe['forma_pagamento'] == 'dinheiro' && $pedido_detalhe['troco_para']): ?>
                                    <p><span class="font-medium">Troco para:</span> <?php echo formatarMoeda($pedido_detalhe['troco_para']); ?></p>
                                <?php endif; ?>
                                <?php if ($pedido_detalhe['observacoes']): ?>
                                    <p><span class="font-medium">Observações:</span> <?php echo htmlspecialchars($pedido_detalhe['observacoes']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Itens do Pedido -->
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-list mr-2 text-red-500"></i> Itens do Pedido
                        </h4>
                        <div class="bg-gray-50 rounded-xl overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Quantidade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Preço Unitário</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php 
                                    $itens = is_array($pedido_detalhe['itens']) ? $pedido_detalhe['itens'] : [];
                                    foreach ($itens as $item): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['nome'] ?? 'Item'); ?></div>
                                                <?php if (!empty($item['observacoes'])): ?>
                                                    <div class="text-xs text-gray-500">Obs: <?php echo htmlspecialchars($item['observacoes']); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo $item['quantidade'] ?? 1; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo formatarMoeda($item['preco'] ?? 0); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                                <?php echo formatarMoeda(($item['preco'] ?? 0) * ($item['quantidade'] ?? 1)); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo formatarMoeda($pedido_detalhe['subtotal']); ?></td>
                                    </tr>
                                    <?php if ($pedido_detalhe['taxa_entrega'] > 0): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Taxa de Entrega:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo formatarMoeda($pedido_detalhe['taxa_entrega']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($pedido_detalhe['desconto'] > 0): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Desconto:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">-<?php echo formatarMoeda($pedido_detalhe['desconto']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-red-600"><?php echo formatarMoeda($pedido_detalhe['total']); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Ações do Pedido -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <?php if ($pedido_detalhe['tempo_estimado']): ?>
                                <p class="text-gray-600">
                                    <i class="far fa-clock text-red-500 mr-1"></i>
                                    Tempo estimado: <?php echo $pedido_detalhe['tempo_estimado']; ?> minutos
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex space-x-3 mt-4 md:mt-0">
                            <!-- Botão para impressão da comanda -->
                            <div id="comanda-impressao" style="display: none;">
                                <div style="text-align: center; margin-bottom: 15px;">
                                    <h2 style="margin: 0; font-size: 16px; font-weight: bold;"><?php echo htmlspecialchars($restaurante['nome']); ?></h2>
                                    <?php if ($restaurante['telefone']): ?>
                                        <p style="margin: 5px 0; font-size: 12px;">Tel: <?php echo htmlspecialchars($restaurante['telefone']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <hr style="border: 1px dashed #000; margin: 10px 0;">
                                <div style="margin-bottom: 10px;">
                                    <p style="margin: 5px 0; font-size: 12px;">
                                        <strong>Pedido:</strong> #<?php echo htmlspecialchars($pedido_detalhe['codigo']); ?>
                                    </p>
                                    <p style="margin: 5px 0; font-size: 12px;">
                                        <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido_detalhe['data_pedido'])); ?>
                                    </p>
                                    <p style="margin: 5px 0; font-size: 12px;">
                                        <strong>Cliente:</strong> <?php echo htmlspecialchars($pedido_detalhe['cliente_nome']); ?>
                                    </p>
                                    <p style="margin: 5px 0; font-size: 12px;">
                                        <strong>Telefone:</strong> <?php echo htmlspecialchars($pedido_detalhe['cliente_telefone']); ?>
                                    </p>
                                </div>
                                <hr style="border: 1px dashed #000; margin: 10px 0;">
                                <table style="width: 100%; margin: 10px 0; font-size: 12px;">
                                    <tr>
                                        <th style="text-align: left; padding: 3px 0; border-bottom: 1px solid #ddd;">Item</th>
                                        <th style="text-align: center; padding: 3px 0; border-bottom: 1px solid #ddd;">Qtd</th>
                                        <th style="text-align: right; padding: 3px 0; border-bottom: 1px solid #ddd;">Valor</th>
                                    </tr>
                                    <?php foreach ($itens as $item): ?>
                                        <tr>
                                            <td style="padding: 3px 0; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($item['nome'] ?? 'Item'); ?></td>
                                            <td style="text-align: center; padding: 3px 0; border-bottom: 1px solid #eee;"><?php echo $item['quantidade'] ?? 1; ?></td>
                                            <td style="text-align: right; padding: 3px 0; border-bottom: 1px solid #eee;"><?php echo formatarMoeda(($item['preco'] ?? 0) * ($item['quantidade'] ?? 1)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                                <hr style="border: 1px dashed #000; margin: 10px 0;">
                                <table style="width: 100%; font-size: 12px;">
                                    <tr>
                                        <td style="text-align: right; padding: 3px 0;">Subtotal:</td>
                                        <td style="text-align: right; padding: 3px 0; width: 80px;"><?php echo formatarMoeda($pedido_detalhe['subtotal']); ?></td>
                                    </tr>
                                    <?php if ($pedido_detalhe['taxa_entrega'] > 0): ?>
                                    <tr>
                                        <td style="text-align: right; padding: 3px 0;">Taxa Entrega:</td>
                                        <td style="text-align: right; padding: 3px 0;"><?php echo formatarMoeda($pedido_detalhe['taxa_entrega']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($pedido_detalhe['desconto'] > 0): ?>
                                    <tr>
                                        <td style="text-align: right; padding: 3px 0;">Desconto:</td>
                                        <td style="text-align: right; padding: 3px 0;">-<?php echo formatarMoeda($pedido_detalhe['desconto']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td style="text-align: right; padding: 5px 0; font-weight: bold;">TOTAL:</td>
                                        <td style="text-align: right; padding: 5px 0; font-weight: bold;"><?php echo formatarMoeda($pedido_detalhe['total']); ?></td>
                                    </tr>
                                </table>
                                <?php if ($pedido_detalhe['observacoes']): ?>
                                <hr style="border: 1px dashed #000; margin: 10px 0;">
                                <p style="font-size: 11px; margin: 5px 0;"><strong>Observações:</strong> <?php echo htmlspecialchars($pedido_detalhe['observacoes']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <button onclick="imprimirComanda()" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-print mr-2"></i> Imprimir Comanda
                            </button>
                            
                            <?php if ($pedido_detalhe['status'] != 'cancelado' && $pedido_detalhe['status'] != 'finalizado'): ?>
                                <form method="POST" action="index.php" class="flex items-center">
                                    <input type="hidden" name="acao" value="atualizar_status_pedido">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="pedido_id" value="<?php echo $pedido_detalhe['id']; ?>">
                                    <select name="novo_status" 
                                            class="p-2 border border-gray-300 rounded-xl mr-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                        <?php 
                                        $proximos_status = [
                                            'recebido' => ['aceito', 'cancelado'],
                                            'aceito' => ['preparo', 'cancelado'],
                                            'preparo' => ['pronto', 'cancelado'],
                                            'pronto' => ['entregue', 'cancelado'],
                                            'entregue' => ['finalizado']
                                        ];
                                        $status_disponiveis = $proximos_status[$pedido_detalhe['status']] ?? [];
                                        ?>
                                        <option value="">Alterar status...</option>
                                        <?php foreach ($status_disponiveis as $status): ?>
                                            <option value="<?php echo $status; ?>">
                                                <?php echo $status_labels[$status] ?? ucfirst($status); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" 
                                            class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200">
                                        <i class="fas fa-check mr-2"></i> Atualizar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter text-red-500 mr-2"></i> Filtros
                </h3>
            </div>
            <form method="GET" action="index.php" class="p-5">
                <input type="hidden" name="pagina" value="pedidos">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                            <option value="">Todos os status</option>
                            <?php foreach ($status_labels as $valor => $label): ?>
                                <option value="<?php echo $valor; ?>" <?php echo $filtro_status == $valor ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                        <input type="date" name="data_inicio" 
                               value="<?php echo htmlspecialchars($filtro_data_inicio); ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                        <input type="date" name="data_fim" 
                               value="<?php echo htmlspecialchars($filtro_data_fim); ?>"
                               class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pedido</label>
                        <select name="tipo_pedido" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                            <option value="">Todos os tipos</option>
                            <option value="delivery" <?php echo $filtro_tipo_pedido == 'delivery' ? 'selected' : ''; ?>>Delivery</option>
                            <option value="retirada" <?php echo $filtro_tipo_pedido == 'retirada' ? 'selected' : ''; ?>>Retirada</option>
                            <option value="local" <?php echo $filtro_tipo_pedido == 'local' ? 'selected' : ''; ?>>Consumo Local</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-between mt-4">
                    <a href="?pagina=pedidos" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-redo mr-2"></i> Limpar Filtros
                    </a>
                    
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-medium">
                        <i class="fas fa-search mr-2"></i> Filtrar Pedidos
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Pedidos -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list text-red-500 mr-2"></i> Pedidos Recentes
                </h3>
            </div>
            <div class="p-4">
                <?php if (empty($pedidos)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-30"></i>
                        <p class="font-medium">Nenhum pedido encontrado</p>
                        <p class="text-sm mt-1">Tente ajustar os filtros ou aguarde novos pedidos</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pedido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">#<?php echo htmlspecialchars($pedido['codigo']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo ucfirst($pedido['tipo_pedido']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($pedido['cliente_nome']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($pedido['cliente_telefone']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-red-600"><?php echo formatarMoeda($pedido['total']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full <?php echo $status_cores[$pedido['status']] ?? 'bg-gray-500'; ?> text-white">
                                                <?php echo $status_labels[$pedido['status']] ?? ucfirst($pedido['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('d/m H:i', strtotime($pedido['data_pedido'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="?pagina=pedidos&ver=<?php echo $pedido['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($pedido['status'] != 'cancelado' && $pedido['status'] != 'finalizado'): ?>
                                                <button onclick="atualizarPedido(<?php echo $pedido['id']; ?>)" 
                                                        class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function atualizarPedido(pedidoId) {
    const novoStatus = prompt('Digite o novo status (aceito, preparo, pronto, entregue, finalizado, cancelado):');
    if (novoStatus) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php';
        
        const acaoInput = document.createElement('input');
        acaoInput.type = 'hidden';
        acaoInput.name = 'acao';
        acaoInput.value = 'atualizar_status_pedido';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = 'csrf_token';
        tokenInput.value = '<?php echo $_SESSION['csrf_token']; ?>';
        
        const pedidoInput = document.createElement('input');
        pedidoInput.type = 'hidden';
        pedidoInput.name = 'pedido_id';
        pedidoInput.value = pedidoId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'novo_status';
        statusInput.value = novoStatus;
        
        form.appendChild(acaoInput);
        form.appendChild(tokenInput);
        form.appendChild(pedidoInput);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?php
}

function render_funcionarios($pdo, $restaurante) {
    global $permissoes_opcoes;
    
    $funcionarios = getFuncionarios($pdo, $restaurante['id']);
    
    // Verificar se é para editar um funcionário
    $editar_funcionario_id = $_GET['editar_funcionario'] ?? null;
    $funcionario_editando = null;
    if ($editar_funcionario_id) {
        $funcionario_editando = getFuncionario($pdo, $editar_funcionario_id);
        if ($funcionario_editando) {
            $funcionario_editando['permissoes'] = explode(',', $funcionario_editando['permissoes']);
        }
    }
?>
<div class="animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 text-white mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-users mr-3"></i> Gerenciar Funcionários
                </h2>
                <p class="text-red-100 mt-2 opacity-90">
                    Gerencie a equipe do seu restaurante
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                    <p class="text-sm font-medium">
                        <span class="font-bold text-xl"><?php echo count($funcionarios); ?></span> funcionários ativos
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Funcionário -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-800 flex items-center">
                <i class="fas fa-user-plus text-red-500 mr-2"></i> 
                <?php echo $funcionario_editando ? 'Editar Funcionário' : 'Novo Funcionário'; ?>
            </h3>
        </div>
        <form method="POST" action="index.php" class="p-5">
            <input type="hidden" name="acao" value="<?php echo $funcionario_editando ? 'editar_funcionario' : 'adicionar_funcionario'; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <?php if ($funcionario_editando): ?>
                <input type="hidden" name="id" value="<?php echo $funcionario_editando['id']; ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                    <input type="text" name="nome" required 
                           value="<?php echo $funcionario_editando ? htmlspecialchars($funcionario_editando['nome']) : ''; ?>"
                           class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                    <input type="email" name="email" required 
                           value="<?php echo $funcionario_editando ? htmlspecialchars($funcionario_editando['email']) : ''; ?>"
                           class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="funcionario@restaurante.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" 
                           value="<?php echo $funcionario_editando ? htmlspecialchars($funcionario_editando['telefone']) : ''; ?>"
                           class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="(11) 99999-9999">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <select name="cargo" 
                            class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                        <option value="atendente" <?php echo ($funcionario_editando && $funcionario_editando['cargo'] == 'atendente') ? 'selected' : ''; ?>>Atendente</option>
                        <option value="cozinheiro" <?php echo ($funcionario_editando && $funcionario_editando['cargo'] == 'cozinheiro') ? 'selected' : ''; ?>>Cozinheiro</option>
                        <option value="entregador" <?php echo ($funcionario_editando && $funcionario_editando['cargo'] == 'entregador') ? 'selected' : ''; ?>>Entregador</option>
                        <option value="gerente" <?php echo ($funcionario_editando && $funcionario_editando['cargo'] == 'gerente') ? 'selected' : ''; ?>>Gerente</option>
                        <option value="admin" <?php echo ($funcionario_editando && $funcionario_editando['cargo'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="ativo" id="ativo" 
                               <?php echo (!$funcionario_editando || $funcionario_editando['ativo']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="ativo" class="ml-2 block text-sm text-gray-900">
                            Funcionário Ativo
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Permissões de Acesso</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <?php foreach ($permissoes_opcoes as $valor => $label): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="permissoes[]" 
                                   value="<?php echo $valor; ?>"
                                   id="permissao_<?php echo $valor; ?>"
                                   <?php echo ($funcionario_editando && in_array($valor, $funcionario_editando['permissoes'])) ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="permissao_<?php echo $valor; ?>" class="ml-2 block text-sm text-gray-900">
                                <?php echo $label; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-gray-500 mt-2">Selecione as permissões que este funcionário terá no sistema.</p>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <?php if ($funcionario_editando): ?>
                    <a href="?pagina=funcionarios" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                <?php endif; ?>
                
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-medium">
                    <i class="fas fa-save mr-2"></i> 
                    <?php echo $funcionario_editando ? 'Atualizar' : 'Salvar'; ?> Funcionário
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Funcionários -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-800 flex items-center">
                <i class="fas fa-list text-red-500 mr-2"></i> Funcionários Cadastrados
            </h3>
        </div>
        <div class="p-4">
            <?php if (empty($funcionarios)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-users text-4xl mb-3 opacity-30"></i>
                    <p class="font-medium">Nenhum funcionário cadastrado</p>
                    <p class="text-sm mt-1">Adicione seu primeiro funcionário acima</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Funcionário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contato</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissões</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($funcionarios as $funcionario): ?>
                                <?php 
                                    $permissoes_array = explode(',', $funcionario['permissoes']);
                                    $permissoes_display = array_slice($permissoes_array, 0, 2);
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center font-bold">
                                                <?php echo strtoupper(substr($funcionario['nome'], 0, 1)); ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($funcionario['nome']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($funcionario['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            <?php 
                                                if ($funcionario['cargo'] == 'gerente' || $funcionario['cargo'] == 'admin') {
                                                    echo 'bg-purple-100 text-purple-800';
                                                } elseif ($funcionario['cargo'] == 'cozinheiro') {
                                                    echo 'bg-orange-100 text-orange-800';
                                                } elseif ($funcionario['cargo'] == 'entregador') {
                                                    echo 'bg-blue-100 text-blue-800';
                                                } else {
                                                    echo 'bg-gray-100 text-gray-800';
                                                }
                                            ?>">
                                            <?php 
                                                $cargos = [
                                                    'atendente' => 'Atendente',
                                                    'cozinheiro' => 'Cozinheiro',
                                                    'entregador' => 'Entregador',
                                                    'gerente' => 'Gerente',
                                                    'admin' => 'Administrador'
                                                ];
                                                echo $cargos[$funcionario['cargo']] ?? ucfirst($funcionario['cargo']);
                                            ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php if ($funcionario['telefone']): ?>
                                                <i class="fas fa-phone mr-1 text-gray-400"></i> <?php echo htmlspecialchars($funcionario['telefone']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php foreach ($permissoes_display as $permissao): ?>
                                                <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded mr-1 mb-1">
                                                    <?php 
                                                        $perm_labels = [
                                                            'ver_pedidos' => 'Ver Pedidos',
                                                            'alterar_status' => 'Alterar Status',
                                                            'gerenciar_cardapio' => 'Cardápio',
                                                            'gerenciar_funcionarios' => 'Funcionários',
                                                            'ver_relatorios' => 'Relatórios',
                                                            'gerenciar_configuracoes' => 'Configurações'
                                                        ];
                                                        echo $perm_labels[$permissao] ?? $permissao;
                                                    ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if (count($permissoes_array) > 2): ?>
                                                <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                                    +<?php echo count($permissoes_array) - 2; ?> mais
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $funcionario['ativo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $funcionario['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="?pagina=funcionarios&editar_funcionario=<?php echo $funcionario['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?pagina=funcionarios&acao=excluir_funcionario&id=<?php echo $funcionario['id']; ?>" 
                                           onclick="return confirm('Tem certeza que deseja excluir este funcionário?')"
                                           class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
}
