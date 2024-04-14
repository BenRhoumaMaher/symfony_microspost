<?php

/**
 * NewPostListener.php
 *
 * This file contains the definition of the newpostlisterner class, 
 * which handles the sending of emails when a new post is created
 *
 * @category Entities
 * @package  App\EventListener
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\EventListener;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * User
 *
 * @category Entities
 *
 * @package App\EventListener
 *
 * @author Maher Ben Rhouma <maherbenrhouma@gmail.com>
 *
 * @license No license (Personal project)
 *
 * @link https://symfony.com/doc/current/controller.html
 */

class NewPostListener
{
    /**
     * Constructor.
     *
     * @param MailerInterface $mailer The mailer
     *
     * @return void
     */
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * Sends email notifications to all users when a new post is persisted.
     *
     * @param LifecycleEventArgs $args The event arguments.
     * 
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Post) {
            return;
        }
        $entityManager = $args->getObjectManager();
        $users = $entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $email = (new Email())
                ->from('maherbenrhouma@gmail.com')
                ->to($user->getEmail())
                ->subject('New Post from ' . $entity->getUser()->getName())
                ->html('<p>See new post ! ' . $entity->getTitle() . '</p>');
            $this->mailer->send($email);
        }
    }
}