<?php
/**
 * PostControllerTest.php
 *
 * This file contains the PostControllerTest class, which tests the Posting functionality of the application.
 *
 * @category Tests
 * @package  App\Tests\Controller
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    [Version Number]
 */
namespace App\Tests\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * PostControllerTest
 *
 * This class contains test cases for the posting functionality of the application.
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
class PostControllerTest extends WebTestCase
{

    /**
     * Test the displaying text.
     *
     * @return void
     */
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Latest posts');
    }

    /**
     * Test seeing content after registering.
     *
     * @return void
     */
    public function testSeeContent(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Register');
        $this->assertSelectorTextNotContains('h1', 'absc');
    }

    /**
     * Test the Creating new post.
     *
     * @return void
     */
    public function testCreatePost(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        //retrieve the test user
        $testUser = $userRepository->findOneByEmail(
            'cordelia.barton@gmail.com'
        );

        //simulate $testUser being logged in
        $client->loginUser($testUser);

        $entityManger = static::getContainer()->get(EntityManagerInterface::class);

        $post = new Post();
        $post->setUser($testUser);
        $post->setCreatedAt(new \DateTimeImmutable('now'));
        $post->setTitle('post title');
        $post->setContent('post content');
        $entityManger->persist($post);
        $entityManger->flush();

        //test e.g the profile page
        $client->request('GET', "http://127.0.0.1:8000/post/edit/{$post->getId()}");
        $this->assertInputValueSame('post[title]', 'post title');
    }

    /**
     * Test counting number of posts in the database.
     *
     * @return void
     */
    public function testDatabaseCount(): void
    {
        $postRepository = static::getContainer()->get(PostRepository::class);
        $totalPost = $postRepository->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(27, $totalPost);
    }

    /**
     * Test adding new post.
     *
     * @return void
     */
    public function testAddPost(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail(
            'cordelia.barton@gmail.com'
        );
        $client->loginUser($testUser);
        $crawler = $client->request(
            'GET',
            'http://127.0.0.1:8000/dashboard/profile'
        );
        $this->assertSelectorTextContains('h2', 'Profile Information');
        $form = $crawler->selectButton('Save')->form(
            [
                'user_form[name]' => 'new name',
            ]
        );
        $client->submit($form);
        $us = $userRepository->findOneBy(
            [
                'name' => 'new name',
            ]
        );
        $this->assertNotNull($us);
        $this->assertSame('new name', $us->getName());
    }

    /**
     * Test registreting using api.
     *
     * @return void
     */
    public function testApiRegister(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            'http://127.0.0.1:8000/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name":"mahers","email":"emailtests@gmail.com","password":"emailtests"}'
        );
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test loggin using api.
     *
     * @return void
     */
    public function testApiLogin(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            'http://127.0.0.1:8000/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"cordelia.barton@gmail.com",
                "password":"cordelia.barton@gmail.com"}'
        );
        $response = $client->getResponse();
        dump(json_decode($response->getContent(), true)['token']);
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test add post using api.
     *
     * @return void
     */
    public function testApiLoginAddPost(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            'http://127.0.0.1:8000/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"cordelia.barton@gmail.com",
                "password":"cordelia.barton@gmail.com"}'
        );
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $client->request(
            'POST',
            'http://127.0.0.1:8000/api/post/new',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . json_decode($response->getContent(), true)
                ['token']
            ],
            '{"title":"post title", "content":"post content"}'
        );
        $reponse = $client->getResponse();
        $this->assertSame(200, $reponse->getStatusCode());
    }
}
