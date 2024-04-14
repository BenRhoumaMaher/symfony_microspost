<?php

namespace App\EventListener;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class newPostListener
{

    public function __construct(private MailerInterface $mailer)
    {
    }

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