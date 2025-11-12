-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12-Nov-2025 às 14:36
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ajudapet`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `administrador`
--

CREATE TABLE `administrador` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` varchar(50) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_desligamento` datetime DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `administrador`
--

INSERT INTO `administrador` (`id`, `nome`, `email`, `senha`, `nivel_acesso`, `data_cadastro`, `data_desligamento`, `ativo`) VALUES
(1, 'Admin Mestre', 'admin@ajudapet.com', '$2y$10$OCrXfGH7eMU0dP6qLU3PbOZcxEYyEOR7Exg4ZJHcg3HYOj3tgJthq', '0', '2025-11-10 12:00:47', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `animal`
--

CREATE TABLE `animal` (
  `id` int(11) NOT NULL,
  `id_admin_cadastro` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `sexo` enum('Macho','Fêmea') DEFAULT NULL,
  `raca` varchar(100) DEFAULT NULL,
  `porte` enum('Pequeno','Médio','Grande') DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `cor_pelagem` varchar(50) DEFAULT NULL,
  `personalidade` text DEFAULT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `castrado` tinyint(1) DEFAULT 0,
  `microchip` varchar(50) DEFAULT NULL,
  `status` enum('Disponível','Em processo','Adotado','Indisponível') DEFAULT 'Disponível',
  `descricao_historia` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_adocao` datetime DEFAULT NULL,
  `id_adotante` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `animal`
--

