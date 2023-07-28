<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['message:read-one','groupement:read-one'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['message:read-one','groupement:read-one'])]

    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['message:read-one','groupement:read-one'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['message:read-one','groupement:read-one'])]

    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[Groups(['message:read-one'])]

    private ?User $recipient = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[Groups(['message:read-one'])]

    private ?Groupement $groupement = null;

    #[ORM\OneToOne(mappedBy: 'message', cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getGroupement(): ?Groupement
    {
        return $this->groupement;
    }

    public function setGroupement(?Groupement $groupement): static
    {
        $this->groupement = $groupement;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        // unset the owning side of the relation if necessary
        if ($image === null && $this->image !== null) {
            $this->image->setMessage(null);
        }

        // set the owning side of the relation if necessary
        if ($image !== null && $image->getMessage() !== $this) {
            $image->setMessage($this);
        }

        $this->image = $image;

        return $this;
    }
}
