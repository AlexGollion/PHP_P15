<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Album;
use App\Entity\Media;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $password = 'password';
        
        $admin = new User();
        $admin->setName('Ina Zaoui');
        $admin->setEmail('ina@zaoui.com');
        $hashPassword = $this->hasher->hashPassword($admin, $password);
        $admin->setPassword($hashPassword);
        $admin->setAdmin(true);
        $manager->persist($admin);
        
        $users = [];
        for ($i = 2; $i < 52; $i++) {
            $user = new User();
            $user->setName('Invité ' . $i);
            $user->setEmail('invité+' . $i . '@example.com');
            $hashPassword = $this->hasher->hashPassword($user, $password);
            $user->setPassword($hashPassword);
            $user->setDescription("Le maître de l'urbanité capturée, explore les méandres des cités avec un regard vif et impétueux, figeant l'énergie des rues dans des instants éblouissants. À travers une technique avant-gardiste, il métamorphose le béton et l'acier en toiles abstraites");
            $user->setAdmin(false);
            $users[] = $user;
            $manager->persist($user);
        }

        $albums = [];
        for ($i = 1; $i < 6; $i++) {
            $album = new Album();
            $album->setName('Album ' . $i);
            $albums[] = $album;
            $manager->persist($album);
        }

        for ($i = 1; $i < 51; $i++) {
            $media = new Media();
            $media->setTitle('Titre ' . $i);
            if ($i < 10) {
                $media->setPath('uploads/000' . $i . '.jpg');
            } 
            else {
                $media->setPath('uploads/00' . $i . '.jpg');
            }

            $albumId = floor(($i - 1) / 10);
            $media->setAlbum($albums[$albumId]);
            $media->setUser($admin);
            $manager->persist($media);
        }

        for ($i = 51; $i <= 2500; $i++) {
            $media = new Media();
            $media->setTitle('Titre ' . $i);
            if ($i < 100) {
                $media->setPath('uploads/00' . $i . '.jpg');
            } else if ($i < 1000) {
                $media->setPath('uploads/0' . $i . '.jpg');
            } else {
                $media->setPath('uploads/' . $i . '.jpg');
            }
            $userId = floor(($i - 51) / 50);
            $media->setUser($users[$userId]);
            $manager->persist($media);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
