<?php

declare(strict_types=1);

namespace App\Utils;

class DayOfWeek
{
    private static ?array $all = null;

    private int $id;

    private string $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function equals(?int $dayOfWeek): bool
    {
        return $dayOfWeek === $this->getId();
    }

    public static function get(int $id): ?DayOfWeek
    {
        foreach (self::getAll() as $day) {
            if ($id === $day->getId()) {
                return $day;
            }
        }

        return null;
    }

    /** @return DayOfWeek[] */
    public static function getAll(): array
    {
        if (is_null(self::$all)) {
            self::$all = [
                new self(1, 'Lunes'),
                new self(2, 'Martes'),
                new self(3, 'Miércoles'),
                new self(4, 'Jueves'),
                new self(5, 'Viernes'),
                new self(6, 'Sábado'),
                new self(7, 'Domingo'),
            ];
        }

        return self::$all;
    }
}
