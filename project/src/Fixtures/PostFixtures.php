<?php

namespace App\Fixtures;

use App\Repository\TagRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use App\Entity\Post\Post;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository
    ){}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = $this->categoryRepository->findAll();
        $tags = $this->tagRepository->findAll();

        for ($i = 0; $i < 150; $i++) {
            $post = new Post();
            $post->setTitle($faker->words(4, true))
                ->setContent($faker->realText(1800))
                ->setState(mt_rand(0, 2) === 1 ? Post::STATES[0] : Post::STATES[1])
                ->setCategory($categories[mt_rand(0, count($categories) - 1)]);

                for ($i = 0; $i < mt_rand(1, 5); $i++) {
                    $post->addTag(
                        $tags[mt_rand(0, count($tags) - 1)]
                    );
                }

            $manager->persist($post);
        }


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryTagFixtures::class];
    }
}

