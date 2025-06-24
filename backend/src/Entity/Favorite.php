<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['favorite:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['favorite:read'])]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(['favorite:read'])]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Groups(['favorite:read'])]
    private ?float $longitude = null;

    #[ORM\Column]
    #[Groups(['favorite:read'])]
    private ?int $position = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['favorite:read'])]
    private bool $showHumidity = false;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['favorite:read'])]
    private bool $showPressure = false;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['favorite:read'])]
    private bool $showWind = false;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['favorite:read'])]
    private bool $showUV = false;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['favorite:read'])]
    private bool $showSunCycle = false;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['favorite:read'])]
    private bool $showVisibility = false;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['favorite:read'])]
    private ?array $gridLayout = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    private ?User $user = null;

    // ===== Getters / Setters =====

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }
    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }
    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }
    public function setPosition(int $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function isShowHumidity(): bool
    {
        return $this->showHumidity;
    }
    public function setShowHumidity(bool $val): static
    {
        $this->showHumidity = $val;
        return $this;
    }

    public function isShowPressure(): bool
    {
        return $this->showPressure;
    }
    public function setShowPressure(bool $val): static
    {
        $this->showPressure = $val;
        return $this;
    }

    public function isShowWind(): bool
    {
        return $this->showWind;
    }
    public function setShowWind(bool $val): static
    {
        $this->showWind = $val;
        return $this;
    }

    public function isShowUV(): bool
    {
        return $this->showUV;
    }
    public function setShowUV(bool $val): static
    {
        $this->showUV = $val;
        return $this;
    }

    public function isShowSunCycle(): bool
    {
        return $this->showSunCycle;
    }
    public function setShowSunCycle(bool $val): static
    {
        $this->showSunCycle = $val;
        return $this;
    }

    public function isShowVisibility(): bool
    {
        return $this->showVisibility;
    }
    public function setShowVisibility(bool $val): static
    {
        $this->showVisibility = $val;
        return $this;
    }

    public function getGridLayout(): ?array
    {
        return $this->gridLayout;
    }
    public function setGridLayout(?array $gridLayout): static
    {
        $this->gridLayout = $gridLayout;
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
}
