<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Post;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        for($a = 0; $a < 3; $a++) {
            $author = new Author();

            $author->setName($faker->firstName());

            $manager->persist($author);

            for($p = 0; $p < mt_rand(10, 20); $p++) {
                $post = new Post();

                $post->setTitle($faker->title())
                    ->setContent($faker->text())
                    ->setPublishedAt(new DateTimeImmutable())
                    ->setStatus($faker->randomElement(['draft', 'published', 'deleted']))
                    ->setAuthor($author)
                ;

                $manager->persist($post);

                }
            }

        $manager->flush();
    }
}
