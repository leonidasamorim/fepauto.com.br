-- Rallye do Sol – Banco de dados
CREATE DATABASE IF NOT EXISTS rallye_inscricoes
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rallye_inscricoes;

CREATE TABLE IF NOT EXISTS inscricoes (
    id                        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    -- Dados pessoais
    nome                      VARCHAR(255)  NOT NULL,
    cpf                       VARCHAR(20)   NOT NULL,
    rg                        VARCHAR(30)   NOT NULL,
    dt_nascimento             DATE          NOT NULL,
    nome_pai                  VARCHAR(255)  NOT NULL,
    nome_mae                  VARCHAR(255)  NOT NULL,
    -- Endereço
    cep                       VARCHAR(10)   NOT NULL,
    endereco                  VARCHAR(255)  NOT NULL,
    bairro                    VARCHAR(100)  NOT NULL,
    cidade                    VARCHAR(100)  NOT NULL,
    estado                    VARCHAR(50)   NOT NULL,
    -- Contato
    email                     VARCHAR(255)  NOT NULL,
    telefone                  VARCHAR(20)   NOT NULL,
    tipo_sangue               VARCHAR(5)    NOT NULL,
    -- Veículo
    veiculo                   VARCHAR(50)   NOT NULL,
    categoria                 VARCHAR(100)  NOT NULL,
    -- Dados do navegador (apenas para Carro/UTV)
    navegador_nome            VARCHAR(255)  DEFAULT NULL,
    navegador_rg              VARCHAR(30)   DEFAULT NULL,
    tipo_sangue_navegador     VARCHAR(5)    DEFAULT NULL,
    -- Carteira da federação
    possui_carteira           TINYINT(1)    DEFAULT NULL,
    carteira_valida           TINYINT(1)    DEFAULT NULL,
    num_carteira              VARCHAR(50)   DEFAULT NULL,
    especificar_carro         VARCHAR(255)  DEFAULT NULL,
    especificar_moto          VARCHAR(255)  DEFAULT NULL,
    especificar_moto_renovacao VARCHAR(255) DEFAULT NULL,
    -- Participação
    participacao              TINYINT(1)    NOT NULL DEFAULT 0,
    -- Financeiro
    valor                     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status_pagamento          ENUM('pendente','pago','cancelado') NOT NULL DEFAULT 'pendente',
    pagseguro_code            VARCHAR(100)  DEFAULT NULL,
    pagseguro_transaction_id  VARCHAR(100)  DEFAULT NULL,
    -- Controle
    created_at                TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_cpf (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Execute painel/criar-admin.php no navegador para definir a senha do admin.
-- Enquanto isso, nenhum usuário admin está criado (tabela vazia é intencional).
