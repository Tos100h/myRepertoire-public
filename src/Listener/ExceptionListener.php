<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    private UrlGeneratorInterface $urlGenerator;
    
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();     

        if ($exception instanceof NotFoundHttpException) {       
            $path = $event->getRequest()->getPathInfo();

            if (str_starts_with($path, "/admin")) {
                $response = new RedirectResponse($this->urlGenerator->generate('app_home'));
                $event->setResponse($response);
            }

            if (str_starts_with($path, "/artists")) {
                $response = new RedirectResponse($this->urlGenerator->generate('app_artist_index'));
                $event->setResponse($response);
            }

            if (str_starts_with($path, "/songs")) {
                $response = new RedirectResponse($this->urlGenerator->generate('app_song_index'));
                $event->setResponse($response);
            }

            if (str_starts_with($path, "/playlists")) {
                $response = new RedirectResponse($this->urlGenerator->generate('app_song_index'));
                $event->setResponse($response);
            }

            if (str_starts_with($path, "/search")) {
                $response = new RedirectResponse($this->urlGenerator->generate('app_song_index'));
                $event->setResponse($response);
            }

            if (str_starts_with($path, "/tags")) {
                $response = new RedirectResponse($this->urlGenerator->generate('app_tag_index'));
                $event->setResponse($response);
            }

            if (null === $event->getResponse()) {
                $response = new RedirectResponse($this->urlGenerator->generate('app_home'));
                $event->setResponse($response);
            }
        }
    }

}