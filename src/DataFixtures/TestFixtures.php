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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $hasher;
    private ParameterBagInterface $params;

    public function __construct(UserPasswordHasherInterface $hasher, ParameterBagInterface $params)
    {
        $this->hasher = $hasher;
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $filesystem = new Filesystem();

        $uploadDirectory = $this->params->get('upload_directory') ?? 'public/uploads';
        
        if (!$filesystem->exists($uploadDirectory)) {
            $filesystem->mkdir($uploadDirectory);
        }

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
            $filename = $this->createTestImageFile($uploadDirectory, $faker);
            $media->setPath($filename);
            $media->setAlbum($albums[0]);
            $media->setUser($admin);
            $manager->persist($media);
        }

        $media = new Media();
        $media->setTitle('Media 11');
        $filename = $this->createTestImageFile($uploadDirectory, $faker);
        $media->setPath($filename);
        $media->setUser($user);
        $manager->persist($media);

        $manager->flush();
    }

    private function createTestImageFile(string $uploadDirectory, $faker): string
    {
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = 'test-' . $faker->slug() . '-' . uniqid() . '.' . $faker->randomElement($extensions);
        $filepath = $uploadDirectory . '/' . $filename;

        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        file_put_contents($filepath, $imageData);
        
        return $filepath;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}