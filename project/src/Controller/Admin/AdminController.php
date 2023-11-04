<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PostRepository;
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
