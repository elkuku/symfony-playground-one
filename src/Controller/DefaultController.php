<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserParamsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends BaseController
{
    #[Route('/', name: 'default', methods: ['GET'])]
    public function index(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
    ): Response {
        return $this->render(
            'default/index.html.twig',
            [
                'controller_name' => 'DefaultController',
                'php_version' => PHP_VERSION,
                'symfony_version' => Kernel::VERSION,
                'project_dir' => $projectDir,
            ]
        );
    }

    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    #[IsGranted(User::ROLES['user'])]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
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
