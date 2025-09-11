<?php

namespace App\tests\functionnal\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Album;
use App\Entity\User;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\User\InMemoryUser;

class AlbumControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        
        /*$connectionDb = $this->createMock(Connection::class);
        $this->client->getContainer()->set(Connection::class, $connectionDb);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('persist')->willReturn(null);
        $this->entityManager->method('remove')->willReturn(null);
        $this->entityManager->method('flush')->willReturn(null);
        $this->client->getContainer()->set(EntityManagerInterface::class, $this->entityManager);*/
    }

    protected function tearDown(): void
    {
        //$this->client->getContainer()->set(Connection::class, null);
        //$this->client->getContainer()->set(EntityManagerInterface::class, null);

        parent::tearDown();
    }

    public function testAlbumAdd(): void
    {

        //$this->mockAlbum();

        $user = new InMemoryUser('ina', '$2y$13$7JS0ehfU8vZhB3Q8o1sPGuoQxkiPGXRGgrAizmNfI5Sgy.Dqt9xoW', ['ROLE_ADMIN']);

        $this->client->loginUser($user);


        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_album_add'));
        $this->assertResponseStatusCodeSame(200);

        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => 'test'
        ]);

        $this->client->submit($form);



        //$response = $this->client->getResponse();
        //echo "POST Status: " . $response->getStatusCode() . "\n";
        //echo "Response Content:\n" . $response->getContent() . "\n";
        //echo "POST Location: " . $response->headers->get('Location') . "\n";
        //echo "Expected: " . $urlGenerator->generate('admin_album_index') . "\n";
        $this->assertResponseRedirects($urlGenerator->generate('admin_album_index'));
    }

    public function testAlbumUpdate(): void
    {
        //$this->mockAlbum();

        $user = new InMemoryUser('ina', '$2y$13$7JS0ehfU8vZhB3Q8o1sPGuoQxkiPGXRGgrAizmNfI5Sgy.Dqt9xoW', ['ROLE_ADMIN']);

        $this->client->loginUser($user);

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
        //$this->mockAlbum();

        $user = new InMemoryUser('ina', '$2y$13$7JS0ehfU8vZhB3Q8o1sPGuoQxkiPGXRGgrAizmNfI5Sgy.Dqt9xoW', ['ROLE_ADMIN']);

        $this->client->loginUser($user);

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_album_delete', ['id' => 1]));
        $this->assertResponseRedirects($urlGenerator->generate('admin_album_index'));
    }

    private function mockAlbum(): void
    {
        $albums = [];

        for ($i = 0; $i < 5; $i++) {
            $album = new Album();
            $album->setName("album $i");
            $rp = new \ReflectionProperty(Album::class, 'id');
            $rp->setAccessible(true);
            $rp->setValue($album, $i);
            $albums[] = $album;
        }

        $mockAlbumRepository = $this->createMock(AlbumRepository::class);
        $mockAlbumRepository->method('findAll')
            ->willReturn($albums);
        $mockAlbumRepository->method('find')
            ->with(1)
            ->willReturn($albums[1]);

        $container = $this->client->getContainer();
        $container->set(AlbumRepository::class, $mockAlbumRepository);
    }
}