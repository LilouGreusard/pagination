<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        // creations de 5 categories
        for ($j = 1; $j <= 5; $j++){
            $category = new Category();
            $category->setName($faker->word);
            $category->setSlug($faker->slug);

            $manager->persist($category);

            for ($i = 1; $i <= 300; $i++){
                $articles = new Articles();
                $articles->setTitle($faker->sentence(6, true));
                $articles->setSlug($faker->sentence(6, true));
                $articles->setAuthor($faker->name);
                $articles->setPicture($faker->imageUrl(640,480, 'abstract', true));
                $articles->setCreatedAt(new \DateTimeImmutable());
                $articles->setCategory($category);
                $articles->setPrice($faker->randomFloat(2, 5, 100));

                $manager->persist($articles);
            }
        }

        $manager->flush();
    }
}
