<?php
// templates/dashboard.php - Template HTML do Dashboard Admin

// Constantes para renderização (apenas para o admin)
$status_cores = [
    'recebido' => 'bg-red-500',
    'aceito' => 'bg-yellow-500',
    'preparo' => 'bg-blue-500',
    'pronto' => 'bg-indigo-500',
    'entregue' => 'bg-green-500',
    'finalizado' => 'bg-gray-500',
    'cancelado' => 'bg-red-800'
];

$status_labels = [
    'recebido' => 'Recebido',
    'aceito' => 'Aceito',
    'preparo' => 'Em Preparo',
    'pronto' => 'Pronto',
    'entregue' => 'Entregue',
    'finalizado' => 'Finalizado',
    'cancelado' => 'Cancelado'
];

$dias_da_semana = [
    'segunda' => 'Segunda-feira', 
    'terca' => 'Terça-feira', 
    'quarta' => 'Quarta-feira', 
    'quinta' => 'Quinta-feira', 
    'sexta' => 'Sexta-feira', 
    'sabado' => 'Sábado', 
    'domingo' => 'Domingo'
];

$formas_pagamento_opcoes = [
    'dinheiro' => 'Dinheiro',
    'pix' => 'Pix',
    'credito' => 'Cartão de Crédito',
    'debito' => 'Cartão de Débito',
    'vale' => 'Vale Refeição/Alimentação'
];

$permissoes_opcoes = [
    'ver_pedidos' => 'Ver Pedidos',
    'alterar_status' => 'Alterar Status',
    'gerenciar_cardapio' => 'Gerenciar Cardápio',
    'gerenciar_funcionarios' => 'Gerenciar Funcionários',
    'ver_relatorios' => 'Ver Relatórios',
    'gerenciar_configuracoes' => 'Gerenciar Configurações'
];

