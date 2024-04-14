<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiController extends AbstractController
{
    #[Route('/api/post/new', name: 'app_api', methods: ['POST'])]
    public function post(Request $requestn, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $data = json_decode($requestn->getContent(), true);
            if (!$data || !$data['title'] || !$data['content']) {
                throw new \Exception("data not valid");
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
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || !$data['name'] || !$data['email'] || !$data['password']) {
                throw new \Exception("data not valid");
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
        } catch (\Exception $e) {
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
