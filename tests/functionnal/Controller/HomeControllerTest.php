<?php

namespace App\tests\functionnal\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\User;
use App\Entity\Album;
use App\Entity\Media;
use App\Repository\UserRepository;
use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;

class HomeControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testGuestsPages(): void
    {
        $this->client = static::createClient();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('guests'));
      
        $guests = $crawler->filter('.guest');

        $this->assertEquals(5, $guests->count());
    }

    public function testGuestPage(): void
    {
        $this->client = static::createClient();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('guest', ['id' => 1]));

        $guest = $crawler->filter('h3')->text();

        $this->assertEquals('User 0', $guest);
    }

    public function testPortfolioPage(): void
    {
        $this->client = static::createClient(); 

        //$this->mockPortfolio();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('portfolio', ['id' => 1]));

        $link = $crawler->filter('.btn');
        $media = $crawler->filter('.media');

        $this->assertEquals(6, $link->count());
        $this->assertEquals(10, $media->count());
    }

    private function mockUsers(): void
    {
        $mockUsers = [];

        for ($i = 0; $i < 5; $i++) {
            $user = $this->createMock(User::class);
            $user->method('getId')->willReturn($i);
            $user->method('getName')->willReturn("user $i");
            $user->method('getEmail')->willReturn("user$i@email.com");
            $user->method('isAdmin')->willReturn(false);

            $mockUsers[] = $user;
        }

        $mockUserRepository = $this->createMock(UserRepository::class);
        $mockUserRepository->method('findBy')
                            ->with(['admin' => false])
                            ->willReturn($mockUsers);
        $mockUserRepository->method('find')
                            ->with(0)
                            ->willReturn($mockUsers[0]);

        //$container = $this->client->getContainer();
        //$container->set(UserRepository::class, $mockUserRepository);
        $this->client->getContainer()->set(UserRepository::class, $mockUserRepository);
    }

    private function mockPortfolio(): void
    {
        
        $mockAlbums = [];
        
        for ($i = 0; $i < 5; $i++) {
            $album = $this->createMock(Album::class);
            $album->method('getId')->willReturn($i);
            $album->method('getName')->willReturn("album $i");
            
            $mockAlbums[] = $album;
        }
        
        $mockAlbumRepository = $this->createMock(AlbumRepository::class);
        $mockAlbumRepository->method('findAll')
        ->willReturn($mockAlbums);
        $mockAlbumRepository->method('find')
        ->with(0)
        ->willReturn($mockAlbums[0]);
        
        $container = $this->client->getContainer();
        $container->set(AlbumRepository::class, $mockAlbumRepository);
        
        $mockUser = $this->createMock(User::class);
        $mockUser->method('getId')->willReturn(0);
        $mockUser->method('getName')->willReturn("Admin");
        $mockUser->method('getEmail')->willReturn("admin@email.com");
        $mockUser->method('isAdmin')->willReturn(true);
        
        $mockMedias = new ArrayCollection();
        
        for ($i = 0; $i < 5; $i++) {
            $media = $this->createMock(Media::class);
            $media->method('getId')->willReturn($i);
            $media->method('getTitle')->willReturn("media $i");
            $media->method('getAlbum')->willReturn($mockAlbums[$i]);
            $media->method('getUser')->willReturn($mockUser);
            
            $mockMedias->add($media);
        }
        $mockUser->method('getMedias')->willReturn($mockMedias);
        
        $mockMediaRepository = $this->createMock(MediaRepository::class);
        $mockMediaRepository->method('findBy')
        ->with(['album' => $mockAlbums[0]])
        ->willReturn([$mockMedias[0]]);
        $mockMediaRepository->method('findBy')
        ->with(['user' => $mockUser])
        ->willReturn([$mockMedias]);
        

        $mockUserRepository = $this->createMock(UserRepository::class);
        $mockUserRepository->method('findOneBy')
                            ->with(['admin' => true])
                            ->willReturn($mockUser);
        
        $container->set(UserRepository::class, $mockUserRepository);
        $container->set(MediaRepository::class, $mockMediaRepository);
        
    }
}