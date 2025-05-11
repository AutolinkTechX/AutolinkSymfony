<?php
namespace App\Entity;

use App\Repository\WorkshopRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: WorkshopRepository::class)]
class Workshop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, name: "name")]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255, name: "image", nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, name: "description")]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: "starts_at")]
    #[Assert\NotBlank(message: "La date de début ne peut pas être vide.")]
    private ?\DateTimeInterface $DateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: "ends_at")]
    #[Assert\NotBlank(message: "La date de fin ne peut pas être vide.")]
    private ?\DateTimeInterface $DateFin = null;

    #[ORM\Column(length: 255, name: "location", nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'integer', name: 'available_places', nullable: true)]
    private ?int $availablePlaces = null;

    #[ORM\Column(type: 'float', name: 'price', nullable: true)]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'workshop', targetEntity: TaskWorkshop::class)]
    private Collection $taskWorkshops;

    public function __construct()
    {
        $this->taskWorkshops = new ArrayCollection();
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->DateDebut;
    }

    public function setDateDebut(\DateTimeInterface $DateDebut): static
    {
        $this->DateDebut = $DateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTimeInterface $DateFin): static
    {
        $this->DateFin = $DateFin;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getAvailablePlaces(): ?int
    {
        return $this->availablePlaces;
    }

    public function setAvailablePlaces(?int $availablePlaces): static
    {
        $this->availablePlaces = $availablePlaces;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTaskWorkshops(): Collection
    {
        return $this->taskWorkshops;
    }

    /**
     * @Assert\Callback
     */
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->DateDebut instanceof \DateTimeInterface && $this->DateFin instanceof \DateTimeInterface) {
            if ($this->DateFin <= $this->DateDebut) {
                $context->buildViolation('La date de fin doit être supérieure à la date de début.')
                    ->atPath('DateFin')
                    ->addViolation();
            }
        }
    }
}