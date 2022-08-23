<?php

declare(strict_types=1);

namespace SqlTark\Compiler;

use SplFixedArray;
use SqlTark\Component\AbstractColumn;
use SqlTark\Component\AbstractCondition;
use SqlTark\Component\AbstractFrom;
use SqlTark\Component\AbstractJoin;
use SqlTark\Component\AdHocTableFromClause;
use SqlTark\Component\BetweenCondition;
use SqlTark\Component\ColumnClause;
use SqlTark\Component\CompareClause;
use SqlTark\Component\ComponentType;
use SqlTark\Component\ExistsCondition;
use SqlTark\Component\FromClause;
use SqlTark\Component\GroupCondition;
use SqlTark\Component\InCondition;
use SqlTark\Component\JoinClause;
use SqlTark\Component\JoinType;
use SqlTark\Component\LikeCondition;
use SqlTark\Component\LikeType;
use SqlTark\Component\LimitClause;
use SqlTark\Component\NullCondition;
use SqlTark\Component\OffsetClause;
use SqlTark\Component\OrderClause;
use SqlTark\Component\RandomOrder;
use SqlTark\Component\RawColumn;
use SqlTark\Component\RawFromClause;
use SqlTark\Component\RawOrder;
use SqlTark\Expressions\BaseExpression;
use SqlTark\Expressions\Column;
use SqlTark\Expressions\Literal;
use SqlTark\Expressions\Variable;
use SqlTark\Helper;
use SqlTark\Query\Query;

abstract class BaseCompiler
{
    public const ParameterPlaceholder = '?';
    public const VariablePrefix = '@';
    public const OpeningIdentifier = '';
    public const ClosingIdentifier = '';
    public const EscapeCharacter = '\\';
    public const DummyTable = null;
    public const FromTableRequired = false;
    public const MaxValue = '18446744073709551615';

    public const EngineCode = EngineType::Generic;

    /**
     * @param AbstractColumn[] $columns
     */
    public function compileColumns(iterable $columns, $withAlias = true)
    {
        $expressionResolver = function ($expression) use ($withAlias) {

            if ($expression instanceof BaseExpression) {
                return $this->compileExpression($expression, $withAlias);
            } elseif ($expression instanceof Query) {
                $resolvedValue = $this->compileQuery($expression);
                $resolvedValue = "($resolvedValue)";

                if ($withAlias) {
                    $alias = $expression->getAlias();
                    if ($alias) $resolvedValue .= ' AS ' . $this->wrapIdentifier($alias);
                }

                return $resolvedValue;
            }
        };

        $result = '';
        foreach ($columns as $index => $column) {
            $resolvedColumn = null;
            if ($column instanceof ColumnClause) {

                $columnContent = $column->getColumn();
                $resolvedColumn = $expressionResolver($columnContent);
            } elseif ($column instanceof RawColumn) {

                $resolvedColumn = $this->compileRaw(
                    $column->getExpression(),
                    $column->getBindings()
                );
            }

            if ($resolvedColumn) {

                if ($index > 0) $result .= ', ';
                $result .= $resolvedColumn;
            }
        }

        return $result;
    }

    /**
     * @param AbstractColumn[] $columns
     */
    public function compileSelect(iterable $columns, bool $isDisctinct = false)
    {
        $result = $this->compileColumns($columns);
        if (empty($result)) {
            $result = '*';
        }

        if($isDisctinct) {
            $result = "DISTINCT $result";
        }

        return "SELECT $result";
    }

    public function compileFrom(?AbstractFrom $table)
    {
        $result = null;
        if ($table instanceof FromClause) {
            $expression = $table->getTable();
            if (is_string($expression)) {
                $result = $this->compileTable($expression);
            } elseif ($expression instanceof Query) {
                $result = '(' . $this->compileQuery($expression) . ') AS ' . $table->getAlias();
            }
        } elseif ($table instanceof RawFromClause) {
            $result = $this->compileRaw(
                $table->getExpression(),
                $table->getBindings()
            );
        } elseif ($table instanceof AdHocTableFromClause) {
            $columns = $table->getColumns();
            $values = $table->getValues();
            $alias = $table->getAlias();

            $result = '(';
            $first = true;
            foreach ($values as $row) {
                if (!$first) {
                    $result .= ' UNION ALL ';
                }

                $result .= 'SELECT ';
                foreach ($row as $index => $value) {
                    $resolvedValue = null;
                    if ($value instanceof BaseExpression) {
                        $resolvedValue = $this->compileExpression($value, false);
                    } elseif ($value instanceof Query) {
                        $resolvedValue = '(' . $this->compileQuery($value) . ')';
                    }

                    if ($first) {
                        $resolvedValue .= ' AS ' . $this->wrapIdentifier($columns[$index]);
                    }

                    if ($index > 0) {
                        $result .= ', ';
                    }

                    $result .= $resolvedValue;
                    if (static::FromTableRequired) {
                        $result .= ' FROM DUAL';
                    }
                }

                $first = false;
            }

            $result .= ') AS ' . $this->wrapIdentifier($alias);
        }

        if (empty($result) && !static::FromTableRequired) {
            $result = static::DummyTable;
        }

        return $result;
    }

