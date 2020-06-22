<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTimeExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('showDate', [$this, 'showDate']),
            new TwigFilter('formatDate', [$this, 'formatDate']),
            new TwigFilter('formatTime', [$this, 'formatTime']),
        ];
    }

    public function showDate(?\DateTime $date): string
    {
        return $date ? $date->format('d/m/Y') : '';
    }

    public function formatDate(?\DateTime $date): string
    {
        return $date ? $date->format('Y-m-d') : '';
    }

    public function formatTime(?\DateTime $time): string
    {
        return $time ? $time->format('H:i') : '';
    }
}
