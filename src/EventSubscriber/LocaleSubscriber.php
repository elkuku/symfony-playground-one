<?php

namespace App\EventSubscriber;

use App\Service\LocaleProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LocaleProvider $localeProvider)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $locale = $this->checkLocale($locale);
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $locale = $request->getSession()->get(
                '_locale',
                $this->localeProvider->getDefaultLocale()
            );
            $locale = $this->checkLocale($locale);
            $request->setLocale($locale);
        }
    }

    private function checkLocale(mixed $locale): string
    {
        if (false === is_string($locale)) {
            throw new \UnexpectedValueException(
                '_locale is not a string :('
            );
        }

        return in_array($locale, $this->localeProvider->getLocales(), true)
            ? $locale
            : $this->localeProvider->getDefaultLocale();
    }
}
