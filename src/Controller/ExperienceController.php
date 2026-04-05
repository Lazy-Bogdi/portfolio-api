<?php

namespace App\Controller;

use App\Dto\Request\CreateExperienceRequest;
use App\Dto\Request\UpdateExperienceRequest;
use App\Dto\Response\ExperienceResponse;
use App\Entity\Experience;
use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/experiences')]
#[OA\Tag(name: 'Experiences')]
class ExperienceController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
        private readonly ExperienceRepository $experienceRepository,
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'List experiences', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: ExperienceResponse::class, groups: ['list']))))]
    public function list(): JsonResponse
    {
        $experiences = $this->experienceRepository->findBy([], ['sortOrder' => 'ASC']);

        return $this->jsonResponse(array_map(ExperienceResponse::fromEntity(...), $experiences), 200, ['list']);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Experience details', content: new OA\JsonContent(ref: new Model(type: ExperienceResponse::class, groups: ['detail'])))]
    public function detail(int $id): JsonResponse
    {
        return $this->jsonResponse(ExperienceResponse::fromEntity($this->findOrFail($id)), 200, ['detail']);
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateExperienceRequest::class)))]
    #[OA\Response(response: 201, description: 'Experience created', content: new OA\JsonContent(ref: new Model(type: ExperienceResponse::class, groups: ['detail'])))]
    public function create(Request $request): JsonResponse
    {
        $dto = $this->deserialize($request, CreateExperienceRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        $experience = new Experience();
        $experience->setPosition($dto->position);
        $experience->setCompany($dto->company);
        $experience->setDateStart($dto->dateStart);
        $experience->setDateEnd($dto->dateEnd);
        $experience->setDescription($dto->description);
        $experience->setStack($dto->stack ?? []);
        $experience->setSortOrder($dto->sortOrder ?? 0);

        $this->em->persist($experience);
        $this->em->flush();

        return $this->jsonResponse(ExperienceResponse::fromEntity($experience), 201, ['detail']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateExperienceRequest::class)))]
    #[OA\Response(response: 200, description: 'Experience updated', content: new OA\JsonContent(ref: new Model(type: ExperienceResponse::class, groups: ['detail'])))]
    public function update(int $id, Request $request): JsonResponse
    {
        $experience = $this->findOrFail($id);

        $dto = $this->deserialize($request, UpdateExperienceRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        if (null !== $dto->position) {
            $experience->setPosition($dto->position);
        }
        if (null !== $dto->company) {
            $experience->setCompany($dto->company);
        }
        if (null !== $dto->dateStart) {
            $experience->setDateStart($dto->dateStart);
        }
        if (null !== $dto->dateEnd) {
            $experience->setDateEnd($dto->dateEnd);
        }
        if (null !== $dto->description) {
            $experience->setDescription($dto->description);
        }
        if (null !== $dto->stack) {
            $experience->setStack($dto->stack);
        }
        if (null !== $dto->sortOrder) {
            $experience->setSortOrder($dto->sortOrder);
        }

        $this->em->flush();

        return $this->jsonResponse(ExperienceResponse::fromEntity($experience), 200, ['detail']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Experience deleted')]
    public function delete(int $id): JsonResponse
    {
        $experience = $this->findOrFail($id);
        $this->em->remove($experience);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    private function findOrFail(int $id): Experience
    {
        return $this->experienceRepository->find($id)
            ?? throw new NotFoundHttpException(sprintf('Experience #%d not found', $id));
    }
}
