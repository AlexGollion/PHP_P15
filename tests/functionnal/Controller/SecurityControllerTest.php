<?php

namespace App\tests\functionnal\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testLoginPage(): void
    {
        $this->client = static::createClient();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_login'));

        $form = $crawler->filter('form')->form();
        $form['_username'] = 'User 1';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $authorizationChecker = $this->client->getContainer()->get(AuthorizationCheckerInterface::class);
        $this->assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY'));
    }

    public function testLoginPageAdmin(): void
    {
        $this->client = static::createClient();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_login'));

        $form = $crawler->filter('form')->form();
        $form['_username'] = 'ina';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $authorizationChecker = $this->client->getContainer()->get(AuthorizationCheckerInterface::class);
        $this->assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY'));
    }

    public function testLoginError(): void
    {
        $this->client = static::createClient();

        $urlGenerator = $this->client->getContainer()->get('router.default');
        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('admin_login'));

        $form = $crawler->filter('form')->form();
        $form['_username'] = 'ina';
        $form['_password'] = 'pass';
        $this->client->submit($form);

        $authorizationChecker = $this->client->getContainer()->get(AuthorizationCheckerInterface::class);
        $this->assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY'));
    }

}