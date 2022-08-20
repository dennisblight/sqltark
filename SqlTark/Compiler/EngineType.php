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
            case self::Generic:
                return 'Generic';
            case self::MySql:
                return 'MySql';
            case self::SqlServer:
                return 'SqlServer';
            case self::PostgreSql:
                return 'PostgreSql';
        }
    }

    private function __construct()
    {
    }
}
