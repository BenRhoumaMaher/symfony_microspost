<?php
/**
 * User.php
 *
 * This file contains the User entity class.
 *
 * @category Entities
 * @package  App\Entity
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 */

namespace App\Entity;

use App\Entity\Post;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]

/**
 * User
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
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: 'the email {{ value }} is not a valid email'
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $posts;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following')]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followers')]
    private Collection $following;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'usersThatLike')]
    #[ORM\JoinTable(name: "post_user_likes")]
    private Collection $likesPosts;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'usersThatDontLike')]
    #[ORM\JoinTable(name: "post_user_dislikes")]
    private Collection $disklikedPosts;

    /**
     * Constructor method.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->likesPosts = new ArrayCollection();
        $this->disklikedPosts = new ArrayCollection();
    }

    /**
     * Get the unique identifier of the user.
     *
     * @return int|null The ID of the user.
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Get the email address of the user.
     *
     * @return string|null The email address of the user.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
    /**
     * Set the email address of the user.
     *
     * @param string $email The email address to set.
     *
     * @return static The updated User entity.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @return string The email address of the user.
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Get the roles assigned to the user.
     *
     * @return array The roles of the user.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set the roles assigned to the user.
     *
     * @param array $roles The roles to set.
     *
     * @return static The updated User entity.
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the hashed password of the user.
     *
     * @return string The hashed password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the hashed password of the user.
     *
     * @param string $password The hashed password to set.
     *
     * @return static The updated User entity.
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Erase the user credentials.
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the name of the user.
     *
     * @return string|null The name of the user.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the user.
     *
     * @param string|null $name The name to set.
     *
     * @return static The updated User entity.
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the posts authored by the user.
     *
     * @return Collection<Post> The posts authored by the user.
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * Add a post authored by the user.
     *
     * @param Post $post The post to add.
     *
     * @return static The updated User entity.
     */
    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUser($this);
        }

        return $this;
    }

    /**
     * Remove a post authored by the user.
     *
     * @param Post $post The post to remove.
     *
     * @return static The updated User entity.
     */
    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Get the profile image of the user.
     *
     * @return Image|null The profile image of the user.
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * Set the profile image of the user.
     *
     * @param Image|null $image The profile image to set.
     *
     * @return static The updated User entity.b
     */
    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the users who are following this user.
     *
     * @return Collection<User> The users who are following this user.
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    /**
     * Add a user to the list of users whom follow this user.
     *
     * @param User $follower The user to add.
     *
     * @return static The updated User entity.
     */
    public function addFollower(self $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }

        return $this;
    }

    /**
     * Remove a user from the list of users whom follow the user.
     *
     * @param User $follower The user to remove.
     *
     * @return static The updated User entity.
     */
    public function removeFollower(self $follower): static
    {
        $this->followers->removeElement($follower);

        return $this;
    }

    /**
     * Get the users whom this user is following.
     *
     * @return Collection<User> The users whom this user is following.
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    /**
     * Add a user to the list of users whom this user is following.
     *
     * @param User $following The user to add.
     *
     * @return static The updated User entity.
     */
    public function addFollowing(self $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
            $following->addFollower($this);
        }

        return $this;
    }
    /**
     * Remove a user from the list of users whom this user is following.
     *
     * @param User $following The user to remove.
     *
     * @return static The updated User entity.
     */
    public function removeFollowing(self $following): static
    {
        if ($this->following->removeElement($following)) {
            $following->removeFollower($this);
        }

        return $this;
    }

    /**
     * Get the posts liked by this user.
     *
     * @return Collection<Post> The posts liked by this user.
     */
    public function getLikesPosts(): Collection
    {
        return $this->likesPosts;
    }

    /**
     * Add a post to the list of posts liked by this user.
     *
     * @param Post $likesPost The post to add.
     *
     * @return static The updated User entity.
     */
    public function addLikesPost(Post $likesPost): static
    {
        if (!$this->likesPosts->contains($likesPost)) {
            $this->likesPosts->add($likesPost);
        }

        return $this;
    }

    /**
     * Remove a post from the list of posts liked by this user.
     *
     * @param Post $likesPost The post to remove.
     *
     * @return static The updated User entity.
     */
    public function removeLikesPost(Post $likesPost): static
    {
        $this->likesPosts->removeElement($likesPost);

        return $this;
    }

    /**
     * Get the posts disliked by this user.
     *
     * @return Collection<Post> The posts disliked by this user.
     */
    public function getDisklikedPosts(): Collection
    {
        return $this->disklikedPosts;
    }

    /**
     * Add a post to the list of posts disliked by this user.
     *
     * @param Post $disklikedPost The post to add.
     *
     * @return static The updated User entity.
     */
    public function addDisklikedPost(Post $disklikedPost): static
    {
        if (!$this->disklikedPosts->contains($disklikedPost)) {
            $this->disklikedPosts->add($disklikedPost);
        }

        return $this;
    }

    /**
     * Remove a post from the list of posts disliked by this user.
     *
     * @param Post $disklikedPost The post to remove.
     *
     * @return static The updated User entity.
     */
    public function removeDisklikedPost(Post $disklikedPost): static
    {
        $this->disklikedPosts->removeElement($disklikedPost);

        return $this;
    }
}
