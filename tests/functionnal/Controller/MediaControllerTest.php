<?php

namespace App\tests\functionnal\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Media;
use App\Entity\Album;
use App\Entity\User;
use App\Repository\MediaRepository;
use Symfony\Component\Security\Core\User\InMemoryUser;

class MediaControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testMediaIndexAdmin(): void
    {
        $user = new InMemoryUser('ina', '$2y$13$7JS0ehfU8vZhB3Q8o1sPGuoQxkiPGXRGgrAizmNfI5Sgy.Dqt9xoW', ['ROLE_ADMIN']);

        $this->client->loginUser($user);

        //$this->mockMediaIndexAdmin();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_media_index'));
        $this->assertResponseStatusCodeSame(200);
    }

    public function testMediaIndex(): void
    {
        //$this->mockMediaIndex();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_media_index'));
        $this->assertResponseStatusCodeSame(200);
    }

    public function testMediaAdd(): void
    {
        $user = new InMemoryUser('ina', '$2y$13$7JS0ehfU8vZhB3Q8o1sPGuoQxkiPGXRGgrAizmNfI5Sgy.Dqt9xoW', ['ROLE_ADMIN']);

        $this->client->loginUser($user);

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_media_add'));
        $this->assertResponseStatusCodeSame(200);
    }

    public function testMediaDelete(): void
    {
        $user = new InMemoryUser('ina', '$2y$13$7JS0ehfU8vZhB3Q8o1sPGuoQxkiPGXRGgrAizmNfI5Sgy.Dqt9xoW', ['ROLE_ADMIN']);

        $this->client->loginUser($user);

        //$this->mockMediaIndexAdmin();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_media_delete', ['id' => 1]));
        $this->assertResponseRedirects($urlGenerator->generate('admin_media_index'));
    }

    private function mockMediaIndexAdmin(): void
    {
        $medias = [];
        $album = new Album();
        $album->setName("album");

        $user = new User();
        $user->setName("ina");

        $testFilePath = sys_get_temp_dir() . '/test_media_' . uniqid() . '.jpeg';
        file_put_contents($testFilePath, 'test content');

        for($i = 0; $i < 10; $i++)
        {
            $media = new Media();
            $rp = new \ReflectionProperty(Media::class, 'id');
            $rp->setAccessible(true);
            $rp->setValue($media, $i);
            $media->setTitle("media $i");
            $media->setPath($testFilePath);
            $media->setAlbum($album);
            $media->setUser($user);
            $medias[] = $media;
        }

        $mockMediaRepository = $this->createMock(MediaRepository::class);
        $mockMediaRepository->method('findBy')
            ->with([], ['id' => 'ASC'], 25, 0)
            ->willReturn($medias);

        $mockMediaRepository->method('find')
            ->with(1)
            ->willReturn($medias[1]);

        $container = $this->client->getContainer();
        $container->set(MediaRepository::class, $mockMediaRepository);
    }

    private function mockMediaIndex(): void
    {
        $medias = [];
        $album = new Album();
        $album->setName("album");

        $user = new User();
        $user->setName("ina");

        for($i = 0; $i < 10; $i++)
        {
            $media = new Media();
            $rp = new \ReflectionProperty(Media::class, 'id');
            $rp->setAccessible(true);
            $rp->setValue($media, $i);
            $media->setTitle("media $i");
            $media->setPath('images/home.jpeg');
            $media->setAlbum($album);
            $media->setUser($user);
            $medias[] = $media;
        }

        $mockMediaRepository = $this->createMock(MediaRepository::class);
        $mockMediaRepository->method('findBy')
            ->with(["user" => $user], ['id' => 'ASC'], 25, 0)
            ->willReturn($medias);

        $container = $this->client->getContainer();
        $container->set(MediaRepository::class, $mockMediaRepository);
    }
}