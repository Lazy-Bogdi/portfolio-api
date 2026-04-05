<?php

namespace App\Dto\Response;

use App\Entity\Skill;
use JMS\Serializer\Annotation as Serializer;

class SkillResponse
{
    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public int $id;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $label;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $category;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public int $level;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public ?string $icon;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('int')]
    public int $sortOrder;

    public static function fromEntity(Skill $skill): self
    {
        $dto = new self();
        $dto->id = $skill->getId();
        $dto->label = $skill->getLabel();
        $dto->category = $skill->getCategory()->value;
        $dto->level = $skill->getLevel();
        $dto->icon = $skill->getIcon();
        $dto->sortOrder = $skill->getSortOrder();

        return $dto;
    }
}
