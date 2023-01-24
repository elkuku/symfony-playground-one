<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Locales;

class LocaleProvider
{
    /**
     * @var string[]
     */
    private array $locales;

    public function __construct(
        #[Autowire('%env(APP_LOCALES)%')] string $locales,
        #[Autowire('%env(APP_DEFAULT_LOCALE)%')] private readonly string $defaultLocale,
    ) {
        $this->locales = explode('|', $locales);
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

    /**
     * @return array<string, string>
     */
    public function getSelectValues(): array
    {
        $values = [];
        foreach ($this->locales as $locale) {
            try {
                $name = Locales::getName($locale);
            } catch (MissingResourceException) {
                $name = $locale;
            }
            $values[$name] = $locale    ;
        }

        return $values;
    }
}
