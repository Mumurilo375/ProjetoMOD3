<?php

namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use DateTime;

#[Entity]
class User
{

    #[Id, Column(name: "id"), GeneratedValue]
    private int $id;

    // Nível de acesso: 'user' (padrão) ou 'admin'
    #[Column(name: "nivelAcesso", options: ["default" => "user"])]
    private string $nivelAcesso;

    #[Column]
    private string $nome;

    #[Column (unique: true)]
    private string $email;

    #[Column]
    private string $senha;  // Guardará o hash da senha

    #[Column(type: "datetime")]
    private DateTime $dataCadastro;

    // Caminho relativo da foto de perfil (ex: /ProjetoMOD3-limpo/public/img/fotoPerfil/abc123.jpg)
    #[Column(nullable: true)]
    private ?string $fotoPerfil = null;

    


    public function __construct(string $nome, string $email, string $senhaPura)
    {
        $this->nome = $nome;
        $this->email = $email;

        $this->setSenha($senhaPura); // chama metodo cria hash

        $this->dataCadastro = new DateTime();

        $this->nivelAcesso = 'user';
    }

    public function setSenha(string $senhaPura): void
    {
        $this->senha = password_hash($senhaPura, PASSWORD_DEFAULT);
    }

     public function verificaSenha(string $senhaPura): bool
    {
        return password_verify($senhaPura, $this->senha);
    }

    public function getId(): int {return $this->id;}

	public function getNome(): string {return $this->nome;}

	public function getEmail(): string {return $this->email;}

	public function getSenha(): string {return $this->senha;}

	public function getDataCadastro(): DateTime {return $this->dataCadastro;}

    public function setNome(string $nome): void {
        $nome = trim($nome);
        if ($nome === '') {
            throw new \InvalidArgumentException('Nome não pode ser vazio');
        }
        $this->nome = $nome;
    }

    public function getFotoPerfil(): ?string { return $this->fotoPerfil; }
    public function setFotoPerfil(?string $path): void { $this->fotoPerfil = $path; }

    // padrão brasileiro data
    public function getDataCadastroBR(): string {
        return $this->dataCadastro->format('d-m-Y');
    }
    public function getDataCadastroBRComHora(): string {
        return $this->dataCadastro->format('d-m-Y H:i');
    }

    public function getNivelAcesso(): string { return $this->nivelAcesso; }
    public function isAdmin(): bool { return strtolower($this->nivelAcesso) === 'admin'; }

    public function save(): void
    {
        $entityManager = Database::getEntityManager();
        $entityManager->persist($this);
        $entityManager->flush();
    }

}
