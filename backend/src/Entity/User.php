<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[OA\Schema(
    schema: "User",
    properties: [
        new OA\Property(property: "id", type: "integer"),
        new OA\Property(property: "firstname", type: "string"),
        new OA\Property(property: "email", type: "string"),
        // Ajoutez ici les autres propriétés exposées par "user:read"
    ],
    type: "object"
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Firstname is required')]
    #[Groups(['user:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Please enter a valid email')]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(min: 6, minMessage: 'Password must be at least {{ limit }} characters')]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 10, options: ['default' => 'Celsius'])]
    #[Groups(['user:read'])]
    #[Assert\Choice(choices: ['Celsius', 'Fahrenheit'], message: 'Choisissez une unité de température valide.')]
    private ?string $uniteTemperature = 'Celsius'; // 'celsius' ou 'fahrenheit'

    #[ORM\Column(length: 10, options: ['default' => 'hPa'])]
    #[Groups(['user:read'])]
    #[Assert\Choice(choices: ['hPa', 'mmHg'], message: 'Choisissez une unité de pression valide.')]
    private ?string $unitePression = 'hPa'; // 'hPa' ou 'mmHg'

    #[ORM\Column(length: 10, options: ['default' => 'km/h'])]
    #[Groups(['user:read'])]
    #[Assert\Choice(choices: ['km/h', 'm/s'], message: 'Choisissez une unité de vent valide.')]
    private ?string $uniteVent = 'km/h'; // 'km/h' ou 'm/s'

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $emailNotification = null;

    /**
     * @var Collection<int, Favorite>
     */
    #[ORM\OneToMany(targetEntity: Favorite::class, mappedBy: 'user')]
    private Collection $favorites;

    public function __construct()
    {
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setUser($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            if ($favorite->getUser() === $this) {
                $favorite->setUser(null);
            }
        }

        return $this;
    }

    public function getUniteTemperature(): ?string
    {
        return $this->uniteTemperature;
    }
    public function setUniteTemperature(string $unit): static
    {
        $this->uniteTemperature = $unit;
        return $this;
    }

    public function getUnitePression(): ?string
    {
        return $this->unitePression;
    }
    public function setUnitePression(string $unit): static
    {
        $this->unitePression = $unit;
        return $this;
    }

    public function getUniteVent(): ?string
    {
        return $this->uniteVent;
    }
    public function setUniteVent(string $unit): static
    {
        $this->uniteVent = $unit;
        return $this;
    }

    public function getEmailNotification(): ?string
    {
        return $this->emailNotification;
    }
    public function setEmailNotification(?string $email): static
    {
        $this->emailNotification = $email;
        return $this;
    }
}
