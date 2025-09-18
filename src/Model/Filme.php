<?php

namespace App\Model;

use App\Core\Database; 
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class Filme
{
    #[Id, Column(type: "integer"), GeneratedValue]
    private int $id;

    #[Column]
    private string $titulo;

    #[Column(type: "text")]
    private string $sinopse;

    #[Column]
    private int $anoLancamento;

    #[Column]
    private string $diretor;

    #[Column]
    private string $genero;

    #[Column]
    private string $capa;

    // Link opcional trailer
    #[Column(nullable: true)]
    private ?string $trailer = null;


    public function __construct(string $titulo, string $sinopse, int $anoLancamento, string $diretor, string $genero, string $capa, ?string $trailer = null)
    {
        $this->titulo = $titulo;
        $this->sinopse = $sinopse;
        $this->anoLancamento = $anoLancamento;
        $this->diretor = $diretor;
        $this->genero = $genero;
        $this->capa = $capa;
        $this->trailer = $trailer ? trim($trailer) : null;
    }

    public function getId(): int {return $this->id;}

	public function getTitulo(): string {return $this->titulo;}

	public function getSinopse(): string {return $this->sinopse;}

	public function getAnoLancamento(): int {return $this->anoLancamento;}

	public function getDiretor(): string {return $this->diretor;}

	public function getGenero(): string {return $this->genero;}

	public function getCapa(): string {return $this->capa;}

    public function getTrailer(): ?string { return $this->trailer; }
    public function setTrailer(?string $url): void {
        $url = $url !== null ? trim($url) : null;
        $this->trailer = $url ?: null;
    }

    // Persiste a entidade no banco usando o EntityManager do Doctrine
    public function save(): void
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    //busca todos os filmes no banco retornando um array de objetos Filme
    public static function findAll(): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(Filme::class);
        return $repository->findAll();
    }


}
