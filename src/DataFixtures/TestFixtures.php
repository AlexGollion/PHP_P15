<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Album;
use App\Entity\Media;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setName('User ' . $i);
            $user->setEmail('user' . $i . '@example.com');
            $user->setAdmin(false);
            $password = 'password';
            $hashPassword = $this->hasher->hashPassword($user, $password);
            $user->setPassword($hashPassword);
            $manager->persist($user);
        }

        $admin = new User();
        $admin->setName('ina');
        $admin->setEmail('ina@example');
        $admin->setAdmin(true);
        $password = 'password';
        $hashPassword = $this->hasher->hashPassword($admin, $password);
        $admin->setPassword($hashPassword);
        $manager->persist($admin);

        $albums = [];
        for ($i = 0; $i < 5; $i++) {
            $album = new Album();
            $album->setName('Album ' . $i);
            $albums[] = $album;
            $manager->persist($album);
        }    

        for ($i = 0; $i < 10; $i++) {
            $media = new Media();
            $media->setTitle('Media ' . $i);
            $media->setPath($faker->imageUrl());
            $media->setAlbum($albums[0]);
            $media->setUser($admin);
            $manager->persist($media);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}