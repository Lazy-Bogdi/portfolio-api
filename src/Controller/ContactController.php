<?php

namespace App\Controller;

use App\Dto\Request\ContactRequest;
use App\Dto\Response\ContactResponse;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Contact')]
class ContactController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
        private readonly ContactRepository $contactRepository,
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('/api/contact', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: ContactRequest::class)))]
    #[OA\Response(response: 201, description: 'Message sent', content: new OA\JsonContent(ref: new Model(type: ContactResponse::class, groups: ['detail'])))]
    public function submit(Request $request): JsonResponse
    {
        $dto = $this->deserialize($request, ContactRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        $contact = new Contact();
        $contact->setName($dto->name);
        $contact->setEmail($dto->email);
        $contact->setMessage($dto->message);

        $this->em->persist($contact);
        $this->em->flush();

        return $this->jsonResponse(ContactResponse::fromEntity($contact), 201, ['detail']);
    }

    #[Route('/api/contacts', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'List contact messages', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: ContactResponse::class, groups: ['list']))))]
    public function list(): JsonResponse
    {
        $contacts = $this->contactRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->jsonResponse(array_map(ContactResponse::fromEntity(...), $contacts), 200, ['list']);
    }

    #[Route('/api/contacts/{id}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Contact message details', content: new OA\JsonContent(ref: new Model(type: ContactResponse::class, groups: ['detail'])))]
    public function detail(int $id): JsonResponse
    {
        return $this->jsonResponse(ContactResponse::fromEntity($this->findOrFail($id)), 200, ['detail']);
    }

    #[Route('/api/contacts/{id}/read', methods: ['PUT'])]
    #[OA\Response(response: 200, description: 'Toggled read status', content: new OA\JsonContent(ref: new Model(type: ContactResponse::class, groups: ['detail'])))]
    public function toggleRead(int $id): JsonResponse
    {
        $contact = $this->findOrFail($id);
        $contact->setIsRead(!$contact->isRead());
        $this->em->flush();

        return $this->jsonResponse(ContactResponse::fromEntity($contact), 200, ['detail']);
    }

    #[Route('/api/contacts/{id}', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Contact message deleted')]
    public function delete(int $id): JsonResponse
    {
        $contact = $this->findOrFail($id);
        $this->em->remove($contact);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    private function findOrFail(int $id): Contact
    {
        return $this->contactRepository->find($id)
            ?? throw new NotFoundHttpException(sprintf('Contact #%d not found', $id));
    }
}
