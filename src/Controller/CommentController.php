<?php 
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\Review;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentController extends AbstractController
{
    #[Route('/comment/{reviewId}', name: 'app_comment', methods: ['POST'])]
    public function addComment(int $reviewId, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $review = $entityManager->getRepository(Review::class)->find($reviewId);

        if (!$review) {
            return new JsonResponse(['success' => false, 'message' => 'Reseña no encontrada'], 404);
        }

        // Decodificar el contenido del request (JSON)
        $data = json_decode($request->getContent(), true);
        $content = $data['content'] ?? null;

        if (!$content) {
            return new JsonResponse(['success' => false, 'message' => 'El contenido no puede estar vacío'], 400);
        }

        try {
            // Crear y guardar comentario
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setReview($review);
            $comment->setContenido($content);
            $comment->setFechaComentario(new \DateTime());

            $entityManager->persist($comment);

            // Crear notificación
            $notification = new Notification();
            $notification->setUser($review->getUser());
            $notification->setType('comment');
            $notification->setReview($review);
            $notification->setMessage($user->getUsername() . ' ha comentado tu reseña: ' . $review->getTitulo() . " " . $content);
            $notification->setCreatedAt(new \DateTime());

            $entityManager->persist($notification);

            // Guardar en base de datos
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Comentario añadido']);
        } catch (\Exception $e) {
            error_log('Error al guardar comentario: ' . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Error al guardar el comentario'], 500);
        }
    }

    #[Route('/comment/delete/{commentId}', name: 'app_delete_comment')]
public function deleteComment(int $commentId, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
{
    $user = $this->getUser();
    $comment = $entityManager->getRepository(Comment::class)->find($commentId);

    if (!$comment) {
        $this->addFlash('error', 'Comentario no encontrado');
        return $this->redirectToRoute('app_home');
    }

    // Verificar permisos
    if ($user !== $comment->getUser() && $user !== $comment->getReview()->getUser()) {
        $this->addFlash('error', 'No tienes permiso para eliminar este comentario');
        return $this->redirectToRoute('app_homepage');
    }

    try {
        $entityManager->remove($comment);

        // Eliminar notificación asociada
        $notification = $entityManager->getRepository(Notification::class)->findOneBy([
            'user' => $comment->getReview()->getUser(),
            'type' => 'comment',
            'review' => $comment->getReview(),
        ]);

        if ($notification) {
            $entityManager->remove($notification);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Comentario eliminado con éxito.');
        return $this->redirectToRoute('app_homepage');
    } catch (\Exception $e) {
        error_log('Error al eliminar comentario: ' . $e->getMessage());
        $this->addFlash('error', 'Error al eliminar el comentario');
        return $this->redirectToRoute('app_homepage');
    }
}

    




}
