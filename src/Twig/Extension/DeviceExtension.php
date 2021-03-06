<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use DeviceDetector\DeviceDetector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DeviceExtension extends AbstractExtension
{
    private DeviceDetector $deviceDetector;

    public function __construct()
    {
        $this->deviceDetector = new DeviceDetector();
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_user_agent_info', [$this, 'getUserAgentInfo']),
        ];
    }

    public function getUserAgentInfo(string $userAgent): DeviceDetector
    {
        $this->deviceDetector->setUserAgent($userAgent);
        $this->deviceDetector->parse();

        return $this->deviceDetector;
    }
}
