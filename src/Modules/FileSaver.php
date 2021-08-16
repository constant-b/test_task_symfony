<?php

namespace App\Modules;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileSaver
{
    private string $targetDirectory;
    private SluggerInterface $slugger;

    const ALLOWED_EXTENSIONS = ["pdf", "bmp", "jpeg", "jpg", "gif", "png", "doc", "docx", "pptx", "ppt", "xls", "xlsx"];

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger         = $slugger;
    }

    public function isAllowedExtension(?string $extension): bool
    {
        return in_array(mb_strtolower($extension), self::ALLOWED_EXTENSIONS);
    }

    public function save(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExtension    = $file->guessExtension();

        if ($this->isAllowedExtension($fileExtension)) {
            $safeFilename = $this->slugger->slug($originalFilename);
            $filename     = $safeFilename . "-" . uniqid() . "." . $file->guessExtension();

            try {
                $file->move($this->targetDirectory, $filename);
            } catch (FileException $exception) {
                dump($exception->getMessage());
            }

            return $filename;
        }

        throw new FileException("Extension isn't allowed!");
    }
}