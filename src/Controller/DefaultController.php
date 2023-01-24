<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserParamsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends BaseController
{
    #[Route('/')]
    public function indexNoLocale(SessionInterface $session): Response
    {
        $locale = $session->get('_locale');
        if ($locale) {
            dd($locale);
        }
        return $this->redirectToRoute('default', ['_locale' => 'en']);
    }

    #[Route('/{_locale<%app.supported_locales%>}/', name: 'default', methods: ['GET'])]
    public function index(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        TranslatorInterface $translator,
    ): Response {
        return $this->render(
            'default/index.html.twig',
            [
                'controller_name' => 'DefaultController',
                'php_version' => PHP_VERSION,
                'symfony_version' => Kernel::VERSION,
                'project_dir' => $projectDir,
                'translatedMessage' => $translator->trans('Symfony is great'),
            ]
        );
    }

    #[Route('/{_locale<%app.supported_locales%>}/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    #[IsGranted(User::ROLES['user'])]
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(UserParamsType::class, $user->getParams());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setParams($form->getData());

            $entityManager->flush();

            $this->addFlash('success', 'User data have been saved.');

            return $this->redirectToRoute('default');
        }

        return $this->render('default/profile.html.twig', [
            'form' => $form,
        ]);
    }
}
