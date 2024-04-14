<?php
/**
 * ImageRepository.php
 *
 * This file contains the definition of the ImageRepository class
 * , which is used to manage Image entity.
 *
 * @category Repositories
 * @package  App\Repository
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2
 */

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository class for managing Image entities.
 * 
 * @extends ServiceEntityRepository<Image>
 *
 * @method   Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method   Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method   Image[]    findAll()
 * @method   Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @category Repositories
 * @package  App\Repository\AdminsRepository
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/controller.html
 * @since    PHP 8.2    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    /**
     * ImageRepository constructor.
     * 
     * @param ManagerRegistry $registry The registry service for managing entity managers.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

}
