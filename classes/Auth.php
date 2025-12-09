<?php
// classes/Auth.php - Gerenciamento de Autenticação

class Auth {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function registrar($dados) {
        $pdo = $this->db->getPdo();
        
        // Validar dados obrigatórios
        $campos_obrigatorios = ['nome', 'email', 'telefone', 'nome_restaurante', 'senha', 'confirmar_senha'];
        foreach ($campos_obrigatorios as $campo) {
            if (empty($dados[$campo])) {
                throw new Exception("O campo " . str_replace('_', ' ', $campo) . " é obrigatório.");
            }
        }
        
        // Validar email
        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('E-mail inválido.');
        }
        
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$dados['email']]);
        if ($stmt->fetch()) {
            throw new Exception('Este e-mail já está cadastrado.');
        }
        
        // Validar força da senha
        if (strlen($dados['senha']) < 6) {
            throw new Exception('A senha deve ter pelo menos 6 caracteres.');
        }
        
        if ($dados['senha'] !== $dados['confirmar_senha']) {
            throw new Exception('As senhas não coincidem.');
        }
        
        $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nome, email, telefone, senha_hash) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                trim($dados['nome']),
                trim($dados['email']),
                trim($dados['telefone']),
                $senha_hash
            ]);
            
            $usuario_id = $pdo->lastInsertId();
            
            // Gerar código único para o restaurante
            $codigo_restaurante = 'REST' . str_pad($usuario_id, 6, '0', STR_PAD_LEFT);
            
            // Criar restaurante padrão
            $stmt = $pdo->prepare("
                INSERT INTO restaurantes (usuario_id, nome, telefone, whatsapp, endereco) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $usuario_id,
                trim($dados['nome_restaurante']),
                $dados['telefone'],
                $dados['telefone'],
                'Endereço a definir'
            ]);
            
            $pdo->commit();
            return $usuario_id;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception('Erro ao criar conta: ' . $e->getMessage());
        }
    }
    
    public function login($email, $senha) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT id, email, senha_hash, nome, avatar, ativo, tipo 
            FROM usuarios 
            WHERE email = ? AND ativo = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($senha, $user['senha_hash'])) {
            // Atualizar último login
            $stmt = $this->db->getPdo()->prepare("
                UPDATE usuarios SET ultimo_login = CURRENT_TIMESTAMP WHERE id = ?
            ");
            $stmt->execute([$user['id']]);
            
            $_SESSION['usuario'] = $user;
            $_SESSION['usuario_id'] = $user['id'];
            return true;
        }
        return false;
    }
    
    public function logout() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    
    public function isLogged() {
        return isset($_SESSION['usuario_id']);
    }
    
    public function getUser() {
        return $_SESSION['usuario'] ?? null;
    }
    
    public function getUserId() {
        return $_SESSION['usuario_id'] ?? null;
    }
}
?>