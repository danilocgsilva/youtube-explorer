<?php

declare(strict_types=1);

namespace App\Entity;

use App\Data\FetchMethod;
use App\Repository\ChannelSearchHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChannelSearchHistoryRepository::class)]
class ChannelSearchHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $searchTerm = null;

    #[ORM\Column(length: 255)]
    private ?string $channelName = null;

    #[ORM\Column(length: 255)]
    private ?string $channelId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $whenFetched = null;

    #[ORM\Column(nullable: true, enumType: FetchMethod::class)]
    private ?FetchMethod $fetchMethod = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(string $searchTerm): static
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function getChannelName(): ?string
    {
        return $this->channelName;
    }

    public function setChannelName(string $channelName): static
    {
        $this->channelName = $channelName;

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

    public function getWhenFetched(): ?\DateTimeInterface
    {
        return $this->whenFetched;
    }

    public function setWhenFetched(\DateTimeInterface $whenFetched): static
    {
        $this->whenFetched = $whenFetched;

        return $this;
    }

    public function getFetchMethod(): ?FetchMethod
    {
        return $this->fetchMethod;
    }

    public function setFetchMethod(?FetchMethod $fetchMethod): static
    {
        $this->fetchMethod = $fetchMethod;

        return $this;
    }
}
