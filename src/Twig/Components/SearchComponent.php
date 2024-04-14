<?php

namespace App\Twig\Components;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('searchComponent')]
final class SearchComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
        private EntityManagerInterface $em,
        private PostRepository $repository
    ) {
    }

    public function getPosts(): array
    {
        return $this->query ? $this->repository->searchPosts($this->query) : [];
    }

}
