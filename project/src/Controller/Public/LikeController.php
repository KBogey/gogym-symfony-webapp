<?php

namespace App\Controller\Public;

use App\Entity\Post\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/like/article/{id}', name: 'post.like')]
    public function like(Post $post, EntityManagerInterface $manager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if($post->isLikedByUser($user)) {
            $post->removeLike($user);
            $manager->flush();

            return $this->json(['message' => 'Le like a été supprimé !']);
        }

        $post->addLike($user);
        $manager->flush();

        return $this->json(['message' => 'Le like a été ajouté !']);

    }
}