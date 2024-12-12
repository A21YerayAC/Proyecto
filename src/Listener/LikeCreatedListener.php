<?php 
// Listener/LikeCreatedListener.php
namespace App\Listener;

use App\Event\LikeCreatedEvent;
use Psr\Log\LoggerInterface;

class LikeCreatedListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onLikeCreated(LikeCreatedEvent $event)
    {
        $like = $event->getLike();
        $this->logger->info('Nuevo like añadido: ' . $like->getId());
        // Puedes agregar lógica para enviar notificaciones aquí.
    }
}
