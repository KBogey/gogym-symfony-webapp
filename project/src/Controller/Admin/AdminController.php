<?php

namespace App\Controller\Admin;

use App\Repository\Post\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(PostRepository $postRepository, EntityManagerInterface $em)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
    }

    #[Route('/admin', name: 'admin.index')]
    public function index(): Response
    {
        $posts = $this->postRepository->findBY([], ['createdAt' =>'desc'], 5);

        return $this->render('admin/index.html.twig', [
        ]);
    }
}
