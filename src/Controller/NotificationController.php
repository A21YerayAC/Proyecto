<?php
namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    public function index(EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        
        // Obtener las notificaciones del usuario
        $notifications = $entityManager->getRepository(Notification::class)->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        // Marcar las notificaciones como leídas
        foreach ($notifications as $notification) {
            if (!$notification->getIsRead()) {
                $notification->setIsRead(true);
                $entityManager->persist($notification);
            }
        }
        $entityManager->flush();  // Guardar los cambios

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
