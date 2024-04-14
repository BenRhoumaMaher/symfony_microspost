<?php
/**
 * UserRepository.php
 *
 * This file contains the definition of the UserRepository class
 * , which is used to manage User entity.
 *
 * @category Repositories
 * @package  App\Repository
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Repository class for managing User entities.
 * 
 * @extends    ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 *
 * @method   User|null find($id, $lockMode = null, $lockVersion = null)
 * @method   User|null findOneBy(array $criteria, array $orderBy = null)
 * @method   User[]    findAll()
 * @method   User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @category Repositories
 * @package  App\Repository\AdminsRepository
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{

    /**
     * PostRepository constructor.
     * 
     * @param ManagerRegistry $registry The registry service for managing entity managers.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find if user following
     * 
     * @param User $authUser The authenticated user.
     * @param User $user     The user to check if following.
     * 
     * @return array
     */
    public function isFollowing($authUser, $user): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.id')
            ->andWhere('u.id = :authUser')
            ->andWhere('following = :user')
            ->innerJoin('u.following', 'following')
            ->setParameter('authUser', $authUser)
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }


    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * 
     * @param UserInterface $user              The user.
     * @param string        $newHashedPassword The new hashed password.
     * 
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
