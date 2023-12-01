<?php

namespace App\Controller\Admin;

use App\Entity\Post\Category;
use App\Form\Admin\CategoryType;
use App\Repository\Post\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function __construct(
        CategoryRepository $repository,
        EntityManagerInterface $em,
    )
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    #[Route('/admin/categories', name: 'admin.categories.index')]
    public function index(): Response
    {
        $categories = $this->repository->findAll();

        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/categorie/create', name: 'admin.category.create')]
    public function new(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie ajoutée avec succès');
            return $this->redirectToRoute('admin.categories.index');
        }
        return $this->render('admin/categories/create.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/categorie/edit/{categoryId}', name: 'admin.category.edit' )]
    public function edit($categoryId, Request $request)
    {
        $category = $this->repository->find($categoryId);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Catégorie modifiée avec succès');
            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/categorie/{categoryId}', name:'admin.category.delete' )]
    public function delete($categoryId, Request $request)
    {
        $category = $this->repository->find($categoryId);

        if($this->isCsrfTokenValid('delete'. $category->getId(), $request->get('_token'))) {
            $this->repository->remove($category, true);
            $this->addFlash('success', 'Catégorie supprimée');
        }

        return $this->redirectToRoute('admin.categories.index', [
        ]);
    }
}