    /**
     * @param AbstractJoin[] $joins
     */
    public function compileJoin(iterable $joins)
    {
        $result = null;
        foreach ($joins as $index => $component) {
            $resolvedJoin = null;
            if ($component instanceof JoinClause) {
                $join = $component->getJoin();
                $table = $join->getOneComponent(ComponentType::From);

                $resolvedTable = $this->compileFrom($table);

                $resolvedJoin = JoinType::syntaxOf($join->getType()) . ' ' . $resolvedTable;
                /** Natural and cross join doesn't need on condition */
                if (!in_array($join->getType(), [JoinType::CrossJoin, JoinType::NaturalJoin])) {
                    $conditions = $join->getComponents(ComponentType::Where);
                    $resolvedCondition = $this->compileConditions($conditions);
                    if (!empty($resolvedCondition)) {
                        $resolvedJoin .= ' ON ' . $resolvedCondition;
                    }
                }
            }

            if (!empty($resolvedJoin)) {
                if ($index > 0) $result .= ' ';
                $result .= $resolvedJoin;
            }
        }

        return $result;
    }

    /**
     * @param AbstractCondition[] $conditions
     */
    public function compileWhere(iterable $conditions)
    {
        $resolvedCondition = $this->compileConditions($conditions);
        if (!empty($resolvedCondition)) {
            return 'WHERE ' . $resolvedCondition;
        }

        return null;
    }

    public function compilePaging(?LimitClause $limitClause, ?OffsetClause $offsetClause)
    {
        if ($limitClause && $limitClause->hasLimit()) {
            $limit = $limitClause->getLimit();
            if ($limitClause->hasLimit() && $offsetClause->hasOffset()) {
                $offset = $offsetClause->getOffset();
                return "LIMIT $offset, $limit";
            }
            return "LIMIT $limit";
        } elseif ($offsetClause && $offsetClause->hasOffset()) {
            $offset = $offsetClause->getOffset();
            return "LIMIT " . static::MaxValue . " OFFSET $offset";
        }
    }

    /**
     * @param AbstractCondition[] $conditions
     */
    public function compileHaving(iterable $conditions)
    {
        $resolvedCondition = $this->compileConditions($conditions, ComponentType::Having);
        if (!empty($resolvedCondition)) {
            return 'HAVING ' . $resolvedCondition;
        }

        return null;
    }

