<?php

namespace App\Manager;

use App\Entity\Song;
use App\Entity\Media;
use App\Repository\SongRepository;
use App\Repository\MediaRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class MediaManager
{
    private MediaRepository $mediaRepository;
    private SongRepository $songRepository;
    private PaginatorInterface $paginator;

    public function __construct(MediaRepository $mediaRepository, SongRepository $songRepository, PaginatorInterface $paginator) {
        $this->mediaRepository = $mediaRepository;
        $this->songRepository = $songRepository;
        $this->paginator = $paginator;
    }

    public function setType(Media $media): Media
    {
        $link = trim($media->getLink());
        $media->setLink($link);

        if(str_contains($link, 'youtube.com/')) {
            $media->setType('youtube');
            $link = $this->editYtLinks($link);
            $media->setLink($link);

            return $media;
        }

        if(str_contains($link, 'musescore.com/') && str_contains($link, '/scores/')) {
            $media->setType('musescore');

            return $media;
        }

        $media->setType('other');

        return $media;
    }

    public function getByLink(string $string): ?Media 
    {
        $result = $this->mediaRepository->findOneBy(['link' => $string]);

        return $result;
    }

    public function create(Media $media): Media
    {      
        $lastRank = $this->getLastrank($media);
        $media->setRank($lastRank+10);
        $media = $this->save($media);
        
        return $media;
    }

    public function getLastrank(Media $media): int 
    {
        $last = $this->mediaRepository->findOneBy(['song' => $media->getSong(), 'type' => $media->getType()], ['rank' => 'DESC']);
        if(null === $last) {

            return 0;
        }

        return $last->getRank();
    }

    public function save(Media $media): Media
    {
        $this->mediaRepository->add($media, true);
        $this->checkRank($media->getSong());

        return $media;
    }

    public function prepareEdit(Media $media): Media
    {
        $media = $this->setType($media);

        return $media;
    }

    public function checkExistingMedia(Media $media): ?Media
    {
        $result = $this->getByLink($media->getLink());
        if ($result === $media) {
            
            return null;
        }

        return $result;
    }

     /* *** */

    public function getBysong(Song $song): array
    {
        $results = $this->mediaRepository->findBy(['song' => $song], ['type' => 'DESC', 'rank' => 'ASC']);
        
        return $results;
    }

    /* *** */







    public function delete(Media $media): void
    {   
        $song = $media->getSong();
        $this->mediaRepository->remove($media, true);
        $this->checkRank($media->getSong());
    }

    public function editYtLinks(string $link): string
    {
        if(str_contains($link,'&list=')) {
            $array = explode('&list=', $link);
            $link = $array[0];
        }

        if(str_contains($link,'&t=')) {
            $array = explode('&t=', $link);
            $link = $array[0];
        }

        return $link;
    }

    public function setRank(Media $media, int $rank): Media 
    {
        $media->setRank($rank);
        $this->save($media);

        return $media;
    }

    public function checkRank(Song $song): void 
    {
        $types = Media::MEDIATYPES;
        foreach ($types as $type) {

            $media = $this->mediaRepository->findBy(['song' => $song, 'type' => $type], ['rank' => 'ASC']);
            
            $index = 1;
            foreach($media as $line){
                if ($line->getRank() != $index * 10) {
                    $line->setRank($index * 10);
                    $this->mediaRepository->add($line, true);
                }
                $index++;
            }
        }

    }

    public function getBySearch(string $search, int $page, int $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->mediaRepository->navbarSearch($search),
            $page,
            $limit
        );

        return $results;
    }

}