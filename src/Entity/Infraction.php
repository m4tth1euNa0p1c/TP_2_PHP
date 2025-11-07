<?php

namespace App\Entity;

use App\Repository\InfractionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: InfractionRepository::class)]
class Infraction
{
    public const TYPE_PENALTY_POINTS = 'PENALTY_POINTS';
    public const TYPE_FINE_EUR = 'FINE_EUR';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $occurredAt = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 160)]
    private ?string $raceName = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::TYPE_PENALTY_POINTS, self::TYPE_FINE_EUR])]
    private ?string $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?string $amount = null;

    #[ORM\ManyToOne(targetEntity: Driver::class, inversedBy: 'infractions')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Driver $driver = null;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'infractions')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Team $team = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOccurredAt(): ?\DateTimeInterface
    {
        return $this->occurredAt;
    }

    public function setOccurredAt(\DateTimeInterface $occurredAt): static
    {
        $this->occurredAt = $occurredAt;

        return $this;
    }

    public function getRaceName(): ?string
    {
        return $this->raceName;
    }

    public function setRaceName(string $raceName): static
    {
        $this->raceName = $raceName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): static
    {
        $this->driver = $driver;

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

    

    #[Assert\Callback]
    public function validateTargetExclusivity(ExecutionContextInterface $context): void
    {
        $hasDriver = $this->driver !== null;
        $hasTeam = $this->team !== null;

        if (!$hasDriver && !$hasTeam) {
            $context->buildViolation('Une infraction doit cibler soit un pilote, soit une écurie.')
                ->atPath('driver')
                ->addViolation();
        }

        if ($hasDriver && $hasTeam) {
            $context->buildViolation('Une infraction ne peut pas cibler à la fois un pilote et une écurie.')
                ->atPath('driver')
                ->addViolation();
        }
    }

    

    #[Assert\Callback]
    public function validateTypeAndAmount(ExecutionContextInterface $context): void
    {
        if ($this->type === self::TYPE_PENALTY_POINTS) {
            $amount = (float) $this->amount;
            if ($amount < 1) {
                $context->buildViolation('Une pénalité en points doit avoir un montant >= 1.')
                    ->atPath('amount')
                    ->addViolation();
            }
        }

        if ($this->type === self::TYPE_FINE_EUR) {
            $amount = (float) $this->amount;
            if ($amount < 0) {
                $context->buildViolation('Une amende doit avoir un montant >= 0.')
                    ->atPath('amount')
                    ->addViolation();
            }
        }
    }
}
