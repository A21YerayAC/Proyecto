<?php
// src/Controller/NotificationController.php
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
        $notifications = $entityManager->getRepository(Notification::class)->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );
        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
