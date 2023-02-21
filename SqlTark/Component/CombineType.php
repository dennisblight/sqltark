<?php

declare(strict_types=1);

namespace SqlTark\Component;

final class CombineType
{
    public const Union = 1;
    public const Except = 2;
    public const Intersect = 3;

    public static function nameOf(int $type): ?string
    {
        switch ($type) {
            case self::Union:
                return 'Union';
            case self::Except:
                return 'Except';
            case self::Intersect:
                return 'Intersect';
        }

        return null;
    }

    public static function syntaxOf(int $type): ?string
    {
        switch ($type) {
            case self::Union:
                return 'UNION';
            case self::Except:
                return 'EXCEPT';
            case self::Intersect:
                return 'INTERSECT';
        }

        return null;
    }

    private function __construct()
    {
    }
}
