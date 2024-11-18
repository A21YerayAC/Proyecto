<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainpageController extends AbstractController
{
    #[Route(path: '/main', name: 'app_homepage')]
    public function index(ReviewRepository $reviewRepository): Response
    {
        // Verifica si el usuario está autenticado
        /** @var User $user */
        $user = $this->getUser();

        // Si está autenticado, obtenemos los usuarios que sigue
        if ($user) {
            $seguido = $user->getSiguiendo(); // Devuelve la colección de usuarios que sigues
            $publicacionesSeguidas = $reviewRepository->findReviewsByUsers($seguido);
        } else {
            // Si no está autenticado, obtenemos todas las publicaciones
            $publicacionesSeguidas = $reviewRepository->findAll();
        }

        // Renderizamos la plantilla con las publicaciones
        return $this->render('main.html.twig', [
            'publicaciones' => $publicacionesSeguidas,
        ]);
    }
}
