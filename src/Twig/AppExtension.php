<?php

namespace App\Twig;

use App\Entity\User;
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

    public function getRoleName($value): string
    {
        return array_search($value, User::ROLES, true);
    }
}
