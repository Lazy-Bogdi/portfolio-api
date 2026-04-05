<?php

namespace App\Dto\Response;

use App\Entity\Media;
use JMS\Serializer\Annotation as Serializer;

class MediaResponse
{
    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public int $id;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $filename;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $originalName;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('string')]
    public string $mimeType;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('int')]
    public int $size;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $url;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:sP'>")]
    public \DateTimeImmutable $createdAt;

    public static function fromEntity(Media $media): self
    {
        $dto = new self();
        $dto->id = $media->getId();
        $dto->filename = $media->getFilename();
        $dto->originalName = $media->getOriginalName();
        $dto->mimeType = $media->getMimeType();
        $dto->size = $media->getSize();
        $dto->url = $media->getPath();
        $dto->createdAt = $media->getCreatedAt();

        return $dto;
    }
}
