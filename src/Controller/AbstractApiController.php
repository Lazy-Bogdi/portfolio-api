<?php

namespace App\Controller;

use App\Dto\Response\ErrorResponse;
use App\Dto\Response\ValidationErrorResponse;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractApiController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    protected function deserialize(Request $request, string $class): ?object
    {
        try {
            $content = $request->getContent();
            if (empty($content)) {
                return null;
            }

            /* @var object */
            return $this->serializer->deserialize($content, $class, 'json');
        } catch (\Exception) {
            return null;
        }
    }

    protected function validate(object $dto): ?JsonResponse
    {
        $violations = $this->validator->validate($dto);

        if (0 === $violations->count()) {
            return null;
        }

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => (string) $violation->getMessage(),
            ];
        }

        return $this->jsonResponse(new ValidationErrorResponse($errors), 422);
    }

    /**
     * @param list<string> $groups
     */
    protected function jsonResponse(mixed $data, int $status = 200, array $groups = []): JsonResponse
    {
        $context = SerializationContext::create();
        if (!empty($groups)) {
            $context->setGroups($groups);
        }

        $json = $this->serializer->serialize($data, 'json', $context);

        return new JsonResponse($json, $status, [], true);
    }

    protected function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return $this->jsonResponse(new ErrorResponse($status, $message), $status);
    }
}
