<?php

namespace App\Entity;

use App\Enum\ProjectCategory;
use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 500)]
    private string $shortDescription;

    #[ORM\Column(type: Types::TEXT)]
    private string $longDescription;

    /** @var list<string> */
    #[ORM\Column(type: Types::JSON)]
    private array $stack = [];

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $urlGithub = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $urlLive = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Media $image = null;

    #[ORM\Column(length: 30, enumType: ProjectCategory::class)]
    private ProjectCategory $category;

    #[ORM\Column]
    private bool $featured = false;

    #[ORM\Column]
    private int $sortOrder = 0;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): string
    {
        return $this->longDescription;
    }

    public function setLongDescription(string $longDescription): self
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /** @return list<string> */
    public function getStack(): array
    {
        return $this->stack;
    }

    /** @param list<string> $stack */
    public function setStack(array $stack): self
    {
        $this->stack = $stack;

        return $this;
    }

    public function getUrlGithub(): ?string
    {
        return $this->urlGithub;
    }

    public function setUrlGithub(?string $urlGithub): self
    {
        $this->urlGithub = $urlGithub;

        return $this;
    }

    public function getUrlLive(): ?string
    {
        return $this->urlLive;
    }

    public function setUrlLive(?string $urlLive): self
    {
        $this->urlLive = $urlLive;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ProjectCategory
    {
        return $this->category;
    }

    public function setCategory(ProjectCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
