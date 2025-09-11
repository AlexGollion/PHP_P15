<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setName('User ' . $i);
            $user->setEmail('user' . $i . '@example.com');
            $user->setAdmin(false);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
