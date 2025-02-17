<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    private ?Channel $channel = null;

    #[ORM\Column(length: 255)]
    private ?string $publishingTime = null;

    #[ORM\Column(length: 255)]
    private ?string $youtubeVideoId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fetchedDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
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

    public function getPublishingTime(): ?string
    {
        return $this->publishingTime;
    }

    public function setPublishingTime(string $publishingTime): static
    {
        $this->publishingTime = $publishingTime;

        return $this;
    }

    public function getYoutubeVideoId(): ?string
    {
        return $this->youtubeVideoId;
    }

    public function setYoutubeVideoId(string $youtubeVideoId): static
    {
        $this->youtubeVideoId = $youtubeVideoId;

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
}
