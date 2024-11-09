<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class LoginSubscriber implements EventSubscriberInterface
{
    use TargetPathTrait;

    public function __construct(private RequestStack $requestStack) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (
            !$event->isMainRequest()
            || $request->isXmlHttpRequest()
            || 'app_security_login' === $request->attributes->get('_route')
        ) {
            return;
        }

        $this->saveTargetPath($this->requestStack->getSession(), 'main', $request->getUri());
    }
}
