<?php

namespace App\Entity;

use App\Repository\MassFetchJobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MassFetchJobRepository::class)]
class MassFetchJob
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end = null;

    /**
     * @var Collection<int, MassFetchIteration>
     */
    #[ORM\OneToMany(targetEntity: MassFetchIteration::class, mappedBy: 'massFetchJob')]
    private Collection $massFetchIterations;

    public function __construct()
    {
        $this->massFetchIterations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeInterface $end): static
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return Collection<int, MassFetchIteration>
     */
    public function getMassFetchIterations(): Collection
    {
        return $this->massFetchIterations;
    }

    public function addMassFetchIteration(MassFetchIteration $massFetchIteration): static
    {
        if (!$this->massFetchIterations->contains($massFetchIteration)) {
            $this->massFetchIterations->add($massFetchIteration);
            $massFetchIteration->setMassFetchJob($this);
        }

        return $this;
    }

    public function removeMassFetchIteration(MassFetchIteration $massFetchIteration): static
    {
        if ($this->massFetchIterations->removeElement($massFetchIteration)) {
            // set the owning side to null (unless already changed)
            if ($massFetchIteration->getMassFetchJob() === $this) {
                $massFetchIteration->setMassFetchJob(null);
            }
        }

        return $this;
    }
}
