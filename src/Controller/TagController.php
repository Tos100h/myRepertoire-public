<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Manager\TagManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/tags")
 */
class TagController extends AbstractController
{
    /**
     * @Route("/", name="app_tag_index", methods={"GET"})
     */
    public function index(Request $request, TagManager $tagManager): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $tags = $tagManager->getTags($page, $limit);

        return $this->render('tag/index.html.twig', [
            'tags' => $tags,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/new", name="app_tag_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, TagManager $tagManager): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagManager->save($tag);
            if ($tag->getId() !== null) {
                $this->addFlash('success', 'Tag : '.$tag->getName().' created');
                
                return $this->redirectToRoute('app_tag_show', ['id' => $tag->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER); 
        }

        return $this->renderForm('tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tag_show", methods={"GET"})
     */
    public function show(Request $request, Tag $tag, TagManager $tagManager): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $songs = $tagManager->getSongs($tag, $page, $limit);

        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
            'songs' => $songs,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_tag_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Tag $tag, TagManager $tagManager): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagManager->save($tag);

            return $this->redirectToRoute('app_tag_show', ['id' => $tag->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tag_delete", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Tag $tag, TagManager $tagManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $name = $tag->getName();
            $tagManager->delete($tag);
            $this->addFlash('warning', 'Tag : '.$name.' deleted');
        }

        return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
