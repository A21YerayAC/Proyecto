<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MainpageController extends AbstractController
{
    #[Route(path: '/main', name: 'app_homepage')]
    public function index(Security $security, ReviewRepository $reviewRepository, UserRepository $userRepository, NotificationRepository $notificationRepository, Request $request): Response
{
    // Obtener el usuario actual
    $user = $security->getUser();

    // Si el usuario está autenticado
    if ($user) {
        // Obtener los usuarios que sigue
        $followedUsers = $user->getFollowing();

        // Convertir la colección en un array si es necesario
        if ($followedUsers instanceof \Doctrine\Common\Collections\Collection) {
            $followedUsers = $followedUsers->toArray();
        }

        if (!empty($followedUsers)) {
            // Obtener publicaciones de los usuarios seguidos
            $followedReviews = $reviewRepository->findReviewsByUsers($followedUsers);

            // Añadir la propiedad isLikedByUser
            foreach ($followedReviews as $review) {
                $review->isLikedByUser = $review->isLikedByUser($user);
            }

            // Obtener el resto de las publicaciones (excluyendo las ya obtenidas)
            $followedUserIds = array_map(fn($user) => $user->getId(), $followedUsers);
            $otherReviews = $reviewRepository->createQueryBuilder('r')
                ->leftJoin('r.user', 'u')
                ->where('u.id NOT IN (:followedUserIds)')
                ->setParameter('followedUserIds', $followedUserIds)
                ->orderBy('r.fechaPublicacion', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            // Si no sigue a nadie, mostrar todas las publicaciones
            $followedReviews = [];
            $otherReviews = $reviewRepository->findBy([], ['fechaPublicacion' => 'DESC']);
        }
    } else {
        // Si no está autenticado, mostrar todas las publicaciones
        $followedReviews = [];
        $otherReviews = $reviewRepository->findBy([], ['fechaPublicacion' => 'DESC']);
    }

    if ($user) {
        $suggestedUsers = $userRepository->findUsersNotFollowed($user);
    } else {
        $suggestedUsers = $userRepository->findAll();
    }
    $suggestedUsers = array_slice($suggestedUsers, 0, 2);

    $notifications = $notificationRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

    return $this->render('main.html.twig', [
        'followedReviews' => $followedReviews,
        'otherReviews' => $otherReviews,
        'suggestedUsers' => $suggestedUsers,
        'notifications' => $notifications,
        'currentUser' => $this->getUser(),
    ]);
}
}