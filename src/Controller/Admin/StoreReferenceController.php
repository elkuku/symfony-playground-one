<?php

namespace App\Controller\Admin;

use App\Entity\Store;
use App\Entity\StoreReference;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
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
                    'message' => 'Please select a file to upload!',
                ]),
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                        'image/*',
                    ],
                ]),
            ]
        );

        if ($violations->count() > 0) {
            return $this->json($violations, 400);
        }

        $filename = $uploaderHelper->uploadStoreReference($uploadedFile);

        $storeReference = (new StoreReference($store))
            ->setFilename($filename)
            ->setOriginalFilename(
                $uploadedFile->getClientOriginalName() ?? $filename
            )
            ->setMimeType(
                $uploadedFile->getMimeType() ?? 'application/octet-stream'
            );

        $entityManager->persist($storeReference);
        $entityManager->flush();

        return $this->json(
            $storeReference,
            201,
            [],
            [
                'groups' => ['main'],
            ]
        );
    }

    #[Route('/admin/store/references/{id}/download', name: 'admin_store_download_reference', methods: ['GET'])]
    public function downloadStoreReference(
        StoreReference $reference,
        UploaderHelper $uploaderHelper
    ) {
        $response = new StreamedResponse(
            function () use ($reference, $uploaderHelper) {
                $outputStream = fopen('php://output', 'wb');
                $fileStream = $uploaderHelper->readStream(
                    $reference->getFilePath(),
                    false
                );
                stream_copy_to_stream($fileStream, $outputStream);
            }
        );

        $response->headers->set('Content-Type', $reference->getMimeType());

        // $disposition = HeaderUtils::makeDisposition(
        //     HeaderUtils::DISPOSITION_ATTACHMENT,
        //     $reference->getOriginalFilename()
        // );
        //
        // $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    #[Route('/admin/store/{id}/references', name: 'admin_store_list_references', methods: ['GET'])]
    public function getStoreReferences(Store $store)
    {
        return $this->json(
            $store->getStoreReferences(),
            200,
            [],
            [
                'groups' => ['main'],
            ]
        );
    }

    #[Route('/admin/store/references/{id}', name: 'admin_store_delete_reference', methods: ['DELETE'])]
    public function deleteStoreReference(
        StoreReference $reference,
        UploaderHelper $uploaderHelper,
        EntityManagerInterface $entityManager
    ): Response {
        $store = $reference->getStore();
        // $this->denyAccessUnlessGranted('MANAGE', $store);

        $entityManager->remove($reference);
        $entityManager->flush();

        try {
            $uploaderHelper->deleteFile($reference->getFilePath(), false);
        } catch (\Exception $e) {
        }

        return new Response(null, 204);
    }

    #[Route('/admin/store/references/{id}', name: 'admin_store_update_reference', methods: ['PUT'])]
    public function updateStoreReference(
        StoreReference $reference,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        Request $request,
        ValidatorInterface $validator,
    ): JsonResponse {
        // $store = $reference->getStore();
        // $this->denyAccessUnlessGranted('MANAGE', $store);

        $serializer->deserialize(
            $request->getContent(),
            StoreReference::class,
            'json',
            [
                'object_to_populate' => $reference,
                'groups'             => ['input'],
            ]
        );

        $violations = $validator->validate($reference);

        if ($violations->count() > 0) {
            return $this->json($violations, 400);
        }

        $entityManager->persist($reference);
        $entityManager->flush();

        return $this->json(
            $reference,
            200,
            [],
            [
                'groups' => ['main'],
            ]
        );
    }

    #[Route('/admin/store/{id}/references/reorder', name: 'admin_store_reorder_reference', methods: ['POST'])]
    public function reorderStoreReferences(
        Store $store,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $orderedIds = json_decode(
            $request->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if ($orderedIds === null) {
            return $this->json(['detail' => 'Invalid body'], 400);
        }

        // from (position)=>(id) to (id)=>(position)
        $orderedIds = array_flip($orderedIds);

        foreach ($store->getStoreReferences() as $reference) {
            $reference->setPosition($orderedIds[$reference->getId()]);
        }

        $entityManager->flush();

        return $this->json(
            $store->getStoreReferences(),
            200,
            [],
            [
                'groups' => ['main'],
            ]
        );
    }
}
