<?php

namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;



// alt shift f para formatar o codigo
#[Entity]
class Filme
{

    #[Column, Id, GeneratedValue]
    private int $id;
    #[Column]
    private string $titulo;
    #[Column]
    private string $sinopse;
    #[Column]
    private int $ano;
    //falta q criar o caminho do poster


    public function __construct(string $titulo, string $sinopse, int $ano)
    {
        $this->titulo = $titulo;
        $this->sinopse = $sinopse;
        $this->ano = $ano;
    }

    public function getId(): int {return $this->id;}

	public function getTitulo(): string {return $this->titulo;}

	public function getSinopse(): string {return $this->sinopse;}

	public function getAno(): int {return $this->ano;}

    public function save(): void //essa função salva o 11filme no banco de dados
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function findAll(): array //essa função busca todos os filmes no banco de dados, retornando um array de objetos Filme.
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(Filme::class); //essa linha serve para obter o repositório da entidade Filme
        return $repository->findAll(); //retorna todos os filmes encontrados, o findAll serve para buscar todos os registros
    }

	

}
