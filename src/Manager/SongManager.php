<?php

namespace App\Manager;

use DateTime;
use App\Entity\Song;
use App\Entity\User;
use App\Entity\Artist;
use App\Model\SongModel;
use App\Manager\ArtistManager;
use App\Repository\SongRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;


class SongManager
{
    private SongRepository $songRepository;
    private ArtistManager $artistManager;
    private PaginatorInterface $paginator;

    public function __construct(SongRepository $songRepository, ArtistManager $artistManager, PaginatorInterface $paginator) {
        $this->songRepository = $songRepository;
        $this->artistManager = $artistManager;
        $this->paginator = $paginator;
    }

    public function getSongs(int $page, int $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->songRepository->findBy([], ['name' => 'ASC']),
            $page,
            $limit,
        );

        return $results;
    }

    public function createFormSongModel(?Artist $artist): SongModel
    {
        $songModel = new SongModel();

        if (null !== $artist) {
            $songModel->setsong(New Song());
            $songModel->getsong()->addArtist($artist);
        }

        return $songModel;
    }

    public function createModel(SongModel $songModel, User $user): Song 
    {
        $song = $this->create($songModel->getSong(), $user);

        $newArtist = $songModel->getNewArtist();
        if (($newArtist !== null) && ($song->getId() !== null)) {
            $this->artistManager->createMultiple($newArtist, $song);
        }

        return $song;
    }

    public function create(Song $song, User $user): Song  
    {
        $song->setCreateBy($user);
        $song->setCreationDate(New DateTime('now'));
        $this->songRepository->add($song, true);
        
        return $song;
    }

    public function save(Song $song): Song  
    {
        $this->songRepository->add($song, true);

        return $song;
    }

    public function delete(Song $song): void  
    {
        $songRepository->remove($song, true);
    }

    




    /* ***** */




    public function getBySearch(string $search, int $page, int $limit) {
        $results = $this->paginator->paginate(
            $this->songRepository->navbarSearch($search),
            $page,
            $limit
        );

        return $results;
    }

}
