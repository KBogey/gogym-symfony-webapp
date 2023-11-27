<?php

namespace App\Fixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // My user
        $user = new User();
        $user->setEmail('alice@wonderland.fr')
            ->setFirstName('Alice')
            ->setRoles(['ROLE_USER','ROLE_ADMIN'])
            ->setPassword(
                $this->hasher->hashPassword($user, 'LapinBlanc')
            );

        $manager->persist($user);

        for ($i = 0; $i < 9; $i++) {
            $user = new User();
            $user->setEmail($faker->email())
                ->setLastName($faker->lastName())
                ->setFirstName($faker->firstName())
                ->setPassword(
                    $this->hasher->hashPassword($user, 'password')
                );

            $manager->persist($user);
        }

        $manager->flush();
    }
}
