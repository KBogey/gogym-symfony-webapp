<?php

namespace App\Fixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post\Post;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $admin = new User();
        $admin->setEmail('alice@wonderland.fr');
        $admin->setRoles(['ROLE_ADMIN','ROLE_USER']);
        $passwordAdmin = $this->passwordHasher->hashPassword(
            $admin,
            'lapinBlanc'
        );
        $admin->setPassword($passwordAdmin);

        $manager->persist($admin);

        for($i =1; $i <= 20; $i++)
        {
            $user = new User();
            $user->setEmail($faker->email());
            $password = $this->passwordHasher->hashPassword($user,'secret');
            $user->setPassword($password);

            $manager->persist($user);
            $this->addReference(User::class . '_' . $i, $user);
        }

        $category1 = new Category();
        $category1->setName('Actualités');
        $manager->persist($category1);

        $this->addReference(Category::class . '_' . 1, $category1);

        $category2 = new Category();
        $category2->setName('Nutrition');
        $manager->persist($category2);

        $this->addReference(Category::class . '_' . 2, $category2);

        $category3 = new Category();
        $category3->setName('Trouver une salle');
        $manager->persist($category3);

        $this->addReference(Category::class . '_' . 3, $category3);

        for($h=1; $h <= 25; $h++)
        {
            $tag = new Tag();
            $tag->setLabel($faker->word());
            $manager->persist($tag);

            $this->addReference(Tag::class . '_' . $h, $tag);
        }

        for($k=1; $k <=50; $k++)
        {
            $post = new Post();
            $post->setTitle($faker->words(mt_rand(1,8), true));
            $post->setContent($faker->paragraphs(mt_rand(2,5), true));
            $post->setState(mt_rand(0,2) === 1 ? Post::STATES[0] : Post::STATES[1]);
            $post->setCategory($this->getReference(Category::class.'_'.mt_rand(1,3)));
            $post->addTag($this->getReference(Tag::class.'_'.mt_rand(1,25)));

            $manager->persist($post);
            $this->addReference(Post::class . '_' . $k, $post);

        }

        for($j=1; $j <= 75; $j++)
        {
            $comment = new Comment();
            $comment->setTitle($faker->words(mt_rand(1,8), true));
            $comment->setContent($faker->paragraphs(mt_rand(1,3), true));
            $comment->setPost($this->getReference(Post::class.'_'.mt_rand(1,50)));
            $comment->setUser($this->getReference(User::class. '_' . mt_rand(1,20)));

            $manager->persist($comment);
        }

        $manager->flush();
    }
}