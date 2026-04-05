<?php

namespace App\Entity;

use App\Repository\ExperienceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
class Experience
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 255)]
    private string $position;

    #[ORM\Column(length: 255)]
    private string $company;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $dateStart;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateEnd = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    /** @var list<string> */
    #[ORM\Column(type: Types::JSON)]
    private array $stack = [];

    #[ORM\Column]
    private int $sortOrder = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getDateStart(): \DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeImmutable $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeImmutable $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
