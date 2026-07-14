<?php
/**
 * EXECUTAR UMA VEZ para criar tabelas
 * Acesso: https://seu-dominio.com/api/db-config.php?init=true&token=sua-senha
 */

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  respond(['error' => 'Use GET method'], 405);
}

$token = $_GET['token'] ?? '';
if ($token !== ADMIN_TOKEN) {
  respond(['error' => 'Invalid token'], 403);
}

$action = $_GET['init'] ?? '';

if ($action === 'true') {
  createTables();
  respond([
    'success' => true,
    'message' => 'Tabelas criadas com sucesso!',
    'timestamp' => date('Y-m-d H:i:s')
  ], 200);
} else {
  respond(['error' => 'Use ?init=true parameter'], 400);
}

function createTables() {
  if (!DB_ENABLED) {
    throw new Exception('Database not enabled in config');
  }

  $pdo = getDBConnection();
  if (!$pdo) {
    throw new Exception('Failed to connect to database');
  }

  $sql_leads = <<<SQL
CREATE TABLE IF NOT EXISTS leads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip VARCHAR(45),
  nome VARCHAR(255) NOT NULL,
  empreendimento VARCHAR(255) NOT NULL,
  whatsapp VARCHAR(20) NOT NULL,
  email VARCHAR(255),
  tipo VARCHAR(100),
  porte VARCHAR(50),
  servicos TEXT,
  mensagem LONGTEXT,
  status ENUM('novo', 'contatado', 'convertido', 'rejeitado') DEFAULT 'novo',
  INDEX(timestamp),
  INDEX(status),
  INDEX(whatsapp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

  $sql_chat = <<<SQL
CREATE TABLE IF NOT EXISTS chat_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(100) UNIQUE,
  timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip VARCHAR(45),
  user_message LONGTEXT,
  bot_reply LONGTEXT,
  messages_count INT DEFAULT 0,
  INDEX(session_id),
  INDEX(timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

  $sql_logs = <<<SQL
CREATE TABLE IF NOT EXISTS admin_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip VARCHAR(45),
  action VARCHAR(100),
  details JSON,
  INDEX(timestamp),
  INDEX(action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

  try {
    $pdo->exec($sql_leads);
    $pdo->exec($sql_chat);
    $pdo->exec($sql_logs);
    return true;
  } catch (Exception $e) {
    throw new Exception('SQL error: ' . $e->getMessage());
  }
}

?>