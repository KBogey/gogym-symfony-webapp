<?php

namespace App\Fixtures;

use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryTagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Category
        $category1 = new Category();
        $category1->setName('Actualités');
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('Nutrition');
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName('Trouver une salle');
        $manager->persist($category3);

        // Tag
        for ($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $tag->setLabel($faker->words(1, true) . ' ' . $i);

            $manager->persist($tag);
        }

        $manager->flush();
    }
}

