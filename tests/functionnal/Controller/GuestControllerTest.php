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
use App\Repository\UserRepository;

class GuestControllerTest extends WebTestCase
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
    public function testGuestIndex(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_guests_index'));
        $this->assertResponseStatusCodeSame(200);
        $line = $crawler->filter('tr');

        $this->assertEquals(6, $line->count());
    }

    public function testGuestAdd(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_guests_add'));
        $this->assertResponseStatusCodeSame(200);

        $form = $crawler->selectButton('Ajouter')->form([
            'user[name]' => 'test',
            'user[email]' => 'test@test.com',
            'user[description]' => 'test de la description',
            'user[password]' => 'password'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects($urlGenerator->generate('admin_guests_index'));
    }

    public function testGuestBlocked(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_guests_blocked', ['id' => 1]));
        $this->assertResponseRedirects($urlGenerator->generate('admin_guests_index'));

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('guests'));
        $guests = $crawler->filter('.guest');

        $this->assertEquals(4, $guests->count());
    }

    public function testGuestDeleteNoMedia(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_guests_delete', ['id' => 1]));
        $this->assertResponseRedirects($urlGenerator->generate('admin_guests_index'));

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_guests_index'));
        $guests = $crawler->filter('tr');

        $this->assertEquals(5, $guests->count());
    }

    public function testGuestDeleteWithMedia(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_guests_delete', ['id' => 5]));
        $this->assertResponseRedirects($urlGenerator->generate('admin_guests_index'));

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_guests_index'));
        $guests = $crawler->filter('tr');

        $this->assertEquals(5, $guests->count());
    }
}