<?php
/**
 * PostRepository.php
 *
 * This file contains the definition of the PostRepository class
 * , which is used to manage Post entity.
 *
 * @category Repositories
 * @package  App\Repository
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Repository class for managing Post entities.
 * 
 * @extends ServiceEntityRepository<Post>
 *
 * @method   Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method   Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method   Post[]    findAll()
 * @category Repositories
 * @package  App\Repository\AdminsRepository
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    /**
     * PostRepository constructor.
     * 
     * @param ManagerRegistry    $registry  The registry service for managing entity managers.
     * @param PaginatorInterface $paginator The paginator service for managing pagination.
     */
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Find all posts.
     * 
     * @param int $page The page number.
     * 
     * @return PaginatorInterface
     */
    public function findAllPosts(int $page)
    {
        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->leftJoin('p.usersThatLike', 'l')
            ->addSelect('COUNT(l) AS HIDDEN likes')
            ->orderBy('likes', 'DESC')
            ->groupBy('p')
            ->getQuery()
            ->getResult();

        return $this->paginator->paginate($query, $page, 3);
    }

    /**
     * Find all user posts.
     * 
     * @param int $page   The page number.
     * @param int $userId The user id.
     * 
     * @return PaginatorInterface
     */
    public function findAllUserPosts(int $page, $userId)
    {
        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->addSelect('COUNT(l) AS HIDDEN likes')
            ->leftJoin('p.usersThatLike', 'l')
            ->orderBy('likes', 'DESC')
            ->where('p.user = :id')
            ->setParameter('id', $userId)
            ->groupBy('p')
            ->getQuery()
            ->getResult();
        return $this->paginator->paginate($query, $page, 3);
    }

    /**
     * Find the liked post.
     * 
     * @param User $authUser The page number.
     * @param int  $postId   The post id.
     * 
     * @return PaginatorInterface
     */
    public function isLiked($authUser, $postId): array
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.id')
            ->andWhere('p.id = :postId')
            ->andWhere('usersThatLike.id = :authUser')
            ->innerJoin('p.usersThatLike', 'usersThatLike')
            ->setParameter('authUser', $authUser)
            ->setParameter('postId', $postId)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        return $query;
    }

    /**
     * Find the disliked post.
     * 
     * @param User $authUser The page number.
     * @param int  $postId   The post id.
     * 
     * @return PaginatorInterface
     */
    public function isDisLiked($authUser, $postId): array
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.id')
            ->andWhere('p.id = :postId')
            ->andWhere('usersThatDontLike.id = :authUser')
            ->innerJoin('p.usersThatDontLike', 'usersThatDontLike')
            ->setParameter('authUser', $authUser)
            ->setParameter('postId', $postId)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        return $query;
    }

    /**
     * Search the posts.
     * 
     * @param string $query The search query.
     * 
     * @return PaginatorInterface
     */
    public function searchPosts(string $query)
    {
        $querybuilder = $this->createQueryBuilder('p');
        $searchTerms = $this->_prepareQuery($query);
        foreach ($searchTerms as $key => $term) {
            $querybuilder
                ->orWhere('p.title LIKE :t_' . $key)
                ->orWhere('p.content LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . trim($term) . '%');
        }
        $dbquery = $querybuilder
            ->select('p.title', 'p.id')
            ->getQuery()
            ->getResult();
        return $dbquery;
    }

    /**
     * Prepare the search query.
     * 
     * @param string $query The search query.
     * 
     * @return PaginatorInterface
     */
    private function _prepareQuery(string $query): array
    {
        $terms = array_unique(explode(' ', $query));

        return array_filter(
            $terms,
            function ($term) {
                return 2 <= mb_strlen($term);
            }
        );
    }
}
