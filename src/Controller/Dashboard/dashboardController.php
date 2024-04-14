<?php
/**
 * DashboardController.php
 *
 * This file contains the definition of the dashbordController class, which handles
 * actions related to the dashbord in the application.
 *
 * @category Controllers
 * @package  App\Controller\Dashboard
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\Controller\Dashboard;

use App\Entity\Image;
use App\Form\DeleteAccountFormType;
use App\Form\UserFormType;
use App\Form\ImageFormType;
use App\Form\ChangePasswordFormType;
use App\Repository\ImageRepository;
use App\Services\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * PostsController
 *
 * @category Controllers
 *
 * @package App\Controller\Dashboard
 *
 * @author Maher Ben Rhouma <maherbenrhouma@gmail.com>
 *
 * @license No license (Personal project)
 *
 * @link https://symfony.com/doc/current/controller.html
 */
class dashboardController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em              The entity manager.
     * @param ImageRepository        $imagerepository The images repository.
     * @param Security               $security        The security object.
     * @param ImageUploader          $imageuploader   The image uploader.
     *
     * @return void
     */
    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
        private ImageRepository $imagerepository,
        private Security $security,
        private ImageUploader $imageuploader
    ) {
    }

    #[Route('/dashboard', name: 'dashboard_index')]
    /**
     * Index Method to display dashboard interface
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render(
            'dashboard/index.html.twig',
            [
                'controller_name' => 'DashboardController',
            ]
        );
    }

    /**
     * Pofile Method to handle functionalities in the user's profile
     * 
     * @param Request $request The request object.
     *
     * @return Response
     */
    public function profile(Request $request): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageFormType::class, $image);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                if ($user->getImage()?->getPath()) {
                    unlink(
                        $this->getParameter(
                            'images_directory'
                        ) . '/' . $user->getImage()->getPath()
                    );
                }
                $newFilename = $this->imageuploader->upload($imageFile);
                $image->setPath($newFilename);
                if ($user->getImage()) {
                    $oldImage = $this->imagerepository->find(
                        $user->getImage()->getId()
                    );
                    $this->entityManagerInterface->remove($oldImage);
                }

                $user->setImage($image);
                $this->entityManagerInterface->persist($image);
                $this->entityManagerInterface->persist($user);
                $this->entityManagerInterface->flush();

                $this->addFlash('status-image', 'image-updated');
            }
            return $this->redirectToRoute('dashboard_index');
        }

        $userForm = $this->createForm(UserFormType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            $this->addFlash('status-profile-information', 'user-updated');
            return $this->redirectToRoute('dashboard_profile');
        }

        $passwordForm = $this->createForm(ChangePasswordFormType::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            $this->addFlash('status-password', 'password-changed');
            return $this->redirectToRoute('dashboard_profile');
        }

        $deleteAccountForm = $this->createForm(DeleteAccountFormType::class, $user);
        $deleteAccountForm->handleRequest($request);
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            $this->security->logout(false);
            $this->entityManagerInterface->remove($user);
            $this->entityManagerInterface->flush();
            $request->getSession()->invalidate();
            return $this->redirectToRoute('posts_index');
        }


        return $this->render(
            'dashboard/edit.html.twig',
            [
                'imageForm' => $form,
                'userForm' => $userForm,
                'passwordForm' => $passwordForm,
                'deleteAccountForm' => $deleteAccountForm
            ]
        );
    }
}
