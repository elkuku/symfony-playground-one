<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Hello DefaultController!');

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user@example.com');
        $client->loginUser($testUser);

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h4', 'Welcome user@example.com');
    }
}
