<?php
// src/Controller/RegistroController.php
namespace App\Controller;

use App\Entity\User;
use App\Controller\ImageUploader; // Importa tu clase de subida de imágenes
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RegistroController extends AbstractController
{
    #[Route(path: '/registro', name: 'app_registro')]
    public function registro(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        SluggerInterface $slugger, 
        ImageUploader $imageUploader,
        ParameterBagInterface $params
    ): Response {
        if ($request->isMethod('POST')) {
            $nombre = $request->request->get('nombre');
            $email = $request->request->get('email');
            $plainPassword = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $foto = $request->files->get('foto');

            // Validar si algún campo está vacío
            if (empty($nombre) || empty($email) || empty($plainPassword) || empty($confirmPassword)) {
                $this->addFlash('error', 'Todos los campos son obligatorios.');
                return $this->redirectToRoute('app_registro');
            }

            // Validar que las contraseñas coincidan
            if ($plainPassword !== $confirmPassword) {
                $this->addFlash('error', 'Las contraseñas no coinciden.');
                return $this->redirectToRoute('app_registro');
            }

            // Validar que el correo no esté en uso
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'El correo ya está registrado.');
                return $this->redirectToRoute('app_registro');
            }

            // Validar que el nombre de usuario no esté en uso
            $existingUsername = $entityManager->getRepository(User::class)->findOneBy(['user' => $nombre]);
            if ($existingUsername) {
                $this->addFlash('error', 'El nombre de usuario ya está registrado.');
                return $this->redirectToRoute('app_registro');
            }

            // Validar formato del correo (opcional)
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'El formato del correo no es válido.');
                return $this->redirectToRoute('app_registro');
            }

            // Procesar la foto de perfil
            $fotoPerfil = null;
            if ($foto) {
                $newFilename = uniqid() . '.' . $foto->guessExtension();
                try {
                    // Usar ImageUploader para subir la imagen al Droplet
                    $imageUploader->uploadImage($foto, $newFilename);

                    // Guardar la ruta de la imagen en la base de datos
                    $fotoPerfil = ('http://' . $params->get('sftp_user') . '/imagenes/' . $newFilename);

                } catch (\Exception $e) {
                    $this->addFlash('error', 'No se pudo subir la foto. Por favor, inténtalo de nuevo.');
                    return $this->redirectToRoute('app_registro');
                }
            }

            // Crear el nuevo usuario
            $user = new User();
            $user->setUsername($nombre);
            $user->setEmail($email);
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );
            $user->setFotoPerfil($fotoPerfil);

            // Guardar el usuario en la base de datos
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirigir al usuario a la página de inicio de sesión
            $this->addFlash('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registro.html.twig');
    }
}
