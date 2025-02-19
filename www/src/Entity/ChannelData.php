<?php

namespace App\Entity;

use App\Repository\ChannelDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChannelDataRepository::class)]
class ChannelData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'channelData')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Channel $channel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fetchedDate = null;

    #[ORM\Column]
    private ?int $videosCount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getFetchedDate(): ?\DateTimeInterface
    {
        return $this->fetchedDate;
    }

    public function setFetchedDate(\DateTimeInterface $fetchedDate): static
    {
        $this->fetchedDate = $fetchedDate;

        return $this;
    }

    public function getVideosCount(): ?int
    {
        return $this->videosCount;
    }

    public function setVideosCount(int $videosCount): static
    {
        $this->videosCount = $videosCount;

        return $this;
    }
}
