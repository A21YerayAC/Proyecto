<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface; // Importa EntityManagerInterface
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $entityManager;

    // Inyecta EntityManagerInterface a través del constructor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    #[Route(path: '/follow/{id}', name: 'follow_user', methods: ['POST'])]
    public function followUser(User $targetUser): Response
    {
        // Obtener el usuario autenticado (quien está haciendo el seguimiento)
        /** @var User $user */
        $user = $this->getUser();

        if ($user === $targetUser) {
            // Si el usuario intenta seguirse a sí mismo, puedes redirigir o mostrar un mensaje de error
            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }

        // Llamar al método addSeguidor para seguir al usuario
        $user->addSeguidor($targetUser);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        // Redirigir al perfil del usuario seguido
        return $this->redirectToRoute('profile', ['id' => $targetUser->getId()]);
    }

   
    #[Route(path: '/following/{id}', name: 'following_user', methods: ['GET'])]
    public function followingUser(User $user): Response
    {
        // Obtener los usuarios que sigues a través de la relación 'siguiendo'
        $seguido = $user->getSiguiendo(); // Devuelve la colección de usuarios que sigue

        // Renderizar una plantilla para mostrar la lista de usuarios seguidos
        return $this->render('user/following.html.twig', [
            'seguido' => $seguido,
        ]);
    }
}
