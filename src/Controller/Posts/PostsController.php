<?php

namespace App\Controller\Posts;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pusher\Pusher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private PostRepository $postRepository
    ) {

    }


    public function index(Request $request): Response
    {
        $posts = $this->postRepository->findAllPosts($request->query->getInt('page', 1));
        // dd($posts);
        return $this->render('posts/index.html.twig', [
            'posts' => $posts
        ]);
    }

    public function new(Request $request, Pusher $pusher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $post->setTitle('write a post title');
        $post->setContent('post content');
        $post->setUser($this->getUser());
        $post->setCreatedAt(new \DateTimeImmutable());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $post = $form->getData();
            $this->em->persist($post);
            $this->em->flush();
            $pusher->trigger('my-channel', 'new-post-event', 'New post: <a href="' . $this->generateUrl('posts_show', ["id" => $post->getId()]) . '">' . $post->getTitle() . '</a>');
            return $this->redirectToRoute('posts_index');
        }
        return $this->render('posts/new.html.twig', [
            'form' => $form
        ]);
    }

    public function show(Post $post): Response
    {
        $isFollowing = $this->userRepository->isFollowing(
            $this->getUser(),
            $post->getUser()
        ) ?? false;
        $isLiked = $this->postRepository->isLiked(
            $this->getUser(),
            $post->getId()
        ) ?? false;
        $isDisliked = $this->postRepository->isDisLiked(
            $this->getUser(),
            $post->getId()
        ) ?? false;
        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'isFollowing' => $isFollowing,
            'isLiked' => $isLiked,
            'isDisliked' => $isDisliked
        ]);
    }

    public function edit(Post $post, Request $request): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('POST_EDIT', $post);
        $post->setUpdatedAt(new \DateTimeImmutable('now'));
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute('posts_index');
        }
        return $this->render('posts/edit.html.twig', [
            'form' => $form
        ]);
    }

    public function delete(Post $post): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('POST_DELETE', $post);
        $this->em->remove($post);
        $this->em->flush();
        return $this->redirectToRoute('posts_index');
    }

    public function user(Request $request, $id): Response
    {
        $posts = $this->postRepository->findAllUserPosts(
            $request->query->getInt('page', 1),
            $id
        );
        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
            'user' => $posts[0]?->getUser()->getName()
        ]);
    }

    public function toggleFollow(User $user, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $isFollowing = $this->userRepository->isFollowing(
            $this->getUser(),
            $user
        ) ?? false;
        if ($isFollowing) {
            $this->getUser()->removeFollowing($user);
        } else {
            $this->getUser()->addFollowing($user);
        }
        $this->em->flush();
        $route = $request->headers->get('referer');
        return $this->redirect($route);
    }
}
