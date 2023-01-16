<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class LocaleProvider
{
    /**
     * @var string[]
     */
    private array $locales;

    public function __construct(
        #[Autowire('%env(APP_LOCALES)%')] string $locales,
        #[Autowire('%env(APP_DEFAULT_LOCALE)%')] private readonly string $defaultLocale,
    )
    {
        $this->locales = explode(',', $locales);
}

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * @return string[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }
}
