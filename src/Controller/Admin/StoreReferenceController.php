<?php

namespace App\Controller\Admin;

use App\Entity\Store;
use App\Entity\StoreReference;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StoreReferenceController extends AbstractController
{
    #[Route('/admin/store/{id}/references', name: 'admin_store_add_reference', methods: ['POST'])]
    public function uploadStoreReference(
        Store $store,
        Request $request,
        UploaderHelper $uploaderHelper,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ) {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('reference');

        $violations = $validator->validate(
            $uploadedFile,
            [
                new NotBlank([
                    'message' => 'Please select a file to upload!'
                ]),
            new File([
                'maxSize' => '5M',
                'mimeTypes' => [
                    'image/*',
                ]
            ])
            ]
        );

        if ($violations->count() > 0) {
            /** @var ConstraintViolation $violation */
            $violation = $violations[0];
            $this->addFlash('danger', $violation->getMessage());

            return $this->redirectToRoute('store_edit', [
                'id' => $store->getId(),
            ]);
        }

        $filename = $uploaderHelper->uploadStoreReference($uploadedFile);

        $storeReference = new StoreReference($store);
        $storeReference->setFilename($filename);
        $storeReference->setOriginalFilename(
            $uploadedFile->getClientOriginalName() ?? $filename
        );
        $storeReference->setMimeType(
            $uploadedFile->getMimeType() ?? 'application/octet-stream'
        );

        $entityManager->persist($storeReference);
        $entityManager->flush();

        return $this->redirectToRoute('store_edit', [
            'id' => $store->getId(),
        ]);
    }

    #[Route('/admin/store/references/{id}/download', name: 'admin_store_download_reference', methods: ['GET'])]
    public function downloadStoreReference(StoreReference $reference, UploaderHelper $uploaderHelper)
    {
        $response = new StreamedResponse(function() use ($reference, $uploaderHelper) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploaderHelper->readStream($reference->getFilePath(), false);
            stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', $reference->getMimeType());

        // $disposition = HeaderUtils::makeDisposition(
        //     HeaderUtils::DISPOSITION_ATTACHMENT,
        //     $reference->getOriginalFilename()
        // );
        //
        // $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
