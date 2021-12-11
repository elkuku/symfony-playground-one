<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\Filesystem;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    public const STORE_IMAGES_PATH = 'store_images';

    public function __construct(
        private Filesystem $publicUploadsFilesystem,
        private RequestStackContext $requestStackContext
    ) {
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$path;
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function uploadStoreImage(UploadedFile $file): string
    {
        $originalFilename = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $newFilename = Urlizer::urlize($originalFilename)
            .'-'.uniqid('', true)
            .'.'.$file->guessExtension();

        $this->publicUploadsFilesystem->write(
            self::STORE_IMAGES_PATH.'/'.$newFilename,
            file_get_contents($file->getPathname())
        );

        return $newFilename;
    }
}
