<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Http\Client\HttpClient;
use Http\Discovery\Strategy\DiscoveryStrategy;

final class HaveIBeenPwnedDiscoveryStrategy implements DiscoveryStrategy
{
    /**
     * {@inheritDoc}
     */
    public static function getCandidates($type)
    {
        if ($type === HttpClient::class) {
            return [
                ['class' => HaveIBeenPwnedClient::class, 'condition' => HaveIBeenPwnedClient::class]
            ];
        }

        return [];
    }
}
