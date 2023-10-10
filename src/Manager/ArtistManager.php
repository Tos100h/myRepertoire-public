<?php

namespace App\Manager;

use App\Entity\Song;
use App\Entity\Artist;
use App\Repository\SongRepository;
use App\Repository\ArtistRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class ArtistManager
{

    private ArtistRepository $artistRepository;
    private SongRepository $songRepository;
    private PaginatorInterface $paginator;

    public function __construct(ArtistRepository $artistRepository, SongRepository $songRepository, PaginatorInterface $paginator) {
        $this->artistRepository = $artistRepository;
        $this->songRepository = $songRepository;
        $this->paginator = $paginator;
    }

    public function getArtists(int $page, int $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->artistRepository->findBy([], ['name' => 'ASC']),
            $page,
            $limit,
        );

        return $results;
    }

    public function save(Artist $artist): Artist
    {
        $this->artistRepository->add($artist, true);

        return $artist;
    }
    
    public function getSongs(Artist $artist, int $page, int $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->songRepository->getSongsByArtist($artist->getId()),
            $page,
            $limit,
        );
        
        return $results;
    }

    public function delete(Artist $artist): void
    {
        $this->artistRepository->remove($artist, true);
    }

    public function createMultiple(string $newArtists, Song $song): void
    {         
        $artistArray = explode(";", $newArtists);
        foreach ($artistArray as $thisArtist) {
            $name = trim($thisArtist);
            $artist = $this->get($name);
            $song->addArtist($artist);
        }
        $this->songRepository->add($song, true);  
    }

    public function get(string $name): Artist
    {
        $artist = $this->artistRepository->findOneBy(['name' => $name]);

        if ($artist === null)
        {
            $artist = New Artist();
            $artist->setName($name);
            $artist = $this->save($artist);
        }

        return $artist;
    }

    public function getBySearch(string $search, int $page, int $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->artistRepository->navbarSearch($search),
            $page,
            $limit
        );

        return $results;
    }
 
}