INSERT INTO `animal` (`id`, `id_admin_cadastro`, `nome`, `sexo`, `raca`, `porte`, `data_nascimento`, `peso`, `cor_pelagem`, `personalidade`, `imagem_url`, `castrado`, `microchip`, `status`, `descricao_historia`, `observacoes`, `data_cadastro`, `data_adocao`, `id_adotante`) VALUES
(1, 1, 'Totó', 'Macho', 'Vira-lata', 'Médio', '2023-07-04', 15.30, 'Preto e branco', 'Dócil', 'animal_691324b0232f9.jpg', 0, '', 'Adotado', 'Extremamente amável e carinhoso, já deita de barriga para cima assim que é chamado', '', '2025-11-11 11:57:36', NULL, NULL),
(2, 1, 'Thor', 'Macho', 'SRD', 'Grande', '2016-02-02', 40.00, 'Preto', 'Dócil', 'animal_691360b5293ea.jfif', 1, '', 'Adotado', 'Thor foi vitima de uma doença grave de pele, sofreu ataques de outros cachorros que o deixaram com sequelas', 'artrose, artrite, obeso', '2025-11-11 16:13:41', '2025-11-12 10:15:03', 1),
(3, 1, 'Mel', 'Fêmea', 'SRD', 'Pequeno', '2023-06-12', 7.00, 'preto', 'Chata', 'animal_69147e8fdc7d5.jfif', 1, '', 'Disponível', 'Mel passou por uma serie de dificuldades blbablablablabla', 'Saudavel', '2025-11-12 12:33:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `solicitacaoadoção`
--

CREATE TABLE `solicitacaoadoção` (
  `id` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `id_admin_avaliador` int(11) DEFAULT NULL,
  `data_solicitacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pendente','Aprovada','Rejeitada') DEFAULT 'Pendente',
  `visto_pelo_solicitante` tinyint(1) NOT NULL DEFAULT 0,
  `observacoes` text DEFAULT NULL,
  `data_visita_sugerida` datetime DEFAULT NULL,
  `data_visita_aprovada` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `solicitacaoadoção`
--

INSERT INTO `solicitacaoadoção` (`id`, `id_animal`, `id_solicitante`, `id_admin_avaliador`, `data_solicitacao`, `status`, `visto_pelo_solicitante`, `observacoes`, `data_visita_sugerida`, `data_visita_aprovada`) VALUES
(1, 1, 1, 1, '2025-11-12 12:18:04', 'Rejeitada', 1, 'banana', '2025-11-13 09:17:00', NULL),
(4, 1, 1, 1, '2025-11-12 12:21:20', 'Aprovada', 1, 'sim, banana', '2025-11-13 09:17:00', '2025-11-13 10:00:00'),
(5, 2, 1, 1, '2025-11-12 12:30:02', 'Rejeitada', 1, 'Maçã\r\n', NULL, NULL),
(6, 2, 1, 1, '2025-11-12 13:14:27', 'Aprovada', 1, 'sga', NULL, '2025-11-12 10:14:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `solicitante`
--

CREATE TABLE `solicitante` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco_completo` varchar(500) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `tipo_moradia` varchar(100) DEFAULT NULL,
  `possui_quintal` enum('Sim','Não') DEFAULT NULL,
  `ja_teve_pets` tinyint(1) DEFAULT NULL,
  `descricao_pets_anteriores` text DEFAULT NULL,
  `experiencia_animais` varchar(255) DEFAULT NULL,
  `disponibilidade_tempo` varchar(255) DEFAULT NULL,
  `motivacao_adotar` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `solicitante`
--

INSERT INTO `solicitante` (`id`, `nome`, `cpf`, `email`, `senha`, `telefone`, `endereco_completo`, `cidade`, `estado`, `cep`, `tipo_moradia`, `possui_quintal`, `ja_teve_pets`, `descricao_pets_anteriores`, `experiencia_animais`, `disponibilidade_tempo`, `motivacao_adotar`, `data_cadastro`, `ativo`) VALUES
(1, 'teste da silva', '123456789010', 'teste@email.com', '$2y$10$K6jaR0gKmX2y3.yBghfUIedmKAzHauaEBiiIYOrmK4n1XCekPex6W', '99999999999', 'rua dos bobos nº0', 'ituvecuty', 'sp', NULL, 'casa', NULL, 1, NULL, NULL, NULL, NULL, '2025-11-10 12:19:15', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `vacinas`
--

CREATE TABLE `vacinas` (
  `id` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `tipo_vacina` varchar(100) NOT NULL,
  `data_aplicacao` date DEFAULT NULL,
  `proxima_dose` date DEFAULT NULL,
  `veterinario` varchar(100) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_animal_admin` (`id_admin_cadastro`),
  ADD KEY `fk_animal_solicitante` (`id_adotante`);

--
-- Índices para tabela `solicitacaoadoção`
--
ALTER TABLE `solicitacaoadoção`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_solicitacao_animal` (`id_animal`),
  ADD KEY `fk_solicitacao_solicitante` (`id_solicitante`),
  ADD KEY `fk_solicitacao_admin` (`id_admin_avaliador`);

--
-- Índices para tabela `solicitante`
--
ALTER TABLE `solicitante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `vacinas`
--
ALTER TABLE `vacinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vacinas_animal` (`id_animal`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `animal`
--
ALTER TABLE `animal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `solicitacaoadoção`
--
ALTER TABLE `solicitacaoadoção`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `solicitante`
--
ALTER TABLE `solicitante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `vacinas`
--
ALTER TABLE `vacinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `fk_animal_admin` FOREIGN KEY (`id_admin_cadastro`) REFERENCES `administrador` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_animal_solicitante` FOREIGN KEY (`id_adotante`) REFERENCES `solicitante` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `solicitacaoadoção`
--
ALTER TABLE `solicitacaoadoção`
  ADD CONSTRAINT `fk_solicitacao_admin` FOREIGN KEY (`id_admin_avaliador`) REFERENCES `administrador` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_solicitacao_animal` FOREIGN KEY (`id_animal`) REFERENCES `animal` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_solicitacao_solicitante` FOREIGN KEY (`id_solicitante`) REFERENCES `solicitante` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `vacinas`
--
ALTER TABLE `vacinas`
  ADD CONSTRAINT `fk_vacinas_animal` FOREIGN KEY (`id_animal`) REFERENCES `animal` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
