<?php

namespace App\Twig\Components;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('likeComponent')]
final class LikeComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public Post $post;
    public $isLiked;
    public $isDisliked;

    #[LiveProp(writable: true)]
    public $likes;

    #[LiveProp(writable: true)]
    public $dislikes;

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {
    }

    #[LiveAction]
    public function like()
    {
        $this->post->addUsersThatLike($this->security->getUser());
        $this->isLiked = true;
        $this->em->persist($this->post);
        $this->em->flush();
    }

    #[LiveAction]
    public function undoLike()
    {
        $this->post->removeUsersThatLike($this->security->getUser());
        $this->isLiked = false;
        $this->em->persist($this->post);
        $this->em->flush();
    }

    #[LiveAction]
    public function dislike()
    {
        $this->post->addUsersThatDontLike($this->security->getUser());
        $this->isDisLiked = true;
        $this->em->persist($this->post);
        $this->em->flush();
    }

    #[LiveAction]
    public function undoDislike()
    {
        $this->post->removeUsersThatDontLike($this->security->getUser());
        $this->isDisLiked = false;
        $this->em->persist($this->post);
        $this->em->flush();
    }
}
