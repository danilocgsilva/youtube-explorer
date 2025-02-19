<?php

namespace App\Entity;

use App\Repository\MassFetchIterationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OwlCorp\DoctrineMicrotime\DBAL\Types\DateTimeMicroType;

#[ORM\Entity(repositoryClass: MassFetchIterationRepository::class)]
class MassFetchIteration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nextPageToken = null;

    #[ORM\ManyToOne(inversedBy: 'massFetchIterations')]
    private ?MassFetchJob $massFetchJob = null;

    #[ORM\Column(nullable: true)]
    private ?int $iterationPosition = null;

    #[ORM\Column(type: DateTimeMicroType::NAME)]
    private ?\DateTimeInterface $time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNextPageToken(): ?string
    {
        return $this->nextPageToken;
    }

    public function setNextPageToken(?string $nextPageToken): static
    {
        $this->nextPageToken = $nextPageToken;

        return $this;
    }

    public function getMassFetchJob(): ?MassFetchJob
    {
        return $this->massFetchJob;
    }

    public function setMassFetchJob(?MassFetchJob $massFetchJob): static
    {
        $this->massFetchJob = $massFetchJob;

        return $this;
    }

    public function getIterationPosition(): ?int
    {
        return $this->iterationPosition;
    }

    public function setIterationPosition(?int $iterationPosition): static
    {
        $this->iterationPosition = $iterationPosition;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }
}
