<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SongRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=SongRepository::class)
 * @ORM\Table(name="rep_song")
 */
class Song
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @NotBlank(message="song must have a name")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ost;

    /**
     * @ORM\ManyToMany(targetEntity=Artist::class, inversedBy="songs")
     */
    private $artists;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="songs")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="song", orphanRemoval=true)
     */
    private $media;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, mappedBy="songs")
     */
    private $playlists;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="songs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createBy;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $originalName;

    public function __construct()
    {
        $this->artists = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->playlists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOst(): ?string
    {
        return $this->ost;
    }

    public function setOst(?string $ost): self
    {
        $this->ost = $ost;

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists[] = $artist;
        }

        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        $this->artists->removeElement($artist);

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getArtistsName(): string
    {
        $string = '';
        $artists = $this->getArtists();
        foreach ($artists as $artist) {
            $string .= $artist->getName() . ', ';
        }

        return substr($string, 0, -2);

    }

    public function getTagsName(): string
    {
        $string = '';
        $tags = $this->getTags();
        foreach ($tags as $tag) {
            $string .= $tag->getName() . ', ';
        }

        return substr($string, 0, -2);

    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->setSong($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getSong() === $this) {
                $medium->setSong(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
            $playlist->addSong($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->removeElement($playlist)) {
            $playlist->removeSong($this);
        }

        return $this;
    }

    public function getCreateBy(): ?User
    {
        return $this->createBy;
    }

    public function setCreateBy(?User $createBy): self
    {
        $this->createBy = $createBy;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }
}
