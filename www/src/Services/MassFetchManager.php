<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\MassFetchJob;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class MassFetchManager
{
    private MassFetchJob $massFetchJob;
    
    public function __construct(private EntityManagerInterface $entityManager,)
    {
    }

    public function getMassFetchJob(): MassFetchJob
    {
        return $this->massFetchJob;
    }
    
    public function start(): void
    {
        $this->massFetchJob = (new MassFetchJob())
            ->setStart(new DateTime());

        $this->entityManager->persist($this->massFetchJob);
        $this->entityManager->flush();
    }

    public function finish(): void
    {
        $this->massFetchJob->setEnd(new DateTime());
        $this->entityManager->persist($this->massFetchJob);
        $this->entityManager->flush();
    }
}
