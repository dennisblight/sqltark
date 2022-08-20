<?php

declare(strict_types=1);

namespace SqlTark;

use SqlTark\Expressions;
use SqlTark\Query\Query;
use InvalidArgumentException;
use SqlTark\Expressions\BaseExpression;

final class Helper
{
    private function __construct()
    {
    }

    public static function flatten(iterable $array): iterable
    {
        $result = [];
        foreach ($array as $item) {
            if (is_iterable($item)) {
                foreach (self::flatten($item) as $item2) {
                    $result[] = $item2;
                }
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    public static function replaceAll(?string $subject, string $match, callable $callback): ?string
    {
        if (empty($subject) || strpos($subject, $match) === false) {
            return $subject;
        }

        $splitted = explode($match, $subject);

        $splitProcess = [];
        for ($i = 1; $i < count($splitted); $i++) {
            $splitProcess[] = $callback($i - 1) . $splitted[$i];
        }

        $result = array_reduce($splitProcess, function ($acc, $item) {
            return $acc . $item;
        }, $splitted[0]);

        return $result;
    }

    public static function joinIterable(string $separator, iterable $array): string
    {
        if (is_array($array) && !isset($array[0])) {
            $array = array_values($array);
        }

        $result = '';
        foreach ($array as $index => $item) {
            $result .= $index == 0 ? $item : ($separator . $item);
        }

        return $result;
    }

    public static function countIterable(iterable $array): int
    {
        $count = 0;
        foreach ($array as $_item) $count++;
        return $count;
    }

    public static function repeat($value, $times): array
    {
        $result = [];
        while ($times--) $result[] = $value;
        return $result;
    }

    public static function expandParameters(string $sql, string $placeholder, array $bindings): string
    {
        return self::replaceAll($sql, $placeholder, function ($i) use ($bindings, $placeholder) {

            $parameter = $bindings[$i];
            if (is_iterable($parameter)) {
                $count = self::countIterable($parameter);
                return self::joinIterable(',', self::repeat($placeholder, $count));
            }

            return (string) $placeholder;
        });
    }

    /**
     * @return string[]
     */
    public static function expandExpression(string $expression): array
    {
        preg_match("/^(?:\w+\.){1,2}{(.*)}/", $expression, $matches);

        if (count($matches) == 0) {
            return [$expression];
        }

        $table = substr($expression, strpos($expression, '.{'));
        $captures = [];
        foreach (explode(',', $matches[1]) as $item) {
            $item = trim($item);
            if (!empty($item)) {
                $captures[] = $item;
            }
        }

        return array_map(function ($item) use ($table) {
            return "$table.$item";
        }, $captures);
    }

    public static function replaceIdentifierUnlessEscaped(string $input, string $escapeCharacter, string $identifier, string $newIndentifier): string
    {
        $escapeCharacter = preg_quote($escapeCharacter);
        $identifier = preg_quote($identifier);

        $pattern = "/(?<!$escapeCharacter)$identifier/";
        $nonEscapedReplace = preg_replace($pattern, $newIndentifier, $input);

        $pattern = "/$escapeCharacter$identifier/";
        return preg_replace($pattern, $identifier, $nonEscapedReplace);
    }

    public static function resolveExpression($expr, string $name)
    {
        if (is_string($expr)) {
            $expr = Expressions::column($expr);
        } elseif (is_scalar($expr) || is_null($expr) || $expr instanceof \DateTime) {
            $expr = Expressions::literal($expr);
        } elseif (!($expr instanceof BaseExpression) && !($expr instanceof Query)) {
            $class = get_class($expr);
            throw new InvalidArgumentException(
                "Could not resolve '$class' for $name parameter."
            );
        }

        return $expr;
    }

    public static function resolveLiteral($expr, string $name)
    {
        if (is_scalar($expr) ||  is_null($expr) || $expr instanceof \DateTime) {
            $expr = Expressions::literal($expr);
        } elseif (!($expr instanceof BaseExpression) && !($expr instanceof Query)) {
            if (is_object($expr) && method_exists($expr, '__toString')) {
                $expr = Expressions::literal((string) $expr);
            } else {
                $class = get_class($expr);
                throw new InvalidArgumentException(
                    "Could not resolve '$class' for $name parameter."
                );
            }
        }

        return $expr;
    }
}
