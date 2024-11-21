<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Review;
use Symfony\Component\HttpFoundation\Response;
use phpseclib3\Net\SFTP;
use App\Form\ReviewFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{
    #[Route('/review/new', name: 'app_newReview')]
    public function createReview(Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Procesar la imagen
            $imagenFile = $form->get('imagen')->getData(); // Obtiene el archivo subido

            if ($imagenFile) {
                // Genera un nombre único para el archivo
                $newFilename = uniqid() . '.' . $imagenFile->guessExtension();

                // Subir al droplet
                $dropletIp = getenv('DROPLET_IP');
                $dropletUser = getenv('DROPLET_USER');
                $dropletPassword = getenv('DROPLET_PASSWORD');

                $sftp = new SFTP($dropletIp);

                if ($sftp->login($dropletUser, $dropletPassword)) {
                    // Ruta donde guardar en el droplet
                    $remotePath = '/var/www/uploads/' . $newFilename;
                    $sftp->put($remotePath, $imagenFile->getPathname(), SFTP::SOURCE_LOCAL_FILE);

                    // Guardar la ruta en la base de datos
                    $review->setTitulo($form->get('titulo')->getData());
                    $review->setContenido($form->get('contenido')->getData());
                    $review->setValoracion($form->get('valoracion')->getData());
                    $review->setFechaPublicacion(new \DateTimeImmutable());
                    $review->setImagenRuta('http://' . $dropletIp . '/uploads/' . $newFilename); // Ruta accesible
                } else {
                    throw new \Exception('No se pudo conectar al droplet.');
                }
            }

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }

        // Renderiza el formulario si no se ha enviado o si no es válido
        return $this->render('formularioReview.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
