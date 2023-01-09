<?php

declare(strict_types=1);

namespace SqlTark\Compiler;

class MySqlCompiler extends BaseCompiler
{
    public static $OpeningIdentifier = '`';
    public static $ClosingIdentifier = '`';
    public static $DummyTable = 'DUAL';

    public static $EngineCode = EngineType::MySql;

    public function quote($value, bool $quoteLike = false): ?string
    {
        if (is_string($value)) {
            $result = str_replace(
                ['\\', "\r", "\n", "\t", "\x08", "'", "\"", "\x1A", "\x00"],
                ['\\\\', '\r', '\n', '\t', '\b', "\'", '\"', '\Z', '\0'],
                $value
            );
            if($quoteLike) {
                $result = str_replace(
                    ['\%', '\_'],
                    ['%', '_'],
                    $result
                );
            }
            return "'$result'";
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
