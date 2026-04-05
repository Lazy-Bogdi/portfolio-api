<?php

namespace App\Controller;

use App\Dto\Request\CreateSkillRequest;
use App\Dto\Request\UpdateSkillRequest;
use App\Dto\Response\SkillResponse;
use App\Entity\Skill;
use App\Enum\SkillCategory;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/skills')]
#[OA\Tag(name: 'Skills')]
class SkillController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
        private readonly SkillRepository $skillRepository,
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'List skills', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: SkillResponse::class, groups: ['list']))))]
    public function list(): JsonResponse
    {
        $skills = $this->skillRepository->findBy([], ['sortOrder' => 'ASC']);

        return $this->jsonResponse(array_map(SkillResponse::fromEntity(...), $skills), 200, ['list']);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Skill details', content: new OA\JsonContent(ref: new Model(type: SkillResponse::class, groups: ['detail'])))]
    public function detail(int $id): JsonResponse
    {
        return $this->jsonResponse(SkillResponse::fromEntity($this->findOrFail($id)), 200, ['detail']);
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateSkillRequest::class)))]
    #[OA\Response(response: 201, description: 'Skill created', content: new OA\JsonContent(ref: new Model(type: SkillResponse::class, groups: ['detail'])))]
    public function create(Request $request): JsonResponse
    {
        $dto = $this->deserialize($request, CreateSkillRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        $skill = new Skill();
        $skill->setLabel($dto->label);
        $skill->setCategory(SkillCategory::from($dto->category));
        $skill->setLevel($dto->level);
        $skill->setIcon($dto->icon);
        $skill->setSortOrder($dto->sortOrder ?? 0);

        $this->em->persist($skill);
        $this->em->flush();

        return $this->jsonResponse(SkillResponse::fromEntity($skill), 201, ['detail']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateSkillRequest::class)))]
    #[OA\Response(response: 200, description: 'Skill updated', content: new OA\JsonContent(ref: new Model(type: SkillResponse::class, groups: ['detail'])))]
    public function update(int $id, Request $request): JsonResponse
    {
        $skill = $this->findOrFail($id);

        $dto = $this->deserialize($request, UpdateSkillRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        if (null !== $dto->label) {
            $skill->setLabel($dto->label);
        }
        if (null !== $dto->category) {
            $skill->setCategory(SkillCategory::from($dto->category));
        }
        if (null !== $dto->level) {
            $skill->setLevel($dto->level);
        }
        if (null !== $dto->icon) {
            $skill->setIcon($dto->icon);
        }
        if (null !== $dto->sortOrder) {
            $skill->setSortOrder($dto->sortOrder);
        }

        $this->em->flush();

        return $this->jsonResponse(SkillResponse::fromEntity($skill), 200, ['detail']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Skill deleted')]
    public function delete(int $id): JsonResponse
    {
        $skill = $this->findOrFail($id);
        $this->em->remove($skill);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    private function findOrFail(int $id): Skill
    {
        return $this->skillRepository->find($id)
            ?? throw new NotFoundHttpException(sprintf('Skill #%d not found', $id));
    }
}
