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
    // Propriedades mapeadas para o banco de dados
    #[Column, Id, GeneratedValue]
    private int $id;

    #[Column]
    private string $titulo;

    #[Column(type: "string", length: 600)]
    private string $sinopse;

    #[Column]
    private int $ano;

    // novo campo: caminho relativo para a capa (ex: uploads/capas/abc.webp)
    // nullable para não quebrar filmes antigos sem capa
    #[Column(type: "string", length: 255, nullable: true)]
    private ?string $capa = null;


    // Construtor: recebe os valores necessários ao criar um filme
    // $capa é opcional para permitir criar filmes sem imagem
    public function __construct(string $titulo, string $sinopse, int $ano, ?string $capa = null)
    {
        $this->titulo = $titulo;
        $this->sinopse = $sinopse;
        $this->ano = $ano;
        $this->capa = $capa;
    }

    // Getters simples para acessar os valores das propriedades
    public function getId(): int { return $this->id; }
    public function getTitulo(): string { return $this->titulo; }
    public function getSinopse(): string { return $this->sinopse; }
    public function getAno(): int { return $this->ano; }
    public function getCapa(): ?string { return $this->capa; }

    // Persiste a entidade no banco usando o EntityManager do Doctrine
    public function save(): void
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    // Busca todos os filmes no banco (retorna um array de objetos Filme)
    public static function findAll(): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(Filme::class);
        return $repository->findAll();
    }
}
