<?php

namespace App\Tests\Functional\Post;

use App\Entity\Post\Post;
use App\Repository\Post\PostRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;

class PostTest extends WebTestCase
{
    public function testPostPageWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var  PostRepository $postRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var  Post $post */
        $post = $postRepository->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('public.post.show', ['slug' => $post->getSlug()])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', ucfirst($post->getTitle()));
    }

    public function testShareOnFacebookWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var  PostRepository $postRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var  Post $post */
        $post = $postRepository->findOneBy([]);

        $postLink = $urlGeneratorInterface->generate('public.post.show', ['slug' => $post->getSlug()]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $postLink
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $link = $crawler->filter('.share.facebook')->link()->getUri();

        $this->assertStringContainsString(
            'https://www.facebook.com/sharer/sharer.php',
            $link
        );

        $this->assertStringContainsString(
            $postLink,
            $link
        );
    }

    public function testShareOnTwitterWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var  PostRepository $postRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var  Post $post */
        $post = $postRepository->findOneBy([]);

        $postLink = $urlGeneratorInterface->generate('public.post.show', ['slug' => $post->getSlug()]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $postLink
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $link = $crawler->filter('.share.twitter')->link()->getUri();

        $this->assertStringContainsString(
            'https://twitter.com/intent/tweet',
            $link
        );

        $this->assertStringContainsString(
            $postLink,
            $link
        );
    }

}