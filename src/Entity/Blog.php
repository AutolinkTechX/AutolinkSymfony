<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(name: 'publishedDate', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $publishedDate = null;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: Comment::class, cascade: ['persist', 'remove'])]
    private Collection $comments;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'integer')]
    private int $likes = 0;

    #[ORM\Column(type: 'integer')]
    private int $dislikes = 0;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }
    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): static { $this->content = $content; return $this; }
    public function getPublishedDate(): ?\DateTimeInterface { return $this->publishedDate; }
    public function setPublishedDate(\DateTimeInterface $publishedDate): static { $this->publishedDate = $publishedDate; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }

    public function getLikes(): int { return $this->likes; }
    public function setLikes(int $likes): self { $this->likes = $likes; return $this; }

    public function getDislikes(): int { return $this->dislikes; }
    public function setDislikes(int $dislikes): self { $this->dislikes = $dislikes; return $this; }
}