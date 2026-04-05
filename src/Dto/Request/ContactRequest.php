<?php

namespace App\Dto\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ContactRequest
{
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public ?string $message = null;
}
