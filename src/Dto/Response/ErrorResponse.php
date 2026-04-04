<?php

namespace App\Dto\Response;

use JMS\Serializer\Annotation as Serializer;

class ErrorResponse
{
    #[Serializer\Type('int')]
    public int $code;

    #[Serializer\Type('string')]
    public string $message;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
