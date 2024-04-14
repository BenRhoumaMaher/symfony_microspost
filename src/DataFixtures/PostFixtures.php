<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }
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
