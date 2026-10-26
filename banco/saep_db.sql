CREATE DATABASE IF NOT EXISTS saep_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saep_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ordens_servico;
DROP TABLE IF EXISTS equipamentos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  cpf_criptografado TEXT NOT NULL,
  telefone VARCHAR(30) NOT NULL,
  email VARCHAR(120) NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE equipamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  tipo VARCHAR(80) NOT NULL,
  marca VARCHAR(80) NOT NULL,
  modelo VARCHAR(100) NOT NULL,
  numero_serie VARCHAR(100) NULL,
  defeito_relatado TEXT NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_equipamento_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE ordens_servico (
  id INT AUTO_INCREMENT PRIMARY KEY,
  equipamento_id INT NOT NULL,
  descricao_servico TEXT NOT NULL,
  status ENUM('Aberta','Em andamento','Aguardando peça','Concluída','Cancelada') NOT NULL DEFAULT 'Aberta',
  valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  data_abertura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  data_conclusao DATETIME NULL,
  CONSTRAINT fk_os_equipamento FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Conta inicial: admin@local.test / password
INSERT INTO usuarios (nome,email,senha_hash) VALUES
('Administrador','admin@local.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.'),
('Mariana Costa','mariana@local.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.'),
('Rafael Souza','rafael@local.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');

-- CPFs fictícios criptografados por exemplo didático (AES-256-CBC).
-- A aplicação inclui clientes novos com criptografia real usando a chave em config.php.
INSERT INTO clientes (nome,cpf_criptografado,telefone,email) VALUES
('Ana Lima','SEED_DEMO_CPF_ANA','69990000001','ana@example.test'),
('Bruno Alves','SEED_DEMO_CPF_BRUNO','69990000002','bruno@example.test'),
('Carla Mendes','SEED_DEMO_CPF_CARLA','69990000003','carla@example.test');

INSERT INTO equipamentos (cliente_id,tipo,marca,modelo,numero_serie,defeito_relatado) VALUES
(1,'Notebook','Dell','Inspiron 15','SN-DEMO-001','Não liga'),
(2,'Desktop','Lenovo','ThinkCentre M70','SN-DEMO-002','Lentidão e superaquecimento'),
(3,'Impressora','HP','LaserJet Pro','SN-DEMO-003','Papel atolando');

INSERT INTO ordens_servico (equipamento_id,descricao_servico,status,valor,data_abertura) VALUES
(1,'Diagnóstico de alimentação','Aberta',80.00,'2026-10-20 09:30:00'),
(2,'Limpeza interna e análise térmica','Em andamento',150.00,'2026-10-21 11:15:00'),
(3,'Revisão do mecanismo de alimentação','Aguardando peça',120.00,'2026-10-22 14:00:00');
