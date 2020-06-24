<?php

declare(strict_types=1);

namespace App\Controller\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionListener
{
    private SessionInterface $session;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(SessionInterface $session, UrlGeneratorInterface $urlGenerator)
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $route = $event->getRequest()->attributes->get('_route');
            if ($route) {
                if (0 === strpos($route, 'api_')) {
                    $event->setResponse(new JsonResponse(
                        ['message' => $exception->getMessage()],
                        Response::HTTP_BAD_REQUEST
                    ));
                } else {
                    $this->session->getFlashBag()->add('error', $exception->getMessage());
                    $event->setResponse(new RedirectResponse(
                        $this->urlGenerator->generate('main'),
                        Response::HTTP_SEE_OTHER
                    ));
                }
            }
        }
    }
}
