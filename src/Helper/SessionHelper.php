<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SessionHelper
{
    private TranslatorInterface $translator;

    private SessionInterface $session;

    public function __construct(TranslatorInterface $translator, SessionInterface $session)
    {
        $this->translator = $translator;
        $this->session = $session;
    }

    public function addFlash(string $type, string $message, array $params = []): void
    {
        $this->session->getFlashBag()->add($type, $this->translator->trans($message, $params, 'messages'));
    }
}
