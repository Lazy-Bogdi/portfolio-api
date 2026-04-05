<?php

namespace App\Dto\Response;

use JMS\Serializer\Annotation as Serializer;

class AuthTokenResponse
{
    #[Serializer\Type('string')]
    public string $token;

    #[Serializer\Type('string')]
    public string $refreshToken;

    public function __construct(string $token, string $refreshToken)
    {
        $this->token = $token;
        $this->refreshToken = $refreshToken;
    }
}
