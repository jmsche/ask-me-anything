<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Repository\TutorialRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutSubscriber implements EventSubscriberInterface
{
    private TutorialRepository $tutorialRepository;

    public function __construct(TutorialRepository $tutorialRepository)
    {
        $this->tutorialRepository = $tutorialRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'redirect',
        ];
    }

    public function redirect(LogoutEvent $event): void
    {
        $response = $event->getResponse() ?? new RedirectResponse('/');
        $referer = $event->getRequest()->headers->get('referer');
        if (false !== strpos($referer, '/tutorial/view')) {
            preg_match('#^https?://([a-z-\.]+)/tutorial/view/([0-9]+)(.+)?$#', $referer, $matches);
            $tutorialId = (int) $matches[2];
            $tutorial = $this->tutorialRepository->find($tutorialId);
            if ($tutorial->isVisible()) {
                $response->setTargetUrl($referer);
                $event->setResponse($response);
            }
        }
    }
}
