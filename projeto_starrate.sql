-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/09/2025 às 01:31
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
-- Banco de dados: `projeto_starrate`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacao`
--

CREATE TABLE `avaliacao` (
  `id` int(11) NOT NULL,
  `nota` int(11) NOT NULL,
  `comentario` longtext DEFAULT NULL,
  `dataAvaliacao` datetime NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `filme_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacao`
--

INSERT INTO `avaliacao` (`id`, `nota`, `comentario`, `dataAvaliacao`, `usuario_id`, `filme_id`) VALUES
(1, 97, 'Sensacional, contêm um incrível plot-twist!, um dos melhores filmes que já assisti.', '2025-09-05 01:39:18', 2, 4),
(2, 98, 'Tive a oportunidade de assistir o filme sem spoilers. O filme passa uma forte crítica social, contém uma atuação grandíssima do ator Brad Pitt e finaliza com um baita plot-twist, uma das primeiras coisas que eu faria caso perdesse a memória seria reassistir este filme.', '2025-09-05 01:43:47', 2, 5),
(3, 100, 'Simplesmente o melhor filme que já assisti! O filme tem 02:49h, mas vale cada segundo. História perfeita, e uma trilha sonora absurda', '2025-09-05 01:48:54', 2, 2),
(4, 87, 'Filme passa uma vibe boa, recomendo assistir de madrugada.', '2025-09-05 03:40:05', 1, 14),
(5, 95, 'Muito bom, trevoso.', '2025-09-05 14:32:35', 3, 13),
(6, 78, 'Divertido, a história está bem encaixada, porém os personagens poderiam ser melhor, e o final foi decepcionante.', '2025-09-06 00:22:43', 1, 10),
(7, 89, NULL, '2025-09-06 14:41:49', 1, 22),
(8, 96, 'Excelente.', '2025-09-06 14:52:23', 1, 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `filme`
--

CREATE TABLE `filme` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `sinopse` longtext NOT NULL,
  `anoLancamento` int(11) NOT NULL,
  `diretor` varchar(255) NOT NULL,
  `genero` varchar(255) NOT NULL,
  `capa` varchar(255) NOT NULL,
  `trailer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `filme`
--

