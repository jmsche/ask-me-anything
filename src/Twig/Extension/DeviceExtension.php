<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Model\ScannedDevice;
use DeviceDetector\DeviceDetector;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DeviceExtension extends AbstractExtension
{
    public function __construct(
        private readonly DeviceDetector $deviceDetector,
        private readonly CacheInterface $deviceDetectorCache,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_user_agent_info', $this->getUserAgentInfo(...)),
        ];
    }

    public function getUserAgentInfo(string $userAgent): ScannedDevice
    {
        return $this->deviceDetectorCache->get('device_detector_' . $userAgent, function () use ($userAgent): ScannedDevice {
            $this->deviceDetector->setUserAgent($userAgent);
            $this->deviceDetector->parse();

            return new ScannedDevice(
                $this->deviceDetector->isBot(),
                $this->deviceDetector->getBot()['name'] ?? null,
                $this->deviceDetector->getBot()['category'] ?? null,
                $this->deviceDetector->getOs()['name'] ?? null,
                $this->deviceDetector->getOs()['version'] ?? null,
                $this->deviceDetector->getOs()['platform'] ?? null,
                $this->deviceDetector->getDeviceName(),
                $this->deviceDetector->getBrandName(),
                $this->deviceDetector->getModel(),
                $this->deviceDetector->getClient()['name'] ?? null,
                $this->deviceDetector->getClient()['version'] ?? null,
            );
        });
    }
}
