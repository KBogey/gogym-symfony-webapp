<?php

namespace App\Controller\Public;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\Post\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property PaginatorInterface $paginator
 */
class HomeController extends AbstractController
{

    public function __construct(
        PaginatorInterface $paginator
    )
    {
        $this->paginator = $paginator;
    }

    #[Route('/', name: 'public.home')]
    public function index(PostRepository $postRepository, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $postRepository->findBySearch($searchData);

            return $this->render('public/posts/search.html.twig', [
               'form' => $form->createView(),
               'posts' => $posts
            ]);
        }

        $news = $postRepository->findBy(['category' => 1, 'state' => ['STATE_PUBLISHED']], ['createdAt' => 'desc']);
        $diets = $postRepository->findBy(['category' => 2], ['createdAt' => 'desc']);

        $postsNews = $this->paginator->paginate(
            $news,
            $request->query->getInt('page', 1),
            4
        );

        $postsDiet = $this->paginator->paginate(
            $diets,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('public/home/index.html.twig', [
            'form' => $form->createView(),
            'postsNews' => $postsNews,
            'postsDiet' => $postsDiet
        ]);
    }
}
