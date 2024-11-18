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

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'user_followers')]
    private Collection $seguidores;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'seguidores')]
    private Collection $siguiendo;

    public function __construct()
    {
        $this->seguidores = new ArrayCollection();
        $this->siguiendo = new ArrayCollection();
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
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setNombre(string $user): self
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

    /**
     * @return Collection<int, User>
     */
    public function getSeguidores(): Collection
    {
        return $this->seguidores;
    }

    public function addSeguidor(User $user): self
    {
        if (!$this->seguidores->contains($user)) {
            $this->seguidores->add($user);
            $user->addSiguiendo($this);  // Asegura que el otro usuario también lo siga
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getSiguiendo(): Collection
    {
        return $this->siguiendo;
    }

    public function addSiguiendo(User $user): self
    {
        if (!$this->siguiendo->contains($user)) {
            $this->siguiendo->add($user);
        }

        return $this;
    }
}
