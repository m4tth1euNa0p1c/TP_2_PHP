<?php

namespace App\Entity;

use App\Repository\EngineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EngineRepository::class)]
class Engine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private ?string $brand = null;

    #[ORM\OneToOne(inversedBy: 'engine', targetEntity: Team::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, unique: true, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Team $team = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }
}