INSERT INTO `filme` (`id`, `titulo`, `sinopse`, `anoLancamento`, `diretor`, `genero`, `capa`, `trailer`) VALUES
(1, 'Donnie Darko', 'Donnie é um jovem excêntrico que despreza a grande maioria de seus colegas de escola. Ele tem visões, em especial de Frank, um coelho gigante que só ele consegue ver e que o encoraja a fazer brincadeiras humilhantes com quem o cerca. Um dia, uma de suas visões o atrai para fora de casa e lhe diz que o mundo acabará dentro de um mês. Donnie inicialmente não acredita, mas, momentos depois, a turbina de um avião cai em sua casa e ele começa a se perguntar qual é o fundo de verdade dessa previsão.', 2001, 'Richard Kelly', 'Terror', 'img/capas/donnieDarko.jpg', 'https://youtu.be/bzLn8sYeM9o?si=LiTYx6HGp-sjfhhf'),
(2, 'Interestelar', 'As reservas naturais da Terra estão chegando ao fim e um grupo de astronautas recebe a missão de verificar possíveis planetas para receberem a população mundial, possibilitando a continuação da espécie. Cooper é chamado para liderar o grupo e aceita a missão sabendo que pode nunca mais ver os filhos. Ao lado de Brand, Jenkins e Doyle, ele seguirá em busca de um novo lar.', 2014, 'Christopher Nolan', 'Ação', 'img/capas/capa_68b8fe7924c160.04682496.png', 'https://youtu.be/i6avfCqKcQo'),
(3, 'A Freira', 'Presa em um convento na Romênia, uma freira comete suicídio. Para investigar o caso, o Vaticano envia um padre assombrado e uma noviça prestes a se tornar freira. Arriscando suas vidas, a fé e até suas almas, os dois descobrem um segredo profano e se confrontam com uma força do mal que toma a forma de uma freira demoníaca e transforma o convento em um campo de batalha.', 2018, 'Corin Hardy', 'Terror', 'img/capas/capa_68b910522de3e4.09327178.jpg', 'https://youtu.be/4V44ew-laC4?si=0sNYDTWvbiKf4VbM'),
(4, 'Sev7n - Os Sete Crimes Capitais', 'A ponto de se aposentar, o detetive William Somerset pega um último caso, com a ajuda do recém-transferido David Mills. Juntos, descobrem uma série de assassinatos e logo percebem que estão lidando com um assassino que tem como alvo pessoas que ele acredita representar os sete pecados capitais.', 1995, 'David Fincher', 'Suspense', 'img/capas/capa_68b9151d24a2c6.67651081.jpg', 'https://youtu.be/KPOuJGkpblk?si=qylWBR9JqCtoVVNW'),
(5, 'Clube da Luta', 'Um homem deprimido que sofre de insônia conhece um estranho vendedor chamado Tyler Durden e se vê morando em uma casa suja depois que seu perfeito apartamento é destruído. A dupla forma um clube com regras rígidas onde homens lutam. A parceria perfeita é comprometida quando uma mulher, Marla, atrai a atenção de Tyler.', 2000, 'David Fincher', 'Ação', 'img/capas/capa_68b92130983b11.92059702.jpg', 'https://youtu.be/O1nDozs-LxI'),
(6, 'Blade Runner 2049', 'Após descobrir um segredo que ameaça o que resta da sociedade, um novo policial parte em busca de Rick Deckard, desaparecido há 30 anos.', 2017, 'Denis Villeneuve', 'Ficção Científica', 'img/capas/capa_68b9239e464264.17233073.png', 'https://youtu.be/xGwe7D0RKWc?si=z7HeVdlN6dRs9OuR'),
(7, 'Até o Último Homem', 'Acompanhe a história de Desmond T. Doss, um médico do exército americano que, durante a Segunda Guerra Mundial, se recusa a pegar em armas. Durante a Batalha de Okinawa ele trabalha na ala médica e salva cerca de 75 homens.', 2016, 'Mel Gibson', 'Guerra', 'img/capas/capa_68b924cb6813e0.77243236.png', 'https://youtu.be/4s4UCxCv_OE?si=1elt5lC0Ty-EYYCS'),
(8, 'F1', 'Na década de 1990, Sonny Hayes era o piloto mais promissor da Fórmula 1 até que um acidente na pista quase encerrou sua carreira. Trinta anos depois, o proprietário de uma equipe de Fórmula 1 em dificuldades convence Sonny a voltar a correr e se tornar o melhor do mundo.', 2025, 'Joseph Kosinski', 'Ação', 'img/capas/capa_68b9266103a900.63955862.jpg', 'https://youtu.be/ZiDphkXCZsQ?si=otyDnYSosh4MYfTN'),
(9, 'Whiplash: Em Busca da Perfeição', 'Andrew sonha em ser o melhor baterista de sua geração. Ele chama a atenção do impiedoso mestre do jazz Terence Fletcher, que ultrapassa os limites e transforma seu sonho em uma obsessão, colocando em risco a saúde física e mental do jovem músico.', 2015, 'Damien Chazelle', 'Drama', 'img/capas/capa_68b928b3d00ed5.06954963.webp', 'https://youtu.be/7d_jQycdQGo?si=DxqshWit8uWVmnCa'),
(10, 'Premonição 6: Laços de Sangue', 'Atormentada por um pesadelo violento e recorrente, uma estudante universitária volta para casa em busca da única pessoa que pode ser capaz de quebrar o ciclo de morte e salvar sua família do terrível destino que inevitavelmente os aguarda.', 2025, 'Zach Lipovsky', 'Suspense', 'img/capas/capa_68b929b1c09f01.63809382.jpg', 'https://youtu.be/TTwbsctteuw?si=RcJBWI9rVqtL8C_7'),
(11, 'El Camino: A Breaking Bad Film', 'Assombrado pelo passado, o fugitivo Jesse Pinkman tenta encontrar um lugar seguro para viver.', 2019, 'Vince Gilligan', 'Ação', 'img/capas/capa_68b92a64530120.28921760.jpg', 'https://youtu.be/2ir8eLkz2tQ?si=n_U4ouC4J-0YFvXp'),
(12, 'Duna', 'Paul Atreides é um jovem brilhante, dono de um destino além de sua compreensão. Ele deve viajar para o planeta mais perigoso do universo para garantir o futuro de seu povo.', 2021, 'Denis Villeneuve', 'Ficção Científica', 'img/capas/capa_68b92b276e17b0.76430560.jpg', 'https://youtu.be/hfLkFZWFmLM?si=_VRv3VGja6gWTsS1'),
(13, 'Batman: O Cavaleiro das Trevas', 'Batman tem conseguido manter a ordem em Gotham com a ajuda de Jim Gordon e Harvey Dent. No entanto, um jovem e anárquico criminoso, conhecido apenas como Coringa, pretende testar o Cavaleiro das Trevas e mergulhar a cidade em um verdadeiro caos.', 2008, 'Christopher Nolan', 'Ficção Científica', 'img/capas/capa_68ba6d358e2f89.15235847.jpg', 'https://youtu.be/zqfz04yCTnE?si=ay0x2NhFh3b9uCEu'),
(14, 'Taxi Driver', 'O motorista de táxi de Nova York Travis Bickle, veterano da Guerra do Vietnã, reflete constantemente sobre a corrupção da vida ao seu redor e sente-se cada vez mais perturbado com a própria solidão e alienação. Apesar de não conseguir fazer contato emocional com ninguém e viver uma vida questionável em busca de diversão, ele se torna obcecado em ajudar uma prostituta de 12 anos que entra em seu táxi para fugir de um cafetão.', 1976, 'Martin Scorsese', 'Drama', 'img/capas/capa_68ba85515d09b7.14523179.jpg', 'https://youtu.be/zdqCqDSTVNI?si=ei9AJlWuauciwyER'),
(15, 'Thunderbolts', 'Presos em uma armadilha mortal, uma equipe nada convencional de anti-heróis embarca em uma missão perigosa que os força a confrontar os cantos mais sombrios de suas vidas.', 2025, 'Jake Schreier', 'Ficção Científica', 'img/capas/capa_68ba8650c060b6.33053699.jpg', 'https://youtu.be/MaLy0D2FTDc?si=IdPhELBSWNAnkN2e'),
(16, 'Um Sonho de Liberdade', 'Em 1947, o jovem banqueiro Andy Dufresne é condenado à prisão perpétua pelo assassinato de sua esposa e do amante dela. No entanto, apenas Andy sabe que não cometeu os crimes. Encarcerado em Shawshank, a penitenciária mais rigorosa do estado do Maine, ele faz amizade com Ellis Boyd \"Red\" Redding, um homem desiludido que está preso há 20 anos.', 1994, 'Frank Darabont', 'Suspense', 'img/capas/capa_68bba2bf34e599.70136745.png', 'https://youtu.be/PLl99DlL6b4'),
(17, 'O Poderoso Chefão', 'Uma família mafiosa luta para estabelecer sua supremacia nos Estados Unidos depois da Segunda Guerra Mundial. Uma tentativa de assassinato deixa o chefão Vito Corleone incapacitado e força os filhos Michael e Sonny a assumir os negócios.', 1972, 'Francis Ford Coppola', 'Guerra', 'img/capas/capa_68bba3772be5d8.95922217.webp', 'https://youtu.be/UaVTIH8mujA'),
(18, 'GoodFellas', 'Na década de 1950 no bairro do Brooklyn, Nova York, o jovem Henry Hill tem a chance de realizar seu sonho de se tornar um gângster quando um mafioso local o recruta para sua gangue. Henry aproveita a oportunidade e ainda conhece James \"Jimmy\" Conway e Tommy DeVito, dois criminosos destemidos e brutais que fazem serviços para a máfia. Impressionado, Henry se junta à dupla para explorar o lucrativo mercado do tráfico de drogas.', 1990, 'Martin Scorsese', 'Guerra', 'img/capas/capa_68bba3f51a5536.14432104.jpg', 'https://youtu.be/qo5jJpHtI1Y'),
(19, 'Parasita', 'Toda a família de Ki-taek está desempregada, vivendo em um porão sujo e apertado. Por obra do acaso, ele começa a dar aulas de inglês para uma garota de família rica. Fascinados com a vida luxuosa destas pessoas, pai, mãe e filhos bolam um plano para se infiltrar também na abastada família, um a um. No entanto, os segredos e mentiras necessários à ascensão social cobram o seu preço.', 2019, 'Bong Joon-ho', 'Terror', 'img/capas/capa_68bba45fb50553.48105359.jpg', 'https://youtu.be/5xH0HfJHsaY'),
(20, 'Superman', 'Superman embarca em uma jornada para reconciliar sua herança kryptoniana com sua criação humana.', 2025, 'James Gunn', 'Ficção Científica', 'img/capas/capa_68bba4e24c76f7.87669998.webp', 'https://youtu.be/6HsfXtgcAE4'),
(21, 'Together', 'Tim e Millie se encontram em uma encruzilhada quando se mudam para o interior. Com as tensões já à flor da pele, um encontro aterrorizante com uma força misteriosa e antinatural ameaça corromper suas vidas, seu amor e sua carne.', 2025, 'Michael Shanks', 'Terror', 'img/capas/capa_68bba5792c7945.53719048.webp', 'https://youtu.be/9DlM58879hs'),
(22, 'Click', 'Um arquiteto casado e com filhos está cada vez mais frustrado por passar a maior parte de seu tempo trabalhando. Um dia, ele encontra um inventor excêntrico que lhe dá um controle remoto universal, com capacidade de acelerar o tempo. No início, ele usa o aparelho para acelerar qualquer momento tedioso, mas se dá conta de que está acelerando o tempo demais e deixando de viver preciosos momentos em família. Desesperado, ele procura o inventor para ajudá-lo a reverter o que fez.', 2006, 'Frank Coraci', 'Comédia', 'img/capas/capa_68bc7206d85c23.22482830.jpg', 'https://youtu.be/3-VfwPpbNg4?si=vux3urS0soPdJRVV'),
(23, 'O poço', 'Em uma prisão onde os detentos são alimentados por uma plataforma descendente, os que estão nos níveis mais altos comem mais do que precisam enquanto os dos andares mais baixos ficam com as migalhas. Até que um homem decide mudar o sistema.', 2019, 'Galder Gaztelu-Urrutia', 'Suspense', 'img/capas/capa_68bc747ee071b0.45651452.webp', 'https://youtu.be/_iEHOuPsRsY?si=_001unrb0N7mw7xB'),
(24, 'A Origem', 'Dom Cobb é um ladrão com a rara habilidade de roubar segredos do inconsciente, obtidos durante o estado de sono. Impedido de retornar para sua família, ele recebe a oportunidade de se redimir ao realizar uma tarefa aparentemente impossível: plantar uma ideia na mente do herdeiro de um império. Para realizar o crime perfeito, ele conta com a ajuda do parceiro Arthur, o discreto Eames e a arquiteta de sonhos Ariadne. Juntos, eles correm para que o inimigo não antecipe seus passos.', 2010, 'Christopher Nolan', 'Ficção Científica', 'img/capas/capa_68bc7627084506.24535779.jpg', 'https://youtu.be/R_VX0e0PX90?si=h23WLr9nu2Kj92zj'),
(25, 'Ilha do Medo', 'Shutter Island é um filme de suspense psicológico neo-noir estadunidense de 2010, dirigido por Martin Scorsese e escrito por Laeta Kalogridis, sendo uma adaptação cinematográfica do romance de 2003 de Dennis Lehane com o mesmo nome.', 2010, 'Martin Scorsese', 'Suspense', 'img/capas/capa_68bc7f7bbdd399.14159784.webp', 'https://youtu.be/YDGldPitxic');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `dataCadastro` datetime NOT NULL,
  `nivelAcesso` varchar(255) NOT NULL DEFAULT 'user',
  `fotoPerfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `user`
--

INSERT INTO `user` (`id`, `nome`, `email`, `senha`, `dataCadastro`, `nivelAcesso`, `fotoPerfil`) VALUES
(1, 'Admin', 'admin123@gmail.com', '$2y$10$xvQ3nCMAIWEiBnOtqNkwVu8rP/o6oGaD4z1D6zK02ja561I8DOT6W', '2025-09-05 00:07:27', 'admin', '/ProjetoMOD3-limpo/public/img/fotoPerfil/perfil_admin_id-1.png'),
(2, 'murilop375', 'murilopm44@gmail.com', '$2y$10$T71OsvMRBCWhJxEKgG4Z9uUM..csWH.WCgcCD9zIZXGd1BT0..EXa', '2025-09-05 00:23:36', 'user', '/ProjetoMOD3-limpo/public/img/fotoPerfil/perfil_ab29d4ad8b421abc.png'),
(3, 'luizao', 'luiz.otavio3213@gmail.com', '$2y$10$AaYmPBwniY9Ux6.fAuSr9O9n1bfGWWaFFCOw2DB/wSl0keCPjFr1q', '2025-09-05 13:37:08', 'user', '/ProjetoMOD3-limpo/public/img/fotoPerfil/perfil_luizao_id-3.jpg'),
(4, 'eduarda', 'eduardalara2007@gmail.com', '$2y$10$qT4uwTmTHAg41hqyz.jsU.VTyPRXtqx5EQRAIrLyIS9V4PC3wS69K', '2025-09-08 20:25:49', 'user', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_EBEAC581DB38439E` (`usuario_id`),
  ADD KEY `IDX_EBEAC581E6E418AD` (`filme_id`);

--
-- Índices de tabela `filme`
--
ALTER TABLE `filme`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_2DA17977E7927C74` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `filme`
--
ALTER TABLE `filme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD CONSTRAINT `FK_EBEAC581DB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_EBEAC581E6E418AD` FOREIGN KEY (`filme_id`) REFERENCES `filme` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
