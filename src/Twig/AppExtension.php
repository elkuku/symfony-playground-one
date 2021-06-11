<?php

namespace App\Twig;

use App\Entity\User;
use JetBrains\PhpStorm\Pure;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

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
