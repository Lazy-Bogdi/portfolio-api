<?php

namespace App\Dto\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSkillRequest
{
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $label = null;

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['backend', 'frontend', 'devops', 'other'])]
    public ?string $category = null;

    #[Serializer\Type('int')]
    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 5)]
    public ?int $level = null;

    #[Serializer\Type('string')]
    #[Assert\Length(max: 100)]
    public ?string $icon = null;

    #[Serializer\Type('int')]
    public ?int $sortOrder = 0;
}
