<?php

declare(strict_types=1);

namespace App\Model;

final readonly class ScannedDevice
{
    public function __construct(
        public bool $isBot,
        public ?string $botName,
        public ?string $botCategory,
        public ?string $osName,
        public ?string $osVersion,
        public ?string $osPlatform,
        public ?string $deviceName,
        public ?string $brandName,
        public ?string $model,
        public ?string $clientName,
        public ?string $clientVersion,
    ) {}
}
