<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/{username}', name: 'app_profile')]
    public function index(string $username, EntityManagerInterface $entityManager): Response
    {
        // Buscar el usuario por su nombre de usuario en la base de datos
        $profileUser = $entityManager->getRepository(User::class)->findOneBy(['user' => $username]);

        // Si no se encuentra el usuario, lanzar una excepción 404
        if (!$profileUser) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        // Renderizar la plantilla del perfil con el usuario encontrado
        return $this->render('profile.html.twig', [
            'user' => $profileUser,
        ]);
    }
}
