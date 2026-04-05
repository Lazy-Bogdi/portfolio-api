<?php

namespace App\Dto\Response;

use App\Entity\Contact;
use JMS\Serializer\Annotation as Serializer;

class ContactResponse
{
    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('int')]
    public int $id;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $name;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('string')]
    public string $email;

    #[Serializer\Groups(['detail'])]
    #[Serializer\Type('string')]
    public string $message;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type("DateTimeImmutable<'Y-m-d\\TH:i:sP'>")]
    public \DateTimeImmutable $createdAt;

    #[Serializer\Groups(['list', 'detail'])]
    #[Serializer\Type('bool')]
    public bool $isRead;

    public static function fromEntity(Contact $contact): self
    {
        $dto = new self();
        $dto->id = $contact->getId();
        $dto->name = $contact->getName();
        $dto->email = $contact->getEmail();
        $dto->message = $contact->getMessage();
        $dto->createdAt = $contact->getCreatedAt();
        $dto->isRead = $contact->isRead();

        return $dto;
    }
}
