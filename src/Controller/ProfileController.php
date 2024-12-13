<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Review;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;

class ProfileController extends AbstractController
{
    private $entityManager;
    private $requestStack;

    // Inyecta el EntityManagerInterface y RequestStack a través del constructor
    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    #[Route('/profile/{username}', name: 'app_profile')]
    public function index(string $username, NotificationRepository $notificationRepository): Response
    {
        // Buscar el usuario por su nombre de usuario
        $profileUser = $this->entityManager->getRepository(User::class)->findOneBy(['user' => $username]);

        if (!$profileUser) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        // Buscar las reseñas asociadas al usuario
        $reviews = $this->entityManager->getRepository(Review::class)->findBy(
            ['user' => $profileUser],
            ['fechaPublicacion' => 'DESC']
        );

        $notifications = $notificationRepository->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('profile.html.twig', [
            'user' => $profileUser,
            'reviews' => $reviews,
            'notifications' => $notifications,
            'currentUser' => $this->getUser(),
        ]);
    }

    #[Route('/profile/{username}/edit', name: 'app_edit_profile')]
public function edit(
    Request $request,
    ImageUploader $imageUploader,
    ParameterBagInterface $params,
    UserPasswordHasherInterface $passwordHasher,
    string $username,
    
): Response {
    // Buscar el usuario que se está editando
    $user = $this->getUser();

    if (!$user) {
        // Si no está autenticado, redirigir a la página de login
        $this->addFlash('error', 'Debes estar autenticado para editar tu perfil.');
        return $this->redirectToRoute('app_login');
    }

    if ($user->getUsername() !== $username) {
        // Si el usuario no tiene acceso al perfil que intenta editar
        $this->addFlash('error', 'No puedes editar el perfil de otro usuario.');
        return $this->redirectToRoute('app_profile', ['username' => $user->getUsername()]);
    }

    // Crear el formulario de edición de perfil
    $form = $this->createForm(ProfileType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $foto = $form->get('fotoPerfil')->getData();

        // Procesar la nueva foto de perfil
        if ($foto) {
            // Subir la nueva foto
            $newFilename = uniqid() . '.' . $foto->guessExtension();
            try {
                // Subir la nueva imagen
                $imageUploader->uploadImage($foto, $newFilename);
                $user->setFotoPerfil('http://' . $params->get('sftp_host') . '/imagenes/' . $newFilename);
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se pudo subir la foto. Por favor, inténtalo de nuevo.');
                return $this->redirectToRoute('app_edit_profile', ['username' => $username]);
            }
        }

        // Si se ingresó una nueva contraseña, actualizarla
        $plainPassword = $form->get('password')->getData();
        if ($plainPassword) {
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
        }

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        $this->addFlash('success', 'Perfil actualizado con éxito.');
        return $this->redirectToRoute('app_profile', ['username' => $user->getUsername()]);
    }

   

    return $this->render('profile/edit.html.twig', [
        'form' => $form->createView(),
        
    ]);
}


    #[Route('/profile/{username}/delete', name: 'app_delete_profile')]
    public function delete(string $username): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Debes estar autenticado para eliminar tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getUsername() !== $username) {
            $this->addFlash('error', 'No puedes eliminar el perfil de otro usuario.');
            return $this->redirectToRoute('app_profile', ['username' => $user->getUsername()]);
        }

        // Eliminar al usuario y sus dependencias
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Invalidar la sesión actual
        $session = $this->requestStack->getSession();
        $session->invalidate();
        $this->container->get('security.token_storage')->setToken(null);

        $this->addFlash('success', 'Usuario eliminado con éxito.');
        return $this->redirectToRoute('app_login');
    }
}
