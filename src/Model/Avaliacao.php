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
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[Entity] //nao permite o user avaliar o mesmo filme mais de uma vez
#[Table(name: "Avaliacao", uniqueConstraints: [
    new UniqueConstraint(name: "uniq_usuario_filme", columns: ["usuario_id", "filme_id"])
])]
class Avaliacao
{
    #[Id, Column(type: "integer"), GeneratedValue]
    private int $id;

    #[Column(type: "integer")]
    private int $nota;

    #[Column(type: "text", nullable: true)]
    private ?string $comentario;

    #[Column(type: "datetime")]
    private DateTime $dataAvaliacao;

    
    
        
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: "usuario_id", referencedColumnName: "id")]
    private User $usuario;

    #[ManyToOne(targetEntity: Filme::class)]
    #[JoinColumn(name: "filme_id", referencedColumnName: "id")]
    private Filme $filme;

    
    
    
    public function __construct(User $usuario, Filme $filme, int $nota, ?string $comentario = null)
    {
        $this->usuario = $usuario;
        $this->filme = $filme;
        $this->setNota($nota);
        $this->comentario = $comentario;
        $this->dataAvaliacao = new DateTime();
    }

    public function getId(): int { return $this->id; }
    public function getNota(): int { return $this->nota; }
    public function setNota(int $nota): void {
        
        if ($nota < 0) { $nota = 0; }
        if ($nota > 100) { $nota = 100; }
        $this->nota = $nota;
    }
    public function getComentario(): ?string { return $this->comentario; }
    public function getDataAvaliacao(): DateTime { return $this->dataAvaliacao; }
    public function getUsuario(): User { return $this->usuario; }
    public function getFilme(): Filme { return $this->filme; }

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

    //função da média
    public static function getMediaPorFilmeId(int $filmeId): ?float
    {
        $em = Database::getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('AVG(a.nota) AS media', 'COUNT(a.id) AS total') //calcula media de avaliacoes com o mesmo filmeId
           ->from(self::class, 'a')
           ->where('a.filme = :fid')
           ->setParameter('fid', $filmeId);
        $res = $qb->getQuery()->getSingleResult();
        $total = (int)($res['total'] ?? 0);
        if ($total === 0) return null;
        return (float)$res['media'];
    }

    
    public static function getContagemPorFilmeId(int $filmeId): int
    { //conta as aval
        $em = Database::getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('COUNT(a.id) AS total')
           ->from(self::class, 'a')
           ->where('a.filme = :fid')
           ->setParameter('fid', $filmeId);
        $res = $qb->getQuery()->getSingleScalarResult();
        return (int)$res;
    }








    
    public static function findByFilmeId(int $filmeId): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(self::class);
        return $repository->findBy(['filme' => $filmeId]);
    }

    
    public static function findOneByUserAndFilme(User $usuario, Filme $filme): ?self
    {
        $em = Database::getEntityManager();
        $repo = $em->getRepository(self::class);
        /** @var self|null $avaliacao */
        $avaliacao = $repo->findOneBy(['usuario' => $usuario, 'filme' => $filme]);
        return $avaliacao;
    }
}