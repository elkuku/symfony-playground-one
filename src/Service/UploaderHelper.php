<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\Filesystem;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    public const STORE_IMAGES_PATH = 'store_images';
    public const STORE_REFERENCE = 'store_reference';

    public function __construct(
        private Filesystem $publicUploadsFilesystem,
        private Filesystem $privateUploadsFilesystem,
        private RequestStackContext $requestStackContext,
        private string $uploadedAssetsBaseUrl,
    ) {
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().$this->uploadedAssetsBaseUrl.'/'.$path;
    }

    public function uploadStoreImage(
        File $file,
        ?string $existingFilename
    ): string {
        $newFilename = $this->uploadFile($file, self::STORE_IMAGES_PATH, true);

        if ($existingFilename) {
            $this->publicUploadsFilesystem->delete(
                self::STORE_IMAGES_PATH.'/'.$existingFilename
            );
        }

        return $newFilename;
    }

    public function uploadStoreReference(File $file): string
    {
        return $this->uploadFile($file, self::STORE_REFERENCE, false);
    }

    private function uploadFile(File $file, string $directory, bool $isPublic): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)).'-'.uniqid().'.'.$file->guessExtension();

        $filesystem = $isPublic ? $this->publicUploadsFilesystem : $this->privateUploadsFilesystem;

        $stream = fopen($file->getPathname(), 'r');

        $filesystem->writeStream(
            $directory.'/'.$newFilename,
            $stream
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFilename;
    }

    /**
     * @return resource
     */
    public function readStream(string $path, bool $isPublic)
    {
        $filesystem = $isPublic ? $this->publicUploadsFilesystem : $this->privateUploadsFilesystem;

        $resource = $filesystem->readStream($path);

        if ($resource === false) {
            throw new \Exception(sprintf('Error opening stream for "%s"', $path));
        }

        return $resource;
    }
}
