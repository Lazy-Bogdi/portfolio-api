<?php

namespace App\Dto\Response;

use App\Entity\Education;
use JMS\Serializer\Annotation as Serializer;

class EducationResponse
{
    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public int $id;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $degree;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $school;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public int $yearStart;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public ?int $yearEnd;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('string')]
    public string $description;

    public static function fromEntity(Education $education): self
    {
        $dto = new self();
        $dto->id = $education->getId();
        $dto->degree = $education->getDegree();
        $dto->school = $education->getSchool();
        $dto->yearStart = $education->getYearStart();
        $dto->yearEnd = $education->getYearEnd();
        $dto->description = $education->getDescription();

        return $dto;
    }
}
