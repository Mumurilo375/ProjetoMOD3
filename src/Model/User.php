<?php

namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

// Classe User: representa um usuário do sistema.
// Contém propriedades (nome, email, senha, etc.), métodos para salvar
// e buscar usuários, e factories para criação padrão/admin.
#[Entity]
class User
{
    #[Id, Column(name: "id"), GeneratedValue]
    private int $id;

    // Nome do usuário mapeado para a coluna `name` no banco
    #[Column(name: "name")]
    private string $nome;

    // Campos não obrigatórios no banco (opcionais no formulário)
    #[Column(name: "sobrenome")]
    private string $sobrenome;
    #[Column(name: "sexo")]
    private string $sexo;
    #[Column(name: "nivel_de_acesso")]
    private string $nivelDeAcesso;
    #[Column(name: "dataDeCadastro")]
    private string $dataDeCadastro;

    // Email e senha mapeados para colunas existentes
    #[Column(name: "email")]
    private string $email;

    #[Column(name: "password")]
    private string $password;

    /**
     * Construtor do usuário.
     * Garante que o nível padrão seja 'user' se o valor recebido for vazio.
     */
    public function __construct(string $nome, string $sobrenome, string $sexo, string $nivelDeAcesso, string $email, string $dataDeCadastro, string $password)
    {
        $this->nome = $nome;
        $this->sobrenome = $sobrenome;
        $this->sexo = $sexo;
        $this->nivelDeAcesso = $nivelDeAcesso ?: 'user';
        $this->email = $email;
        $this->dataDeCadastro = $dataDeCadastro;
        $this->password = $password;
    }

    // Getters básicos
    public function getId(): int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getName(): string { return $this->nome; } // compatibilidade
    public function getSobrenome(): string { return $this->sobrenome; }
    public function getSexo(): string { return $this->sexo; }
    public function getNivelDeAcesso(): string { return $this->nivelDeAcesso; }
    public function getEmail(): string { return $this->email; }
    public function getDataDeCadastro(): string { return $this->dataDeCadastro; }
    public function getPassword(): string { return $this->password; }

    // Persiste a entidade no banco (Doctrine EntityManager)
    public function save(): void
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    // Retorna todos os usuários (uso do repository do Doctrine)
    public static function findAll(): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(User::class);
        return $repository->findAll();
    }

    // Valida a senha comparando sha256 (demo)
    public function validatePassword(string $password): bool
    {
        return $this->password == hash('sha256', $password);
    }

    // Factory para criar usuário padrão (usado no signup)
    public static function createFromSignup(string $nome, string $email, string $plainPassword): self
    {
        $sobrenome = '';
        $sexo = 'ND';
        $nivelDeAcesso = 'user';
        $dataDeCadastro = date('Y-m-d H:i:s');
        $hashed = hash('sha256', $plainPassword);

        return new self($nome, $sobrenome, $sexo, $nivelDeAcesso, $email, $dataDeCadastro, $hashed);
    }

    // Factory para criar administradores explicitamente
    public static function createAdmin(string $nome, string $email, string $plainPassword, string $sobrenome = '', string $sexo = 'ND'): self
    {
        $nivelDeAcesso = 'admin';
        $dataDeCadastro = date('Y-m-d H:i:s');
        $hashed = hash('sha256', $plainPassword);

        return new self($nome, $sobrenome, $sexo, $nivelDeAcesso, $email, $dataDeCadastro, $hashed);
    }
}
