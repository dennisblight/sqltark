<?php

declare(strict_types=1);

namespace SqlTark\Compiler;


class MySqlCompiler extends BaseCompiler
{
    public const OpeningIdentifier = '`';
    public const ClosingIdentifier = '`';
    public const DummyTable = 'DUAL';

    public const EngineCode = EngineType::MySql;
}
