<?php

namespace App\Dto\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateProjectRequest
{
    #[Serializer\Type('string')]
    #[Assert\Length(max: 255)]
    public ?string $title = null;

    #[Serializer\Type('string')]
    #[Assert\Length(max: 500)]
    public ?string $shortDescription = null;

    #[Serializer\Type('string')]
    public ?string $longDescription = null;

    /** @var list<string>|null */
    #[Serializer\Type('array<string>')]
    public ?array $stack = null;

    #[Serializer\Type('string')]
    #[Assert\Length(max: 500)]
    public ?string $urlGithub = null;

    #[Serializer\Type('string')]
    #[Assert\Length(max: 500)]
    public ?string $urlLive = null;

    #[Serializer\Type('int')]
    public ?int $imageId = null;

    #[Serializer\Type('string')]
    #[Assert\Choice(choices: ['fullstack', 'devops', 'open-source'])]
    public ?string $category = null;

    #[Serializer\Type('bool')]
    public ?bool $featured = null;

    #[Serializer\Type('int')]
    public ?int $sortOrder = null;
}