$icones_disponiveis = [
    'fas fa-utensils' => 'Utensílios',
    'fas fa-hamburger' => 'Hambúrguer',
    'fas fa-pizza-slice' => 'Pizza',
    'fas fa-beer' => 'Bebida',
    'fas fa-coffee' => 'Café',
    'fas fa-ice-cream' => 'Sobremesa',
    'fas fa-cocktail' => 'Coquetel',
    'fas fa-drumstick-bite' => 'Frango',
    'fas fa-fish' => 'Peixe',
    'fas fa-carrot' => 'Vegetal',
    'fas fa-lemon' => 'Limão',
    'fas fa-apple-alt' => 'Maçã',
    'fas fa-bread-slice' => 'Pão',
    'fas fa-cheese' => 'Queijo',
    'fas fa-bacon' => 'Bacon',
    'fas fa-egg' => 'Ovo',
    'fas fa-wine-glass' => 'Vinho',
    'fas fa-mug-hot' => 'Chávena',
    'fas fa-cookie' => 'Biscoito',
    'fas fa-birthday-cake' => 'Bolo'
];
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> Premium - Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'pulse-glow': 'pulseGlow 2s infinite',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' }
                        },
                        bounceIn: {
                            '0%': { transform: 'scale(0.3)', opacity: '0' },
                            '50%': { transform: 'scale(1.05)', opacity: '0.7' },
                            '70%': { transform: 'scale(0.9)', opacity: '0.9' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        },
                        pulseGlow: {
                            '0%, 100%': { boxShadow: '0 0 5px rgba(239, 68, 68, 0.4)' },
                            '50%': { boxShadow: '0 0 20px rgba(239, 68, 68, 0.8)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }
        
        .sidebar {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            z-index: 40;
            height: 100vh;
            top: 0;
            left: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-item {
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(to bottom, #ef4444, #dc2626);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar-item:hover::before,
        .sidebar-item.active::before {
            transform: translateX(0);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(239, 68, 68, 0.15);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            #sidebarOverlay {
                display: none;
            }
            
            .sidebar.active + #sidebarOverlay {
                display: block;
            }
        }
        
        @media (min-width: 769px) {
            .sidebar {
                transform: translateX(0) !important;
            }
            
            .main-content {
                margin-left: 16rem; /* 256px */
            }
            
            #sidebarOverlay {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar Toggle para Mobile -->
    <button id="sidebarToggle" class="md:hidden fixed top-4 left-4 z-50 w-10 h-10 bg-red-500 text-white rounded-full shadow-lg flex items-center justify-center">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay para mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Sidebar Premium -->
    <aside id="sidebar" class="sidebar w-64">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-utensils text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white"><?php echo APP_NAME; ?></h1>
                    <p class="text-xs text-gray-400">Premium Edition</p>
                </div>
            </div>
        </div>
        
        <nav class="p-4 space-y-1">
            <!-- Painel -->
            <a href="?pagina=painel" 
               class="sidebar-item flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl transition-all duration-200 <?php echo $pagina_atual == 'painel' ? 'active bg-gray-800 text-white' : ''; ?>">
                <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                <span class="font-medium">Painel</span>
                <?php if ($stats['pedidos_hoje'] > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse-glow">
                        <?php echo $stats['pedidos_hoje']; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <a href="cardapio.php?restaurante_id=<?php echo $restaurante['id']; ?>" target="_blank" 
               class="sidebar-item flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl transition-all duration-200">
                <i class="fas fa-external-link-alt w-5 h-5 mr-3"></i>
                <span class="font-medium">Ver Cardápio</span>
                <span class="ml-auto text-xs text-green-400 animate-pulse">
                    <i class="fas fa-eye"></i>
                </span>
            </a>
            
            <!-- Pedidos -->
            <a href="?pagina=pedidos" 
               class="sidebar-item flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl transition-all duration-200 <?php echo $pagina_atual == 'pedidos' ? 'active bg-gray-800 text-white' : ''; ?>">
                <i class="fas fa-receipt w-5 h-5 mr-3"></i>
                <span class="font-medium">Pedidos</span>
                <?php 
                    $pedidos_pendentes = getPedidos($pdo, $restaurante['id'], ['status' => 'recebido']);
                    if (count($pedidos_pendentes) > 0):
                ?>
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse">
                        <?php echo count($pedidos_pendentes); ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <!-- Cardápio -->
            <a href="?pagina=cardapio" 
               class="sidebar-item flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl transition-all duration-200 <?php echo $pagina_atual == 'cardapio' ? 'active bg-gray-800 text-white' : ''; ?>">
                <i class="fas fa-utensils w-5 h-5 mr-3"></i>
                <span class="font-medium">Cardápio</span>
            </a>
            
            <!-- Configurações -->
            <a href="?pagina=configuracoes" 
               class="sidebar-item flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl transition-all duration-200 <?php echo $pagina_atual == 'configuracoes' ? 'active bg-gray-800 text-white' : ''; ?>">
                <i class="fas fa-cog w-5 h-5 mr-3"></i>
                <span class="font-medium">Configurações</span>
            </a>
            
            <!-- Funcionários -->
            <a href="?pagina=funcionarios" 
               class="sidebar-item flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl transition-all duration-200 <?php echo $pagina_atual == 'funcionarios' ? 'active bg-gray-800 text-white' : ''; ?>">
                <i class="fas fa-users w-5 h-5 mr-3"></i>
                <span class="font-medium">Funcionários</span>
            </a>
            
            <!-- Botão de Logout -->
            <div class="pt-4 mt-4 border-t border-gray-800">
                <a href="?acao=logout" 
                   class="sidebar-item flex items-center p-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl transition-all duration-200">
                    <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                    <span class="font-medium">Sair</span>
                </a>
            </div>
        </nav>
        
        <!-- Footer Sidebar -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800">
            <div class="text-center">
                <p class="text-xs text-gray-500">v<?php echo APP_VERSION; ?></p>
                <p class="text-xs text-gray-600 mt-1"><?php echo htmlspecialchars($restaurante['nome']); ?></p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div id="main-content" class="main-content transition-all duration-300 min-h-screen">
        <!-- Header Premium -->
        <header class="bg-white shadow-md h-16 md:h-20 flex items-center justify-between px-4 md:px-8 sticky top-0 z-30">
            <div class="flex items-center space-x-4">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                    <?php 
                        $titulo = [
                            'painel' => 'Painel de Controle',
                            'pedidos' => 'Gestão de Pedidos',
                            'cardapio' => 'Gerenciar Cardápio',
                            'configuracoes' => 'Configurações',
                            'funcionarios' => 'Funcionários'
                        ];
                        echo $titulo[$pagina_atual] ?? 'Dashboard';
                    ?>
                </h2>
            </div>
            
            <div class="flex items-center space-x-3 md:space-x-4">
                <!-- Notificações -->
                <?php if ($stats['pedidos_hoje'] > 0): ?>
                    <div class="relative">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center text-red-600 cursor-pointer">
                            <i class="fas fa-bell"></i>
                        </div>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                            <?php echo min($stats['pedidos_hoje'], 9); ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <!-- Usuário -->
                <div class="flex items-center space-x-3 group cursor-pointer">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($usuario_logado['nome']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($restaurante['nome']); ?></p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-110 transition-transform duration-200">
                        <?php echo strtoupper(substr($usuario_logado['nome'], 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-4 md:p-6">
            <!-- Mensagens -->
            <?php if ($sucesso): ?>
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-100 border border-green-200 rounded-2xl p-4 animate-fade-in">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-green-800">Sucesso!</h3>
                            <div class="text-green-700"><?php echo $sucesso; ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-100 border border-red-200 rounded-2xl p-4 animate-fade-in">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-red-800">Atenção</h3>
                            <div class="text-red-700"><?php echo $erro; ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Conteúdo Dinâmico -->
            <div class="animate-fade-in">
                <?php 
                switch ($pagina_atual) {
                    case 'painel':
                        render_painel($pdo, $restaurante, $stats, $pedidos_recebidos);
                        break;
                    case 'pedidos':
                        render_pedidos($pdo, $restaurante);
                        break;
                    case 'cardapio':
                        render_cardapio($pdo, $restaurante);
                        break;
                    case 'configuracoes':
                        render_configuracoes($pdo, $restaurante);
                        break;
                    case 'funcionarios':
                        render_funcionarios($pdo, $restaurante);
                        break;
                    default:
                        render_painel($pdo, $restaurante, $stats, $pedidos_recebidos);
                        break;
                }
                ?>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Toggle Sidebar Mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('hidden');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        });
        
        // Fechar sidebar ao clicar no overlay
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        });
        
        // Fechar sidebar ao clicar em um link (mobile)
        document.querySelectorAll('.sidebar-item').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });
        
        // Fechar sidebar ao redimensionar para desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
        
        // Prevenir fechamento ao clicar dentro do sidebar
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Formatação de campos monetários
        document.addEventListener('DOMContentLoaded', function() {
            const moedaInputs = document.querySelectorAll('.input-moeda');
            
            moedaInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = (value / 100).toFixed(2) + '';
                    value = value.replace(".", ",");
                    value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                    value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
                    e.target.value = 'R$ ' + value;
                });
                
                // Formatar valor inicial se existir
                if (input.value) {
                    let value = input.value.replace(/\D/g, '');
                    value = (value / 100).toFixed(2) + '';
                    value = value.replace(".", ",");
                    value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                    value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
                    input.value = 'R$ ' + value;
                }
            });
        });
    </script>
</body>
</html>