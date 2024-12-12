<?php 
// Event/LikeCreatedEvent.php
namespace App\Event;

use App\Entity\Like;
use Symfony\Contracts\EventDispatcher\Event;

class LikeCreatedEvent extends Event
{
    public const NAME = 'like.created';

    private $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }

    public function getLike(): Like
    {
        return $this->like;
    }
}

