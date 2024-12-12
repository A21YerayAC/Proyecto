<?php

namespace App\Entity;
use App\Entity\User;
use App\Entity\Review;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\CommentRepository;


// Entidad Comment
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenido = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaComentario = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
#[ORM\JoinColumn(nullable: false)]
private ?User $user = null;

#[ORM\ManyToOne(targetEntity: Review::class, inversedBy: 'comments')]
#[ORM\JoinColumn(nullable: false)]
private ?Review $review = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): static
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getFechaComentario(): ?\DateTimeInterface
    {
        return $this->fechaComentario;
    }

    public function setFechaComentario(\DateTimeInterface $fechaComentario): static
    {
        $this->fechaComentario = $fechaComentario;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): static
    {
        $this->review = $review;

        return $this;
    }
}
