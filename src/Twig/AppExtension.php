<?php

namespace App\Twig;

use App\Entity\User;
use App\Service\UploaderHelper;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('role_name', [$this, 'getRoleName']),
        ];
    }

    #[Pure]
    public function getRoleName(
        $value
    ): string {
        return array_search($value, User::ROLES, true);
    }
}
