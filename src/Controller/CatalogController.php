<?php

namespace App\Controller;

use App\Entity\Store;
use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/catalog')]
class CatalogController extends AbstractController
{
    #[Route('/bytag/{name}', name: 'app_catalog_by_tag', methods: ['GET'])]
    public function byTag(
        Tag $tag,
    ): Response {
        return $this->render(
            'catalog/bytag.html.twig',
            [
                'tag' => $tag,
            ]
        );
    }

    #[Route('/store/{name}', name: 'app_catalog_store', methods: ['GET'])]
    public function store(Store $store): Response
    {
        return $this->render(
            'catalog/store.html.twig',
            [
                'store' => $store,
            ]
        );

    }

}
