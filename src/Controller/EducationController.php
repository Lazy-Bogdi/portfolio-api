<?php

namespace App\Controller;

use App\Dto\Request\CreateEducationRequest;
use App\Dto\Request\UpdateEducationRequest;
use App\Dto\Response\EducationResponse;
use App\Entity\Education;
use App\Repository\EducationRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/education')]
#[OA\Tag(name: 'Education')]
class EducationController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
        private readonly EducationRepository $educationRepository,
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'List education', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: EducationResponse::class, groups: ['list']))))]
    public function list(): JsonResponse
    {
        $items = $this->educationRepository->findBy([], ['yearStart' => 'DESC']);

        return $this->jsonResponse(array_map(EducationResponse::fromEntity(...), $items), 200, ['list']);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Education details', content: new OA\JsonContent(ref: new Model(type: EducationResponse::class, groups: ['detail'])))]
    public function detail(int $id): JsonResponse
    {
        return $this->jsonResponse(EducationResponse::fromEntity($this->findOrFail($id)), 200, ['detail']);
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateEducationRequest::class)))]
    #[OA\Response(response: 201, description: 'Education created', content: new OA\JsonContent(ref: new Model(type: EducationResponse::class, groups: ['detail'])))]
    public function create(Request $request): JsonResponse
    {
        $dto = $this->deserialize($request, CreateEducationRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        $education = new Education();
        $education->setDegree($dto->degree);
        $education->setSchool($dto->school);
        $education->setYearStart($dto->yearStart);
        $education->setYearEnd($dto->yearEnd);
        $education->setDescription($dto->description);

        $this->em->persist($education);
        $this->em->flush();

        return $this->jsonResponse(EducationResponse::fromEntity($education), 201, ['detail']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateEducationRequest::class)))]
    #[OA\Response(response: 200, description: 'Education updated', content: new OA\JsonContent(ref: new Model(type: EducationResponse::class, groups: ['detail'])))]
    public function update(int $id, Request $request): JsonResponse
    {
        $education = $this->findOrFail($id);

        $dto = $this->deserialize($request, UpdateEducationRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        if (null !== $dto->degree) {
            $education->setDegree($dto->degree);
        }
        if (null !== $dto->school) {
            $education->setSchool($dto->school);
        }
        if (null !== $dto->yearStart) {
            $education->setYearStart($dto->yearStart);
        }
        if (null !== $dto->yearEnd) {
            $education->setYearEnd($dto->yearEnd);
        }
        if (null !== $dto->description) {
            $education->setDescription($dto->description);
        }

        $this->em->flush();

        return $this->jsonResponse(EducationResponse::fromEntity($education), 200, ['detail']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Education deleted')]
    public function delete(int $id): JsonResponse
    {
        $education = $this->findOrFail($id);
        $this->em->remove($education);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    private function findOrFail(int $id): Education
    {
        return $this->educationRepository->find($id)
            ?? throw new NotFoundHttpException(sprintf('Education #%d not found', $id));
    }
}
