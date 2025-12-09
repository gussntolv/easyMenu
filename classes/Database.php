<?php
// classes/Database.php - Gerenciamento de Banco de Dados SQLite

require_once __DIR__ . '/../config.php';

class Database {
    private $pdo;
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    private function __construct() {
        try {
            $db_file = DATA_DIR . 'easymenu.db';
            
            // Verificar se o arquivo do banco existe
            if (!file_exists($db_file)) {
                // Criar diretório se não existir
                if (!is_dir(DATA_DIR)) {
                    mkdir(DATA_DIR, 0755, true);
                }
            }
            
            $this->pdo = new PDO('sqlite:' . $db_file);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->exec('PRAGMA foreign_keys = ON');
            $this->pdo->exec('PRAGMA journal_mode = WAL');
            
            // Criar tabelas se necessário
            $this->createTables();
            
        } catch (PDOException $e) {
            error_log("Erro no banco de dados: " . $e->getMessage());
            die("Erro crítico no banco de dados. Por favor, tente novamente mais tarde.");
        }
    }
    
    private function createTables() {
        $tables = [
            // Tabela de usuários
            "CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE NOT NULL,
                senha_hash TEXT NOT NULL,
                nome TEXT NOT NULL,
                telefone TEXT,
                avatar TEXT,
                data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
                ultimo_login DATETIME,
                ativo INTEGER DEFAULT 1,
                tipo TEXT DEFAULT 'restaurante'
            )",
            
            // Tabela de restaurantes
            "CREATE TABLE IF NOT EXISTS restaurantes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER UNIQUE,
                nome TEXT NOT NULL,
                slogan TEXT,
                descricao TEXT,
                logo TEXT,
                banner TEXT,
                cnpj TEXT,
                endereco TEXT,
                numero TEXT,
                bairro TEXT,
                cidade TEXT DEFAULT 'São Paulo',
                estado TEXT DEFAULT 'SP',
                cep TEXT,
                telefone TEXT,
                whatsapp TEXT,
                instagram TEXT,
                site TEXT,
                horario_abertura TIME DEFAULT '10:00',
                horario_fechamento TIME DEFAULT '22:00',
                dias_funcionamento TEXT DEFAULT 'segunda,terca,quarta,quinta,sexta,sabado,domingo',
                cor_principal TEXT DEFAULT '#DC2626',
                cor_secundaria TEXT DEFAULT '#F59E0B',
                cor_texto TEXT DEFAULT '#1F2937',
                cor_fundo TEXT DEFAULT '#FFFFFF',
                tema TEXT DEFAULT 'claro',
                taxa_entrega DECIMAL(10,2) DEFAULT 5.00,
                pedido_minimo DECIMAL(10,2) DEFAULT 15.00,
                raio_entrega INTEGER DEFAULT 5,
                aceita_retirada INTEGER DEFAULT 1,
                aceita_delivery INTEGER DEFAULT 1,
                aceita_local INTEGER DEFAULT 1,
                formas_pagamento TEXT DEFAULT 'dinheiro,pix,credito,debito',
                ativo INTEGER DEFAULT 1,
                visualizacoes INTEGER DEFAULT 0,
                avaliacao_media DECIMAL(3,2) DEFAULT 5.0,
                total_avaliacoes INTEGER DEFAULT 0,
                data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
            )",
            
            // Tabela de categorias
            "CREATE TABLE IF NOT EXISTS categorias (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                restaurante_id INTEGER,
                nome TEXT NOT NULL,
                descricao TEXT,
                icone TEXT DEFAULT 'fas fa-utensils',
                ordem INTEGER DEFAULT 0,
                ativo INTEGER DEFAULT 1,
                data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id) ON DELETE CASCADE
            )",
            
            // Tabela de produtos
            "CREATE TABLE IF NOT EXISTS produtos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                categoria_id INTEGER,
                nome TEXT NOT NULL,
                descricao TEXT,
                preco DECIMAL(10,2) NOT NULL,
                preco_promocional DECIMAL(10,2),
                ingredientes TEXT,
                informacoes_nutricionais TEXT,
                tempo_preparo INTEGER DEFAULT 20,
                imagem TEXT,
                destaque INTEGER DEFAULT 0,
                disponivel INTEGER DEFAULT 1,
                controle_estoque INTEGER DEFAULT 0,
                estoque_atual INTEGER DEFAULT 0,
                estoque_minimo INTEGER DEFAULT 0,
                ordem INTEGER DEFAULT 0,
                tags TEXT,
                data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
            )",
            
            // Tabela de pedidos
            "CREATE TABLE IF NOT EXISTS pedidos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                codigo TEXT UNIQUE NOT NULL,
                restaurante_id INTEGER,
                itens_json TEXT NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                taxa_entrega DECIMAL(10,2) DEFAULT 0.00,
                desconto DECIMAL(10,2) DEFAULT 0.00,
                total DECIMAL(10,2) NOT NULL,
                tipo_pedido TEXT NOT NULL,
                forma_pagamento TEXT NOT NULL,
                status TEXT DEFAULT 'recebido',
                cliente_nome TEXT NOT NULL,
                cliente_telefone TEXT NOT NULL,
                cliente_email TEXT,
                endereco_entrega TEXT,
                complemento TEXT,
                ponto_referencia TEXT,
                cep TEXT,
                troco_para DECIMAL(10,2),
                observacoes TEXT,
                tempo_estimado INTEGER,
                avaliacao INTEGER,
                comentario_avaliacao TEXT,
                data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
                data_aceito DATETIME,
                data_preparo DATETIME,
                data_pronto DATETIME,
                data_entrega DATETIME,
                data_finalizado DATETIME,
                FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id) ON DELETE CASCADE
            )",
            
            // Tabela para funcionários
            "CREATE TABLE IF NOT EXISTS funcionarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                restaurante_id INTEGER,
                usuario_id INTEGER,
                nome TEXT NOT NULL,
                email TEXT NOT NULL,
                telefone TEXT,
                cargo TEXT DEFAULT 'atendente',
                permissoes TEXT DEFAULT 'ver_pedidos,alterar_status',
                ativo INTEGER DEFAULT 1,
                data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id) ON DELETE CASCADE,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
            )"
        ];
        
        foreach ($tables as $tableSQL) {
            try {
                $this->pdo->exec($tableSQL);
            } catch (PDOException $e) {
                error_log("Erro ao criar tabela: " . $e->getMessage());
            }
        }
        
        // Criar índices para performance
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_pedidos_status ON pedidos(status, restaurante_id)",
            "CREATE INDEX IF NOT EXISTS idx_produtos_categoria ON produtos(categoria_id, disponivel, ordem)",
            "CREATE INDEX IF NOT EXISTS idx_categorias_restaurante ON categorias(restaurante_id, ativo, ordem)",
            "CREATE INDEX IF NOT EXISTS idx_pedidos_data ON pedidos(data_pedido, restaurante_id)"
        ];
        
        foreach ($indexes as $indexSQL) {
            try {
                $this->pdo->exec($indexSQL);
            } catch (Exception $e) {
                // Índice pode já existir, continuar
            }
        }
    }
    
    public function getPdo() {
        return $this->pdo;
    }
}
?>