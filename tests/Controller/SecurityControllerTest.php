<?php

namespace App\tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client = null;

    public function testIndex(): void
    {
        $this->client = static::createClient();
        $this->client->request('GET', '/login');
        static::assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testLogout(): void
    {
        $this->client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('mail@mail.com');

        $this->client->loginUser($testUser);
        $this->client->request('GET', '/logout');
        static::assertEquals(
            Response::HTTP_FOUND,
            $this->client->getResponse()->getStatusCode()
        );
    }
}
