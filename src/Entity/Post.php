<?php

/**
 * Posts.php
 *
 * This file contains the class of the Posts management page.
 *
 * @category Entities
 * @package  App\Entity
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: "posts")]

/**
 * Posts
 *
 * @category Entities
 *
 * @package App\Entity
 *
 * @author Maher Ben Rhouma <maherbenrhouma@gmail.com>
 *
 * @license No license (Personal project)
 *
 * @link https://symfony.com/doc/current/controller.html
 */
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    #[Assert\Length(max: 100, maxMessage: 'Title cannot be longer than 20 characters')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Type('string')]
    #[Assert\Length(max: 3000, maxMessage: 'Content cannot be longer than 1000 characters')]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likesPosts')]
    private Collection $usersThatLike;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'disklikedPosts')]
    private Collection $usersThatDontLike;

    /**
     * Construct of the class Posts
     */
    public function __construct()
    {
        $this->usersThatLike = new ArrayCollection();
        $this->usersThatDontLike = new ArrayCollection();
    }

    /**
     * Gets the ID of the post.
     *
     * @return int|null The ID of the post, or null if not set.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the title of the post.
     *
     * @return string|null The title of the post, or null if not set.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the post.
     *
     * @param string $title The title to set.
     *
     * @return static The updated entity instance.
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the content of the post.
     *
     * @return string|null The content of the post, or null if not set.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the content of the post.
     *
     * @param string $content The content to set.
     *
     * @return static The updated entity instance.
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the creation date of the post.
     *
     * @return \DateTimeImmutable|null The creation date of the post, or null if not set.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * Sets the creation date of the post.
     *
     * @param \DateTimeImmutable $created_at The creation date to set.
     *
     * @return static The updated entity instance.
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Gets the last update date of the post.
     *
     * @return \DateTimeImmutable|null The last update date of the post, or null if not set.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * Sets the last update date of the post.
     *
     * @param \DateTimeImmutable|null $updated_at The last update date to set.
     *
     * @return static The updated entity instance.
     */
    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Gets the user who created the post.
     *
     * @return User|null The user who created the post, or null if not set.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Sets the user who created the post.
     *
     * @param User|null $user The user to set.
     *
     * @return static The updated entity instance.
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the collection of users who liked the post.
     *
     * @return Collection<int, User> The collection of users who liked the post.
     */
    public function getUsersThatLike(): Collection
    {
        return $this->usersThatLike;
    }

    /**
     * Adds a user to the collection of users who liked the post.
     *
     * @param User $user The user to add.
     *
     * @return static The updated entity instance.
     */
    public function addUsersThatLike(User $user): static
    {
        if (!$this->usersThatLike->contains($user)) {
            $this->usersThatLike->add($user);
            $user->addLikesPost($this);
        }

        return $this;
    }

    /**
     * Removes a user from the collection of users who liked the post.
     *
     * @param User $user The user to remove.
     *
     * @return static The updated entity instance.
     */
    public function removeUsersThatLike(User $user): static
    {
        if ($this->usersThatLike->removeElement($user)) {
            $user->removeLikesPost($this);
        }

        return $this;
    }

    /**
     * Gets the collection of users who disliked the post.
     *
     * @return Collection<int, User> The collection of users who disliked the post.
     */
    public function getUsersThatDontLike(): Collection
    {
        return $this->usersThatDontLike;
    }

    /**
     * Adds a user to the collection of users who disliked the post.
     *
     * @param User $user The user to add.
     *
     * @return static The updated entity instance.
     */
    public function addUsersThatDontLike(User $user): static
    {
        if (!$this->usersThatDontLike->contains($user)) {
            $this->usersThatDontLike->add($user);
            $user->addDisklikedPost($this);
        }

        return $this;
    }

    /**
     * Removes a user from the collection of users who disliked the post.
     *
     * @param User $user The user to remove.
     *
     * @return static The updated entity instance.
     */
    public function removeUsersThatDontLike(User $user): static
    {
        if ($this->usersThatDontLike->removeElement($user)) {
            $user->removeDisklikedPost($this);
        }

        return $this;
    }
}
