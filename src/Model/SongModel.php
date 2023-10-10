<?php

namespace App\Model;

use App\Entity\Song;
use Symfony\Component\Validator\Constraints as Assert;

class SongModel
{
    /**
     * @Assert\Valid
     */
    private Song $song;
    private ?string $newArtist = null;


    /**
     * Get the value of song
     */ 
    public function getSong()
    {
        return $this->song;
    }

    /**
     * Set the value of song
     *
     * @return  self
     */ 
    public function setSong($song)
    {
        $this->song = $song;

        return $this;
    }

    /**
     * Get the value of newArtist
     */ 
    public function getNewArtist()
    {
        return $this->newArtist;
    }

    /**
     * Set the value of newArtist
     *
     * @return  self
     */ 
    public function setNewArtist($newArtist)
    {
        $this->newArtist = $newArtist;

        return $this;
    }

}