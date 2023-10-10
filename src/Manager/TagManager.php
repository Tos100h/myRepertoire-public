<?php

namespace App\Manager;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\SongRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class TagManager
{

    private TagRepository $tagRepository;
    private SongRepository $songRepository;
    private PaginatorInterface $paginator;

    public function __construct(TagRepository $tagRepository, SongRepository $songRepository, PaginatorInterface $paginator) {
        $this->tagRepository = $tagRepository;
        $this->songRepository = $songRepository;
        $this->paginator = $paginator;
    }

    public function getTags(int $page, int $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->tagRepository->findAll(),
            $page,
            $limit
        );

        return $results;
    }

    public function save(Tag $tag): Tag
    {
        $this->tagRepository->add($tag, true);
        return $tag;
    }

    public function getSongs(Tag $tag, int $page, int $limit): SlidingPagination
    {
        $results = $this->paginator->paginate(
            $this->songRepository->getSongsByTag($tag->getId()),
            $page,
            $limit,
        );

        return $results;
    }

    public function delete(Tag $tag): void
    {
        $this->tagRepository->remove($tag, true);
    }
}