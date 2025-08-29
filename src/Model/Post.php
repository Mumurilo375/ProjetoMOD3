<?php

namespace App\Model;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class Post
{
    #[Column, Id, GeneratedValue]
    private int $id;

    #[Column]
    private string $content;

    #[ManyToOne]
    private User $user;

    #[Column]
    private DateTime $postDate;

    public function __construct(string $content, User $user, DateTime $postDate)
    {
        $this->content = $content;
        $this->user = $user;
        $this->postDate = $postDate;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPostDate(): DateTime
    {
        return $this->postDate;
    }

    public function getPostAge(): int
    {
        $today = new DateTime();
        $interval = $this->postDate->diff($today);
        return $interval->d;
    }
}
