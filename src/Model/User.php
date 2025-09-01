<?php

namespace App\Model; //definindo o namespace para a classe User, o namespace é usado para organizar o código e evitar conflitos de nomes entre classes, o App\Model é o namespace do projeto

use App\Core\Database;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class User
{
    #[Column, Id, GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column]
    private string $email;

    #[Column]
    private string $password;

    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = hash('sha256', $password);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function save(): void //essa função salva o usuário no banco de dados
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function findAll(): array //essa função busca todos os usuários no banco de dados, retornando um array de objetos User. essa função veio do public/avaliacoes.php 
    {
        $em = Database::getEntityManager();//essa linha serve para obter o EntityManager do Doctrine, que basicamente seria o responsável por gerenciar as entidades e suas operações no banco de dados
        $repository = $em->getRepository(User::class);
        return $repository->findAll();
    }

    public function validatePassword(string $password): bool //essa função valida a senha do usuário, retornando true se a senha estiver correta e false caso contrário
    {
        return $this->password == hash('sha256', $password); // compara a senha armazenada com o hash da senha fornecida
    }
}
