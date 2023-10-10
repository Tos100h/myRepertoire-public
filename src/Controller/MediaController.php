<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\Media;
use App\Form\MediaType;
use App\Manager\MediaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/media")
 * @IsGranted("ROLE_USER")
 */
class MediaController extends AbstractController
{
    /**
     * @Route("/new/song={song}", name="app_media_new", methods={"GET", "POST"})
     */
    public function new(Song $song, Request $request, MediaManager $mediaManager): Response
    {
        $media = New Media();

        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $mediaManager->setType($media);
            $result = $mediaManager->getByLink($media->getLink());

            if ($result === null) {
                $media->setSong($song);
                $mediaManager->create($media);

                return $this->redirectToRoute('app_song_show', ['id' => $song->getId()], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('warning', 'this link already exist on the song : '.$result->getSong()->getName().' Media not created'); 

            return $this->redirectToRoute('app_song_show', ['id' => $song->getId()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('media/new.html.twig', [
            'song' => $song,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{media}/edit", name="app_media_edit", methods={"GET", "POST"})
     */
    public function edit(Media $media, Request $request, MediaManager $mediaManager): Response
    {
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $mediaManager->prepareEdit($media);

            $result = $mediaManager->checkExistingMedia($media);
            if( $result === null ) {
                $media = $mediaManager->save($media);
            } else {
                $this->addFlash('warning', 'this link already exist on the song : '.$result->getSong()->getName().' , media name: '.$result->getName().'Media not saved'); 
            }

            return $this->redirectToRoute('app_song_show', ['id' => $media->getsong()->getId()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('media/edit.html.twig', [
            'form' => $form->createView(),
            'media' => $media,
        ]);

    }

    /**
     * @Route("/{media}/setrank/{rank}", name="app_media_rank", methods={"GET", "POST"})
     */
    public function setRank(Media $media, int $rank, Request $request, MediaManager $mediaManager): Response
    {
        $mediaManager->setRank($media, $rank);

        return $this->redirectToRoute('app_song_show', ['id' => $media->getsong()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{media}/setrank-last", name="app_media_rank_last", methods={"GET", "POST"})
     */
    public function setLastRank(Media $media, Request $request, MediaManager $mediaManager): Response
    {
        $lastRank = $mediaManager->getLastrank($media);
        $mediaManager->setRank($media, $lastRank+10);

        return $this->redirectToRoute('app_song_show', ['id' => $media->getsong()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/delete", name="app_media_delete", methods={"POST"})
     */
    public function delete(Media $media, Request $request, MediaManager $mediaManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$media->getId(), $request->request->get('_token'))) {
            $mediaManager->delete($media);
        }

        return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
    }
}
