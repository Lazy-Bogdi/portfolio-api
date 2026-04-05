<?php

namespace App\Dto\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateEducationRequest
{
    #[Serializer\Type('string')]
    #[Assert\Length(max: 255)]
    public ?string $degree = null;

    #[Serializer\Type('string')]
    #[Assert\Length(max: 255)]
    public ?string $school = null;

    #[Serializer\Type('int')]
    public ?int $yearStart = null;

    #[Serializer\Type('int')]
    public ?int $yearEnd = null;

    #[Serializer\Type('string')]
    public ?string $description = null;
}
