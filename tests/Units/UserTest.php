<?php

namespace App\Tests\Units;

use App\Entity\User;
use App\Entity\Media;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class UserTest extends TestCase
{
    public function testUserGetterSetter() : void
    {
        $dataUser = [
            'name' => 'test',
            'email' => 'test@test.com',
            'description' => 'test de la description'
        ];

        $user = new User();
        $user->setName($dataUser['name']);
        $user->setEmail($dataUser['email']);
        $user->setDescription($dataUser['description']);

        $this->assertEquals($dataUser['name'], $user->getName());
        $this->assertEquals($dataUser['email'], $user->getEmail());
        $this->assertEquals($dataUser['description'], $user->getDescription());
    }   

    public function testUserAdmin() : void
    {
        $user = new User();
        $user->setAdmin(true);

        $this->assertTrue($user->isAdmin());

        $user->setAdmin(false);

        $this->assertFalse($user->isAdmin());
    } 

    public function testUserMedias() : void
    {
        $user = new User();
        $medias = new ArrayCollection();

        for($i = 0; $i < 10; $i++)
        {
            $media = new Media();
            $medias->add($media);
        }

        $user->setMedias($medias);

        $this->assertCount(10, $user->getMedias());

    } 
}