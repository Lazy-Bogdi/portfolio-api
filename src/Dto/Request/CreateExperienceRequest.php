<?php

namespace App\Dto\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CreateExperienceRequest
{
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $position = null;

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $company = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    #[Assert\NotNull]
    public ?\DateTimeImmutable $dateStart = null;

    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    public ?\DateTimeImmutable $dateEnd = null;

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    public ?string $description = null;

    /** @var list<string>|null */
    #[Serializer\Type('array<string>')]
    public ?array $stack = null;

    #[Serializer\Type('int')]
    public ?int $sortOrder = 0;
}
