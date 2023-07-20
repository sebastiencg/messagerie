<?php

namespace App\Entity;

use App\Repository\GroupementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupementRepository::class)]
class Groupement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groupement:read-one','groupement:read-all'])]

    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'groupements')]
    #[Groups(['groupement:read-one','groupement:read-all'])]

    private ?User $master = null;

    #[ORM\OneToMany(mappedBy: 'groupement', targetEntity: Message::class)]
    #[Groups(['groupement:read-all'])]

    private Collection $messages;

    #[ORM\Column(length: 255)]
    #[Groups(['groupement:read-one','groupement:read-all'])]

    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['groupement:read-one','groupement:read-all'])]

    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Validity::class)]
    private Collection $validities;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groupementsMember')]
    #[Groups(['groupement:read-all'])]

    private Collection $nember;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->validities = new ArrayCollection();
        $this->nember = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaster(): ?User
    {
        return $this->master;
    }

    public function setMaster(?User $master): static
    {
        $this->master = $master;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setGroupement($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getGroupement() === $this) {
                $message->setGroupement(null);
            }
        }

        return $this;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Validity>
     */
    public function getValidities(): Collection
    {
        return $this->validities;
    }

    public function addValidity(Validity $validity): static
    {
        if (!$this->validities->contains($validity)) {
            $this->validities->add($validity);
            $validity->setGroupe($this);
        }

        return $this;
    }

    public function removeValidity(Validity $validity): static
    {
        if ($this->validities->removeElement($validity)) {
            // set the owning side to null (unless already changed)
            if ($validity->getGroupe() === $this) {
                $validity->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getNember(): Collection
    {
        return $this->nember;
    }

    public function addNember(User $nember): static
    {
        if (!$this->nember->contains($nember)) {
            $this->nember->add($nember);
        }

        return $this;
    }

    public function removeNember(User $nember): static
    {
        $this->nember->removeElement($nember);

        return $this;
    }
}
