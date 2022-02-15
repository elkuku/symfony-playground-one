<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use ZipArchive;

class FileUploader
{
    public function __construct(
        private readonly string $projectDir,
        private readonly SluggerInterface $slugger)
    {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $newDirname = $safeFilename.'-'.uniqid();
        $newFilename = $newDirname.'.'.$file->guessExtension();

        $uploadDir = $this->projectDir.'/var/maxfields';
        $path = '';

        try {
            $file->move($uploadDir, $newFilename);

            $zip = new ZipArchive;
            if (true === $zip->open($uploadDir.'/'.$newFilename)) {
                $zip->extractTo($uploadDir.'/'.$newDirname);
                $zip->close();
            } else {
                throw new \UnexpectedValueException(
                    'Unable to extract zip archive'
                );
            }

            $finder = new Finder();

            $finder->files()->in($uploadDir.'/'.$newDirname);

            foreach ($finder as $entry) {
                if ('portals.txt' === $entry->getFilename()) {
                    $path = $entry->getPath();
                }
            }

            if (!$path) {
                throw new \UnexpectedValueException(
                    'file "portals.txt" not found in zip archive'
                );
            }
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $path;
    }

    public function getTargetDirectory(): string
    {
        return $this->projectDir;
    }
}
