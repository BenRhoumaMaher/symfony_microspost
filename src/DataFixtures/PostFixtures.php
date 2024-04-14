<?php

/**
 * PostFixtures.php
 *
 * This file contains the definition of the PostFixtures class, which is responsible for
 * loading dummy data into the database for testing purposes.
 *
 * @category Fixtures
 * @package  App\DataFixtures
 * @author   Maher Ben Rhouma <maherbenrhouma@gmail.com>
 * @license  No license (Personal project)
 * @link     https://symfony.com/doc/current/doctrine.html#fixtures
 * @since    PHP 8.2
 */

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * PostFixtures
 *
 * @category Fixtures
 *
 * @package App\DataFixtures
 *
 * @author Maher Ben Rhouma <maherbenrhouma@gmail.com>
 *
 * @license No license (Personal project)
 *
 * @link https://symfony.com/doc/current/doctrine.html#fixtures
 */

class PostFixtures extends Fixture
{
    /**
     * Constructor for PostFixtures class
     *
     * @param UserPasswordHasherInterface $hasher The user password hasher interface
     *
     * @return void
     */
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    /**
     * Load method to load dummy data into the database
     *
     * @param ObjectManager $manager The object manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i <= 3; $i++) {
            $user = new User();
            $email = $faker->email;
            $user->setEmail($email);
            $user->setPassword($this->hasher->hashPassword($user, $email));
            $user->setName($faker->name);
            $manager->persist($user);

            for ($j = 0; $j <= 5; $j++) {
                $post = new Post();
                $post->setTitle($faker->sentence(10));
                $post->setContent($faker->text(3000));
                $number = $faker->numberBetween(-100, -2);
                $post->setCreatedAt(new \DateTimeImmutable($number . ' days'));
                $post->setUser($user);
                $manager->persist($post);
            }

        }

        $manager->flush();
    }
}
