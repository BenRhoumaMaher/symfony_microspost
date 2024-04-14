<?php

/**
 * LoginControllerTest.php
 *
 * This file contains the LoginControllerTest class, which tests the login functionality of the application.
 *
 * @category Tests
 * @package  App\Tests\Controller
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    [Version Number]
 */

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * LoginControllerTest
 *
 * This class contains test cases for the login functionality of the application.
 *
 * @category Tests
 *
 * @package App\Tests\Controller
 *
 * @author Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * 
 * @license No license (Personal project)
 * 
 * @link https://symfony.com/doc/current/controller.html
 */

class LoginControllerTest extends WebTestCase
{
    /**
     * Test the login functionality.
     *
     * @return void
     */
    public function testSomething(): void
    {
        // Create a client for making requests
        $client = static::createClient();

        // Get the user repository from the container
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user from the repository
        $testUser = $userRepository->findOneBy(
            ['email' => 'cordelia.barton@gmail.com']
        );

        // Log in the test user
        $client->loginUser($testUser);

        // Request the profile page
        $client->request('GET', 'http://127.0.0.1:8000/dashboard/');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Assert that the profile page contains the text 'Profile'
        $this->assertSelectorTextContains('', 'Profile');
    }
}
