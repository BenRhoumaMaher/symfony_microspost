<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail(
            'cordelia.barton@gmail.com'
        );

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // test e.g the profile page
        $client->request('GET', 'http://127.0.0.1:8000/dashboard/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('', 'Profile');
    }
}
