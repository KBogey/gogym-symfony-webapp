<?php

namespace App\Controller\Admin;

use App\Entity\Post\Post;
use App\Form\Admin\PostType;
use App\Repository\Post\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(
        PostRepository $repository,
        EntityManagerInterface $em,
    )
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    #[Route('/admin/articles', name: 'admin.posts.index')]
    public function index(): Response
    {
        $posts = $this->repository->findAll();

        return $this->render('admin/posts/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/admin/article/create', name: 'admin.post.create')]
    public function new(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash('success', 'Article ajouté avec succès');
            return $this->redirectToRoute('admin.posts.index');
        }
        return $this->render('admin/posts/create.html.twig', [
            'posts' => $post,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/article/modifier/{postId}', name: 'admin.post.edit' )]
    public function edit($postId, Request $request)
    {
        $post = $this->repository->find($postId);

        $originalTags = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($post->getTags() as $tag) {
            $originalTags->add($tag);
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach ($originalTags as $tag) {
                if (false === $post->getTags()->contains($tag)) {

                    $tag->getTasks()->removeElement($post);
                    $this->em->persist($tag);
                }
            }
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('admin.posts.index');
        }

        return $this->render('admin/posts/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/article/{postId}', name:'admin.post.delete' )]
    public function delete($postId, Request $request)
    {
        $post = $this->repository->find($postId);

        if($this->isCsrfTokenValid('delete'. $post->getId(), $request->get('_token'))) {
            $this->repository->remove($post, true);
            $this->addFlash('success', 'Article supprimé avec succès');
        }

        return $this->redirectToRoute('admin.posts.index', [
        ]);
    }


}
