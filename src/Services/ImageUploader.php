<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploader
{
    public function __construct(private $targetDirectory)
    {
    }

    public function upload(UploadedFile $imageFile)
    {
        $newFilename = uniqid() . '.' . $imageFile->guessExtension();
        try {
            $imageFile->move(
                $this->getTargetDirectory(),
                $newFilename
            );
        } catch (FileException $e) {
        }
        return $newFilename;
    }

    /**
     * Get the value of targetDirectory
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}