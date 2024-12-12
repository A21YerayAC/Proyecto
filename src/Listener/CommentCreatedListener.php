<?php
// src/EventListener/CommentCreatedListener.php
namespace App\Listener;

use App\Event\CommentCreatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class CommentCreatedListener
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function onCommentCreated(CommentCreatedEvent $event): void
    {
        $comment = $event->getComment();

        // Generar la notificación que se enviará al receptor
        $notificationMessage = sprintf(
            'Usuario %s ha comentado tu reseña: %s',
            $comment->getUser()->getUser(),
            $comment->getContenido()
        );

        // Aquí podrías crear un servicio o una entidad para manejar las notificaciones
        // Y luego enviarlas usando un mecanismo adecuado como un correo, una cola, etc.
    }
}
