<?php

namespace App\Dto\Response;

use JMS\Serializer\Annotation as Serializer;

class ValidationErrorResponse
{
    #[Serializer\Type('int')]
    public int $code = 422;

    #[Serializer\Type('string')]
    public string $message = 'Validation failed';

    /** @var array<array{field: string, message: string}> */
    #[Serializer\Type('array')]
    public array $errors = [];

    /**
     * @param array<array{field: string, message: string}> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }
}
