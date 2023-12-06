<?php

namespace App\Controller\Public;

use App\Entity\Post\Comment;
use App\Entity\Post\Post;
use App\Form\CommentType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property PostRepository $repository
 * @property PaginatorInterface $paginator
 */
class PostController extends AbstractController
{
    public function __construct(
        PostRepository $repository,
        PaginatorInterface $paginator
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    #[Route('/actus', name: 'public.news.index')]
    public function indexNews(Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $this->repository->findBySearch($searchData);

            return $this->render('public/posts/search.html.twig', [
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }

        $news = $this->repository->findBy(['category' => 1, 'state' => ['STATE_PUBLISHED'] ] , ['createdAt' => 'desc'] );

        $posts = $this->paginator->paginate(
            $news,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('public/posts/indexNews.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts,
        ]);
    }

    #[Route('/nutrition', name: 'public.diets.index')]
    public function indexDiets(Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $this->repository->findBySearch($searchData);

            return $this->render('public/posts/search.html.twig', [
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }

        $diets = $this->repository->findBy(['category' => 2, 'state' => ['STATE_PUBLISHED'] ], ['createdAt' => 'desc']);

        $posts = $this->paginator->paginate(
            $diets,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('public/posts/indexDiets.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }

    #[Route('/article/{slug}', name: 'public.post.show', methods: ['GET', 'POST'])]
    public function show(Post $post, Request $request, EntityManagerInterface $em): Response
    {

        $comment = new Comment();
        $comment->setPost($post);
        if($this->getUser()) {
            $comment->setAuthor($this->getUser());
        }

        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);
        if($formComment->isSubmitted() && $formComment->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a bien été enregistré. Il sera soumis à modération dans les plus brefs délais.');

            return $this->redirectToRoute('public.post.show', ['slug' => $post->getSlug()]);
        }

        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $this->repository->findBySearch($searchData);

            return $this->render('public/posts/search.html.twig', [
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }

        return $this->render('public/posts/show.html.twig', [
            'formComment' => $formComment->createView(),
            'form' => $form->createView(),
            'post' => $post
        ]);
    }
}
