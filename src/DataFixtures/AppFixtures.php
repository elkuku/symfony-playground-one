<?php

namespace App\DataFixtures;

use App\Entity\Store;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\UploaderHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

class AppFixtures extends Fixture
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            (new User())
                ->setUserIdentifier('user')
                ->setRole(User::ROLES['user'])
        );

        $manager->persist(
            (new User())
                ->setUserIdentifier('admin')
                ->setRole(User::ROLES['admin'])
        );

        $manager->flush();
    }
}
