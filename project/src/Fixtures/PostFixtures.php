<?php

namespace App\Fixtures;

use App\Entity\Post\Post;
use App\Repository\Post\CategoryRepository;
use App\Repository\Post\TagRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

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

        $posts = [];
        for ($i = 0; $i < 100; $i++) {
            $post = new Post();
            $post->setTitle($faker->unique()->words(4, true))
                ->setContent($faker->realText(1800))
                ->setState(mt_rand(0, 2) === 1 ? Post::STATES[0] : Post::STATES[1])
                ->setCategory($categories[mt_rand(0, count($categories) - 1)]);

            $manager->persist($post);
            $posts[] = $post;
        }

        foreach ($posts as $post) {
            for ($i = 0; $i < mt_rand(1, 5); $i++) {
                $post->addTag(
                    $tags[mt_rand(0, count($tags) - 1)]
                );
            }
        }


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryTagFixtures::class];
    }
}

