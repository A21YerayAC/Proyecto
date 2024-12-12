<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use App\Repository\ReviewRepository;
use App\Entity\Notification;
use App\Repository\NotificationRepository;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, UserRepository $userRepository, ReviewRepository $reviewRepository, NotificationRepository $notificationRepository): Response
    {
        $query = $request->query->get('query', '');

        if (!$query) {
            $this->addFlash('warning', 'Por favor, introduce un término de búsqueda.');
            return $this->redirectToRoute('homepage'); // Cambia 'homepage' por tu ruta principal
        }

        // Buscar usuarios que coincidan con la consulta
        $users = $userRepository->createQueryBuilder('u')
            ->where('u.user LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        // Buscar publicaciones asociadas a los usuarios encontrados
        $reviews = $reviewRepository->createQueryBuilder('r')
            ->innerJoin('r.user', 'u')
            ->where('u.user LIKE :query OR r.titulo LIKE :query OR r.contenido LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

            $notifications = $notificationRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        // Renderizar resultados
        return $this->render('search/results.html.twig', [
            'query' => $query,
            'users' => $users,
            'reviews' => $reviews,
            'notifications' => $notifications,
            'currentUser' => $this->getUser(),
        ]);
    }

}
