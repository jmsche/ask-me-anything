<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SessionHelper
{
    public function __construct(
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
    ) {
    }

    public function addFlash(string $type, string $message, array $params = []): void
    {
        $this->requestStack->getSession()->getFlashBag()->add($type, $this->translator->trans($message, $params, 'messages'));
    }
}
