<?php

namespace App\Model;

use App\Core\Database;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class Avaliacao
{
    #[Id, Column(type: "integer"), GeneratedValue]
    private int $id;

    #[Column(type: "integer")]
    private int $nota; // Ex: Uma nota de 1 a 5

    // Comentários podem ser opcionais, então permitimos que seja nulo (nullable: true)
    #[Column(type: "text", nullable: true)]
    private ?string $comentario;

    #[Column(type: "datetime")]
    private DateTime $dataAvaliacao;

    
    
    // --- FOREIGN KEYS ---

    
    
    /* Muitas avaliações pertencem a Um user. */
    #[ManyToOne(targetEntity: User::class, inversedBy: "avaliacoes")]
    #[JoinColumn(name: "usuario_id", referencedColumnName: "id")]
    private User $usuario;

    /* Muitas avaliações pertencem a Um filme. */
    #[ManyToOne(targetEntity: Filme::class, inversedBy: "avaliacoes")]
    #[JoinColumn(name: "filme_id", referencedColumnName: "id")]
    private Filme $filme;

    
    
    
    /**
     * O construtor exige os dados mínimos para uma avaliação existir:
     * o usuário que avaliou, o filme que foi avaliado e a nota.
     * O comentário é opcional.
     */
    public function __construct(User $usuario, Filme $filme, int $nota, ?string $comentario = null)
    {
        $this->usuario = $usuario;
        $this->filme = $filme;
        $this->nota = $nota;
        $this->comentario = $comentario;
        $this->dataAvaliacao = new DateTime();
    }

    // --- Getters ---
    public function getId(): int { return $this->id; }
    public function getNota(): int { return $this->nota; }
    public function getComentario(): ?string { return $this->comentario; }
    public function getDataAvaliacao(): DateTime { return $this->dataAvaliacao; }
    public function getUsuario(): User { return $this->usuario; }
    public function getFilme(): Filme { return $this->filme; }

    // Persiste a entidade no banco usando o EntityManager do Doctrine
    public function save(): void
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function findAll(): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(self::class);
        return $repository->findAll();
    }








    /**
     * Exemplo de um "findBy" mais específico, muito útil para esta classe.
     * Busca todas as avaliações de um filme específico.
     * @param int $filmeId O ID do filme que você quer buscar as avaliações.
     * @return array Retorna um array de objetos Avaliacao.
     */
    public static function findByFilmeId(int $filmeId): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(self::class);
        // O método findBy do repositório permite buscar por qualquer critério
        return $repository->findBy(['filme' => $filmeId]);
    }
    // Como usar: Quando um usuário estiver na página do filme "Interestelar" (que tem, digamos, o ID 15), você pode chamar Avaliacao::findByFilmeId(15); para pegar só as avaliações daquele filme e exibi-las na página.
}