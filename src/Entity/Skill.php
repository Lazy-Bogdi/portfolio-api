<?php

namespace App\Entity;

use App\Enum\SkillCategory;
use App\Repository\SkillRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 100)]
    private string $label;

    #[ORM\Column(length: 30, enumType: SkillCategory::class)]
    private SkillCategory $category;

    #[ORM\Column]
    private int $level;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column]
    private int $sortOrder = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCategory(): SkillCategory
    {
        return $this->category;
    }

    public function setCategory(SkillCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

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
