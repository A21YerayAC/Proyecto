<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Review;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ReviewFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\ImageUploader;  // Asegúrate de importar la clase
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ReviewController extends AbstractController
{
    #[Route('/review/new', name: 'app_newReview')]
public function createReview(Request $request, EntityManagerInterface $entityManager, ImageUploader $imageUploader, Security $security, ParameterBagInterface $params): Response
{
    $review = new Review();
    $form = $this->createForm(ReviewFormType::class, $review);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Asignar el usuario actual al objeto Review
        $user = $security->getUser();  // Obtiene el usuario autenticado
        if ($user) {
            $review->setUser($user);  // Asigna el usuario al campo 'user'
        } else {
            // Si no hay usuario autenticado, manejar el caso de error
            $this->addFlash('error', 'Debes estar autenticado para publicar una reseña.');
            return $this->redirectToRoute('app_login');  // Redirige al login si no hay usuario autenticado
        }

        // Procesar las imágenes
        $photos = $form->get('photos')->getData(); // Obtiene las imágenes subidas
        if ($photos) {
            foreach ($photos as $photo) {
                // Generar un nombre único para cada imagen
                $newFilename = uniqid() . '.' . $photo->guessExtension();
                try {
                    // Usar ImageUploader para subir la imagen al Droplet
                    $imageUploader->uploadImage($photo, $newFilename);

                    // Guardar la ruta de la imagen en la base de datos
                    $review->addPhoto('http://' . $params->get('sftp_host') . '/imagenes/' . $newFilename);
                } catch (\Exception $e) {
                    // Si ocurre algún error con la subida
                    $this->addFlash('error', 'Error al subir la imagen: ' . $e->getMessage());
                    return $this->redirectToRoute('app_newReview');
                }
            }
        }

        // Otros campos del formulario
        $review->setTitulo($form->get('titulo')->getData());
        $review->setContenido($form->get('contenido')->getData());
        $review->setValoracion($form->get('valoracion')->getData());
        $review->setFechaPublicacion(new \DateTimeImmutable());

        // Persistir la reseña
        $entityManager->persist($review);
        $entityManager->flush();

        return $this->redirectToRoute('app_homepage');
    }

    return $this->render('formularioReview.html.twig', [
        'form' => $form->createView(),
    ]);
}}