<?php

namespace App\Controller;


use App\Manager\SongManager;
use App\Manager\MediaManager;
use App\Manager\ArtistManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private const TYPES = ['songs', 'artists', 'media'];

    /**
     * @Route("/search", name="app_search_navbar", methods={"GET", "POST"})
     */
    public function search(Request $request, SongManager $songManager, ArtistManager $artistManager, MediaManager $mediaManager): Response
    {
        $search = $request->query->get("search");
        $limit = $request->query->getInt('limit', 3);

        $songs = $songManager->getBySearch($search, 1, $limit);
        $artists = $artistManager->getBySearch($search, 1, $limit);
        $media = $mediaManager->getBySearch($search, 1, $limit);

        return $this->render('search/index.html.twig', [
            'search' => $search,
            'limit' => $limit,
            'songs' => $songs,
            'artists' => $artists,
            'media' => $media,
        ]);
    }

    /**
     * @Route("/search/more={type}", name="app_search_navbar_more", methods={"GET", "POST"})
     */
    public function moreResults(string $type, Request $request, SongManager $songManager, ArtistManager $artistManager, MediaManager $mediaManager): Response
    {
        if (!in_array($type, SELF::TYPES)) {
            return $this->redirectToRoute('app_search_navbar', ['search' => $search], Response::HTTP_SEE_OTHER);
        }

        $search = $request->query->get("search");
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 20);

        if ($type === 'songs') {
            $results = $songManager->getBySearch($search, $page, $limit);
        }

        if ($type === 'artists') {
            $results = $artistManager->getBySearch($search, $page, $limit);
        }

        if ($type === 'media') {
            $results = $mediaManager->getBySearch($search, $page, $limit);
        }

        return $this->render('search/more.html.twig', [
            'search' => $search,
            'type' => $type,
            'results' => $results,
            'limit' => $limit,
        ]);
    }
}
