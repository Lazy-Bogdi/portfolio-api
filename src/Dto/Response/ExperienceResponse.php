<?php

namespace App\Dto\Response;

use App\Entity\Experience;
use JMS\Serializer\Annotation as Serializer;

class ExperienceResponse
{
    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public int $id;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $position;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $company;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    public \DateTimeImmutable $dateStart;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d'>")]
    public ?\DateTimeImmutable $dateEnd;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('string')]
    public string $description;

    /** @var list<string> */
    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('array<string>')]
    public array $stack;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('int')]
    public int $sortOrder;

    public static function fromEntity(Experience $experience): self
    {
        $dto = new self();
        $dto->id = $experience->getId();
        $dto->position = $experience->getPosition();
        $dto->company = $experience->getCompany();
        $dto->dateStart = $experience->getDateStart();
        $dto->dateEnd = $experience->getDateEnd();
        $dto->description = $experience->getDescription();
        $dto->stack = $experience->getStack();
        $dto->sortOrder = $experience->getSortOrder();

        return $dto;
    }
}
