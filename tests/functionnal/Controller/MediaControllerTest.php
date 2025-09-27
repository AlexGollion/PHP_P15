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
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private User $userLogged;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->userRepository = $this->client->getContainer()->get('doctrine')->getRepository(User::class);

        
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testMediaIndexAdmin(): void
    {
        $this->userLogged = $this->userRepository->findOneBy(['name' => 'ina']);
        $this->client->loginUser($this->userLogged);

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_media_index'));
        $this->assertResponseStatusCodeSame(200);
    }

    public function testMediaIndex(): void
    {
        $this->userLogged = $this->userRepository->findOneBy(['name' => 'User 0']);
        $this->client->loginUser($this->userLogged);

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_media_index'));
        $this->assertResponseStatusCodeSame(200);
    }

    public function testMediaAdd(): void
    {
        $this->userLogged = $this->userRepository->findOneBy(['name' => 'ina']);
        $this->client->loginUser($this->userLogged);

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_media_add'));
        $this->assertResponseStatusCodeSame(200);

        $testImagePath = tempnam(sys_get_temp_dir(), 'test_image_') . '.png';
        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        file_put_contents($testImagePath, $imageData);
    
        $uploadedFile = new UploadedFile(
            $testImagePath,
            'test-image.png',
            'image/png',
            null,
            true // Test mode
        );

        $form = $crawler->selectButton('Ajouter')->form([
            'media[title]' => 'test',
            'media[file]' => $uploadedFile
        ]);

        $this->client->submit($form);

        if (file_exists($testImagePath)) {
            unlink($testImagePath);
        }

        $this->assertResponseRedirects($urlGenerator->generate('admin_media_index'));
    }


    public function testMediaDelete(): void
    {
        $this->userLogged = $this->userRepository->findOneBy(['name' => 'ina']);
        $this->client->loginUser($this->userLogged);

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_media_delete', ['id' => 1]));
        $this->assertResponseRedirects($urlGenerator->generate('admin_media_index'));
    }
}