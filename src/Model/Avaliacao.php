<?php

namespace App\Model;

use DateTime; //importando a classe DateTime do PHP para manipular datas, que será usada para armazenar a data de criação do post
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class Avaliacao
{
    // id auto-increment
    #[Column, Id, GeneratedValue]
    private int $id;

    // texto da avaliação
    #[Column]
    private string $content;

    // relacionamento ManyToOne: cada avaliação pertence a um User
    #[ManyToOne]
    private User $user;

    // data do post
    #[Column]
    private DateTime $postDate;

    // construtor: recebe o texto, o usuário e a data
    public function __construct(string $content, User $user, DateTime $postDate)
    {
        $this->content = $content;
        $this->user = $user;
        $this->postDate = $postDate;
    }

    // getters simples
    public function getId(): int { return $this->id; }
    public function getContent(): string { return $this->content; }
    public function getUser(): User { return $this->user; }
    public function getPostDate(): DateTime { return $this->postDate; }

    // retorna a diferença em dias entre a data do post e hoje
    public function getPostAge(): int
    {
        $today = new DateTime();
        $interval = $this->postDate->diff($today);
        return $interval->d;
    }
}
