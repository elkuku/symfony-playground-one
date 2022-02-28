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
            new TwigFilter('role_name', $this->getRoleName(...)),
            new TwigFilter('role_names', $this->getRoleNames(...)),
        ];
    }

    /**
     * @param array<string> $values
     */
    public function getRoleNames(array $values): string
    {
        $roles = [];
        foreach ($values as $value) {
            $roles[] = $this->getRoleName($value);
        }

        return implode(', ', $roles);
    }

    public function getRoleName(string $value): string
    {
        return array_search($value, User::ROLES, true) ?: '';
    }
}
