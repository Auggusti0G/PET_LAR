-- =====================================================================
--  PET LAR - Sistema de Gestão de Adoção de Animais
--  Script do banco de dados (MySQL / MariaDB)
--
--  Trabalho A3 - Desenvolvimento Web com PHP
--  Centro Universitário de Brasília - UniCEUB
--  Professor: Francisco Molina
--
--  Integrantes:
--    Rafael Augusto Santana Gil
--    Ana Cecília Batista
--
--  Como usar: abra este arquivo no MySQL Workbench (ou phpMyAdmin)
--  e execute tudo de uma vez. O script pode ser executado várias
--  vezes, pois apaga e recria o banco do zero.
-- =====================================================================

-- 1. Cria o banco de dados (apaga o antigo, se existir)
DROP DATABASE IF EXISTS petlar;
CREATE DATABASE petlar CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE petlar;

-- ---------------------------------------------------------------------
-- 2. TABELAS
-- ---------------------------------------------------------------------

-- Usuários que podem entrar no sistema (módulo de autenticação)
CREATE TABLE usuario (
    id    INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome  VARCHAR(200) NOT NULL,
    login VARCHAR(100) NOT NULL UNIQUE,      -- não pode repetir login
    senha VARCHAR(255) NOT NULL              -- guarda o md5 da senha (texto)
);

-- Espécies de animais atendidas pela ONG (Cão, Gato...)
CREATE TABLE especie (
    id        INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(100) NOT NULL UNIQUE,
    descricao VARCHAR(255)
);

-- Animais resgatados que estão (ou estiveram) no abrigo
CREATE TABLE animal (
    id           INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(100) NOT NULL,
    especie_id   INTEGER NOT NULL,
    sexo         VARCHAR(10)  NOT NULL,      -- Macho / Fêmea
    porte        VARCHAR(10)  NOT NULL,      -- Pequeno / Médio / Grande
    idade        INTEGER      NOT NULL,      -- idade aproximada em anos
    data_entrada DATE         NOT NULL,      -- dia em que chegou ao abrigo
    status       VARCHAR(20)  NOT NULL DEFAULT 'Disponível', -- Disponível / Em tratamento / Adotado
    observacao   VARCHAR(255),
    -- RESTRICT: não deixa apagar uma espécie que tenha animais
    FOREIGN KEY (especie_id) REFERENCES especie(id) ON DELETE RESTRICT
);

-- Pessoas interessadas em adotar
CREATE TABLE adotante (
    id              INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(200) NOT NULL,
    cpf             VARCHAR(14)  NOT NULL UNIQUE,  -- um cadastro por CPF
    data_nascimento DATE         NOT NULL,         -- usado para conferir maioridade
    telefone        VARCHAR(20)  NOT NULL,
    email           VARCHAR(150),
    cidade          VARCHAR(100) NOT NULL
);

-- Voluntários da ONG (quem acompanha as adoções)
CREATE TABLE voluntario (
    id       INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome     VARCHAR(200) NOT NULL,
    telefone VARCHAR(20)  NOT NULL,
    email    VARCHAR(150),
    funcao   VARCHAR(100) NOT NULL           -- Ex.: Cuidador, Veterinário, Entrevistador
);

-- Adoções realizadas (liga animal + adotante + voluntário)
CREATE TABLE adocao (
    id            INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    animal_id     INTEGER NOT NULL,
    adotante_id   INTEGER NOT NULL,
    voluntario_id INTEGER NOT NULL,
    data_adocao   DATE    NOT NULL,
    observacao    VARCHAR(255),
    FOREIGN KEY (animal_id)     REFERENCES animal(id)     ON DELETE RESTRICT,
    FOREIGN KEY (adotante_id)   REFERENCES adotante(id)   ON DELETE RESTRICT,
    FOREIGN KEY (voluntario_id) REFERENCES voluntario(id) ON DELETE RESTRICT
);

