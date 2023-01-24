<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends BaseController
{
    #[Route('/{_locale<%app.supported_locales%>}/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }
}
