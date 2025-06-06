<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Name is required", groups: ["registration"])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "LastName is required", groups: ["registration"])]
    private ?string $lastName = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Phone number is required", groups: ["registration"])]
    private ?int $phone = null;

    #[ORM\Column(length: 255, unique: true, options: ["message" => "This email is already in use."])]
    #[Assert\NotBlank(message: "Email is required", groups: ["registration", "reset_password"])]
    #[Assert\Email(message: "The email {{ value }} is not a valid email address", groups: ["registration", "reset_password"])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Password is required", groups: ["registration"])]
    #[Assert\Length(min: 6, max: 255, minMessage: "Your password must contain at least {{ limit }} characters.", maxMessage: "Your password must be less than {{ limit }} characters.", groups: ["registration", "reset_password"])]
    private ?string $password = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Verification token is required", groups: ["registration"])]
    private ?string $verificationToken = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'client')]
    private Collection $commandes;

    #[ORM\OneToMany(targetEntity: MaterielRecyclable::class, mappedBy: 'user')]
    private Collection $materielRecyclables;

    #[ORM\OneToMany(targetEntity: ListArticle::class, mappedBy: 'user')]
    private Collection $listArticles;

    #[ORM\OneToMany(targetEntity: Favorie::class, mappedBy: 'user')]
    private Collection $favories;

    
    public function __construct()
    {
        $this->address = new Address();
        $this->commandes = new ArrayCollection();
        $this->materielRecyclables = new ArrayCollection();
        $this->listArticles = new ArrayCollection();
        $this->favories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
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

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->getRole() ? [$this->getRole()->getName()] : [];
    }

    public function eraseCredentials()
    {
        // No credentials to erase
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setClient($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getClient() === $this) {
                $commande->setClient(null);
            }
        }
        return $this;
    }

    public function getMaterielRecyclables(): Collection
    {
        return $this->materielRecyclables;
    }

    public function addMaterielRecyclable(MaterielRecyclable $materielRecyclable): static
    {
        if (!$this->materielRecyclables->contains($materielRecyclable)) {
            $this->materielRecyclables->add($materielRecyclable);
            $materielRecyclable->setUser($this);
        }
        return $this;
    }

    public function removeMaterielRecyclable(MaterielRecyclable $materielRecyclable): static
    {
        if ($this->materielRecyclables->removeElement($materielRecyclable)) {
            // set the owning side to null (unless already changed)
            if ($materielRecyclable->getUser() === $this) {
                $materielRecyclable->setUser(null);
            }
        }
        return $this;
    }

    public function getListArticles(): Collection
    {
        return $this->listArticles;
    }

    public function addListArticle(ListArticle $listArticle): static
    {
        if (!$this->listArticles->contains($listArticle)) {
            $this->listArticles->add($listArticle);
            $listArticle->setUser($this);
        }
        return $this;
    }

    public function removeListArticle(ListArticle $listArticle): static
    {
        if ($this->listArticles->removeElement($listArticle)) {
            // set the owning side to null (unless already changed)
            if ($listArticle->getUser() === $this) {
                $listArticle->setUser(null);
            }
        }
        return $this;
    }

    public function getFavories(): Collection
    {
        return $this->favories;
    }

    public function addFavory(Favorie $favory): static
    {
        if (!$this->favories->contains($favory)) {
            $this->favories->add($favory);
            $favory->setUser($this);
        }
        return $this;
    }

    public function removeFavory(Favorie $favory): static
    {
        if ($this->favories->removeElement($favory)) {
            // set the owning side to null (unless already changed)
            if ($favory->getUser() === $this) {
                $favory->setUser(null);
            }
        }
        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): static
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;
        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}