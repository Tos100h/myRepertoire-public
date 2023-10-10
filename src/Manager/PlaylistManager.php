<?php

namespace App\Manager;

use App\Entity\Song;
use App\Entity\User;
use App\Entity\Artist;
use App\Entity\Playlist;
use App\Repository\SongRepository;
use App\Repository\PlaylistRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class PlaylistManager
{
    private PlaylistRepository $playlistRepository;
    private SongRepository $songRepository;
    private PaginatorInterface $paginator;

    public function __construct(PlaylistRepository $playlistRepository, SongRepository $songRepository, PaginatorInterface $paginator) {
        $this->playlistRepository = $playlistRepository;
        $this->songRepository = $songRepository;
        $this->paginator = $paginator;
    }

    public function getPlaylistsByUser(User $user, int $page, int $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->playlistRepository->findBy(['user' => $user], ['name' => 'ASC']),
            $page,
            $limit,
        );

        return $results;
    }

    public function create(Playlist $playlist, User $user): Playlist
    {
        $playlist->setUser($user);
        $this->playlistRepository->add($playlist, true);

        return $playlist;
    }

    public function canViewPlaylist(Playlist $playlist,?User $user): bool
    {
        if ($playlist->isPublic()) {
            return true;
        }

        if ($playlist->getUser() === $user) {
            return true;
        }

        return false;
    }

    public function getSongs($playlist, $page, $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->songRepository->getSongsByPlaylist($playlist->getId()),
            $page,
            $limit,
        );

        return $results;
    }

    public function canEditPlaylist(Playlist $playlist, User $user): bool
    {
        if ($playlist->getUser() === $user) {
            return true;
        }

        return false;
    }

    public function save(Playlist $playlist): Playlist
    {
        $this->playlistRepository->add($playlist, true);

        return $playlist;
    }
    
    public function delete(Playlist $playlist): void
    {
        $this->playlistRepository->remove($playlist, true);
    }

    public function removePlaylist(Song $song, Playlist $playlist): void
    {
        $playlist->removeSong($song);
        $this->playlistRepository->add($playlist, true);

    }

    public function addPlaylists(User $user, Song $song) : void
    {
        $playlists = $song->getPlaylists()->toArray();
        foreach ($playlists as $playlist) {
            $playlist->addSong($song);
            $this->playlistRepository->add($playlist, true);
        }
    }

    /* ******* */

    


    public function getBySongAndUser(User $user, Song $song) {
        $p = $this->playlistRepository->getBySongAndUser($user, $song);

        return $p;
    }

}