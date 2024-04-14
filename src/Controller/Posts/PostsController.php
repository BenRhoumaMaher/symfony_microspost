<?php
/**
 * PostsController.php
 *
 * This file contains the definition of the PostsController class, which handles
 * actions related to posts in the application.
 *
 * @category Controllers
 * @package  App\Controller\Posts
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\Controller\Posts;

use Pusher\Pusher;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use DateTimeImmutable;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * PostsController
 *
 * @category Controllers
 *
 * @package App\Controller\Posts
 *
 * @author Maher Ben Rhouma <maherbenrhouma@gmail.com>
 *
 * @license No license (Personal project)
 *
 * @link https://symfony.com/doc/current/controller.html
 */

class PostsController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em             The entity manager.
     * @param UserRepository         $userRepository The user repository.
     * @param PostRepository         $postRepository The posts repository.
     *
     * @return void
     */
    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
        private UserRepository $userRepository,
        private PostRepository $postRepository
    ) {

    }

    /**
     * Index Method to display all the posts
     * 
     * @param Request $request The request object.
     * 
     * @return Response
     */
    public function index(Request $request): Response
    {
        $posts = $this->postRepository->findAllPosts($request->query->getInt('page', 1));
        // dd($posts);
        return $this->render(
            'posts/index.html.twig',
            [
                'posts' => $posts
            ]
        );
    }

    /**
     * Create new post
     * 
     * @param Request $request The request object.
     * @param Pusher  $pusher  The pusher object.
     *
     * @return Response
     */
    public function new(Request $request, Pusher $pusher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $post->setTitle('write a post title');
        $post->setContent('post content');
        $post->setUser($this->getUser());
        $post->setCreatedAt(new DateTimeImmutable());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $post = $form->getData();
            $this->entityManagerInterface->persist($post);
            $this->entityManagerInterface->flush();
            $pusher->trigger('my-channel', 'new-post-event', 'New post: <a href="' . $this->generateUrl('posts_show', ["id" => $post->getId()]) . '">' . $post->getTitle() . '</a>');
            return $this->redirectToRoute('posts_index');
        }
        return $this->render(
            'posts/new.html.twig',
            [
                'form' => $form
            ]
        );
    }

    /**
     * Displaying single post
     *
     * @param mixed $post The posts entity.
     *
     * @return Response
     */
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
        return $this->render(
            'posts/show.html.twig',
            [
                'post' => $post,
                'isFollowing' => $isFollowing,
                'isLiked' => $isLiked,
                'isDisliked' => $isDisliked
            ]
        );
    }

    /**
     * Editing a post
     *
     * @param mixed   $post    The posts entity.
     * @param Request $request The request object.
     *
     * @return Response
     */
    public function edit(Post $post, Request $request): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('POST_EDIT', $post);
        $post->setUpdatedAt(new \DateTimeImmutable('now'));
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $this->entityManagerInterface->persist($post);
            $this->entityManagerInterface->flush();
            return $this->redirectToRoute('posts_index');
        }
        return $this->render(
            'posts/edit.html.twig',
            [
                'form' => $form
            ]
        );
    }

    /**
     * Deleting a post
     *
     * @param mixed $post The posts entity.
     *
     * @return Response
     */
    public function delete(Post $post): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('POST_DELETE', $post);
        $this->entityManagerInterface->remove($post);
        $this->entityManagerInterface->flush();
        return $this->redirectToRoute('posts_index');
    }

    /**
     * Showing use profile
     *
     * @param Request $request The request object.
     * @param int     $id      The loggedin user id.
     *
     * @return Response
     */
    public function user(Request $request, $id): Response
    {
        $posts = $this->postRepository->findAllUserPosts(
            $request->query->getInt('page', 1),
            $id
        );
        return $this->render(
            'posts/index.html.twig',
            [
                'posts' => $posts,
                'user' => $posts[0]?->getUser()->getName()
            ]
        );
    }

    /**
     * Toggle the following of users
     *
     * @param User    $user    The user object.
     * @param Request $request The request object.
     *
     * @return Response
     */
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
        $this->entityManagerInterface->flush();
        $route = $request->headers->get('referer');
        return $this->redirect($route);
    }
}
