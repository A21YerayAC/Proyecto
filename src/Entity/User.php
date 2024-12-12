<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 255)]
    private $user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fotoPerfil = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(
        name: 'user_followers',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'follower_id', referencedColumnName: 'id')]
    )]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'followers')]
    private Collection $following;

    public function __construct()
    {
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
    }

    // Métodos para obtener los seguidores y seguidos
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function getFollowing(): Collection
    {
        return $this->following;
    }

    // Método para seguir a otro usuario
    public function follow(User $user): void
    {
        if (!$this->isFollowing($user)) {
            $this->following->add($user);
            $user->followers->add($this);
        }
    }

    // Método para dejar de seguir a otro usuario
    public function unfollow(User $user): void
    {
        if ($this->isFollowing($user)) {
            $this->following->removeElement($user);
            $user->followers->removeElement($this);
        }
    }

    // Verificar si el usuario sigue a otro
    public function isFollowing(User $user): bool
    {
        return $this->following->contains($user);
    }

    public function getFotoPerfil(): ?string
    {
        return $this->fotoPerfil;
    }

    public function setFotoPerfil(?string $fotoPerfil): static
    {
        $this->fotoPerfil = $fotoPerfil;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getUsername(): string
    {
        return $this->user;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUsername(string $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

}
