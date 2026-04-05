<?php

namespace App\Controller;

use App\Dto\Request\CreateProjectRequest;
use App\Dto\Request\UpdateProjectRequest;
use App\Dto\Response\ProjectResponse;
use App\Entity\Project;
use App\Enum\ProjectCategory;
use App\Repository\MediaRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/projects')]
#[OA\Tag(name: 'Projects')]
class ProjectController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
        private readonly ProjectRepository $projectRepository,
        private readonly MediaRepository $mediaRepository,
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Parameter(name: 'featured', in: 'query', required: false, schema: new OA\Schema(type: 'boolean'))]
    #[OA\Response(response: 200, description: 'List projects', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: ProjectResponse::class, groups: ['list']))))]
    public function list(Request $request): JsonResponse
    {
        $criteria = [];
        if ($request->query->has('featured')) {
            $criteria['featured'] = $request->query->getBoolean('featured');
        }

        $projects = $this->projectRepository->findBy($criteria, ['sortOrder' => 'ASC']);
        $dtos = array_map(ProjectResponse::fromEntity(...), $projects);

        return $this->jsonResponse($dtos, 200, ['list']);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Project details', content: new OA\JsonContent(ref: new Model(type: ProjectResponse::class, groups: ['detail'])))]
    public function detail(int $id): JsonResponse
    {
        return $this->jsonResponse(ProjectResponse::fromEntity($this->findOrFail($id)), 200, ['detail']);
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CreateProjectRequest::class)))]
    #[OA\Response(response: 201, description: 'Project created', content: new OA\JsonContent(ref: new Model(type: ProjectResponse::class, groups: ['detail'])))]
    public function create(Request $request): JsonResponse
    {
        $dto = $this->deserialize($request, CreateProjectRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        $project = new Project();
        $project->setTitle($dto->title);
        $project->setShortDescription($dto->shortDescription);
        $project->setLongDescription($dto->longDescription);
        $project->setStack($dto->stack ?? []);
        $project->setUrlGithub($dto->urlGithub);
        $project->setUrlLive($dto->urlLive);
        $project->setCategory(ProjectCategory::from($dto->category));
        $project->setFeatured($dto->featured ?? false);
        $project->setSortOrder($dto->sortOrder ?? 0);

        if (null !== $dto->imageId) {
            $media = $this->mediaRepository->find($dto->imageId);
            $project->setImage($media);
        }

        $this->em->persist($project);
        $this->em->flush();

        return $this->jsonResponse(ProjectResponse::fromEntity($project), 201, ['detail']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: UpdateProjectRequest::class)))]
    #[OA\Response(response: 200, description: 'Project updated', content: new OA\JsonContent(ref: new Model(type: ProjectResponse::class, groups: ['detail'])))]
    public function update(int $id, Request $request): JsonResponse
    {
        $project = $this->findOrFail($id);

        $dto = $this->deserialize($request, UpdateProjectRequest::class);
        if (null === $dto) {
            return $this->errorResponse('Invalid JSON body', 400);
        }

        $validationError = $this->validate($dto);
        if (null !== $validationError) {
            return $validationError;
        }

        if (null !== $dto->title) {
            $project->setTitle($dto->title);
        }
        if (null !== $dto->shortDescription) {
            $project->setShortDescription($dto->shortDescription);
        }
        if (null !== $dto->longDescription) {
            $project->setLongDescription($dto->longDescription);
        }
        if (null !== $dto->stack) {
            $project->setStack($dto->stack);
        }
        if (null !== $dto->urlGithub) {
            $project->setUrlGithub($dto->urlGithub);
        }
        if (null !== $dto->urlLive) {
            $project->setUrlLive($dto->urlLive);
        }
        if (null !== $dto->category) {
            $project->setCategory(ProjectCategory::from($dto->category));
        }
        if (null !== $dto->featured) {
            $project->setFeatured($dto->featured);
        }
        if (null !== $dto->sortOrder) {
            $project->setSortOrder($dto->sortOrder);
        }
        if (null !== $dto->imageId) {
            $media = $this->mediaRepository->find($dto->imageId);
            $project->setImage($media);
        }

        $this->em->flush();

        return $this->jsonResponse(ProjectResponse::fromEntity($project), 200, ['detail']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Project deleted')]
    public function delete(int $id): JsonResponse
    {
        $project = $this->findOrFail($id);
        $this->em->remove($project);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }

    private function findOrFail(int $id): Project
    {
        return $this->projectRepository->find($id)
            ?? throw new NotFoundHttpException(sprintf('Project #%d not found', $id));
    }
}
