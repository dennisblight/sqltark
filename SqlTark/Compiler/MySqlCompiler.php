<?php

declare(strict_types=1);

namespace SqlTark\Compiler;


class MySqlCompiler extends BaseCompiler
{
    public const OpeningIdentifier = '`';
    public const ClosingIdentifier = '`';
    public const DummyTable = 'DUAL';

    public const EngineCode = EngineType::MySql;

    public function quote($value): ?string
    {
        if (is_string($value)) {
            return "'" . str_replace(
                ['\\', "\r", "\n", "\t", "\x08", "'", "\"", "\x1A", "\x00"],
                ['\\\\', '\r', '\n', '\t', '\b', "\'", '\"', '\Z', '\0'],
                $value
            ) . "'";
        } elseif (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } elseif (is_scalar($value)) {
            return (string) $value;
        } elseif (is_null($value)) {
            return 'NULL';
        } elseif ($value instanceof \DateTime) {
            return "'" . $value->format('Y-m-d H:i:s') . "'";
        }
    }
}
