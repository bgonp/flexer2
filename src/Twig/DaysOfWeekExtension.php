<?php

declare(strict_types=1);

namespace App\Twig;

use App\Utils\DayOfWeek;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DaysOfWeekExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [new TwigFunction('daysOfWeek', [$this, 'getDaysOfWeek'])];
    }

    public function getDaysOfWeek(): array
    {
        return DayOfWeek::getAll();
    }
}