    /**
     * @param AbstractCondition[] $conditions
     */
    public function compileConditions(iterable $conditions, $type = ComponentType::Where)
    {
        $expressionResolver = function ($expression, $wrapQuery = true) {

            if ($expression instanceof BaseExpression) {
                return $this->compileExpression($expression, false);
            } elseif ($expression instanceof Query) {
                $resolvedValue = $this->compileQuery($expression);
                if ($wrapQuery) $resolvedValue = "($resolvedValue)";
                return $resolvedValue;
            }
        };

        $result = null;
        foreach ($conditions as $index => $condition) {
            $resolvedCondition = null;
            if ($condition instanceof CompareClause) {
                $left = $condition->getLeft();
                $right = $condition->getRight();
                $operator = $condition->getOperator();

                $resolvedLeft = $expressionResolver($left);
                $resolvedRight = $expressionResolver($right);

                $resolvedCondition = "$resolvedLeft $operator $resolvedRight";
                if ($condition->getNot()) {
                    $resolvedCondition = "NOT ($resolvedCondition)";
                }
            } elseif ($condition instanceof BetweenCondition) {
                $column = $condition->getColumn();
                $lower = $condition->getLower();
                $higher = $condition->getHigher();

                $resolvedColumn = $expressionResolver($column);
                $resolvedLower = $expressionResolver($lower);
                $resolvedHigher = $expressionResolver($higher);

                $resolvedCondition = $resolvedColumn;
                if ($condition->getNot()) {
                    $resolvedCondition .= ' NOT';
                }

                $resolvedCondition .= " BETWEEN ($resolvedLower AND $resolvedHigher)";
            } elseif ($condition instanceof ExistsCondition) {
                $query = $condition->getQuery();
                $resolvedQuery = $this->compileQuery($query);

                $resolvedCondition = $condition->getNot() ? 'NOT EXISTS' : 'EXISTS';
                $resolvedCondition .= "($resolvedQuery)";
            } elseif ($condition instanceof NullCondition) {
                $column = $condition->getColumn();

                $resolvedCondition = $expressionResolver($column);
                $resolvedCondition .= $condition->getNot() ? ' IS NOT NULL' : ' IS NULL';
            } elseif ($condition instanceof LikeCondition) {
                $column = $condition->getColumn();
                $resolvedColumn = $expressionResolver($column);

                $value = $condition->getValue();
                $escape = $condition->getEscapeCharacter();

                $operator = $condition->getNot() ? 'NOT LIKE' : 'LIKE';
                if ($condition->isCaseSensitive()) {
                    $operator .= ' BINARY';
                }

                switch ($condition->getType()) {
                    case LikeType::Contains:
                        $value = "%$value%";
                        break;
                    case LikeType::Starts:
                        $value = "$value%";
                        break;
                    case LikeType::Ends:
                        $value = "%$value";
                        break;
                }

                $value = $this->quote($value);

                $resolvedCondition = "$column $operator $value";
                if ($escape) {
                    $resolvedCondition .= " ESCAPE " . $this->quote($escape);
                }
            } elseif ($condition instanceof InCondition) {
                $column = $condition->getColumn();
                $values = $condition->getValues();

                $resolvedColumn = $expressionResolver($column);
                $resolvedValues = '';
                if ($values instanceof Query) {
                    $resolvedValues = $this->compileQuery($values);
                } else {
                    $first = true;
                    foreach ($values as $value) {
                        if (!$first) $resolvedValues .= ', ';
                        $resolvedValues .= $expressionResolver($value);
                        $first = false;
                    }
                }

                $resolvedCondition = $resolvedColumn;
                $resolvedCondition .= $condition->getNot() ? ' NOT IN ' : ' IN ';
                $resolvedCondition .= "($resolvedValues)";
            } elseif ($condition instanceof GroupCondition) {
                $clauses = $condition->getCondition()->getComponents($type);
                $resolvedCondition = "(" . $this->compileConditions($clauses) . ")";
            }

            if ($resolvedCondition) {
                if ($index > 0) $result .= $condition->getOr() ? ' OR ' : ' AND ';
                $result .= $resolvedCondition;
            }
        }

        return $result;
    }

    // public fucntion 

    public function compileQuery(Query $query)
    {
        $selects = [];
        $from = null;
        $joins = [];
        $where = [];
        $groupBy = [];
        $havings = [];
        $orderBy = [];
        $limit = null;
        $offset = null;

        foreach ($query->getComponents() as $component) {
            switch ($component->getComponentType()) {
                case ComponentType::Select:
                    $selects[] = $component;
                    break;
                case ComponentType::From:
                    $from = $component;
                    break;
                case ComponentType::Join:
                    $joins[] = $component;
                    break;
                case ComponentType::Where:
                    $where[] = $component;
                    break;
                case ComponentType::GroupBy:
                    $groupBy[] = $component;
                    break;
                case ComponentType::Having:
                    $havings[] = $component;
                    break;
                case ComponentType::OrderBy:
                    $orderBy[] = $component;
                    break;
                case ComponentType::Limit:
                    $limit = $component;
                    break;
                case ComponentType::Offset:
                    $offset = $component;
                    break;
            }
        }

        $result = $this->compileSelect($selects, $query->isDistict());
        $result .= ' FROM ' . $this->compileFrom($from);

        $resolvedJoin = $this->compileJoin($joins);
        if($resolvedJoin) {
            $result .= ' ' . $resolvedJoin;
        }

        $resolvedWhere = $this->compileWhere($where);
        if($resolvedWhere) {
            $result .= ' ' . $resolvedWhere;
        }

        $resolvedGroupBy = $this->compileGroupBy($groupBy);
        if($resolvedGroupBy) {
            $result .= ' ' . $resolvedGroupBy;
        }

        $resolvedHaving = $this->compileHaving($havings);
        if($resolvedHaving) {
            $result .= ' ' . $resolvedHaving;
        }

        $resolvedOrderBy = $this->compileOrderBy($orderBy);
        if($resolvedOrderBy) {
            $result .= ' ' . $resolvedOrderBy;
        }

        $resolvedPaging = $this->compilePaging($limit, $offset);
        if($resolvedPaging) {
            $result .= ' ' . $resolvedPaging;
        }

        return $result;
    }

    /**
     * @param AbstractColumn[] $columns
     */
    public function compileGroupBy(iterable $columns)
    {
        $result = $this->compileColumns($columns, false);
        if (empty($result)) {
            return null;
        }

        return 'GROUP BY ' . $result;
    }

