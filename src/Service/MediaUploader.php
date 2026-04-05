<?php

namespace App\Service;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaUploader
{
    public function __construct(
        private readonly string $uploadDir,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): Media
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $this->slugger->slug($originalName);
        $filename = $safeName.'-'.uniqid().'.'.$file->guessExtension();

        $file->move($this->uploadDir, $filename);

        $media = new Media();
        $media->setFilename($filename);
        $media->setOriginalName($file->getClientOriginalName());
        $media->setMimeType($file->getClientMimeType());
        $media->setSize($file->getSize());
        $media->setPath('/uploads/'.$filename);

        return $media;
    }

    public function remove(Media $media): void
    {
        $filepath = $this->uploadDir.'/'.$media->getFilename();
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
