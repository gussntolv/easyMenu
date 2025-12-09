<?php
// EasyMenu - Sistema Completo de Cardápio Online Premium
// config.php - Configurações globais para admin e cardápio público

// Iniciar sessão apenas se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Sao_Paulo');

// Configurações globais
define('APP_NAME', 'EasyMenu');
define('APP_VERSION', '3.0 - Premium Edition');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('DATA_DIR', __DIR__ . '/data/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Criar diretórios necessários
$diretorios_necessarios = [
    DATA_DIR, 
    UPLOAD_DIR, 
    UPLOAD_DIR . 'logos/', 
    UPLOAD_DIR . 'banners/', 
    UPLOAD_DIR . 'produtos/', 
    DATA_DIR . 'backups/'
];

foreach ($diretorios_necessarios as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        // Criar arquivo .htaccess para proteger diretórios
        if (strpos($dir, 'uploads') !== false || strpos($dir, 'data') !== false) {
            file_put_contents($dir . '.htaccess', "Deny from all\n");
        }
    }
}
?>