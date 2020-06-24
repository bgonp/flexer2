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
            new TwigFilter('getDay', [$this, 'getDay']),
            new TwigFilter('getMonthName', [$this, 'getMonthName']),
            new TwigFilter('getCalendarUntil', [$this, 'getCalendar']),
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

    public function getDay(?\DateTime $date): string
    {
        return $date ? $date->format('j') : '';
    }

    public function getMonthName(?\DateTime $date): string
    {
        return $date ? ucfirst(\IntlDateFormatter::formatObject($date, 'LLLL')) : '';
    }

    public function getCalendar(\DateTime $initDate, \DateTime $finishDate): array
    {
        $interval = new \DateInterval('P1D');
        $currentDate = clone $initDate;
        $lastDate = clone $finishDate;

        if ('01' !== $currentDate->format('d')) {
            $year = (int) $currentDate->format('Y');
            $month = (int) $currentDate->format('m');
            $currentDate->setDate($year, $month, 1);
        }

        if (($lastDay = $lastDate->format('t')) !== $lastDate->format('d')) {
            $year = (int) $lastDate->format('Y');
            $month = (int) $lastDate->format('m');
            $lastDate->setDate($year, $month, (int) $lastDay);
        }

        $calendar = [];
        while ($currentDate < $finishDate) {
            $month = [
                'name' => $this->getMonthName($currentDate),
                'weeks' => [],
            ];
            do {
                if ('01' === $currentDate->format('d')) {
                    $week = array_fill(0, $initDate->format('N') - 1, null);
                } else {
                    $week = [];
                }
                while (count($week) < 7) {
                    $week[] = clone $currentDate;
                    $currentDate->add($interval);
                    if ('01' === $currentDate->format('d')) {
                        while (count($week) < 7) {
                            $week[] = null;
                        }
                    }
                }
                $month['weeks'][] = $week;
            } while ('01' !== $currentDate->format('d'));

            while (7 > count(end($month['weeks']))) {
                end($month['weeks'])[] = null;
            }

            $calendar[] = $month;
        }

        return $calendar;
    }
}
