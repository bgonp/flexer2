<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\Uid\Uuid;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UuidExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('encodeUuid', [$this, 'encodeUuid']),
            new TwigFilter('decodeUuid', [$this, 'decodeUuid']),
        ];
    }

    public function encodeUuid(string $rfc4122): string
    {
        return Uuid::fromString($rfc4122)->toBase58();
    }

    public function decodeUuid(string $base58): string
    {
        return Uuid::fromString($base58)->toRfc4122();
    }
}
