-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/04/2026 às 22:34
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `portal_saude_bem_estar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `noticia` longtext NOT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `autor` int(11) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `noticia`, `data`, `autor`, `imagem`) VALUES
(9, 'SUS amplia serviços e aposta em atendimento digital e online', 'O Sistema Único de Saúde está passando por uma modernização significativa em 2026. Entre as novidades, destaca-se a ampliação do atendimento digital, permitindo que pacientes tenham acesso a consultas e orientações médicas sem sair de casa.\r\n\r\nO teleatendimento tem sido especialmente útil na área de saúde mental, oferecendo suporte psicológico de forma mais rápida e acessível. Além disso, novos investimentos estão sendo feitos na construção de unidades de saúde e na aquisição de equipamentos modernos.\r\n\r\nEssas mudanças visam reduzir filas, melhorar o atendimento e tornar o sistema mais eficiente, beneficiando milhões de brasileiros.', '2026-03-30 15:45:36', 6, 'imagens/69cd7f7d775e7.png'),
(10, 'Pequenos hábitos podem transformar sua saúde em 2026', 'Especialistas em saúde destacam que pequenas mudanças na rotina podem trazer grandes benefícios para o bem-estar. Dormir bem, manter uma alimentação equilibrada e praticar atividades físicas regularmente são atitudes simples, mas essenciais.\r\n\r\nEstudos indicam que a falta de sono e o sedentarismo estão entre os principais fatores de risco para diversas doenças. Por outro lado, pessoas que adotam hábitos saudáveis apresentam melhor disposição, menos estresse e maior qualidade de vida.\r\n\r\nA tendência para 2026 é valorizar o equilíbrio e a constância, mostrando que não é necessário fazer mudanças radicais para viver melhor.', '2026-03-30 15:51:46', 6, 'imagens/69cac6427826e.png'),
(11, 'Restrição de sono pode afetar hormônios e desempenho', 'Em uma live recente, Paulo Muzy destacou os impactos negativos da falta de sono no corpo humano. Segundo ele, dormir pouco não afeta apenas o cansaço, mas também prejudica diretamente a produção hormonal, incluindo testosterona e hormônio do crescimento.\r\n\r\nEle explicou que a privação de sono pode reduzir o desempenho físico, dificultar o ganho de massa muscular e aumentar o acúmulo de gordura corporal. Além disso, o problema também interfere na concentração e no humor.\r\n\r\nMuzy reforça que dormir bem deve ser tratado como prioridade, assim como treino e alimentação, sendo essencial para quem busca saúde e performance.', '2026-03-30 16:41:11', 7, 'imagens/69cad1d769b6f.png'),
(12, 'Treinamento de força é essencial até na terceira idade', 'De acordo com conteúdos recentes publicados por Paulo Muzy, o treinamento de força não é apenas para jovens, mas também fundamental para idosos. Estudos mostram que exercícios com peso ajudam a aumentar a força, a massa muscular e a funcionalidade no dia a dia.\r\n\r\nEle explica que o envelhecimento naturalmente causa perda muscular, mas isso pode ser reduzido com treino adequado. Além disso, a prática melhora o equilíbrio, reduz o risco de quedas e aumenta a independência dos idosos.\r\n\r\nA recomendação é que o treino seja feito com acompanhamento profissional, respeitando os limites de cada pessoa.', '2026-03-30 16:49:43', 7, 'imagens/69cad3d7acb93.png'),
(14, 'Saúde mental se torna prioridade entre brasileiros em 2026', 'Nos últimos anos, a saúde mental ganhou destaque no Brasil, e em 2026 isso se tornou ainda mais evidente. Pesquisas recentes mostram que a maioria da população está mais preocupada com o bem-estar emocional, buscando formas de lidar com o estresse, ansiedade e pressão do dia a dia.\r\n\r\nEspecialistas apontam que o aumento do uso das redes sociais, a rotina acelerada e as mudanças no mundo do trabalho contribuíram para esse cenário. Como resultado, campanhas de conscientização e serviços de apoio psicológico vêm crescendo, incentivando o autocuidado e a busca por ajuda profissional.\r\n\r\nAlém disso, práticas como meditação, exercícios físicos e pausas na rotina têm sido recomendadas para melhorar a qualidade de vida. A tendência mostra que cuidar da mente passou a ser tão importante quanto cuidar do corpo.', '2026-04-01 16:43:22', 6, 'imagens/69cd755ae5620.png'),
(17, 'Tecnologia no SUS reduz tempo de diagnóstico de doenças raras', 'Uma nova tecnologia implementada no Sistema Único de Saúde (SUS) promete revolucionar o diagnóstico de doenças raras no Brasil. Com o uso de sequenciamento completo de DNA, o tempo de identificação dessas doenças pode cair de até 7 anos para cerca de 6 meses.\r\n\r\nA expectativa é que o sistema consiga atender até 20 mil pacientes por ano, trazendo mais rapidez e precisão nos resultados. Isso permite iniciar tratamentos mais cedo, aumentando as chances de sucesso e melhorando a qualidade de vida dos pacientes.', '2026-04-01 17:23:27', 6, 'imagens/69cd7fada5102.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `ativo` tinyint(1) DEFAULT 1,
  `adm` tinyint(1) NOT NULL DEFAULT 0,
  `aprovado` tinyint(1) NOT NULL DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `data_criacao`, `ativo`, `adm`, `aprovado`, `foto`) VALUES
(6, 'Gabriel Bandasz', 'gabrielprade15@gmail.com', '$2y$10$bkBc9PCYcNRPgVy5Nefea.kkCaQPUZRFoQX350qoSUCTYNGyrYHt6', '2026-03-30 14:58:47', 1, 1, 1, 'imagens/69cad866353fa.png'),
(7, 'Paulo Muzy', 'paulomuzy10@gmail.com', '$2y$10$0VuOKLEEWY3MluIyyv9wPudCU2lxT0SQ2/N9Lsf2eIXTRb2cjblxS', '2026-03-30 16:37:50', 1, 0, 1, 'imagens/69cadc86e93a2.png');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_noticias_autor` (`autor`),
  ADD KEY `idx_noticias_data` (`data`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuarios_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `noticias_ibfk_1` FOREIGN KEY (`autor`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
