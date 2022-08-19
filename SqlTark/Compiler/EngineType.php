<?php

declare(strict_types=1);

namespace SqlTark\Compiler;

final class EngineType
{
    public const Generic = 1;
    public const MySql = 2;
    public const SqlServer = 3;
    public const PostgreSql = 4;

    public static function nameOf(int $engine): ?string
    {
        switch ($engine) {
            case 1:
                return 'Generic';
            case 2:
                return 'MySql';
            case 3:
                return 'SqlServer';
            case 4:
                return 'PostgreSql';
        }
    }

    private function __construct()
    {
    }
}
