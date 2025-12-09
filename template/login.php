<?php
// templates/login.php - Template de Login

// Definir constantes CSS/JS específicas do login
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> Premium - Sistema de Cardápio Online</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="login-card rounded-3xl shadow-2xl w-full max-w-4xl overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- Lado Esquerdo - Apresentação Premium -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-8 md:p-12 flex flex-col justify-center relative overflow-hidden">
                <!-- Efeito de partículas -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white rounded-full animate-float"></div>
                    <div class="absolute top-1/3 right-1/4 w-24 h-24 bg-white rounded-full animate-float" style="animation-delay: 0.5s"></div>
                    <div class="absolute bottom-1/4 left-1/3 w-20 h-20 bg-white rounded-full animate-float" style="animation-delay: 1s"></div>
                </div>
                
                <div class="relative z-10">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-utensils text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold"><?php echo APP_NAME; ?> <span class="text-yellow-300">Premium</span></h1>
                    </div>
                    
                    <p class="text-xl opacity-90 mb-8 leading-relaxed">
                        Sistema premium de cardápio online para restaurantes de alto padrão.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-check-circle text-yellow-300"></i>
                            <span>Cardápio digital premium personalizável</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-check-circle text-yellow-300"></i>
                            <span>Gestão de pedidos em tempo real avançada</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-check-circle text-yellow-300"></i>
                            <span>Relatórios analytics completos</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-check-circle text-yellow-300"></i>
                            <span>Comandas digitais e impressão</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lado Direito - Formulários -->
            <div class="bg-white p-8 md:p-12">
                <!-- Tabs Premium -->
                <div class="flex space-x-1 bg-gray-100 rounded-2xl p-1 mb-8">
                    <button id="tabLogin" 
                            onclick="showLogin()"
                            class="flex-1 py-3 px-4 rounded-xl font-bold transition-all duration-300 bg-white text-red-600 shadow-lg transform scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i> Entrar
                    </button>
                    <button id="tabSignup" 
                            onclick="showSignup()"
                            class="flex-1 py-3 px-4 rounded-xl font-bold transition-all duration-300 text-gray-600 hover:text-red-600">
                        <i class="fas fa-user-plus mr-2"></i> Cadastrar
                    </button>
                </div>
                
                <!-- Mensagens -->
                <?php if ($erro): ?>
                    <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Atenção</h3>
                                <div class="mt-1 text-sm text-red-700"><?php echo $erro; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Formulário de Login -->
                <form id="loginForm" method="post" class="space-y-6">
                    <input type="hidden" name="acao" value="login">
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">E-mail</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" name="email" required 
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                                   placeholder="seu@email.com">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Senha</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" name="senha" required 
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Sua senha">
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3 px-4 rounded-xl font-bold hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-in-alt mr-2"></i> Entrar no Sistema
                    </button>
                </form>
                
                <!-- Formulário de Cadastro -->
                <form id="signupForm" method="post" class="space-y-6 hidden">
                    <input type="hidden" name="acao" value="registrar">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Seu Nome</label>
                            <input type="text" name="nome" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Seu nome completo">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Restaurante</label>
                            <input type="text" name="nome_restaurante" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Nome do restaurante">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">E-mail</label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                               placeholder="seu@email.com">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Telefone (WhatsApp)</label>
                        <input type="text" name="telefone" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                               placeholder="(99) 99999-9999">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Senha</label>
                            <input type="password" name="senha" required minlength="6"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Mínimo 6 caracteres">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                            <input type="password" name="confirmar_senha" required minlength="6"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Repita a senha">
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3 px-4 rounded-xl font-bold hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl">
                        <i class="fas fa-rocket mr-2"></i> Criar Conta Premium
                    </button>
                </form>
                
                <script>
                    function showLogin() {
                        document.getElementById('loginForm').classList.remove('hidden');
                        document.getElementById('signupForm').classList.add('hidden');
                        document.getElementById('tabLogin').classList.add('bg-white', 'text-red-600', 'shadow-lg', 'scale-105');
                        document.getElementById('tabLogin').classList.remove('text-gray-600');
                        document.getElementById('tabSignup').classList.remove('bg-white', 'text-red-600', 'shadow-lg', 'scale-105');
                        document.getElementById('tabSignup').classList.add('text-gray-600');
                    }
                    
                    function showSignup() {
                        document.getElementById('loginForm').classList.add('hidden');
                        document.getElementById('signupForm').classList.remove('hidden');
                        document.getElementById('tabSignup').classList.add('bg-white', 'text-red-600', 'shadow-lg', 'scale-105');
                        document.getElementById('tabSignup').classList.remove('text-gray-600');
                        document.getElementById('tabLogin').classList.remove('bg-white', 'text-red-600', 'shadow-lg', 'scale-105');
                        document.getElementById('tabLogin').classList.add('text-gray-600');
                    }
                    
                    <?php if ($erro && isset($_POST['acao']) && $_POST['acao'] === 'registrar'): ?>
                        showSignup();
                    <?php endif; ?>
                </script>
            </div>
        </div>
    </div>
</body>
</html>