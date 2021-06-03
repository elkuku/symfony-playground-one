<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setEmail('user@example.com')
            ->setRole('ROLE_USER');

        $manager->persist($user);

        $adminUser = (new User())
            ->setEmail('admin@example.com')
            ->setRole('ROLE_ADMIN');

        $manager->persist($adminUser);

        $manager->flush();
    }
}
