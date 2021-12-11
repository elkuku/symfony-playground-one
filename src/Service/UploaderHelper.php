<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    public const STORE_IMAGES_PATH = 'store_images';

    public function __construct(
        private string $uploadsPath,
        private RequestStackContext $requestStackContext
    ) {
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$path;
    }

    public function uploadStoreImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadsPath.'/'.self::STORE_IMAGES_PATH;

        $originalFilename = pathinfo(
            $uploadedFile->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'
            .$uploadedFile->guessExtension();
        $uploadedFile->move(
            $destination,
            $newFilename
        );

        return $newFilename;
    }
}
