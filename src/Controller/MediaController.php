<?php

namespace App\Controller;

use App\Dto\Response\MediaResponse;
use App\Repository\MediaRepository;
use App\Service\MediaUploader;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/media')]
#[OA\Tag(name: 'Media')]
class MediaController extends AbstractApiController
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/svg+xml',
    ];

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
        private readonly MediaRepository $mediaRepository,
        private readonly MediaUploader $mediaUploader,
        private readonly int $uploadMaxSize,
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'List media', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: MediaResponse::class, groups: ['list']))))]
    public function list(): JsonResponse
    {
        $media = $this->mediaRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->jsonResponse(array_map(MediaResponse::fromEntity(...), $media), 200, ['list']);
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\MediaType(mediaType: 'multipart/form-data', schema: new OA\Schema(properties: [new OA\Property(property: 'file', type: 'string', format: 'binary')])))]
    #[OA\Response(response: 201, description: 'Media uploaded', content: new OA\JsonContent(ref: new Model(type: MediaResponse::class, groups: ['detail'])))]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        if (null === $file) {
            return $this->errorResponse('No file uploaded', 400);
        }

        if (!\in_array($file->getClientMimeType(), self::ALLOWED_MIME_TYPES, true)) {
            return $this->errorResponse(sprintf('Invalid file type "%s". Allowed: %s', $file->getClientMimeType(), implode(', ', self::ALLOWED_MIME_TYPES)), 422);
        }

        if ($file->getSize() > $this->uploadMaxSize) {
            return $this->errorResponse(sprintf('File too large. Max size: %d MB', $this->uploadMaxSize / 1024 / 1024), 422);
        }

        $media = $this->mediaUploader->upload($file);

        $this->em->persist($media);
        $this->em->flush();

        return $this->jsonResponse(MediaResponse::fromEntity($media), 201, ['detail']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Media deleted')]
    public function delete(int $id): JsonResponse
    {
        $media = $this->mediaRepository->find($id)
            ?? throw new NotFoundHttpException(sprintf('Media #%d not found', $id));

        $this->mediaUploader->remove($media);
        $this->em->remove($media);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}
