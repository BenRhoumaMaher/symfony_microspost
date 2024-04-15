<?php
/**
 * ApiController.php
 *
 * This file contains the definition of the ApiController class, which handles
 * actions related to API in the application.
 *
 * @category Controllers
 * @package  App\Controller\Api
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\Controller\Api;

use Exception;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * ApiController
 *
 * @category Controllers
 *
 * @package App\Controller\Api
 *
 * @author Maher Ben Rhouma <maherbenrhouma@gmail.com>
 *
 * @license No license (Personal project)
 *
 * @link https://symfony.com/doc/current/controller.html
 */
class ApiController extends AbstractController
{
    #[Route('/api/post/new', name: 'app_api', methods: ['POST'])]
    /**
     * Post Method to add posts to database using the api
     * 
     * @param Request                $request       The request object.
     * @param EntityManagerInterface $entityManager The entity manager.
     *
     * @return JsonResponse
     */
    public function post(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || !$data['title'] || !$data['content']) {
                throw new Exception("data not valid");
            }
            $post = new Post();
            $post->setTitle($data['title']);
            $post->setContent($data['content']);
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->json(
                [
                    'status' => 'success',
                    'message' => 'Post created successfully',
                ],
                200
            );
        } catch (\Exception $e) {
            return $this->json(
                [
                    'status' => 'error',
                    'error' => 'Post not added',
                    'message' => $e->getMessage(),
                ],
                400
            );
        }
    }

    #[Route('api/register', methods: ['POST'])]
    /**
     * Register Method to resgiter user using the api
     * 
     * @param Request                     $request        The request object.
     * @param EntityManagerInterface      $entityManager  The entity manager.
     * @param UserPasswordHasherInterface $passwordHasher The password hasher.
     *
     * @return JsonResponse
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || !$data['name'] || !$data['email'] || !$data['password']) {
                throw new Exception("data not valid");
            }
            $user = new User();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->json(
                [
                    'message' => 'User registered!',
                ],
                200
            );
        } catch (Exception $e) {
            return $this->json(
                [
                    'error' => 'User not registered!',
                    'message' => $e->getMessage()
                ],
                400
            );
        }
    }
}
