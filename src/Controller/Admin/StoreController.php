<?php

namespace App\Controller\Admin;

use App\Entity\Store;
use App\Form\ReferenceType;
use App\Form\StoreType;
use App\Repository\StoreRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/store')]
class StoreController extends AbstractController
{
    #[Route('/', name: 'store_index', methods: ['GET'])]
    public function index(StoreRepository $storeRepository): Response
    {
        return $this->render('store/index.html.twig', [
            'stores' => $storeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'store_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UploaderHelper $uploaderHelper
    ): Response {
        $store = new Store();
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadStoreImage($uploadedFile, $store->getImageFilename());
                $store->setImageFilename($newFilename);
            }

            $entityManager->persist($store);
            $entityManager->flush();

            return $this->redirectToRoute(
                'store_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('store/new.html.twig', [
            'store' => $store,
            'form'  => $form,
        ]);
    }

    #[Route('/{id}', name: 'store_show', methods: ['GET'])]
    public function show(Store $store): Response
    {
        return $this->render('store/show.html.twig', [
            'store' => $store,
        ]);
    }

    #[Route('/{id}/edit', name: 'store_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Store $store,
        EntityManagerInterface $entityManager,
        UploaderHelper $uploaderHelper
    ): Response {
        $form = $this->createForm(StoreType::class, $store);
        $referenceForm = $this->createForm(ReferenceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadStoreImage($uploadedFile, $store->getImageFilename());
                $store->setImageFilename($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute(
                'store_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('store/edit.html.twig', [
            'store' => $store,
            'form'  => $form,
            'referenceForm' => $referenceForm,
        ]);
    }

    #[Route('/{id}', name: 'store_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Store $store,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete'.$store->getId(),
            $request->request->get('_token')
        )
        ) {
            $entityManager->remove($store);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'store_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
