<?php
// src/Controller/FollowController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class FollowController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Seguir a un usuario
    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(User $user): RedirectResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser) {
            $currentUser->follow($user);
            $this->entityManager->flush();
            $this->addFlash('success', '¡Ahora sigues a ' . $user->getUsername() . '!');
        } else {
            $this->addFlash('error', 'Debes estar logueado para seguir a alguien.');
        }

        return $this->redirectToRoute('app_profile', ['username' => $user->getUsername()]);
    }

    // Dejar de seguir a un usuario
    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(User $user): RedirectResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser) {
            $currentUser->unfollow($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Has dejado de seguir a ' . $user->getUser() . '.');
        } else {
            $this->addFlash('error', 'Debes estar logueado para dejar de seguir a alguien.');
        }

        return $this->redirectToRoute('app_profile', ['username' => $user->getUser()]);
    }
}
