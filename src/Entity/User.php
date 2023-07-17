<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['message:read-one','groupement:read-all','groupement:read-one'])]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'master', targetEntity: Groupement::class)]
    private Collection $groupements;

    #[ORM\OneToMany(mappedBy: 'ofUser1', targetEntity: Friend::class, orphanRemoval: true)]
    private Collection $friends;

    #[ORM\OneToMany(mappedBy: 'member', targetEntity: Groupement::class)]
    private Collection $groupementsMember;

    #[ORM\OneToMany(mappedBy: 'ofUser', targetEntity: Validity::class)]
    private Collection $validities;


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->groupements = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->groupementsMember = new ArrayCollection();
        $this->validities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupement>
     */
    public function getGroupements(): Collection
    {
        return $this->groupements;
    }

    public function addGroupement(Groupement $groupement): static
    {
        if (!$this->groupements->contains($groupement)) {
            $this->groupements->add($groupement);
            $groupement->setMaster($this);
        }

        return $this;
    }

    public function removeGroupement(Groupement $groupement): static
    {
        if ($this->groupements->removeElement($groupement)) {
            // set the owning side to null (unless already changed)
            if ($groupement->getMaster() === $this) {
                $groupement->setMaster(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friend>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(Friend $friend): static
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
            $friend->setOfUser1($this);
        }

        return $this;
    }

    public function removeFriend(Friend $friend): static
    {
        if ($this->friends->removeElement($friend)) {
            // set the owning side to null (unless already changed)
            if ($friend->getOfUser1() === $this) {
                $friend->setOfUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupement>
     */
    public function getGroupementsMember(): Collection
    {
        return $this->groupementsMember;
    }

    public function addGroupementsMember(Groupement $groupementsMember): static
    {
        if (!$this->groupementsMember->contains($groupementsMember)) {
            $this->groupementsMember->add($groupementsMember);
            $groupementsMember->setMember($this);
        }

        return $this;
    }

    public function removeGroupementsMember(Groupement $groupementsMember): static
    {
        if ($this->groupementsMember->removeElement($groupementsMember)) {
            // set the owning side to null (unless already changed)
            if ($groupementsMember->getMember() === $this) {
                $groupementsMember->setMember(null);
            }
        }

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
            $validity->setOfUser($this);
        }

        return $this;
    }

    public function removeValidity(Validity $validity): static
    {
        if ($this->validities->removeElement($validity)) {
            // set the owning side to null (unless already changed)
            if ($validity->getOfUser() === $this) {
                $validity->setOfUser(null);
            }
        }

        return $this;
    }

}
