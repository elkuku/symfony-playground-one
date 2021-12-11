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
    public function __construct(private UploaderHelper $uploaderHelper)
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

        $tag1 = (new Tag())
            ->setName('Pollo');
        $tag2 = (new Tag())
            ->setName('Alitas');
        $tag3 = (new Tag())
            ->setName('Pizza');
        $tag4 = (new Tag())
            ->setName('Carne');

        $manager->persist($tag1);
        $manager->persist($tag2);
        $manager->persist($tag3);
        $manager->persist($tag4);

        $logo1 = $this->uploaderHelper
            ->uploadStoreImage(new File(__DIR__.'/images/KFC-logo.png'), null);

        $logo2 = $this->uploaderHelper
            ->uploadStoreImage(new File(__DIR__.'/images/santas-alitas-logo.png'), null);

        $store1 = (new Store())
            ->setName('KFC')
            ->setImageFilename($logo1)
            ->addTag($tag1)
            ->addTag($tag2);

        $store2 = (new Store())
            ->setName('Santas Alitas')
            ->setImageFilename($logo2)
            ->addTag($tag2);

        $manager->persist($store1);
        $manager->persist($store2);

        $manager->flush();
    }
}
