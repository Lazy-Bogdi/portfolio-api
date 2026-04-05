<?php

namespace App\Dto\Response;

use App\Entity\Project;
use JMS\Serializer\Annotation as Serializer;

class ProjectResponse
{
    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public int $id;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $title;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $shortDescription;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('string')]
    public string $longDescription;

    /** @var list<string> */
    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('array<string>')]
    public array $stack;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('string')]
    public ?string $urlGithub;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('string')]
    public ?string $urlLive;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public ?string $imageUrl;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $category;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('bool')]
    public bool $featured;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('int')]
    public int $sortOrder;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:sP'>")]
    public \DateTimeImmutable $createdAt;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:sP'>")]
    public \DateTimeImmutable $updatedAt;

    public static function fromEntity(Project $project): self
    {
        $dto = new self();
        $dto->id = $project->getId();
        $dto->title = $project->getTitle();
        $dto->shortDescription = $project->getShortDescription();
        $dto->longDescription = $project->getLongDescription();
        $dto->stack = $project->getStack();
        $dto->urlGithub = $project->getUrlGithub();
        $dto->urlLive = $project->getUrlLive();
        $dto->imageUrl = $project->getImage()?->getPath();
        $dto->category = $project->getCategory()->value;
        $dto->featured = $project->isFeatured();
        $dto->sortOrder = $project->getSortOrder();
        $dto->createdAt = $project->getCreatedAt();
        $dto->updatedAt = $project->getUpdatedAt();

        return $dto;
    }
}
