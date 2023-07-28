<?php

namespace App\Entity;

use App\Repository\ValidityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidityRepository::class)]
class Validity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'validities')]
    private ?User $ofUser = null;

    #[ORM\ManyToOne(inversedBy: 'validities')]
    private ?Groupement $groupe = null;

    #[ORM\Column]
    private ?bool $validity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfUser(): ?User
    {
        return $this->ofUser;
    }

    public function setOfUser(?User $ofUser): static
    {
        $this->ofUser = $ofUser;

        return $this;
    }

    public function getGroupe(): ?Groupement
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupement $groupe): static
    {
        $this->groupe = $groupe;

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
