<?php // Abre o bloco PHP para executar a lógica no servidor

use App\Model\Filme; // Importa a classe Filme do seu projeto (src/Model/Filme.php)

require_once __DIR__ . '/../vendor/autoload.php'; // Carrega o autoload do Composer

$idsDesejados = [6, 7]; // IDs que queremos exibir lado a lado

$lista = Filme::findAll(); // Busca todos os filmes usando o método do projeto

// Vamos coletar os dados dos IDs desejados em um array (em uma única passada)
$dados = []; // Guardará os filmes encontrados, indexados pelo ID
foreach ($lista as $f) { // Percorre a lista retornada do banco
	$id = (int)$f->getId(); // Obtém o ID do filme atual
	if (in_array($id, $idsDesejados, true)) { // Se este ID é um dos que queremos
		// Extrai os campos com fallback para strings vazias
		$dados[$id] = [
			'titulo' => (string)($f->getTitulo() ?? ''), // Título do filme
			'sinopse' => (string)($f->getSinopse() ?? ''), // Sinopse do filme
			'ano' => (string)($f->getAno() ?? ''), // Ano do filme
			'poster' => (string)($f->getPosterCaminho() ?? ''), // Caminho/URL da capa do filme
		]; // Fim do array do filme
		if (count($dados) === count($idsDesejados)) { // Se já encontramos todos os desejados
			break; // Podemos encerrar o loop mais cedo
		} // Fim do if de early-exit
	} // Fim do if de filtro por ID
} // Fim do foreach principal

?> <!-- Fecha o bloco PHP antes de iniciar o HTML de saída -->

<!DOCTYPE html> <!-- Define o tipo de documento HTML -->
<html lang="pt-br"> <!-- Define o idioma da página como português do Brasil -->
<head> <!-- Início do cabeçalho do documento -->
	<meta charset="UTF-8"> <!-- Define a codificação de caracteres como UTF-8 -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade em dispositivos móveis -->
		<title>Visualizar Filmes #6 e #7</title> <!-- Título da aba do navegador indicando ambos os IDs -->
	<style> /* Estilos inline simples para apresentação rápida */
		body { font-family: Arial, sans-serif; background: #111; color: #eee; padding: 24px; } /* Define fonte, cores e espaçamento */
				.cards { display: flex; gap: 16px; flex-wrap: wrap; } /* Container para posicionar os cards lado a lado */
				.card { background: #1b1f24; border: 1px solid #2a2f36; border-radius: 12px; padding: 16px; width: 100%; max-width: 540px; } /* Caixa do conteúdo */
		.cover { max-width: 100%; height: auto; border-radius: 8px; border: 1px solid #2a2f36; } /* Estilo para a imagem de capa */
		.meta { margin: 8px 0; } /* Espaço entre os campos de texto */
		.label { color: #9aa4b2; font-weight: bold; } /* Cor e destaque para as labels */
	</style> <!-- Fim dos estilos inline -->
</head> <!-- Fim do cabeçalho -->
<body> <!-- Início do corpo da página -->
		<div class="cards"> <!-- Container para colocar dois cards lado a lado -->
			<?php foreach ([6, 7] as $idMostrado): // Itera sobre os IDs 6 e 7 para montar cada card ?>
				<div class="card"> <!-- Card de um dos filmes (6 ou 7) -->
					<h1><!-- Título visual do card -->
						<?php if (isset($dados[$idMostrado])): // Se encontramos o filme no array de dados ?>
							<?= htmlspecialchars($dados[$idMostrado]['titulo'], ENT_QUOTES, 'UTF-8') ?> <!-- Título do filme, escapado -->
						<?php else: // Caso não encontrado, informa ?>
							Filme #<?= htmlspecialchars((string)$idMostrado, ENT_QUOTES, 'UTF-8') ?> não encontrado <!-- Mensagem de fallback -->
						<?php endif; ?> <!-- Fim da verificação de existência do filme -->
					</h1> <!-- Fim do título do card -->

					<?php if (isset($dados[$idMostrado])): // Só renderiza meta se houver dados ?>
						<p class="meta"> <!-- Sinopse -->
							<span class="label">Sinopse:</span> <!-- Rótulo -->
							<?= nl2br(htmlspecialchars($dados[$idMostrado]['sinopse'], ENT_QUOTES, 'UTF-8')) ?> <!-- Conteúdo da sinopse -->
						</p> <!-- Fim sinopse -->

						<p class="meta"> <!-- Ano -->
							<span class="label">Ano:</span> <!-- Rótulo -->
							<?= htmlspecialchars($dados[$idMostrado]['ano'], ENT_QUOTES, 'UTF-8') ?> <!-- Conteúdo do ano -->
						</p> <!-- Fim ano -->

						<?php if ($dados[$idMostrado]['poster'] !== ''): // Se há caminho/URL de poster ?>
							<p class="meta"> <!-- Rótulo da capa -->
								<span class="label">Capa:</span>
							</p>
							<img class="cover" src="<?= htmlspecialchars($dados[$idMostrado]['poster'], ENT_QUOTES, 'UTF-8') ?>" alt="Capa do filme <?= htmlspecialchars($dados[$idMostrado]['titulo'], ENT_QUOTES, 'UTF-8') ?>"> <!-- Imagem -->
						<?php else: // Sem poster ?>
							<p class="meta"> <!-- Fallback da capa -->
								<span class="label">Capa:</span>
								(nenhuma imagem cadastrada)
							</p>
						<?php endif; // Fim ver poster ?>
					<?php endif; // Fim ver dados do filme ?>
				</div> <!-- Fim do card -->
			<?php endforeach; // Fim do loop dos dois IDs ?>
		</div> <!-- Fim do container de cards lado a lado -->
</body> <!-- Fim do corpo da página -->
</html> <!-- Fim do documento HTML -->

