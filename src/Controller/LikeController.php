<?php
// src/Controller/LikeController.php
namespace App\Controller;

use App\Entity\Like;
use App\Entity\Notification;
use App\Entity\Review;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class LikeController extends AbstractController
{
    #[Route('/like/{reviewId}', name: 'app_like')]
    public function addLike(int $reviewId, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $review = $entityManager->getRepository(Review::class)->find($reviewId);

        if (!$review) {
            return new JsonResponse(['success' => false, 'message' => 'Review not found'], 404);
        }

        $like = new Like();
        $like->setUser($user);
        $like->setReview($review);
        $like->setFechaLike(new \DateTime());
        $entityManager->persist($like);

        // Crear notificación
        $notification = new Notification();
        $notification->setUser($review->getUser());
        $notification->setType('like');
        $notification->setReview($review);
        $notification->setMessage($user->getUsername() . ' le ha dado like a tu reseña ' . $review->getTitulo());
        $notification->setCreatedAt(new \DateTime());
        $entityManager->persist($notification);

        var_dump($notification);

        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Like añadido']);
   }

#[Route('review/{id}/like', name: 'toggle_like', methods: ['POST'])]
public function toggleLike(Review $review, EntityManagerInterface $entityManager): JsonResponse
{
    $user = $this->getUser();

    if (!$user) {
        return new JsonResponse(['success' => false, 'message' => 'Usuario no autenticado.'], 403);
    }

    $like = $entityManager->getRepository(Like::class)->findOneBy([
        'user' => $user,
        'review' => $review,
    ]);

    if ($like) {
        // Si ya existe un like, lo quitamos
        $notification = $entityManager->getRepository(Notification::class)->findOneBy([
            'user' => $review->getUser(),
            'type' => 'like',
            'review' => $review,
        ]);

        $entityManager->remove($like);
        if ($notification) {
            $entityManager->remove($notification);
        }
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'liked' => false]);
    } else {
        // Si no existe, lo agregamos
        $like = new Like();
        $like->setUser($user);
        $like->setReview($review);
        $like->setFechaLike(new \DateTime());
        $entityManager->persist($like);

        // Crear notificación
        $notification = new Notification();
        $notification->setUser($review->getUser());
        $notification->setType('like');
        $notification->setReview($review);
        $notification->setMessage($user->getUsername() . ' le ha dado like a tu reseña ' . $review->getTitulo());
        $notification->setCreatedAt(new \DateTime());
        $entityManager->persist($notification);

        $entityManager->flush();

        return new JsonResponse(['success' => true, 'liked' => true]);
    }
}
}
