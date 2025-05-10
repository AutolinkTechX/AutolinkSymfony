<?php

namespace App\Entity;

use App\Repository\TaskWorkshopRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: TaskWorkshopRepository::class)]
class TaskWorkshop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: "starts_at")]
    #[Assert\NotBlank(message: 'La date de début ne peut pas être vide.')]
    private ?\DateTimeInterface $DateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: "ends_at")]
    #[Assert\NotBlank(message: 'La date de fin ne peut pas être vide.')]
    private ?\DateTimeInterface $DateFin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le statut ne peut pas être vide.')]
    private ?string $status = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull(message: 'Le workshop associé est obligatoire.')]
    private ?Workshop $workshop = null;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getWorkshop(): ?Workshop
    {
        return $this->workshop;
    }

    public function setWorkshop(?Workshop $workshop): static
    {
        $this->workshop = $workshop;

        return $this;
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
