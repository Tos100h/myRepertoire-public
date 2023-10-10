<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Artist;
use App\Form\SongType;
use App\Entity\Playlist;
use App\Model\SongModel;
use App\Form\SongModelType;
use App\Manager\SongManager;
use App\Manager\MediaManager;
use App\Manager\PlaylistManager;
use App\Manager\SongModelManager;
use App\Form\PlaylistSelectorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/songs")
 */
class SongController extends AbstractController
{
    /**
     * @Route("/", name="app_song_index", methods={"GET"})
     */
    public function index(Request $request, SongManager $songManager): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $songs = $songManager->getSongs($page, $limit);

        return $this->render('song/index.html.twig', [
            'songs' => $songs,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/new/{artist}", name="app_song_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, SongManager $songManager, Artist $artist = null): Response
    {
        $songModel = $songManager->createFormSongModel($artist);
        
        $form = $this->createForm(SongModelType::class, $songModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $song = $songManager->createModel($songModel, $user);

            if ($song->getId() !== null) {
                $this->addFlash('success', 'Song : '.$song->getName().' created');

                return $this->redirectToRoute('app_song_show', ['id' => $song->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('song/new.html.twig', [
            'song' => $songModel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_song_show", methods={"GET"})
     */
    public function show(Song $song, PlaylistManager $playlistManager, MediaManager $mediaManager): Response
    {
        $user = $this->getUser();
        $playlists = [];
        if ($user instanceof User) {
            $playlists = $playlistManager->getBySongAndUser($user, $song);
        }

        $media = $mediaManager->getBysong($song);

        return $this->render('song/show.html.twig', [
            'song' => $song,
            'types' => Media::MEDIATYPES,
            'playlists' => $playlists,
            'media' => $media
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_song_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Song $song, SongManager $songManager): Response
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $songManager->save($song);
            
            return $this->redirectToRoute('app_song_show', ['id' => $song->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('song/edit.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_song_delete", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Song $song, SongManager $songManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$song->getId(), $request->request->get('_token'))) {
            $name = $song->getName();
            $songManager->delete($song);
            $this->addFlash('warning', 'Song : '.$name.' deleted');
        }

        return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/add-to-playlist/{song}", name="app_song_add_playlist", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function editPlaylists(Song $song, Request $request, PlaylistManager $playlistManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PlaylistSelectorType::class, $song, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlistManager->addPlaylists($user, $song);
            return $this->redirectToRoute('app_song_show', ['id' => $song->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('playlist/selector.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }
}