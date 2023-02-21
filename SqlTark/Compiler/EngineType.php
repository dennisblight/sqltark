<?php

declare(strict_types=1);

namespace SqlTark\Compiler;

final class EngineType
{
    public const Generic = 1;
    public const MySql = 2;
    public const SQLServer = 3;
    public const PostgreSql = 4;

    public static function nameOf(int $engine): ?string
    {
        switch ($engine) {
            case self::Generic:
                return 'Generic';
            case self::MySql:
                return 'MySql';
            case self::SQLServer:
                return 'SQLServer';
            case self::PostgreSql:
                return 'PostgreSql';
        }

        return null;
    }

    private function __construct()
    {
    }
}