-- Vacinas aplicadas em cada animal
CREATE TABLE vacinacao (
    id             INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    animal_id      INTEGER      NOT NULL,
    vacina         VARCHAR(100) NOT NULL,
    data_aplicacao DATE         NOT NULL,
    proxima_dose   DATE,                     -- pode ficar vazio (dose única)
    veterinario    VARCHAR(200) NOT NULL,
    -- CASCADE: se o animal for apagado, as vacinas dele também são
    FOREIGN KEY (animal_id) REFERENCES animal(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------------
-- 3. CARGA INICIAL (dados de exemplo)
-- ---------------------------------------------------------------------

-- Toda senha passa pelo md5, pois o login.php compara com md5()
-- Senha de todos os usuários: 123456
INSERT INTO usuario (nome, login, senha) VALUES ('Administrador',              'admin',       md5('123456'));
INSERT INTO usuario (nome, login, senha) VALUES ('Rafael Augusto Santana Gil', 'rafael.ag',   md5('123456'));
INSERT INTO usuario (nome, login, senha) VALUES ('Ana Cecília Batista',        'ana.cecilia', md5('123456'));

INSERT INTO especie (nome, descricao) VALUES ('Cão',    'Cães de todas as raças e vira-latas');
INSERT INTO especie (nome, descricao) VALUES ('Gato',   'Gatos domésticos');
INSERT INTO especie (nome, descricao) VALUES ('Coelho', 'Coelhos domésticos');
INSERT INTO especie (nome, descricao) VALUES ('Ave',    'Calopsitas, periquitos e outras aves domésticas');
INSERT INTO especie (nome, descricao) VALUES ('Roedor', 'Hamsters e porquinhos-da-índia');

INSERT INTO animal (nome, especie_id, sexo, porte, idade, data_entrada, status, observacao) VALUES
('Thor',      1, 'Macho', 'Grande',  3, '2026-01-15', 'Adotado',       'Muito brincalhão, ama crianças'),
('Mel',       1, 'Fêmea', 'Médio',   2, '2026-02-03', 'Adotado',       'Castrada e dócil'),
('Pipoca',    2, 'Fêmea', 'Pequeno', 1, '2026-02-20', 'Adotado',       'Encontrada em caixa de papelão'),
('Bolinha',   3, 'Macho', 'Pequeno', 1, '2026-03-11', 'Disponível',    'Come muita cenoura'),
('Luna',      2, 'Fêmea', 'Pequeno', 4, '2026-03-28', 'Adotado',       'Calma, gosta de colo'),
('Rex',       1, 'Macho', 'Grande',  6, '2026-04-09', 'Em tratamento', 'Recuperando de cirurgia na pata'),
('Fred',      4, 'Macho', 'Pequeno', 2, '2026-05-02', 'Disponível',    'Calopsita que assobia'),
('Nina',      1, 'Fêmea', 'Pequeno', 1, '2026-05-19', 'Adotado',       'Filhote, muito energética'),
('Simba',     2, 'Macho', 'Médio',   3, '2026-06-07', 'Disponível',    'Laranja, independente'),
('Paçoca',    1, 'Macho', 'Médio',   5, '2026-06-30', 'Disponível',    'Vira-lata caramelo'),
('Amora',     5, 'Fêmea', 'Pequeno', 1, '2026-07-14', 'Disponível',    'Porquinho-da-índia'),
('Zeus',      1, 'Macho', 'Grande',  8, '2026-08-01', 'Disponível',    'Idoso, tranquilo'),
('Mia',       2, 'Fêmea', 'Pequeno', 2, '2026-08-22', 'Adotado',       'Tímida no começo'),
('Floquinho', 3, 'Fêmea', 'Pequeno', 2, '2026-09-10', 'Em tratamento', 'Tratando infecção no olho'),
('Bob',       1, 'Macho', 'Médio',   4, '2026-10-05', 'Disponível',    'Resgatado na chuva');

INSERT INTO adotante (nome, cpf, data_nascimento, telefone, email, cidade) VALUES
('Mariana Souza',      '123.456.789-01', '1990-04-12', '(61) 99876-1234', 'mariana@email.com',  'Brasília'),
('Carlos Henrique',    '234.567.890-12', '1985-09-30', '(61) 99765-2345', 'carlos@email.com',   'Taguatinga'),
('Juliana Ferreira',   '345.678.901-23', '1998-01-22', '(61) 99654-3456', 'juliana@email.com',  'Águas Claras'),
('Pedro Almeida',      '456.789.012-34', '1979-11-05', '(61) 99543-4567', 'pedro@email.com',    'Ceilândia'),
('Fernanda Lima',      '567.890.123-45', '2001-07-17', '(61) 99432-5678', 'fernanda@email.com', 'Brasília'),
('Lucas Martins',      '678.901.234-56', '1995-03-08', '(61) 99321-6789', 'lucas@email.com',    'Samambaia'),
('Beatriz Rocha',      '789.012.345-67', '1988-12-25', '(61) 99210-7890', 'beatriz@email.com',  'Guará');

INSERT INTO voluntario (nome, telefone, email, funcao) VALUES
('Dra. Paula Mendes',  '(61) 98111-1111', 'paula@petlar.org',  'Veterinária'),
('João Victor',        '(61) 98222-2222', 'joao@petlar.org',   'Entrevistador'),
('Camila Torres',      '(61) 98333-3333', 'camila@petlar.org', 'Cuidadora'),
('Rodrigo Nunes',      '(61) 98444-4444', 'rodrigo@petlar.org','Entrevistador'),
('Larissa Campos',     '(61) 98555-5555', 'larissa@petlar.org','Cuidadora');

-- As adoções abaixo combinam com os animais marcados como 'Adotado'
INSERT INTO adocao (animal_id, adotante_id, voluntario_id, data_adocao, observacao) VALUES
(1,  1, 2, '2026-03-02', 'Casa com quintal grande'),
(2,  2, 4, '2026-04-18', 'Família com duas crianças'),
(3,  3, 2, '2026-05-10', 'Primeiro pet da adotante'),
(5,  5, 4, '2026-06-21', 'Apartamento telado'),
(8,  4, 2, '2026-08-09', 'Retorno de visita agendado'),
(13, 6, 4, '2026-10-12', 'Adaptação acompanhada por 30 dias');

INSERT INTO vacinacao (animal_id, vacina, data_aplicacao, proxima_dose, veterinario) VALUES
(1,  'V10 (polivalente)', '2026-01-20', '2027-01-20', 'Dra. Paula Mendes'),
(1,  'Antirrábica',       '2026-01-20', '2027-01-20', 'Dra. Paula Mendes'),
(2,  'V10 (polivalente)', '2026-02-10', '2027-02-10', 'Dra. Paula Mendes'),
(3,  'V4 felina',         '2026-03-01', '2026-11-05', 'Dra. Paula Mendes'),
(5,  'V4 felina',         '2026-04-02', '2027-04-02', 'Dra. Paula Mendes'),
(6,  'Antirrábica',       '2026-04-15', '2027-04-15', 'Dra. Paula Mendes'),
(8,  'V10 (polivalente)', '2026-05-25', '2026-11-15', 'Dra. Paula Mendes'),
(9,  'V4 felina',         '2026-06-12', '2026-11-20', 'Dra. Paula Mendes'),
(10, 'V10 (polivalente)', '2026-07-05', '2027-07-05', 'Dra. Paula Mendes'),
(10, 'Antirrábica',       '2026-07-05', '2027-07-05', 'Dra. Paula Mendes'),
(12, 'Antirrábica',       '2026-08-03', '2027-08-03', 'Dra. Paula Mendes'),
(13, 'V4 felina',         '2026-08-28', '2026-11-28', 'Dra. Paula Mendes'),
(15, 'V10 (polivalente)', '2026-10-08', '2026-11-08', 'Dra. Paula Mendes');
