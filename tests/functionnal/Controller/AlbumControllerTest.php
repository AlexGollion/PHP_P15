<?php

namespace App\tests\functionnal\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Album;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;

class AlbumControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private User $userLogged;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->userRepository = $this->client->getContainer()->get('doctrine')->getRepository(User::class);

        $this->userLogged = $this->userRepository->findOneBy(['name' => 'ina']);
        $this->client->loginUser($this->userLogged);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testAlbumAdd(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_album_add'));
        $this->assertResponseStatusCodeSame(200);

        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => 'test'
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects($urlGenerator->generate('admin_album_index'));
    }

    public function testAlbumUpdate(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_album_update', ['id' => 1]));
        $this->assertResponseStatusCodeSame(200);   
        
        $form = $crawler->selectButton('Modifier')->form([
            'album[name]' => 'test'
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects($urlGenerator->generate('admin_album_index'));
    }

    public function testAlbumDelete(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_album_delete', ['id' => 1]));
        $this->assertResponseRedirects($urlGenerator->generate('admin_album_index'));
    }
}