<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column(length: 255)]
    private ?string $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tempUnit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $windUnit = null;

    #[ORM\Column]
    private ?bool $showHumidity = null;

    #[ORM\Column]
    private ?bool $showPressure = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    private ?User $user = null;

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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getTempUnit(): ?string
    {
        return $this->tempUnit;
    }

    public function setTempUnit(?string $tempUnit): static
    {
        $this->tempUnit = $tempUnit;

        return $this;
    }

    public function getWindUnit(): ?string
    {
        return $this->windUnit;
    }

    public function setWindUnit(?string $windUnit): static
    {
        $this->windUnit = $windUnit;

        return $this;
    }

    public function isShowHumidity(): ?bool
    {
        return $this->showHumidity;
    }

    public function setShowHumidity(bool $showHumidity): static
    {
        $this->showHumidity = $showHumidity;

        return $this;
    }

    public function isShowPressure(): ?bool
    {
        return $this->showPressure;
    }

    public function setShowPressure(bool $showPressure): static
    {
        $this->showPressure = $showPressure;

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
