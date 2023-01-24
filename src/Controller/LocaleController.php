<?php

namespace App\Controller;

use App\Service\LocaleProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends BaseController
{
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
