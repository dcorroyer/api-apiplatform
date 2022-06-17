<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
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
            ->setRoles((array)"ROLE_ADMIN")
        ;

        $manager->persist($user);

        $apiToken = new ApiToken();
        $apiToken->setToken("29bb05d59541ebb4ea50986a7ec6f26bc45211e23f3f1bdfe3abf27d6ffcf3513371ffef444d45f141f9a01eb2f719cb9bc51728067c108e58568f88");
        $apiToken->setUser($user);

        $manager->persist($apiToken);

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
