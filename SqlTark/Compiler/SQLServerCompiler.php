<?php

declare(strict_types=1);

namespace SqlTark\Compiler;

use SqlTark\Component\LimitClause;
use SqlTark\Component\OffsetClause;

class SQLServerCompiler extends BaseCompiler
{
    public static $OpeningIdentifier = '[';
    public static $ClosingIdentifier = ']';
    public static $DummyTable = null;

    public static $EngineCode = EngineType::SQLServer;

    public function quote($value, bool $quoteLike = false): ?string
    {
        if (is_string($value)) {
            return "'" . str_replace("'", "''", $value) . "'";
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

    public function compilePaging(?LimitClause $limitClause, ?OffsetClause $offsetClause): ?string
    {
        $resolvedPaging = null;

        if($offsetClause && $offsetClause->hasOffset()) {
            $offset = $offsetClause->getOffset();
            $resolvedPaging = ($resolvedPaging ?? '') . (" OFFSET {$offset} ROWS");
        }

        if($limitClause && $limitClause->hasLimit()) {
            $limit = $limitClause->getLimit();
            $resolvedPaging = ($resolvedPaging ?? '') . (" FETCH NEXT {$limit} ROWS ONLY");
        }

        return $resolvedPaging ? trim($resolvedPaging) : $resolvedPaging;
    }
}
