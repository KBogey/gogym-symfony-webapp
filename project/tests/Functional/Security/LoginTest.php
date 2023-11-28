<?php

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testLoginWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator*/
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('login')
        );

        $form = $crawler->filter('form[name=login]')->form([
            'email' => 'alice@wonderland.fr',
            'password' => 'LapinBlanc'
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('public.home');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginWithBadCredentials(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator*/
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('login')
        );

        $form = $crawler->filter('form[name=login]')->form([
            'email' => 'alice@wonderland.fr',
            'password' => 'Lap'
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('div.error-login');
        $this->assertSelectorTextContains('div.error-login', 'Identifiants invalides.');
    }

    public function testLogoutWorks(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        /** @Var User $user */
        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $client->request(
          Request::METHOD_GET,
          $urlGenerator->generate('logout')
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('public.home');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function testLoginWithRememberMe(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $this->assertBrowserNotHasCookie('REMEMBERME');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('login')
        );

        $form = $crawler->filter('form[name=login]')->form([
           'email' => 'alice@wonderland.fr',
           'password' => 'LapinBlanc',
            '_remember_me' => 'on'
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('public.home');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertBrowserHasCookie('REMEMBERME');
    }
}