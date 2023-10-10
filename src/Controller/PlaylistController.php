<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Manager\PlaylistManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/playlists")
 */
class PlaylistController extends AbstractController
{
    /**
     * @Route("/", name="app_playlist_index", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request, PlaylistManager $playlistManager): Response
    {
        $user = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $playlists = $playlistManager->getPlaylistsByUser($user, $page, $limit);

        return $this->render('playlist/index.html.twig', [
            'playlists' => $playlists,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/new", name="app_playlist_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, PlaylistManager $playlistManager): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $playlist = $playlistManager->create($playlist, $user);
            if ( $playlist->getId() !== null ){     
                $this->addFlash('success', 'Playlist : '.$playlist->getName().' created');

                return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_playlist_show', ['id' => $playlist->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('playlist/new.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_playlist_show", methods={"GET"})
     */
    public function show(Request $request, Playlist $playlist, PlaylistManager $playlistManager): Response
    {
        $user = $this->getUser();

        if ( false === $playlistManager->canViewPlaylist($playlist, $user)) {
            $this->addFlash('warning', 'This playlist is private');

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $songs = $playlistManager->getSongs($playlist, $page, $limit);     

        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
            'songs' => $songs,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_playlist_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Playlist $playlist, PlaylistManager $playlistManager): Response
    {
        $user = $this->getUser();

        if (false === $playlistManager->canEditPlaylist($playlist, $user)) {
            $this->addFlash('warning', "you're not he owner of this playlist");

            return $this->redirectToRoute('app_playlist_show', ['id' => $playlist->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlistManager->save($playlist);

            return $this->redirectToRoute('app_playlist_show', ['id' => $playlist->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('playlist/edit.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_playlist_delete", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Playlist $playlist, PlaylistManager $playlistManager): Response
    {
        $user = $this->getUser();

        if (false === $playlistManager->canEditPlaylist($playlist, $user)) {
            $this->addFlash('warning', "you're not he owner of this playlist");

            return $this->redirectToRoute('app_playlist_show', ['id' => $playlist->getId()], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$playlist->getId(), $request->request->get('_token'))) {
            $name = $playlist->getName();
            $playlistManager->delete($playlist);
            $this->addFlash('warning', 'Playlist : "'.$name.'" deleted');
        }

        return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/remove-from-playlist/song={song}/playlist={playlist}", name="app_playlist_remove_song", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function removePlaylist(Request $request, Song $song, Playlist $playlist, PlaylistManager $playlistManager): Response
    {
        $user = $this->getUser();

        if ( false === $playlistManager->canEditPlaylist($playlist, $user)) {
            $this->addFlash('warning', "you're not he owner of this playlist");

            return $this->redirectToRoute('app_playlist_show', ['id' => $playlist->getId()], Response::HTTP_SEE_OTHER);
        }

        $refererPath = $request->headers->get('referer');

        $playlistManager->removePlaylist($song, $playlist);
        $this->addFlash('warning', 'Song : "'.$song->getName().'" removed from : '.$playlist->getName());

        if (str_contains($refererPath, '/songs/')){
            return $this->redirectToRoute('app_song_show', ['id' => $song->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_playlist_show', ['id' => $playlist->getId()], Response::HTTP_SEE_OTHER);
    }
}
