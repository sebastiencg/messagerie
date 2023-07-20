<?php

namespace App\Entity;

use App\Repository\FriendRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FriendRepository::class)]
class Friend
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'friends')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['friend:read-one'])]

    private ?User $ofUser1 = null;

    #[ORM\ManyToOne(inversedBy: 'friends')]
    #[Groups(['friend:read-one'])]

    private ?User $ofUser2 = null;

    #[ORM\Column]
    #[Groups(['friend:read-one'])]

    private ?bool $validity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfUser1(): ?User
    {
        return $this->ofUser1;
    }

    public function setOfUser1(?User $ofUser1): static
    {
        $this->ofUser1 = $ofUser1;

        return $this;
    }

    public function getOfUser2(): ?User
    {
        return $this->ofUser2;
    }

    public function setOfUser2(?User $ofUser2): static
    {
        $this->ofUser2 = $ofUser2;

        return $this;
    }

    public function isValidity(): ?bool
    {
        return $this->validity;
    }

    public function setValidity(bool $validity): static
    {
        $this->validity = $validity;

        return $this;
    }
}
