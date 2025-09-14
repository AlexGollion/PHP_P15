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

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('portfolio', ['id' => 1]));

        $link = $crawler->filter('.btn');
        $media = $crawler->filter('.media');

        $this->assertEquals(6, $link->count());
        $this->assertEquals(10, $media->count());
    }
}