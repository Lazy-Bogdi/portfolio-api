<?php

namespace App\Dto\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest
{
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    public ?string $password = null;
}
