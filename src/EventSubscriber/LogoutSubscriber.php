<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Repository\TutorialRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(private TutorialRepository $tutorialRepository) {}

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
        if (str_contains($referer, '/tutorial/view')) {
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
