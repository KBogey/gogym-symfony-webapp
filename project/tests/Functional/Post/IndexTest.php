<?php

namespace App\Tests\Functional\Post;

use App\Entity\Category;
use App\Entity\Post\Post;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IndexTest extends WebTestCase
{
    public function testHomePageWorks(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('h4');
        $this->assertSelectorTextContains('h4', 'Les dernières news');
    }

    public function testPaginationWorks(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $posts = $crawler->filter('div.card');
        $this->assertEquals(8, count($posts));
    }

    public function testSearchBarWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var PostRepository $postRepository */
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var Post $post */
        $post = $postRepository->findOneBy([]);

        /** @var Tag $tag */
        $tag = $post->getTags()[0];

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('public.home')
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $searchs = [
            substr($post->getTitle(), 0, 3),
            substr($tag->getLabel(), 0, 3)
        ];

        foreach ($searchs as $search) {
            $form = $crawler->filter('form[name=search]')->form([
                'search[q]' => $search
            ]);

            $crawler = $client->submit($form);

            $this->assertResponseIsSuccessful();
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            $this->assertRouteSame('public.home');

            $nbPosts = count($crawler->filter('div.card'));
            $postsTitle = $crawler->filter('div.card a');
            $count = 0;

            foreach ($postsTitle as $title) {
                if (
                    str_contains($title->textContent, $search) ||
                    str_contains($tag->getLabel(), $search) ||
                    str_contains($post->getContent(), $search)
                ) {
                    $count++;
                }
            }

            $this->assertEquals($nbPosts, $count);
        }
    }

    public function testSearchBarReturnsNoItems(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('public.home')
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter('form[name=search]')->form([
                'search[q]' => 'aaazzzeerrrtttyyy'
        ]);

        $crawler = $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('public.home');

        $this->assertSelectorExists('form[name=search]');
        $this->assertSelectorNotExists('div.card');
    }

}