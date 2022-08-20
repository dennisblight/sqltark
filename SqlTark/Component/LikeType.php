<?php

declare(strict_types=1);

namespace SqlTark\Component;

final class LikeType
{
    public const Like = 0;
    public const Starts = 1;
    public const Ends = 2;
    public const Contains = 3;

    public static function nameOf(int $type): ?string
    {
        switch ($type) {
            case self::Like:
                return 'Like';
            case self::Starts:
                return 'Starts';
            case self::Ends:
                return 'Ends';
            case self::Contains:
                return 'Contains';
        }
    }

    private function __construct()
    {
    }
}
