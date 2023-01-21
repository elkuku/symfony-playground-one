<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserParamsType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LocaleProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends BaseController
{
    #[Route('/', name: 'default', methods: ['GET'])]
    public function index(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        #[Autowire('%env(APP_ENV)%')] string $appEnv,
    ): Response {
        return $this->render(
            'default/index.html.twig',
            [
                'controller_name' => 'DefaultController',
                'php_version' => PHP_VERSION,
                'symfony_version' => Kernel::VERSION,
                'project_dir' => $projectDir,
                'app_env' => $appEnv,
                'translatedMessage' => $translator->trans('Symfony is great'),
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

    #[Route(
        path: '/{_locale}/about',
        name: 'about',
        requirements: [
            '_locale' => 'en|es|de',
        ],
    )]
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }

    #[Route('/switch-locale/{locale}', name: 'switch_locale', methods: ['GET'])]
    public function switchLocale(
        string $locale,
        SessionInterface $session,
        Request $request,
        LocaleProvider $localeProvider,
    ): RedirectResponse {
        $locale = in_array($locale, $localeProvider->getLocales())
            ? $locale
            : $localeProvider->getDefaultLocale();
        $session->set('_locale', $locale);
        $request->setLocale($locale);

        // This won't work if the referer already includes a locale parameter e.g. https://domain.tld/en/about
        // return $this->redirect($request->headers->get('referer'));
        return $this->redirectToRoute('default');
    }
}