    /**
     * @param AbstractColumn[] $columns
     */
    public function compileOrderBy(iterable $columns)
    {
        $expressionResolver = function ($expression) {

            if ($expression instanceof BaseExpression) {
                return $this->compileExpression($expression, false);
            } elseif ($expression instanceof Query) {
                $resolvedValue = $this->compileQuery($expression);
                $resolvedValue = "($resolvedValue)";

                return $resolvedValue;
            }
        };

        $result = '';
        foreach ($columns as $index => $column) {
            $resolvedColumn = null;
            if ($column instanceof OrderClause) {

                $columnContent = $column->getColumn();
                $isAscending = $column->isAscending();
                $resolvedColumn = $expressionResolver($columnContent);
                $resolvedColumn .= $isAscending ? ' ASC' : ' DESC';
            } elseif ($column instanceof RawOrder) {

                $resolvedColumn = $this->compileRaw(
                    $column->getExpression(),
                    $column->getBindings()
                );
            } elseif ($column instanceof RandomOrder) {
                $resolvedColumn = 'RAND()';
            }

            if ($resolvedColumn) {
                if ($index > 0) $result .= ', ';
                $result .= $resolvedColumn;
            }
        }

        if (empty($result)) {
            return null;
        }

        return 'ORDER BY ' . $result;
    }

    /**
     * @param string $expression
     * @param SplFixedArray<BaseExpression> $bindings
     */
    public function compileRaw(string $expression, iterable $bindings = [])
    {
        $expression = trim($expression, " \t\n\r\0\x0B,");
        return Helper::replaceAll($expression, static::ParameterPlaceholder, function ($index) use ($bindings) {
            return $this->compileExpression($bindings[$index], false);
        });
    }

    public function compileExpression(BaseExpression $expression, bool $withAlias = true)
    {
        if ($expression instanceof Literal) {
            return $this->compileLiteral($expression);
        } elseif ($expression instanceof Variable) {
            return $this->compileVariable($expression);
        } elseif ($expression instanceof Column) {
            return $this->compileColumn($expression, $withAlias);
        }
    }

    public function compileLiteral(Literal $literal)
    {
        $value = $literal->getValue();
        $result = $this->quote($value);

        return $this->wrapFunction($result, $literal->getWrap());
    }

    public function compileVariable(Variable $variable)
    {
        $result = trim($variable->getName());

        if (isset($result[0]) && $result[0] != static::VariablePrefix) {
            $result = static::VariablePrefix . $result;
        }

        return $this->wrapFunction($result, $variable->getWrap());
    }

    public function compileColumn(Column $column, bool $withAlias = true)
    {
        $result = trim($column->getName());
        if (empty($result) || $result == '*') {
            return $this->wrapFunction('*', $column->getWrap());
        }

        $aliasSplit = array_map(
            function ($item) {
                return $this->wrapIdentifier($item);
            },
            $this->aliasFinder($result)
        );

        $columnExression = $this->wrapFunction($aliasSplit[0], $column->getWrap());
        if ($withAlias && isset($aliasSplit[1])) {
            $columnExression .= ' AS ' . $aliasSplit[1];
        }

        return $columnExression;
    }

    public function compileTable(string $name)
    {
        $result = trim($name);
        if (empty($result)) {
            return null;
        }

        $aliasSplit = array_map(
            function ($item) {
                return $this->wrapIdentifier($item);
            },
            $this->aliasFinder($result)
        );

        $result = $aliasSplit[0];
        if (isset($aliasSplit[1])) {
            $result .= ' AS ' . $aliasSplit[1];
        }

        return $result;
    }

    public function quote($value)
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
            return '\'' . $value->format('Y-m-d H:i:s') . '\'';
        }
    }

    protected function wrapFunction(string $value, ?string $wrapper)
    {
        if ($wrapper) {
            return $wrapper . "($value)";
        }

        return $value;
    }

    protected function wrapIdentifier(string $value)
    {
        $splitName = [];
        foreach (explode('.', $value) as $item) {
            $item = trim($item);
            if (!empty($item)) {
                if ($item != '*') {
                    if ($item[0] != static::OpeningIdentifier) {
                        $item = static::OpeningIdentifier . $item;
                    }

                    if ($item[strlen($item) - 1] != static::ClosingIdentifier) {
                        $item = $item . static::ClosingIdentifier;
                    }
                }
                $splitName[] = $item;
            }
        }

        return join('.', $splitName);
    }

    protected function aliasFinder(string $value)
    {
        $pos = stripos($value, ' as ');
        if (false === $pos) {
            return [trim($value)];
        }

        return [trim(substr($value, 0, $pos)), trim(substr($value, 4 + $pos))];
    }
}
