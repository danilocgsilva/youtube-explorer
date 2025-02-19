<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChannelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'channel')]
    private Collection $videos;

    /**
     * @var Collection<int, ChannelData>
     */
    #[ORM\OneToMany(targetEntity: ChannelData::class, mappedBy: 'channel')]
    private Collection $channelData;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->channelData = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setChannel($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getChannel() === $this) {
                $video->setChannel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChannelData>
     */
    public function getChannelData(): Collection
    {
        return $this->channelData;
    }

    public function addChannelData(ChannelData $channelData): static
    {
        if (!$this->channelData->contains($channelData)) {
            $this->channelData->add($channelData);
            $channelData->setChannel($this);
        }

        return $this;
    }

    public function removeChannelData(ChannelData $channelData): static
    {
        if ($this->channelData->removeElement($channelData)) {
            // set the owning side to null (unless already changed)
            if ($channelData->getChannel() === $this) {
                $channelData->setChannel(null);
            }
        }

        return $this;
    }
}
