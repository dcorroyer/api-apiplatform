<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Post;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");

        $user->setEmail("admin@api.com")
            ->setPassword($hash)
        ;

        $manager->persist($user);

        for($a = 0; $a < 3; $a++) {
            $author = new Author();

            $author->setName($faker->firstName());

            $manager->persist($author);

            for($p = 0; $p < mt_rand(10, 20); $p++) {
                $post = new Post();

                $post->setTitle($faker->title())
                    ->setContent($faker->text())
                    ->setPublishedAt(new DateTime())
                    ->setStatus($faker->randomElement(['draft', 'published', 'deleted']))
                    ->setAuthor($author)
                ;

                $manager->persist($post);

                }
            }

        $manager->flush();
    }
}
