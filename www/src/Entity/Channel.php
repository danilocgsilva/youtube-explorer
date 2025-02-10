<?php

namespace App\Entity;

use App\Repository\ChannelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChannelRepository::class)]
class Channel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channelName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channelAlias = null;

    #[ORM\Column(length: 255)]
    private ?string $channelId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannelName(): ?string
    {
        return $this->channelName;
    }

    public function setChannelName(?string $channelName): static
    {
        $this->channelName = $channelName;

        return $this;
    }

    public function getChannelAlias(): ?string
    {
        return $this->channelAlias;
    }

    public function setChannelAlias(?string $channelAlias): static
    {
        $this->channelAlias = $channelAlias;

        return $this;
    }

    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    public function setChannelId(string $channelId): static
    {
        $this->channelId = $channelId;

        return $this;
    }
}
