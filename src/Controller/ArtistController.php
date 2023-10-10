<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Manager\ArtistManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/artists")
 */
class ArtistController extends AbstractController
{
    /**
     * @Route("/", name="app_artist_index", methods={"GET"})
     */
    public function index(Request $request, ArtistManager $artistManager): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $artists = $artistManager->getArtists($page, $limit);

        return $this->render('artist/index.html.twig', [
            'artists' => $artists,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/new", name="app_artist_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, ArtistManager $artistManager): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $artistManager->save($artist);
            if ( $artist->getId() !== null ){
                $this->addFlash('success', 'Artist : '.$artist->getName().' created');
                
                return $this->redirectToRoute('app_artist_show', ['id' => $artist->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_artist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('artist/new.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_artist_show", methods={"GET"})
     */
    public function show(Request $request, Artist $artist, ArtistManager $artistManager): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $songs = $artistManager->getSongs($artist, $page, $limit);

        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
            'songs' => $songs,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_artist_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Artist $artist, ArtistManager $artistManager): Response
    {
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $artits = $artistManager->save($artist);

            return $this->redirectToRoute('app_artist_show', ['id' => $artist->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('artist/edit.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_artist_delete", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Artist $artist, ArtistManager $artistManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->request->get('_token'))) {
            $name = $artist->getName();
            $artistManager->delete($artist);
            $this->addFlash('warning', 'Artist : '.$name.' deleted');
        }

        return $this->redirectToRoute('app_artist_index', [], Response::HTTP_SEE_OTHER);
    }
}
